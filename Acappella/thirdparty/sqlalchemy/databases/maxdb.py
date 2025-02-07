# maxdb.py
#
# This module is part of SQLAlchemy and is released under
# the MIT License: http://www.opensource.org/licenses/mit-license.php

"""Support for the MaxDB database.

TODO: More module docs!  MaxDB support is currently experimental.

Overview
--------

The ``maxdb`` dialect is **experimental** and has only been tested on 7.6.03.007
and 7.6.00.037.  Of these, **only 7.6.03.007 will work** with SQLAlchemy's ORM.
The earlier version has severe ``LEFT JOIN`` limitations and will return
incorrect results from even very simple ORM queries.

Only the native Python DB-API is currently supported.  ODBC driver support
is a future enhancement.

Connecting
----------

The username is case-sensitive.  If you usually connect to the
database with sqlcli and other tools in lower case, you likely need to
use upper case for DB-API.

Implementation Notes
--------------------

Also check the DatabaseNotes page on the wiki for detailed information.

With the 7.6.00.37 driver and Python 2.5, it seems that all DB-API
generated exceptions are broken and can cause Python to crash.

For 'somecol.in_([])' to work, the IN operator's generation must be changed
to cast 'NULL' to a numeric, i.e. NUM(NULL).  The DB-API doesn't accept a
bind parameter there, so that particular generation must inline the NULL value,
which depends on [ticket:807].

The DB-API is very picky about where bind params may be used in queries.

Bind params for some functions (e.g. MOD) need type information supplied.
The dialect does not yet do this automatically.

Max will occasionally throw up 'bad sql, compile again' exceptions for
perfectly valid SQL.  The dialect does not currently handle these, more
research is needed.

MaxDB 7.5 and Sap DB <= 7.4 reportedly do not support schemas.  A very
slightly different version of this dialect would be required to support
those versions, and can easily be added if there is demand.  Some other
required components such as an Max-aware 'old oracle style' join compiler
(thetas with (+) outer indicators) are already done and available for
integration- email the devel list if you're interested in working on
this.

"""
import datetime, itertools, re

from sqlalchemy import exc, schema, sql, util
from sqlalchemy.sql import operators as sql_operators, expression as sql_expr
from sqlalchemy.sql import compiler, visitors
from sqlalchemy.engine import base as engine_base, default
from sqlalchemy import types as sqltypes


__all__ = [
    'MaxString', 'MaxUnicode', 'MaxChar', 'MaxText', 'MaxInteger',
    'MaxSmallInteger', 'MaxNumeric', 'MaxFloat', 'MaxTimestamp',
    'MaxDate', 'MaxTime', 'MaxBoolean', 'MaxBlob',
    ]


class _StringType(sqltypes.String):
    _type = None

    def __init__(self, length=None, encoding=None, **kw):
        super(_StringType, self).__init__(length=length, **kw)
        self.encoding = encoding

    def get_col_spec(self):
        if self.length is None:
            spec = 'LONG'
        else:
            spec = '%s(%s)' % (self._type, self.length)

        if self.encoding is not None:
            spec = ' '.join([spec, self.encoding.upper()])
        return spec

    def bind_processor(self, dialect):
        if self.encoding == 'unicode':
            return None
        else:
            def process(value):
                if isinstance(value, unicode):
                    return value.encode(dialect.encoding)
                else:
                    return value
            return process

    def result_processor(self, dialect):
        def process(value):
            while True:
                if value is None:
                    return None
                elif isinstance(value, unicode):
                    return value
                elif isinstance(value, str):
                    if self.convert_unicode or dialect.convert_unicode:
                        return value.decode(dialect.encoding)
                    else:
                        return value
                elif hasattr(value, 'read'):
                    # some sort of LONG, snarf and retry
                    value = value.read(value.remainingLength())
                    continue
                else:
                    # unexpected type, return as-is
                    return value
        return process


class MaxString(_StringType):
    _type = 'VARCHAR'

    def __init__(self, *a, **kw):
        super(MaxString, self).__init__(*a, **kw)


class MaxUnicode(_StringType):
    _type = 'VARCHAR'

    def __init__(self, length=None, **kw):
        super(MaxUnicode, self).__init__(length=length, encoding='unicode')


class MaxChar(_StringType):
    _type = 'CHAR'


class MaxText(_StringType):
    _type = 'LONG'

    def __init__(self, *a, **kw):
        super(MaxText, self).__init__(*a, **kw)

    def get_col_spec(self):
        spec = 'LONG'
        if self.encoding is not None:
            spec = ' '.join((spec, self.encoding))
        elif self.convert_unicode:
            spec = ' '.join((spec, 'UNICODE'))

        return spec


