# mapper/sync.py
# Copyright (C) 2005, 2006, 2007, 2008 Michael Bayer mike_mp@zzzcomputing.com
#
# This module is part of SQLAlchemy and is released under
# the MIT License: http://www.opensource.org/licenses/mit-license.php

"""private module containing functions used for copying data between instances
based on join conditions.
"""

from sqlalchemy.orm import exc, util as mapperutil

def populate(source, source_mapper, dest, dest_mapper, synchronize_pairs):
    for l, r in synchronize_pairs:
        try:
            value = source_mapper._get_state_attr_by_column(source, l)
        except exc.UnmappedColumnError:
            _raise_col_to_prop(False, source_mapper, l, dest_mapper, r)

        try:
            dest_mapper._set_state_attr_by_column(dest, r, value)
        except exc.UnmappedColumnError:
            _raise_col_to_prop(True, source_mapper, l, dest_mapper, r)

def clear(dest, dest_mapper, synchronize_pairs):
    for l, r in synchronize_pairs:
        if r.primary_key:
            raise AssertionError("Dependency rule tried to blank-out primary key column '%s' on instance '%s'" % (r, mapperutil.state_str(dest)))
        try:
            dest_mapper._set_state_attr_by_column(dest, r, None)
        except exc.UnmappedColumnError:
            _raise_col_to_prop(True, None, l, dest_mapper, r)

def update(source, source_mapper, dest, old_prefix, synchronize_pairs):
    for l, r in synchronize_pairs:
        try:
            oldvalue = source_mapper._get_committed_attr_by_column(source.obj(), l)
            value = source_mapper._get_state_attr_by_column(source, l)
        except exc.UnmappedColumnError:
            _raise_col_to_prop(False, source_mapper, l, None, r)
        dest[r.key] = value
        dest[old_prefix + r.key] = oldvalue

def populate_dict(source, source_mapper, dict_, synchronize_pairs):
    for l, r in synchronize_pairs:
        try:
            value = source_mapper._get_state_attr_by_column(source, l)
        except exc.UnmappedColumnError:
            _raise_col_to_prop(False, source_mapper, l, None, r)

        dict_[r.key] = value

def source_changes(uowcommit, source, source_mapper, synchronize_pairs):
    for l, r in synchronize_pairs:
        try:
            prop = source_mapper._get_col_to_prop(l)
        except exc.UnmappedColumnError:
            _raise_col_to_prop(False, source_mapper, l, None, r)
        (added, unchanged, deleted) = uowcommit.get_attribute_history(source, prop.key, passive=True)
        if added and deleted:
            return True
    else:
        return False

def dest_changes(uowcommit, dest, dest_mapper, synchronize_pairs):
    for l, r in synchronize_pairs:
        try:
            prop = dest_mapper._get_col_to_prop(r)
        except exc.UnmappedColumnError:
            _raise_col_to_prop(True, None, l, dest_mapper, r)
        (added, unchanged, deleted) = uowcommit.get_attribute_history(dest, prop.key, passive=True)
        if added and deleted:
            return True
    else:
        return False

def _raise_col_to_prop(isdest, source_mapper, source_column, dest_mapper, dest_column):
    if isdest:
        raise exc.UnmappedColumnError("Can't execute sync rule for destination column '%s'; mapper '%s' does not map this column.  Try using an explicit `foreign_keys` collection which does not include this column (or use a viewonly=True relation)." % (dest_column, source_mapper))
    else:
        raise exc.UnmappedColumnError("Can't execute sync rule for source column '%s'; mapper '%s' does not map this column.  Try using an explicit `foreign_keys` collection which does not include destination column '%s' (or use a viewonly=True relation)." % (source_column, source_mapper, dest_column))
