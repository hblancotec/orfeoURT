#!/usr/bin/python
# -*- coding: iso-8859-15 -*-

from __future__ import with_statement
import sys, os, types, datetime, uuid, copy

# Para declarativedata
from sqlalchemy.schema         import Table, Column, MetaData
from sqlalchemy.orm            import synonym as _orm_synonym, mapper, comparable_property
from sqlalchemy.orm.interfaces import MapperProperty
from sqlalchemy.orm.properties import PropertyLoader, ColumnProperty
from sqlalchemy.orm.attributes import *
from sqlalchemy.types          import *
from sqlalchemy.sql.expression import _ScalarSelect
from sqlalchemy                import util, exceptions

from sqlalchemy                      import *
from sqlalchemy.orm                  import *
from sqlalchemy.orm.mapper           import Mapper
from sqlalchemy.ext.declarative      import DeclarativeMeta
from sqlalchemy.ext.associationproxy import AssociationProxy, _AssociationList
from sqlalchemy.orm.session          import *
from sqlalchemy.ext.declarative      import *
import sqlalchemy.orm.session as session

from onetoproxy  import *

from utils       import utils, ac_validate
from utils.utils import *

ac_DataSource = objectDic({})
utils_Register_Object('ac_DataSource', ac_DataSource)

def dataClass_register(name, classObj):
    name = name.upper()
    utils_Register_Object(name, classObj)
    ac_DataSource[name] = classObj    

def dataClass_getSource(name, message):
    
    name = name.upper()
    source = None    
    if ac_DataSource.has_key(name):
        return ac_DataSource[name]
    else:        
        raise Exception, "dataClass_getSource: no existe source [%s] > de: %s " % (name, message)
ac_register_function(dataClass_getSource)

# selecciona valores dinamicos, de acuerdo a valores del registro
# !!!!!!!!!!! NO USAR EN SORT !!!!!!!!!!!!!!!!!
class dinamyc_attr(object):
   def __init__(self, key, default, dicOptions={}):
      self.key        = key
      self.dicOptions = dicOptions
      self.default    = default

   def __get__(self, obj, objclass):
      value = "&...............&"
      if obj is None:
         return self.default
      field = getattr(obj, self.key, None)
      for k, v in self.dicOptions.items():
         if v == field:             
            return getattr(obj, k)         
      return value
utils_Register_Object('dinamyc_attr', dinamyc_attr)

# REGRESA UN ATRIBUTO QUE NO ES COLUMN O ONETOONE
# DE UNA CLASE REMOTA CON RELACION A ESTA
# !!!!!!!!!!! NO USAR EN SORT !!!!!!!!!!!!!!!!!
class ref_attr(object):
   def __init__(self, localclass, relation, remoteclass, remoteattribute):
      self.localclass      = localclass
      self.relation        = relation   
      self.remoteclass     = remoteclass
      self.remoteattribute = remoteattribute

   def __get__(self, obj, objclass):
      if obj is None:
         return getattr(self.remoteclass, self.remoteattribute)
      relationObj = getattr(obj, self.relation)
      value = "&...............&"
      if relationObj != None:
         value = getattr(relationObj, self.remoteattribute)      
      return value
utils_Register_Object('ref_attr', ref_attr)

#operadores para where de sql
import operator
def ac_in(col, values):
   return col.in_(*values)
   
def ac_in_(col, value):
   return col.in_(value)

ac_SqlOperators = {operator.lt    :operator.lt,
                   "<"            :operator.lt, #menor
                      
                   operator.le    :operator.le,
                   "<="           :operator.le, #menor o igual
                      
                   operator.gt    :operator.gt,                      
                   ">"            :operator.gt, #mayor
                      
                   operator.ge    :operator.ge,
                   ">="           :operator.ge, #mayor o igual

                   operator.eq    :operator.eq,
                   "=="           :operator.eq, #igual
                   "="            :operator.eq, #igual

                   operator.ne    :operator.ne,
                   "!="           :operator.ne, #no igual                       

                   "in"           :ac_in, #in, en
                   "in_"          :ac_in_, #in, en
                  }

#operadores para ordenamientos
ac_sortSqlOperators = {asc   :asc,
                       "asc"  :asc, #ascendente
                       desc   :desc,
                       "desc" :desc  #descendente                      
                      }

#session global del sistema
_Session = sessionmaker(autoflush=True, autocommit=True)
utils_Register_Object("_Session",  _Session)

# extension para eventos de session
class MySessionExtension(SessionExtension):
    pass

# amplia la clase de session
def dynamicData_DbSession():
    class DBExtended(_Session):
        def __init__(self, **kwarg):
            super(DBExtended, self).__init__(**kwarg)
       
    return DBExtended(extension=MySessionExtension())    
utils_Register_Function(dynamicData_DbSession)

_DBSession = dynamicData_DbSession() #session generica

utils_Register_Object("dySession",  dynamicData_DbSession)