class MaxInteger(sqltypes.Integer):
    def get_col_spec(self):
        return 'INTEGER'


class MaxSmallInteger(MaxInteger):
    def get_col_spec(self):
        return 'SMALLINT'


class MaxNumeric(sqltypes.Numeric):
    """The FIXED (also NUMERIC, DECIMAL) data type."""

    def __init__(self, precision=None, scale=None, **kw):
        kw.setdefault('asdecimal', True)
        super(MaxNumeric, self).__init__(scale=scale, precision=precision,
                                         **kw)

    def bind_processor(self, dialect):
        return None

    def get_col_spec(self):
        if self.scale and self.precision:
            return 'FIXED(%s, %s)' % (self.precision, self.scale)
        elif self.precision:
            return 'FIXED(%s)' % self.precision
        else:
            return 'INTEGER'


class MaxFloat(sqltypes.Float):
    """The FLOAT data type."""

    def get_col_spec(self):
        if self.precision is None:
            return 'FLOAT'
        else:
            return 'FLOAT(%s)' % (self.precision,)


class MaxTimestamp(sqltypes.DateTime):
    def get_col_spec(self):
        return 'TIMESTAMP'

    def bind_processor(self, dialect):
        def process(value):
            if value is None:
                return None
            elif isinstance(value, basestring):
                return value
            elif dialect.datetimeformat == 'internal':
                ms = getattr(value, 'microsecond', 0)
                return value.strftime("%Y%m%d%H%M%S" + ("%06u" % ms))
            elif dialect.datetimeformat == 'iso':
                ms = getattr(value, 'microsecond', 0)
                return value.strftime("%Y-%m-%d %H:%M:%S." + ("%06u" % ms))
            else:
                raise exc.InvalidRequestError(
                    "datetimeformat '%s' is not supported." % (
                    dialect.datetimeformat,))
        return process

    def result_processor(self, dialect):
        def process(value):
            if value is None:
                return None
            elif dialect.datetimeformat == 'internal':
                return datetime.datetime(
                    *[int(v)
                      for v in (value[0:4], value[4:6], value[6:8],
                                value[8:10], value[10:12], value[12:14],
                                value[14:])])
            elif dialect.datetimeformat == 'iso':
                return datetime.datetime(
                    *[int(v)
                      for v in (value[0:4], value[5:7], value[8:10],
                                value[11:13], value[14:16], value[17:19],
                                value[20:])])
            else:
                raise exc.InvalidRequestError(
                    "datetimeformat '%s' is not supported." % (
                    dialect.datetimeformat,))
        return process


class MaxDate(sqltypes.Date):
    def get_col_spec(self):
        return 'DATE'

    def bind_processor(self, dialect):
        def process(value):
            if value is None:
                return None
            elif isinstance(value, basestring):
                return value
            elif dialect.datetimeformat == 'internal':
                return value.strftime("%Y%m%d")
            elif dialect.datetimeformat == 'iso':
                return value.strftime("%Y-%m-%d")
            else:
                raise exc.InvalidRequestError(
                    "datetimeformat '%s' is not supported." % (
                    dialect.datetimeformat,))
        return process

    def result_processor(self, dialect):
        def process(value):
            if value is None:
                return None
            elif dialect.datetimeformat == 'internal':
                return datetime.date(
                    *[int(v) for v in (value[0:4], value[4:6], value[6:8])])
            elif dialect.datetimeformat == 'iso':
                return datetime.date(
                    *[int(v) for v in (value[0:4], value[5:7], value[8:10])])
            else:
                raise exc.InvalidRequestError(
                    "datetimeformat '%s' is not supported." % (
                    dialect.datetimeformat,))
        return process


class MaxTime(sqltypes.Time):
    def get_col_spec(self):
        return 'TIME'

    def bind_processor(self, dialect):
        def process(value):
            if value is None:
                return None
            elif isinstance(value, basestring):
                return value
            elif dialect.datetimeformat == 'internal':
                return value.strftime("%H%M%S")
            elif dialect.datetimeformat == 'iso':
                return value.strftime("%H-%M-%S")
            else:
                raise exc.InvalidRequestError(
                    "datetimeformat '%s' is not supported." % (
                    dialect.datetimeformat,))
        return process

    def result_processor(self, dialect):
        def process(value):
            if value is None:
                return None
            elif dialect.datetimeformat == 'internal':
                t = datetime.time(
                    *[int(v) for v in (value[0:4], value[4:6], value[6:8])])
                return t
            elif dialect.datetimeformat == 'iso':
                return datetime.time(
                    *[int(v) for v in (value[0:4], value[5:7], value[8:10])])
            else:
                raise exc.InvalidRequestError(
                    "datetimeformat '%s' is not supported." % (
                    dialect.datetimeformat,))
        return process