#redefinicion de base declarativa
def ac_declarative_base(engine=None, metadata=None, mapper=None):
    lcl_metadata = metadata or MetaData()
    if engine:
        lcl_metadata.bind = engine
    class Base(object):
        __metaclass__ = DeclarativeMeta
        metadata = lcl_metadata
        if mapper:
           __mapper_cls__ = mapper
        _decl_class_registry = {}
        def __init__(self, *args, **kwargs):           
           self.__ISERROR__ = self.validateSetAttributes(kwargs)
           
    return Base

ac_DBBase = ac_declarative_base()   

#operadores para ordenamientos
ac_sortSqlOperators = {asc    :asc,
                       "asc"  :asc, #ascendente
                       desc   :desc,
                       "desc" :desc  #descendente                      
                      }


count = 0;

# clase de extension para definicion de clases de bases de datos
class ac_ExtDbBase(object):    
    # nombres de las columnas para este datasource 
    __columnsAttrs__    = {} # hay que definirlo en cada clase derivada
    # orden visual en pantalla, generacion a partir de source
    __visualorder__     = [] # hay que definirlo en cada clase derivada
    # arributos de las columnas de datos dynamicos
    __columnsdynamics__ = {} # hay que definirlo en cada clase derivada
    __DBSession__       = None
    __ID__              = "s_id"
    __SOURCE__          = False
    __ISERROR__         = False

    ##################################################
    ########### funciones de objeto/instancia ########
    ##################################################    
    # asigna engine
    def setEngine(self, engine=None):
        self.metadata.bind = engine     

    # lee engine
    def getEngine(self, engine=None):
        return self.metadata.bind

    # regresa el campo de la clase
    def getField(self, k):
        p = getattr(type(self), k, None)
        if p != None:
           field = None
           if   type(p) in [InstrumentedAttribute, Mapper._CompileOnAttr]:
              field = p.property.columns[0]
            
           return field
         
        return False 

    # regresa el tipo del campo
    def typeField(self, field):
        typeF    = type(field)
        typedata = field.type
        # columna sqlalchemy
        if   typeF == Column:            
           if type(field.type) == Unicode:
              typedata = "Unicode"
               
        return typedata                    

    # valida todos los campos de la clase, propios, extendidos, y relacionados
    def validateData(self, name, value):
        messages = []
        if self.__validators__.has_key(name):
            validators = self.__validators__[name]
            # lista de tuplas de validadores para campo - name           
            for v in validators:
                "v tupla, 0:nombbre de la funcion, 1:parametros"
                validator = validatorsDict__[v[0]]
                params    = v[1]
                params['value'] = value
                val       = validator(**params)
                if val:
                   messages.append(val)
                   
        return False if len(messages) == 0 else messages

    # valida que existan atributos en la clase y
    # asigna valores en creacion de objeto
    def validateSetAttributes(self, kwargs):
        errorResult = [] #listado de errores
        # valida que la columna exista para este profile
        for k in kwargs:
            # valor del argumento
            value = kwargs[k]
            # campo de la entidad
            field     = self.getField(k)
            if not field:
               message = "En la tabla: {%s}, No existe Campo con Nombre: {%s} " % \
                          (self.__dataName__, k)
               errorResult.append(message)
            else:                  
               # tipo del campo
               typefield = self.typeField(field)
               # u"Text", viene de la base de datos, todo texto se
               # almacena y lee en unicode
               if (typefield in [u"Text", "Unicode"]) and kwargs[k] != None:                  
                  value = unicode(kwargs[k])

               if not hasattr(type(self), k):
                  message = "En la tabla: {%s}, No existe Campo con Nombre: {%s} " % \
                            (self.__dataName__, k)
                  errorResult.append(message)
               else:
                  # asigna atributo en el objeto=self
                  setattr(self, k, value)
                
        return errorResult

    # valor por default para columnas
    def defaultColumns(self, kwargs):
        for c in self.__table__.c:           
            # argumentos sin  valor para columna
            if (not kwargs.has_key(c.name)) or (kwargs[c.name] == None):
                # valor por default de la clase
                if self.__columnsAttrs__.has_key(c.name):
                    if self.__columnsAttrs__[c.name].has_key("default"):
                        default = self.__columnsAttrs__[c.name]["default"]
                        if default in [types.FunctionType, types.BuiltinFunctionType]:
                           default = default()                         
                        value = default
                        field = self.getField(c.name)
                        if field:
                           if self.typeField(field) in [u"Text", "Unicode"]:
                              value = unicode(value)
                           kwargs[c.name] = value
        return kwargs    

    # valida todos los campos de la clase, propios, extendidos, y relacionados
    def validateColumns(self, kwargs):
        # valor por default columnas
        kwargs = self.defaultColumns(kwargs)
       
        # validacion de atributos clase definida
        errorResult = self.validateSetAttributes(kwargs)        
        
        if len(errorResult) > 0: 
           return errorResult                         
                        
        #listado de errores
        errorResult = [] 
        # columnas a validar valor
        items = [c for c in self.__table__.c \
                 # si no permite nulos y no tiene default,
                 # hay que validar que tenga valor
                 if ((not c.nullable) and (c.default == None)) or
                 # o simplemente viene un valor
                    (kwargs.has_key(c.name))]
                      
        # nombres columnas de la tabla
        keys  = [c.name for c in items]
        dicFields = {} # diccionario de campos a validar
        if len(keys) > 0:
            dicFields =  dict(zip(keys, items))
        
        # valida valor de campo uno a uno 
        for k in dicFields.keys():
            # nombre del campo
            nameField = k
            # nombre descriptivo
            if self.__columnsAttrs__.has_key(k) and self.__columnsAttrs__[k].has_key("name"):                        
               nameField = self.__columnsAttrs__[k]["name"]
               
            # se valida que exista valor para este campo
            if kwargs.has_key(k):                
                val = self.validateData(k, kwargs[k])
                if val:
                   message = "En la tabla: {%s}, Para: {%s}, validaciones: %s " % (self.__dataName__, nameField, val)
                   errorResult.append(message) 
            else:
                message = "En la tabla: {%s}, Falta Valor Para: {%s} " % (self.__dataName__, nameField)
                errorResult.append(message) 
               
        if len(errorResult) > 0:
           return errorResult
           
        return False

    # lee los valores del objeto
    def getObjectValues(self):        
        fields = dict(zip([c.name for c in self.__table__.c],
                          [None   for c in self.__table__.c]))        
        
        for c in self.__dict__.keys():
           if c in self.__table__.c:
              fields[c] = self.__dict__[c]

        return fields

    # valida valores del registro para salvar y actualizar
    def validateSaveUpdate(self):                
        fields = self.getObjectValues()        
        return self.validateColumns(fields)

    # valida valores del registro para salvar y actualizar
    def validateDuplicate(self, update=-1):
        for idx in self.__table__.indexes:
           # si es unico
           if idx.unique: 
              whereList   = []
              whereValue  = []
              whereFields = []
              # para cada columna del indice                
              for c in idx.columns:
                 # lista de filtros por index
                 value = self.__dict__[c.name]
                 whereList.append(getattr(type(self), c.name) == value)                 
                 whereValue.append(value)                    
                 if self.__columnsAttrs__.has_key(c.name) and \
                    self.__columnsAttrs__[c.name].has_key("name"):                        
                    whereFields.append(self.__columnsAttrs__[c.name]["name"])
                 else:
                    whereFields.append(c.name)
                     
              session = dynamicData_DbSession()              
              q = session.query(getattr(self.__class__ , self.__ID__))
              for w in whereList:
                 q = q.filter(w)

              try:
                 if update > 0:
                    duplicate = q.all()
                 else:
                    duplicate = q.count()                   
              except Exception, e:
                 print ">>>>>>>>>>", str(e)

              if   (update == -1) and (duplicate > 0):
                 return "En la tabla: {%s}, Ya existe(n) registro(s) para: {%s} " % \
                        (self.__dataName__, dict(zip(whereFields, whereValue)))
              elif (update > 0) and (len(duplicate) > 0) and (update not in duplicate[0]):
                 return "En la tabla: {%s}, Ya existe(n) registro(s) para: {%s} " % \
                        (self.__dataName__, dict(zip(whereFields, whereValue))) 
        
        return False

    # salva validando que no existan llaves duplicadas    
    def save(self):
        # para cada campo
        val = self.validateSaveUpdate()        
        if not val:           
           # para cada indice de la tabla
           val = self.validateDuplicate()
           if not val:    
              try:
                 session = self.getSession()
                 session.save(self)        
                 session.flush()
              except Exception, e:
                 return "En la tabla: {%s}, Error creando registro {%s} " % \
                        (self.__dataName__, str(e))
           else:
              return val                       
        else:
           return val
         
        return False
        
    ##################################################
    ########### funciones de clase ###################
    ##################################################    
    def getSession(cls, DBSession=None):
        return dynamicData_DbSession()
        #return _Session()
              
    # crea filtro por source, metodo de clases
    def getSourceName(cls):
        key    = 's_source'
        source = getattr(cls, key, None)        
        if source != None:
            if cls.__columnsAttrs__.has_key(key) and \
               cls.__columnsAttrs__[key].has_key('default'):
               source = cls.__columnsAttrs__[key]['default']
        return source

    # valida si es onetoone columna
    def isOneToOne(cls, c):
       f   = cls.validateFieldInClass(c, "")
       key = getattr(f, 'key', None)       
       if key:          
          if cls.__mapper__._init_properties.has_key(key):
             mp = cls.__mapper__._init_properties[key]             
             if isinstance(mp, OneToOneMapperProperty):
                return True
       return False     

    def getOneToOneRelations(cls, c):
       relations = []
       if cls.isOneToOne(c):                    
          f     = cls.validateFieldInClass(c, "")
          mp    = cls.__mapper__._init_properties[f.key]
          relations.append(mp.comparator.prop.relation.primaryjoin)
          prop  = mp.relation.mapper.get_property(mp.remote_property_name)
          mprcm = mp.proxy_property.remote_class.__mapper__
          i = 5
          while prop and mprcm._init_properties.has_key(prop.key) and i > 0:
             i = i - 1 
             mp = mprcm._init_properties[prop.key]             
             relations.append(mp.comparator.prop.relation.primaryjoin)
             mprcm = mp.proxy_property.remote_class.__mapper__
                    
       return relations

    # crea filtro por source, metodo de clases
    def makeSourceFilter(cls, key='s_source'):
        filter = []
        # maneja fuente
        if cls.__SOURCE__:
           source = getattr(cls, key, None)                
           if source != None:
              if cls.__columnsAttrs__.has_key(key) and \
                 cls.__columnsAttrs__[key].has_key('default'):
                  filter = source ==  cls.__columnsAttrs__[key]['default']
                  
        return filter      

    # retorna diccionario con keys:nombre de tabla, value:nombre generico
    def getDictNames(cls, columns):
        return dict(zip(columns, cls.getFieldNames(columns)))      

    # existe campo por nombre descriptivo
    # regresa nombre columna base de datos
    def getFieldName(cls, c, callBy=""):
        if type(getattr(cls, c, None)) == types.NoneType:
            find = False
            # busca si el campo esta renombrado para esta clase
            for d in cls.__columnsAttrs__.keys():
                name = cls.__columnsAttrs__[d].get("name", None)
                if name == c:
                    c = d
                    find= True
                    break
            # no encontro el clase, ni renombrado    
            if not find:
                message = callBy + " - En la tabla: {%s}, No existe Campo con Nombre: {%s} " % \
                         (cls.__dataName__, c)        
                raise Exception, message
               
        return c    

    # retorna? campo -por nombre-, en clase, metodo de clases
    def validateFieldInClass(cls, c, callBy=""):        
        c = cls.getFieldName(c, callBy="")
        return getattr(cls, c)        

    # retorna? lista de columnas -por nombre-, metodo de clases
    def validateFieldsInClass(cls, columns=[], callBy=""):
        colObj = []
        for c in columns:
            col = cls.validateFieldInClass(c, callBy)
            colObj.append(col)
        return colObj

    # existe? campo en tabla local -por nombre-, en clase, metodo de clases
    def validateOnlyMapperFieldInClass(cls, c, callBy=""):
        p = cls.validateFieldInClass(c, callBy)
        if (type(p) in [InstrumentedAttribute, Mapper._CompileOnAttr]) or \
           (isinstance(p, OneToOneProxy)):   
            return p
        else:
            return None 

    # existe? lista de columnas de la tabla local existan, metodo de clases
    def validateOnlyMapperFieldsInClass(cls, columns=[], callBy=""):
        colObj = []
        for c in columns:
            col = cls.validateOnlyMapperFieldInClass(c, callBy)
            if col != None:
               colObj.append(col)
        return colObj

    # retorna un campo de la clase
    def getFieldData(cls, f):        
        if   (type(f) in [InstrumentedAttribute, Mapper._CompileOnAttr]):
            field = f.property.columns[0]
            
        elif (getattr(f, 'property', None) != None) or \
             (isinstance(f, OneToOneProxy)) : #onetooneproxy
            field = f.property
            i = 5
            # itera sobre mapperproperty anidados
            while getattr(field, 'columns', None) == None and i > 0:
               i = i - 1
               field = field.property
                
            field = field.columns[0]    
           
        elif type(f) == _ScalarSelect: 
            for k in cls.__dict__:
                # si es asociacion
                if type(cls.__dict__[k]) == AssociationProxy_Proxy:
                    if hash(getattr(cls, k)) == hash(f):
                        field = cls.__columnsdynamics__[k]
        else:            
            raise Exception, "getFieldData, {%s} - tipo desconocido {%s}, en clase {%s}" % (f, type(f), cls)
        return field
        
    # retorna un campo de la clase
    def getFieldDataAttr(cls, f, attr):
        field = cls.getFieldData(f)
        return getattr(field, attr)

    # retorna atributos de item visual o de procesamiento
    def getFieldAllAttrs(cls, f, callBy=""):        
        col = cls.validateFieldInClass(f, callBy)
        fd  = cls.getFieldData(col)        
        nf  = cls.getFieldName(f, callBy)
        dicAttrs = {}
        colAttrs = None
        if cls.__columnsAttrs__.has_key(nf):            
            colAttrs = cls.__columnsAttrs__[nf]
            dicAttrs.update(colAttrs)
            
        # aqui no estamos manejando atributos dinamicos ni de relacion
        
        # nombre del campo en la tabla o clase
        dicAttrs['field'] = nf
        
        # nombre para label del campo
        label = nf # deafult
        if colAttrs and colAttrs.has_key("label"):             
            label = colAttrs["label"]
        elif colAttrs and colAttrs.has_key("name"):
            label = colAttrs["name"]
        dicAttrs['label'] = label

        # tipo        
        typeF = "TEXT" # default
        if colAttrs and colAttrs.has_key("type"):
            typeF = colAttrs["type"]
        else:
            typeC = type(fd.type)
            if   typeC in [Unicode, String, UnicodeText, VARCHAR, NCHAR, CHAR]:
                typeF = "TEXT"                
            elif typeC in [INT, SMALLINT, Integer, SmallInteger, Smallinteger]:
                typeF = "INTEGER"
            elif typeC in [FLOAT, NUMERIC, DECIMAL, Float]:
                typeF = "FLOAT"
            elif typeC in [TIMESTAMP, DATETIME, DateTime]:
                typeF = "DATETIME"
            elif typeC in [DATE, Date]:
                typeF = "DATE"
            elif typeC in [TIME, Time]:
                typeF = "TIME"
        dicAttrs['type'] = typeF       

        # longitud
        length = 10 # default
        if colAttrs and colAttrs.has_key("length"):
            length = colAttrs["length"]
        dicAttrs['length'] = length  

        # nullable
        nullable = False # nullable
        if colAttrs and colAttrs.has_key("nullable"):
            nullable = colAttrs["nullable"]
        else:
            nullable = fd.nullable
        dicAttrs['nullable'] = nullable
        
        # inputValidator
        inputValidator = "" # validador de entrada
        if colAttrs and colAttrs.has_key("inputValidator"):
            inputValidator = colAttrs["inputValidator"]
        dicAttrs['inputValidator'] = inputValidator  

        # default visual
        default = "" # validador de entrada
        if colAttrs and colAttrs.has_key("default"):
            if type(colAttrs["default"]) in \
               [types.FunctionType, types.BuiltinFunctionType]:               
               default = colAttrs["default"]()
            else: 
               default = colAttrs["default"]        
        dicAttrs['valueDefault'] = default        

        # protegido
        protected = False # mostrar visualmente- default
        if colAttrs and colAttrs.has_key("protected"):
           protected = colAttrs["protected"]
        dicAttrs['protected'] = protected

        # formato
        format = 'Y-m-d' # ano, mes, dia
        if colAttrs and colAttrs.has_key("format"):            
           format = colAttrs["format"]
        dicAttrs['format'] = format

        # descripcion
        description = 10 # default
        if colAttrs and colAttrs.has_key("description"):            
            description = colAttrs["description"]
        else:
            description = fd.description
            
        if description != dicAttrs['field']:
           dicAttrs['description'] = description
        else:   
           dicAttrs['description'] = ""
           
        # !!!! esto deberia calcularse en el navegdor
        size = int(dicAttrs['length']) * 6
        if size > 450: size = 450
        if size < 30 : size = 105
        dicAttrs['width']     = size
        dicAttrs['maxLength'] = int(dicAttrs['length'])            
                    
        return dicAttrs

    def getFieldExtjs(cls, f, callBy=""):
        dicExtJs = {}
        fAttrs = cls.getFieldAllAttrs(f, callBy)                                 
        dicExtJs['name']       = fAttrs['field'] + "_@_@_" + str(uuid.uuid1())
        dicExtJs['id']         = dicExtJs['name']                   
        dicExtJs['_id']        = fAttrs.get('_id', "")
        dicExtJs['_register']  = fAttrs.get('_register', False)
        dicExtJs['_field']     = fAttrs['field']
        dicExtJs['_source']    = cls.__name__
        dicExtJs['fieldLabel'] = fAttrs['label']
        dicExtJs['disabled']   = fAttrs.get('disabled', False)
        dicExtJs['value']      = fAttrs['valueDefault']
        dicExtJs['maxLength']  = fAttrs['length']
        dicExtJs['name']  = fAttrs['field'] + "_@_@_" + str(uuid.uuid1())
        dicExtJs['width']      = int(round(dicExtJs['maxLength'] * 7.5))
        if dicExtJs['width'] > 450: dicExtJs['width'] = 450
        
        # tipo de widget
        xtype = 'textfield'
        if  fAttrs.has_key('combo'):
           xtype = 'combo'
           dicExtJs['emptyText']    = fAttrs.get('emptyText', "")
           
        elif  fAttrs.has_key('comborelation'):
           xtype = 'comboboxex' 
           dicExtJs['emptyText']    = fAttrs.get('emptyText', "")
           dicExtJs['displayField'] = fAttrs['comborelation'].get('displayField', 'name')
           dicExtJs['valueField']   = fAttrs['comborelation'].get('valueField',   'id')
           dicExtJs['hiddenName']   = fAttrs['comborelation'].get('hiddenName',   'name')          
           dicExtJs['emptyText']    = fAttrs['comborelation'].get('emptyText', '')        
           dicExtJs['reload']       = fAttrs['comborelation'].get('reload', False)
           dicExtJs['autoLoad']     = fAttrs['comborelation'].get('autoLoad', True)           
           dicExtJs['url']          = fAttrs['comborelation'].get('url', '')
           dicExtJs['tpl']          = fAttrs['comborelation'].get('tpl', '')
           dicExtJs['fields']       = fAttrs['comborelation'].get('fields', ['name', 'id'])
           dicExtJs['listeners']    = fAttrs['comborelation'].get('listeners', {})
           del dicExtJs['maxLength']

        elif  fAttrs.has_key('comboboxlov'):
           xtype = 'comboboxlov' 
           dicExtJs['emptyText']    = fAttrs.get('emptyText', "")
           dicExtJs['displayField'] = fAttrs['comboboxlov'].get('displayField', 'name')
           dicExtJs['valueField']   = fAttrs['comboboxlov'].get('valueField',   'id')
           dicExtJs['hiddenName']   = fAttrs['comboboxlov'].get('hiddenName',   'name')          
           dicExtJs['emptyText']    = fAttrs['comboboxlov'].get('emptyText', '')        
           dicExtJs['reload']       = fAttrs['comboboxlov'].get('reload', False)
           dicExtJs['autoLoad']     = fAttrs['comboboxlov'].get('autoLoad', True)           
           dicExtJs['url']          = fAttrs['comboboxlov'].get('url', '')
           dicExtJs['tpl']          = fAttrs['comboboxlov'].get('tpl', '')   
           dicExtJs['fields']       = fAttrs['comboboxlov'].get('fields', ['name', 'id'])
           dicExtJs['listeners']    = fAttrs['comboboxlov'].get('listeners', {})
           del dicExtJs['maxLength']           
           
        elif  fAttrs.has_key('radio'):
           xtype = 'radiogroup'
           dicExtJs['radio'] = fAttrs['radio']

        elif  fAttrs.has_key('checkbox'):
           xtype = 'checkboxgroup'
           dicExtJs['checkbox'] = fAttrs['checkbox']
           
        elif  fAttrs.has_key('textarea'):
           xtype = 'textarea'
           dicExtJs['anchor'] = fAttrs['textarea'].get('anchor')
           
        elif fAttrs['type'] in ["TEXT"]:
           xtype = 'textfield'
           
        elif fAttrs['type'] in ["INTEGER"]:
           xtype = "numberfield"
           dicExtJs['allowDecimals']    = False
           dicExtJs['allowNegative']    = False
           dicExtJs['decimalPrecision'] = 0
           
        elif fAttrs['type'] in ["FLOAT"]:
           xtype = "numberfield"
           dicExtJs['allowDecimals']    = True
           dicExtJs['allowNegative']    = True
           dicExtJs['decimalPrecision'] = 2
           
        elif fAttrs['type'] in ["DATETIME"]:
           xtype = "datefield"
           dicExtJs['format'] = fAttrs.get('format', "Y-m-d")
           
        elif fAttrs['type'] in ["DATE"]:
           xtype = "datefield"
           dicExtJs['format'] = fAttrs.get('format', "Y-m-d")
           
        elif fAttrs['type'] in ["TIME"]:
           xtype = "timefield"           
           # faltan atributos
           
        elif fAttrs['type'] in ["PASSWORD"]:
           xtype = 'textfield'
           dicExtJs['inputType'] = 'password'
           
        dicExtJs['xtype']   = xtype
        
        # tooltip del campo
        if fAttrs['description'] != "":
           dicExtJs['tooltip'] = {'tip': fAttrs['description'], 
                                  'width': 150}                
        
        # si permite valores nulos o blancos
        if (xtype in ['textfield', 'textarea', 'datefield', 'numberfield',
                      'comboboxex', 'comboboxlov', 'radiogroup',
                      'checkboxgroup']):
           dicExtJs['allowBlank'] = fAttrs['nullable']
           
        return dicExtJs

    ##################################################
    #### Funciones de clase para operaciones SQL  ####
    ##################################################    
    # crea lista de columnas para select
    def makeListColumns(cls, columns, callBy, strict=False):
        colObjs = []
        # columnas de datos
        if len(columns) > 0:
            if (cls.__ID__ not in columns) and (not strict):
                columns.append(cls.__ID__)
            # columnas existen? en tabla de clase
            colObjs = cls.validateOnlyMapperFieldsInClass(columns, callBy)            

        # todas las columnas
        if (len(colObjs) == 0):
            colObjs = [c for c in cls.__mapper__.columns]
        return colObjs

    # crea lista de filtros
    def makeListFilter(cls, filters, callBy):
        filtersList = []
        # columnas de datos
        for f in filters:
           field = cls.validateOnlyMapperFieldInClass(f['field'], callBy)
           if ac_SqlOperators.has_key(f['operator']):
              op = ac_SqlOperators[f['operator']]
              filtersList.append(op(field, f['value']))
           else:
              raise Exception, "makeListFilter, operador de filtro desconocido {%s}, en clase {%s}" % (f, cls)
               
        return filtersList

    # crea lista de columnas para ordenamiento
    def makeListSort(cls, sorts, callBy):
        sortList = []   
        # creacion de ordenamientos basados en columnas
        for s in sorts:
            if    type(s) == types.DictType:
                op = ac_sortSqlOperators.get(s["order"], None)                
                if not op == None:
                    col = cls.validateFieldInClass(s["field"], callBy)
                    sortList.append(op(col))            
                else:
                    raise Exception, "{%s}> error creando sort {%s}" % (callBy, str(s))
                
            elif  type(s) == types.StringType:
                col = cls.validateFieldInClass(s, callBy)
                sortList.append(asc(col))      
            else:
                pass    
        return sortList    
    
    # retorna lista de nombres de la tabla o dynamico
    def getFieldNames(cls, columns=[], callBy=""):
        colNames = []
        for c in columns:
            col = cls.getFieldName(c, callBy)
            colNames.append(col)
        return colNames    

    # crea un select basico, de todos los registros
    def allSelect(cls, columns=[], filters=[], sort=[], commands=[], 
                  execute=True, callBy="", preWhere=[], strict=False,
                  sessionDB=None):
        # filtros del select por valor de source de la clase
        filterSource = cls.makeSourceFilter()
        
        # lista de columnas para recuperar por el select
        colList      = cls.makeListColumns(columns, callBy, strict)
        
        # ordenamientos
        sortList     = cls.makeListSort(sort, callBy)

        session      = sessionDB if sessionDB != None else cls.getSession()
        
        ## crea select
        # ordenamientos del resultado   
        if len(sortList) > 0:           
           q = session.query(cls).order_by(sortList)            
        else:            
           q = session.query(cls)
                   
        # filtros de source
        if len(filterSource) > 0:
           q = q.filter(filterSource)

        # lista de filtros
        filters = cls.makeListFilter(filters, callBy)
        for f in filters:            
           q = q.filter(f)
        
        # filtros predefinidos, ya creados.   
        # filtros predefinidos, ya creados.   
        for f in preWhere:            
           q = q.filter(f)

        #print q
        if execute:
           return q.all()
        else:
           return q

    # crea un update basico
    def simpleUpdate(cls, object, fields, sessionDB=None):
        # para cada campo
        id  = getattr(object, object.__ID__)
        val = object.validateSetAttributes(fields)
        if not val:           
           val = object.validateSaveUpdate()        
           if not val:           
              # para cada indice de la tabla
              val = object.validateDuplicate(id)
              if not val:
                 try:
                    session = sessionDB if sessionDB != None else cls.getSession()
                    session.update(object)
                    session.flush()
                 except Exception, e:
                    return "En la tabla: {%s}, Error actualizando registro {%s} " % \
                           (cls.__dataName__, str(e))
              
        return val      

    # crea un delete basico    
    def simpleDelete(cls, object, sessionDB=None):
        try:
           session = sessionDB if sessionDB != None else cls.getSession()                       
           session.delete(object)
           session.flush()
        except Exception, e:
           return "En la tabla: {%s}, Error actualizando registro {%s} " % \
                  (cls.__dataName__, str(e))

    def count(cls, filters=[], preWhere=[], session=None):
        q      = cls.allSelect(filters=filters, preWhere=preWhere,\
                               execute=False, sessionDB=session)
        result = q.count()
        return result

    def getOneBy(cls, filters=[], sort=[], commands=[], execute=True,
                 callBy="", preWhere=[], strict=False, session=None):
        result = cls.allSelect(filters=filters, sort=sort, sessionDB=session)
        if len(result) > 0:
           return result[0]
        else:
           return None        

    def getOneByEqual(cls, values={}, sort=[], commands=[], execute=True,
                      callBy="", preWhere=[], strict=False, session=None):
        filters = []
        for k, v in values.items():           
           filters.append({'field':k, 'operator': '=', 'value':v})
        return cls.getOneBy(filters=filters, sort=sort, commands=commands,
                            execute=execute, callBy=callBy, preWhere=preWhere,
                            strict=strict, session=session)

    def getOneById(cls, value=0, sort=[], commands=[], execute=True,
                   callBy="", preWhere=[], strict=False, session=None):
        filters = [{'field':cls.__ID__, 'operator': '=', 'value':value}]
        return cls.getOneBy(filters=filters, sort=sort, commands=commands,
                            execute=execute, callBy=callBy, preWhere=preWhere,
                            strict=strict, session=session)
      
    def getByIdIn(cls, value=[], sort=[], commands=[], execute=True, callBy="",
                  preWhere=[], strict=False, session=session):
        filters=[{'field':cls.__ID__, 'operator': 'in_', 'value':value}]
        return cls.allSelect(filters=filters, sort=sort, commands=commands,
                             execute=execute, callBy=callBy, preWhere=preWhere,
                             strict=strict, sessionDB=session)  

    getSession                      = classmethod(getSession)
    getSourceName                   = classmethod(getSourceName)
    makeSourceFilter                = classmethod(makeSourceFilter)
    validateFieldsInClass           = classmethod(validateFieldsInClass)
    validateFieldInClass            = classmethod(validateFieldInClass)
    validateOnlyMapperFieldsInClass = classmethod(validateOnlyMapperFieldsInClass)
    validateOnlyMapperFieldInClass  = classmethod(validateOnlyMapperFieldInClass)
    getFieldData                    = classmethod(getFieldData)
    getFieldDataAttr                = classmethod(getFieldDataAttr)
    makeListColumns                 = classmethod(makeListColumns)
    makeListSort                    = classmethod(makeListSort)
    allSelect                       = classmethod(allSelect)
    simpleUpdate                    = classmethod(simpleUpdate)
    simpleDelete                    = classmethod(simpleDelete)
    count                           = classmethod(count)
    getFieldNames                   = classmethod(getFieldNames)
    getFieldName                    = classmethod(getFieldName)
    getFieldAllAttrs                = classmethod(getFieldAllAttrs)
    getDictNames                    = classmethod(getDictNames)
    getFieldExtjs                   = classmethod(getFieldExtjs)
    getOneBy                        = classmethod(getOneBy)
    getOneByEqual                   = classmethod(getOneByEqual)
    getOneById                      = classmethod(getOneById)
    getByIdIn                       = classmethod(getByIdIn)
    makeListFilter                  = classmethod(makeListFilter)
    isOneToOne                      = classmethod(isOneToOne)
    getOneToOneRelations            = classmethod(getOneToOneRelations)
       
setattr(ac_ExtDbBase, "__DBSession__", _DBSession)       

# diccionario de clases de datos
_dynamicDataDict = {}

# registra fuente en diccionario global
def dynamicData_LoadSource(name, source):
    _dynamicDataDict[name.upper()] = source
utils_Register_Function(dynamicData_LoadSource)

# valida fuente en diccionario global
def dynamicData_IsSource(source, strict=True, message= ""):
    dP = getDynamicSource(source)
    if dP != None:
        return dP["source"]
    
    if _dynamicDataDict.has_key(source.upper()):
        return _dynamicDataDict[source.upper()]
    
    if strict:
        raise Exception, "fuente no existe [%s] - [%s]" % (source, message)
utils_Register_Function(dynamicData_IsSource)

# perfiles dinamicos
_dynamicSourceDict = {} 

#carga perfile dinamicos
def registerDynamicSource(source, navigate, callsource):
	d = {}
	d["navigate"] = navigate
	d["source"]   = callsource
	_dynamicSourceDict[source] = d
utils_Register_Function(registerDynamicSource)

#carga perfile dinamicos
def getDynamicSource(source):
   if _dynamicSourceDict.has_key(source):
      return _dynamicSourceDict[source]
   else:
      return None
utils_Register_Function(getDynamicSource)