class MaxBoolean(sqltypes.Boolean):
    def get_col_spec(self):
        return 'BOOLEAN'


class MaxBlob(sqltypes.Binary):
    def get_col_spec(self):
        return 'LONG BYTE'

    def bind_processor(self, dialect):
        def process(value):
            if value is None:
                return None
            else:
                return str(value)
        return process

    def result_processor(self, dialect):
        def process(value):
            if value is None:
                return None
            else:
                return value.read(value.remainingLength())
        return process


colspecs = {
    sqltypes.Integer: MaxInteger,
    sqltypes.Smallinteger: MaxSmallInteger,
    sqltypes.Numeric: MaxNumeric,
    sqltypes.Float: MaxFloat,
    sqltypes.DateTime: MaxTimestamp,
    sqltypes.Date: MaxDate,
    sqltypes.Time: MaxTime,
    sqltypes.String: MaxString,
    sqltypes.Binary: MaxBlob,
    sqltypes.Boolean: MaxBoolean,
    sqltypes.Text: MaxText,
    sqltypes.CHAR: MaxChar,
    sqltypes.TIMESTAMP: MaxTimestamp,
    sqltypes.BLOB: MaxBlob,
    sqltypes.Unicode: MaxUnicode,
    }

ischema_names = {
    'boolean': MaxBoolean,
    'char': MaxChar,
    'character': MaxChar,
    'date': MaxDate,
    'fixed': MaxNumeric,
    'float': MaxFloat,
    'int': MaxInteger,
    'integer': MaxInteger,
    'long binary': MaxBlob,
    'long unicode': MaxText,
    'long': MaxText,
    'long': MaxText,
    'smallint': MaxSmallInteger,
    'time': MaxTime,
    'timestamp': MaxTimestamp,
    'varchar': MaxString,
    }


class MaxDBExecutionContext(default.DefaultExecutionContext):
    def post_exec(self):
        # DB-API bug: if there were any functions as values,
        # then do another select and pull CURRVAL from the
        # autoincrement column's implicit sequence... ugh
        if self.compiled.isinsert and not self.executemany:
            table = self.compiled.statement.table
            index, serial_col = _autoserial_column(table)

            if serial_col and (not self.compiled._safeserial or
                               not(self._last_inserted_ids) or
                               self._last_inserted_ids[index] in (None, 0)):
                if table.schema:
                    sql = "SELECT %s.CURRVAL FROM DUAL" % (
                        self.compiled.preparer.format_table(table))
                else:
                    sql = "SELECT CURRENT_SCHEMA.%s.CURRVAL FROM DUAL" % (
                        self.compiled.preparer.format_table(table))

                if self.connection.engine._should_log_info:
                    self.connection.engine.logger.info(sql)
                rs = self.cursor.execute(sql)
                id = rs.fetchone()[0]

                if self.connection.engine._should_log_debug:
                    self.connection.engine.logger.debug([id])
                if not self._last_inserted_ids:
                    # This shouldn't ever be > 1?  Right?
                    self._last_inserted_ids = \
                      [None] * len(table.primary_key.columns)
                self._last_inserted_ids[index] = id

        super(MaxDBExecutionContext, self).post_exec()

    def get_result_proxy(self):
        if self.cursor.description is not None:
            for column in self.cursor.description:
                if column[1] in ('Long Binary', 'Long', 'Long Unicode'):
                    return MaxDBResultProxy(self)
        return engine_base.ResultProxy(self)


class MaxDBCachedColumnRow(engine_base.RowProxy):
    """A RowProxy that only runs result_processors once per column."""

    def __init__(self, parent, row):
        super(MaxDBCachedColumnRow, self).__init__(parent, row)
        self.columns = {}
        self._row = row
        self._parent = parent

    def _get_col(self, key):
        if key not in self.columns:
            self.columns[key] = self._parent._get_col(self._row, key)
        return self.columns[key]

    def __iter__(self):
        for i in xrange(len(self._row)):
            yield self._get_col(i)

    def __repr__(self):
        return repr(list(self))

    def __eq__(self, other):
        return ((other is self) or
                (other == tuple([self._get_col(key)
                                 for key in xrange(len(self._row))])))
    def __getitem__(self, key):
        if isinstance(key, slice):
            indices = key.indices(len(self._row))
            return tuple([self._get_col(i) for i in xrange(*indices)])
        else:
            return self._get_col(key)

    def __getattr__(self, name):
        try:
            return self._get_col(name)
        except KeyError:
            raise AttributeError(name)


class MaxDBResultProxy(engine_base.ResultProxy):
    _process_row = MaxDBCachedColumnRow