#### cache ############################
# retorna datos en formato diccionario con nombre de campo destino
# s_id con valor de registro
def toDictColumns(rows, columns, idkey = 'id'):
   dicData = []
   g = 0
   for r in rows:
      g = g + 1
      dictRow       = {} #diccionario por registro
      dictRow[idkey] = getattr(r, idkey)
      for col in columns:
         value = getattr(r, col)
         if type(value) == datetime.datetime:
            value = str(value)
         dictRow[col] = value
      dicData.append(dictRow)
   return dicData
utils_Register_Function(toDictColumns)

# tamaño lista de fuente de datos
def cacheGetSize(source, preWhere=[]):   
   dataClass = dataClass_getSource(source, "")
   count     = dataClass.count(preWhere=preWhere)
   return count
utils_Register_Function(cacheGetSize)

# regresa datos ordenados para widgets de tablas de navegacion y otros
def getProfileCacheSortData(sortSource, sortField, order,
                            firstRow, lastRow, columns, preWhere = []):
    dataClass     = dataClass_getSource(sortSource, whoami())
    sortAscending = True if str(order) == "ASC" else False
    sort          = {"field": sortField}
    sort["order"] = "asc" if sortAscending else "desc"
    columns       = [str(c) for c in columns]
    sortField     = str(sortField)
    preWhereC     = [f for f in preWhere]

    relation = dataClass.getOneToOneRelations(sortField)
    preWhere.extend(relation)
       
    if len(columns) > 0 and (sortField not in columns):
       columns.append(sortField)
       
    q        = dataClass.allSelect(columns, sort=[sort],
                                   preWhere=preWhere, execute=False)
    rows     = q[firstRow:lastRow]
    dicData  = toDictColumns(rows, columns, dataClass.__ID__)
    response = {'items'      : dicData,
                'version'    : 0,
                'total_count': cacheGetSize(sortSource, preWhereC)}    
    return response
utils_Register_Function(getProfileCacheSortData)

def jsonDic(arrayData):
   dicData = {'items'      : arrayData,
              'version'    : 0,
              'total_count': len(arrayData)}   
   return dicData
utils_Register_Function(jsonDic)

# convierte resultados de sql, a diccionario JSON
def sqlResultToDic(rows, fields, id):
   arrayData = toDictColumns(rows, fields, id)
   data      = jsonDic(arrayData)
   return data
utils_Register_Function(sqlResultToDic)

# convierte un solo registro sql , a diccionario
def toOneDictRow(row, fields):
   data = {}
   if row:
      for f in fields:
         value   = getattr(row, f)
         if type(value) == datetime.datetime:
            value = str(value)
         data[f] = value
   return data
utils_Register_Function(toOneDictRow)

def toOneListDictRow(row, fields):
   data = {}
   if row:
      for f in fields:
         value   = getattr(row, f)
         if type(value) == datetime.datetime:
            value = str(value)
         data[f] = value
   return [data]
utils_Register_Function(toOneListDictRow)

# extiende one to one proxy,
# para atributos que apuntan a una misma tabla
# dentro de una misma clase!!!!
def OneToOneMapperProxy(DATACLASS, relation, localField, REMOTEDATACLASS, remoteField):
  setattr(DATACLASS, 'p_' + localField, OneToOneProxy(relation, REMOTEDATACLASS, remoteField))
  proxyAttr = getattr(DATACLASS, 'p_' + localField)
  property  = OneToOneMapperProperty(proxyAttr, DATACLASS.__mapper__.get_property(relation))
  setattr(DATACLASS, localField, property)
  return DATACLASS
utils_Register_Function(OneToOneMapperProxy)