class MaxDBDialect(default.DefaultDialect):
    name = 'maxdb'
    supports_alter = True
    supports_unicode_statements = True
    max_identifier_length = 32
    supports_sane_rowcount = True
    supports_sane_multi_rowcount = False
    preexecute_pk_sequences = True

    # MaxDB-specific
    datetimeformat = 'internal'

    def __init__(self, _raise_known_sql_errors=False, **kw):
        super(MaxDBDialect, self).__init__(**kw)
        self._raise_known = _raise_known_sql_errors

        if self.dbapi is None:
            self.dbapi_type_map = {}
        else:
            self.dbapi_type_map = {
                'Long Binary': MaxBlob(),
                'Long byte_t': MaxBlob(),
                'Long Unicode': MaxText(),
                'Timestamp': MaxTimestamp(),
                'Date': MaxDate(),
                'Time': MaxTime(),
                datetime.datetime: MaxTimestamp(),
                datetime.date: MaxDate(),
                datetime.time: MaxTime(),
            }

    def dbapi(cls):
        from sapdb import dbapi as _dbapi
        return _dbapi
    dbapi = classmethod(dbapi)

    def create_connect_args(self, url):
        opts = url.translate_connect_args(username='user')
        opts.update(url.query)
        return [], opts

    def type_descriptor(self, typeobj):
        if isinstance(typeobj, type):
            typeobj = typeobj()
        if isinstance(typeobj, sqltypes.Unicode):
            return typeobj.adapt(MaxUnicode)
        else:
            return sqltypes.adapt_type(typeobj, colspecs)

    def create_execution_context(self, connection, **kw):
        return MaxDBExecutionContext(self, connection, **kw)

    def do_execute(self, cursor, statement, parameters, context=None):
        res = cursor.execute(statement, parameters)
        if isinstance(res, int) and context is not None:
            context._rowcount = res

    def do_release_savepoint(self, connection, name):
        # Does MaxDB truly support RELEASE SAVEPOINT <id>?  All my attempts
        # produce "SUBTRANS COMMIT/ROLLBACK not allowed without SUBTRANS
        # BEGIN SQLSTATE: I7065"
        # Note that ROLLBACK TO works fine.  In theory, a RELEASE should
        # just free up some transactional resources early, before the overall
        # COMMIT/ROLLBACK so omitting it should be relatively ok.
        pass

    def get_default_schema_name(self, connection):
        try:
            return self._default_schema_name
        except AttributeError:
            name = self.identifier_preparer._normalize_name(
                connection.execute('SELECT CURRENT_SCHEMA FROM DUAL').scalar())
            self._default_schema_name = name
            return name

    def has_table(self, connection, table_name, schema=None):
        denormalize = self.identifier_preparer._denormalize_name
        bind = [denormalize(table_name)]
        if schema is None:
            sql = ("SELECT tablename FROM TABLES "
                   "WHERE TABLES.TABLENAME=? AND"
                   "  TABLES.SCHEMANAME=CURRENT_SCHEMA ")
        else:
            sql = ("SELECT tablename FROM TABLES "
                   "WHERE TABLES.TABLENAME = ? AND"
                   "  TABLES.SCHEMANAME=? ")
            bind.append(denormalize(schema))

        rp = connection.execute(sql, bind)
        found = bool(rp.fetchone())
        rp.close()
        return found

    def table_names(self, connection, schema):
        if schema is None:
            sql = (" SELECT TABLENAME FROM TABLES WHERE "
                   " SCHEMANAME=CURRENT_SCHEMA ")
            rs = connection.execute(sql)
        else:
            sql = (" SELECT TABLENAME FROM TABLES WHERE "
                   " SCHEMANAME=? ")
            matchname = self.identifier_preparer._denormalize_name(schema)
            rs = connection.execute(sql, matchname)
        normalize = self.identifier_preparer._normalize_name
        return [normalize(row[0]) for row in rs]

    def reflecttable(self, connection, table, include_columns):
        denormalize = self.identifier_preparer._denormalize_name
        normalize = self.identifier_preparer._normalize_name

        st = ('SELECT COLUMNNAME, MODE, DATATYPE, CODETYPE, LEN, DEC, '
              '  NULLABLE, "DEFAULT", DEFAULTFUNCTION '
              'FROM COLUMNS '
              'WHERE TABLENAME=? AND SCHEMANAME=%s '
              'ORDER BY POS')

        fk = ('SELECT COLUMNNAME, FKEYNAME, '
              '  REFSCHEMANAME, REFTABLENAME, REFCOLUMNNAME, RULE, '
              '  (CASE WHEN REFSCHEMANAME = CURRENT_SCHEMA '
              '   THEN 1 ELSE 0 END) AS in_schema '
              'FROM FOREIGNKEYCOLUMNS '
              'WHERE TABLENAME=? AND SCHEMANAME=%s '
              'ORDER BY FKEYNAME ')

        params = [denormalize(table.name)]
        if not table.schema:
            st = st % 'CURRENT_SCHEMA'
            fk = fk % 'CURRENT_SCHEMA'
        else:
            st = st % '?'
            fk = fk % '?'
            params.append(denormalize(table.schema))

        rows = connection.execute(st, params).fetchall()
        if not rows:
            raise exc.NoSuchTableError(table.fullname)

        include_columns = set(include_columns or [])

        for row in rows:
            (name, mode, col_type, encoding, length, scale,
             nullable, constant_def, func_def) = row

            name = normalize(name)

            if include_columns and name not in include_columns:
                continue

            type_args, type_kw = [], {}
            if col_type == 'FIXED':
                type_args = length, scale
                # Convert FIXED(10) DEFAULT SERIAL to our Integer
                if (scale == 0 and
                    func_def is not None and func_def.startswith('SERIAL')):
                    col_type = 'INTEGER'
                    type_args = length,
            elif col_type in 'FLOAT':
                type_args = length,
            elif col_type in ('CHAR', 'VARCHAR'):
                type_args = length,
                type_kw['encoding'] = encoding
            elif col_type == 'LONG':
                type_kw['encoding'] = encoding

            try:
                type_cls = ischema_names[col_type.lower()]
                type_instance = type_cls(*type_args, **type_kw)
            except KeyError:
                util.warn("Did not recognize type '%s' of column '%s'" %
                          (col_type, name))
                type_instance = sqltypes.NullType

            col_kw = {'autoincrement': False}
            col_kw['nullable'] = (nullable == 'YES')
            col_kw['primary_key'] = (mode == 'KEY')

            if func_def is not None:
                if func_def.startswith('SERIAL'):
                    if col_kw['primary_key']:
                        # No special default- let the standard autoincrement
                        # support handle SERIAL pk columns.
                        col_kw['autoincrement'] = True
                    else:
                        # strip current numbering
                        col_kw['server_default'] = schema.DefaultClause(
                            sql.text('SERIAL'))
                        col_kw['autoincrement'] = True
                else:
                    col_kw['server_default'] = schema.DefaultClause(
                        sql.text(func_def))
            elif constant_def is not None:
                col_kw['server_default'] = schema.DefaultClause(sql.text(
                    "'%s'" % constant_def.replace("'", "''")))

            table.append_column(schema.Column(name, type_instance, **col_kw))

        fk_sets = itertools.groupby(connection.execute(fk, params),
                                    lambda row: row.FKEYNAME)
        for fkeyname, fkey in fk_sets:
            fkey = list(fkey)
            if include_columns:
                key_cols = set([r.COLUMNNAME for r in fkey])
                if key_cols != include_columns:
                    continue

            columns, referants = [], []
            quote = self.identifier_preparer._maybe_quote_identifier

            for row in fkey:
                columns.append(normalize(row.COLUMNNAME))
                if table.schema or not row.in_schema:
                    referants.append('.'.join(
                        [quote(normalize(row[c]))
                         for c in ('REFSCHEMANAME', 'REFTABLENAME',
                                   'REFCOLUMNNAME')]))
                else:
                    referants.append('.'.join(
                        [quote(normalize(row[c]))
                         for c in ('REFTABLENAME', 'REFCOLUMNNAME')]))

            constraint_kw = {'name': fkeyname.lower()}
            if fkey[0].RULE is not None:
                rule = fkey[0].RULE
                if rule.startswith('DELETE '):
                    rule = rule[7:]
                constraint_kw['ondelete'] = rule

            table_kw = {}
            if table.schema or not row.in_schema:
                table_kw['schema'] = normalize(fkey[0].REFSCHEMANAME)

            ref_key = schema._get_table_key(normalize(fkey[0].REFTABLENAME),
                                            table_kw.get('schema'))
            if ref_key not in table.metadata.tables:
                schema.Table(normalize(fkey[0].REFTABLENAME),
                             table.metadata,
                             autoload=True, autoload_with=connection,
                             **table_kw)

            constraint = schema.ForeignKeyConstraint(columns, referants,
                                                     **constraint_kw)
            table.append_constraint(constraint)

    def has_sequence(self, connection, name):
        # [ticket:726] makes this schema-aware.
        denormalize = self.identifier_preparer._denormalize_name
        sql = ("SELECT sequence_name FROM SEQUENCES "
               "WHERE SEQUENCE_NAME=? ")

        rp = connection.execute(sql, denormalize(name))
        found = bool(rp.fetchone())
        rp.close()
        return found


class MaxDBCompiler(compiler.DefaultCompiler):
    operators = compiler.DefaultCompiler.operators.copy()
    operators[sql_operators.mod] = lambda x, y: 'mod(%s, %s)' % (x, y)

    function_conversion = {
        'CURRENT_DATE': 'DATE',
        'CURRENT_TIME': 'TIME',
        'CURRENT_TIMESTAMP': 'TIMESTAMP',
        }

    # These functions must be written without parens when called with no
    # parameters.  e.g. 'SELECT DATE FROM DUAL' not 'SELECT DATE() FROM DUAL'
    bare_functions = set([
        'CURRENT_SCHEMA', 'DATE', 'FALSE', 'SYSDBA', 'TIME', 'TIMESTAMP',
        'TIMEZONE', 'TRANSACTION', 'TRUE', 'USER', 'UID', 'USERGROUP',
        'UTCDATE', 'UTCDIFF'])

    def default_from(self):
        return ' FROM DUAL'

    def for_update_clause(self, select):
        clause = select.for_update
        if clause is True:
            return " WITH LOCK EXCLUSIVE"
        elif clause is None:
            return ""
        elif clause == "read":
            return " WITH LOCK"
        elif clause == "ignore":
            return " WITH LOCK (IGNORE) EXCLUSIVE"
        elif clause == "nowait":
            return " WITH LOCK (NOWAIT) EXCLUSIVE"
        elif isinstance(clause, basestring):
            return " WITH LOCK %s" % clause.upper()
        elif not clause:
            return ""
        else:
            return " WITH LOCK EXCLUSIVE"

    def apply_function_parens(self, func):
        if func.name.upper() in self.bare_functions:
            return len(func.clauses) > 0
        else:
            return True

    def visit_function(self, fn, **kw):
        transform = self.function_conversion.get(fn.name.upper(), None)
        if transform:
            fn = fn._clone()
            fn.name = transform
        return super(MaxDBCompiler, self).visit_function(fn, **kw)

    def visit_cast(self, cast, **kwargs):
        # MaxDB only supports casts * to NUMERIC, * to VARCHAR or
        # date/time to VARCHAR.  Casts of LONGs will fail.
        if isinstance(cast.type, (sqltypes.Integer, sqltypes.Numeric)):
            return "NUM(%s)" % self.process(cast.clause)
        elif isinstance(cast.type, sqltypes.String):
            return "CHR(%s)" % self.process(cast.clause)
        else:
            return self.process(cast.clause)

    def visit_sequence(self, sequence):
        if sequence.optional:
            return None
        else:
            return (self.dialect.identifier_preparer.format_sequence(sequence) +
                    ".NEXTVAL")

    class ColumnSnagger(visitors.ClauseVisitor):
        def __init__(self):
            self.count = 0
            self.column = None
        def visit_column(self, column):
            self.column = column
            self.count += 1

    def _find_labeled_columns(self, columns, use_labels=False):
        labels = {}
        for column in columns:
            if isinstance(column, basestring):
                continue
            snagger = self.ColumnSnagger()
            snagger.traverse(column)
            if snagger.count == 1:
                if isinstance(column, sql_expr._Label):
                    labels[unicode(snagger.column)] = column.name
                elif use_labels:
                    labels[unicode(snagger.column)] = column._label

        return labels

    def order_by_clause(self, select):
        order_by = self.process(select._order_by_clause)

        # ORDER BY clauses in DISTINCT queries must reference aliased
        # inner columns by alias name, not true column name.
        if order_by and getattr(select, '_distinct', False):
            labels = self._find_labeled_columns(select.inner_columns,
                                                select.use_labels)
            if labels:
                for needs_alias in labels.keys():
                    r = re.compile(r'(^| )(%s)(,| |$)' %
                                   re.escape(needs_alias))
                    order_by = r.sub((r'\1%s\3' % labels[needs_alias]),
                                     order_by)

        # No ORDER BY in subqueries.
        if order_by:
            if self.is_subquery():
                # It's safe to simply drop the ORDER BY if there is no
                # LIMIT.  Right?  Other dialects seem to get away with
                # dropping order.
                if select._limit:
                    raise exc.InvalidRequestError(
                        "MaxDB does not support ORDER BY in subqueries")
                else:
                    return ""
            return " ORDER BY " + order_by
        else:
            return ""

    def get_select_precolumns(self, select):
        # Convert a subquery's LIMIT to TOP
        sql = select._distinct and 'DISTINCT ' or ''
        if self.is_subquery() and select._limit:
            if select._offset:
                raise exc.InvalidRequestError(
                    'MaxDB does not support LIMIT with an offset.')
            sql += 'TOP %s ' % select._limit
        return sql

    def limit_clause(self, select):
        # The docs say offsets are supported with LIMIT.  But they're not.
        # TODO: maybe emulate by adding a ROWNO/ROWNUM predicate?
        if self.is_subquery():
            # sub queries need TOP
            return ''
        elif select._offset:
            raise exc.InvalidRequestError(
                'MaxDB does not support LIMIT with an offset.')
        else:
            return ' \n LIMIT %s' % (select._limit,)

    def visit_insert(self, insert):
        self.isinsert = True
        self._safeserial = True

        colparams = self._get_colparams(insert)
        for value in (insert.parameters or {}).itervalues():
            if isinstance(value, sql_expr._Function):
                self._safeserial = False
                break

        return ''.join(('INSERT INTO ',
                         self.preparer.format_table(insert.table),
                         ' (',
                         ', '.join([self.preparer.format_column(c[0])
                                    for c in colparams]),
                         ') VALUES (',
                         ', '.join([c[1] for c in colparams]),
                         ')'))


class MaxDBDefaultRunner(engine_base.DefaultRunner):
    def visit_sequence(self, seq):
        if seq.optional:
            return None
        return self.execute_string("SELECT %s.NEXTVAL FROM DUAL" % (
            self.dialect.identifier_preparer.format_sequence(seq)))


class MaxDBIdentifierPreparer(compiler.IdentifierPreparer):
    reserved_words = set([
        'abs', 'absolute', 'acos', 'adddate', 'addtime', 'all', 'alpha',
        'alter', 'any', 'ascii', 'asin', 'atan', 'atan2', 'avg', 'binary',
        'bit', 'boolean', 'byte', 'case', 'ceil', 'ceiling', 'char',
        'character', 'check', 'chr', 'column', 'concat', 'constraint', 'cos',
        'cosh', 'cot', 'count', 'cross', 'curdate', 'current', 'curtime',
        'database', 'date', 'datediff', 'day', 'dayname', 'dayofmonth',
        'dayofweek', 'dayofyear', 'dec', 'decimal', 'decode', 'default',
        'degrees', 'delete', 'digits', 'distinct', 'double', 'except',
        'exists', 'exp', 'expand', 'first', 'fixed', 'float', 'floor', 'for',
        'from', 'full', 'get_objectname', 'get_schema', 'graphic', 'greatest',
        'group', 'having', 'hex', 'hextoraw', 'hour', 'ifnull', 'ignore',
        'index', 'initcap', 'inner', 'insert', 'int', 'integer', 'internal',
        'intersect', 'into', 'join', 'key', 'last', 'lcase', 'least', 'left',
        'length', 'lfill', 'list', 'ln', 'locate', 'log', 'log10', 'long',
        'longfile', 'lower', 'lpad', 'ltrim', 'makedate', 'maketime',
        'mapchar', 'max', 'mbcs', 'microsecond', 'min', 'minute', 'mod',
        'month', 'monthname', 'natural', 'nchar', 'next', 'no', 'noround',
        'not', 'now', 'null', 'num', 'numeric', 'object', 'of', 'on',
        'order', 'packed', 'pi', 'power', 'prev', 'primary', 'radians',
        'real', 'reject', 'relative', 'replace', 'rfill', 'right', 'round',
        'rowid', 'rowno', 'rpad', 'rtrim', 'second', 'select', 'selupd',
        'serial', 'set', 'show', 'sign', 'sin', 'sinh', 'smallint', 'some',
        'soundex', 'space', 'sqrt', 'stamp', 'statistics', 'stddev',
        'subdate', 'substr', 'substring', 'subtime', 'sum', 'sysdba',
        'table', 'tan', 'tanh', 'time', 'timediff', 'timestamp', 'timezone',
        'to', 'toidentifier', 'transaction', 'translate', 'trim', 'trunc',
        'truncate', 'ucase', 'uid', 'unicode', 'union', 'update', 'upper',
        'user', 'usergroup', 'using', 'utcdate', 'utcdiff', 'value', 'values',
        'varchar', 'vargraphic', 'variance', 'week', 'weekofyear', 'when',
        'where', 'with', 'year', 'zoned' ])

    def _normalize_name(self, name):
        if name is None:
            return None
        if name.isupper():
            lc_name = name.lower()
            if not self._requires_quotes(lc_name):
                return lc_name
        return name

    def _denormalize_name(self, name):
        if name is None:
            return None
        elif (name.islower() and
              not self._requires_quotes(name)):
            return name.upper()
        else:
            return name

    def _maybe_quote_identifier(self, name):
        if self._requires_quotes(name):
            return self.quote_identifier(name)
        else:
            return name


class MaxDBSchemaGenerator(compiler.SchemaGenerator):
    def get_column_specification(self, column, **kw):
        colspec = [self.preparer.format_column(column),
                   column.type.dialect_impl(self.dialect).get_col_spec()]

        if not column.nullable:
            colspec.append('NOT NULL')

        default = column.default
        default_str = self.get_column_default_string(column)

        # No DDL default for columns specified with non-optional sequence-
        # this defaulting behavior is entirely client-side. (And as a
        # consequence, non-reflectable.)
        if (default and isinstance(default, schema.Sequence) and
            not default.optional):
            pass
        # Regular default
        elif default_str is not None:
            colspec.append('DEFAULT %s' % default_str)
        # Assign DEFAULT SERIAL heuristically
        elif column.primary_key and column.autoincrement:
            # For SERIAL on a non-primary key member, use
            # DefaultClause(text('SERIAL'))
            try:
                first = [c for c in column.table.primary_key.columns
                         if (c.autoincrement and
                             (isinstance(c.type, sqltypes.Integer) or
                              (isinstance(c.type, MaxNumeric) and
                               c.type.precision)) and
                             not c.foreign_keys)].pop(0)
                if column is first:
                    colspec.append('DEFAULT SERIAL')
            except IndexError:
                pass
        return ' '.join(colspec)

    def get_column_default_string(self, column):
        if isinstance(column.server_default, schema.DefaultClause):
            if isinstance(column.default.arg, basestring):
                if isinstance(column.type, sqltypes.Integer):
                    return str(column.default.arg)
                else:
                    return "'%s'" % column.default.arg
            else:
                return unicode(self._compile(column.default.arg, None))
        else:
            return None

    def visit_sequence(self, sequence):
        """Creates a SEQUENCE.

        TODO: move to module doc?

        start
          With an integer value, set the START WITH option.

        increment
          An integer value to increment by.  Default is the database default.

        maxdb_minvalue
        maxdb_maxvalue
          With an integer value, sets the corresponding sequence option.

        maxdb_no_minvalue
        maxdb_no_maxvalue
          Defaults to False.  If true, sets the corresponding sequence option.

        maxdb_cycle
          Defaults to False.  If true, sets the CYCLE option.

        maxdb_cache
          With an integer value, sets the CACHE option.

        maxdb_no_cache
          Defaults to False.  If true, sets NOCACHE.
        """

        if (not sequence.optional and
            (not self.checkfirst or
             not self.dialect.has_sequence(self.connection, sequence.name))):

            ddl = ['CREATE SEQUENCE',
                   self.preparer.format_sequence(sequence)]

            sequence.increment = 1

            if sequence.increment is not None:
                ddl.extend(('INCREMENT BY', str(sequence.increment)))

            if sequence.start is not None:
                ddl.extend(('START WITH', str(sequence.start)))

            opts = dict([(pair[0][6:].lower(), pair[1])
                         for pair in sequence.kwargs.items()
                         if pair[0].startswith('maxdb_')])

            if 'maxvalue' in opts:
                ddl.extend(('MAXVALUE', str(opts['maxvalue'])))
            elif opts.get('no_maxvalue', False):
                ddl.append('NOMAXVALUE')
            if 'minvalue' in opts:
                ddl.extend(('MINVALUE', str(opts['minvalue'])))
            elif opts.get('no_minvalue', False):
                ddl.append('NOMINVALUE')

            if opts.get('cycle', False):
                ddl.append('CYCLE')

            if 'cache' in opts:
                ddl.extend(('CACHE', str(opts['cache'])))
            elif opts.get('no_cache', False):
                ddl.append('NOCACHE')

            self.append(' '.join(ddl))
            self.execute()


class MaxDBSchemaDropper(compiler.SchemaDropper):
    def visit_sequence(self, sequence):
        if (not sequence.optional and
            (not self.checkfirst or
             self.dialect.has_sequence(self.connection, sequence.name))):
            self.append("DROP SEQUENCE %s" %
                        self.preparer.format_sequence(sequence))
            self.execute()


def _autoserial_column(table):
    """Finds the effective DEFAULT SERIAL column of a Table, if any."""

    for index, col in enumerate(table.primary_key.columns):
        if (isinstance(col.type, (sqltypes.Integer, sqltypes.Numeric)) and
            col.autoincrement):
            if isinstance(col.default, schema.Sequence):
                if col.default.optional:
                    return index, col
            elif (col.default is None or
                  (not isinstance(col.server_default, schema.DefaultClause))):
                return index, col

    return None, None

dialect = MaxDBDialect
dialect.preparer = MaxDBIdentifierPreparer
dialect.statement_compiler = MaxDBCompiler
dialect.schemagenerator = MaxDBSchemaGenerator
dialect.schemadropper = MaxDBSchemaDropper
dialect.defaultrunner = MaxDBDefaultRunner
