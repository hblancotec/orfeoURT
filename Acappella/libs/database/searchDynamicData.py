#!/usr/bin/python
# -*- coding: iso-8859-15 -*-

from __future__ import with_statement
import sys, os, types, datetime, uuid

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

ac_dicSearchs = objectDic({})
utils_Register_Object('ac_dicSearchs', ac_dicSearchs)

def setQueryUuid(q):
   uuid = getUuid()
   ac_dicSearchs[uuid] = q
   return uuid
utils_Register_Function(setQueryUuid)

def getQueryUuid(uuid):
   return ac_dicSearchs[uuid]
utils_Register_Function(getQueryUuid)

# limpia el diccionario de claves sin valores
def clearDict(fields, exceptList=[]):
   return dict([(k, v) for k,v in fields.items() \
                if k not in exceptList and \
                   unicode(v) != u"" and v != None])
utils_Register_Function(clearDict)
                
# carga filtros like para una entidad
def dataClassLikeFilter(DATACLASS, fields={}, exceptList=[], prefix=''):
   filters = []
   for k,v in fields.items():
      if (k not in exceptList) and (unicode(v) != u"") and (v != None):
         if prefix != '' and k.find(prefix) == 0: 
            k = k[len(prefix):]
         filters.append(getattr(DATACLASS, k).like(u'%'+unicode(v)+'%')) 
   return filters
utils_Register_Function(dataClassLikeFilter)

# carga filtros equal para una entidad
def dataClassEqualFilter(DATACLASS, fields={}, exceptList=[], prefix=''):
   filters = []
   for k,v in fields.items():
      if (k not in exceptList) and (unicode(v) != u"") and (v != None):
         if prefix != '' and k.find(prefix) == 0: 
            k = k[len(prefix):] 
         filters.append(getattr(DATACLASS, k) == u""+unicode(v)) 
   return filters
utils_Register_Function(dataClassEqualFilter)

# carga filtros para rango de valores, uno a uno
def dataClassRangeFilter(DATACLASS, fields={}, lowKeyData='', upperKeyData='',
                         lowKey='', upperKey='', prefix=''):
   filters = []
   if lowKey   == "": lowKey   = lowKeyData
   if upperKey == "": upperKey = upperKeyData
      
   return and_(getattr(DATACLASS, lowKeyData)   >= fields[lowKey],
               getattr(DATACLASS, upperKeyData) <= fields[upperKey])
utils_Register_Function(dataClassRangeFilter)

# crea filtro con querys
def dataClassQueryFilters(DATACLASS, filters, session):
   q = session.query(DATACLASS)
   for f in filters:            
      q = q.filter(f)    
   return q
utils_Register_Function(dataClassQueryFilters)

def sourceBusquedaData(DATACLASS, uuid, columns, sortDir,
                       sortField, start, limit, count, prefix=''):
   session = dySession()
   
   q = getQueryUuid(uuid)
    
   orderField    = getattr(DATACLASS, sortField)
   if str(sortDir) == "ASC":
      orderField = orderField.asc()
   else:
      orderField = orderField.desc()
   q             = q.order_by(orderField)
    
   rows          = q[int(start):int(start)+int(limit)]
   
   dicData       = toDictColumns(rows, columns, DATACLASS.__ID__)
   response      = {'items'      : dicData,
                    'version'    : 0,
                    'total_count': count}
   
   return dicResultTrue(response)
utils_Register_Function(sourceBusquedaData)

# crea query basico de busqueda por clase
def makeSourceSearchQuery(source, fields, columns, sortField=None, sortDir='ASC',
                          sessionDB=None):
   q = None
   DATACLASS  = dataClass_getSource(source, whoami())   
   if DATACLASS:
      if sessionDB:
         session = sessionDB
      else:
         session = dySession()
      # filtros
      filters = []
      for k,v in fields.items():
         if (v != None):
            filters.append(getattr(DATACLASS, k) == v)

      q = session.query(DATACLASS)
      for f in filters:            
         q = q.filter(f)                                

      # ordenamiento
      if sortField:
         orderField = getattr(DATACLASS, sortField)    
         if str(sortDir) == "ASC":
            orderField = orderField.asc()
         else:
            orderField = orderField.desc()
         q = q.order_by(orderField)
   return q     

# busca un registro basado en una clase y filtros
# si pide atributo de relacion, especifica si
# el atributo es escalar o lista
def sourceSearchOne(source, fields, columns, sortField=None,
                    sortDir='ASC', attrScalar=False, attr='', id='id',
                    sessionDB=None):
   data = jsonDic([])
   rows = None
   q = makeSourceSearchQuery(source, fields, columns, sortField=None, sortDir='ASC',
                             sessionDB=sessionDB)
   if q:
      row = q.one()
      # atributo del registro
      attrd  = getattr(row, attr, None)
      # si no pide atributo True, si pide devuelve atributo
      attrOk = True if attr == '' else attrd
      if (row) and (attrOk):
        if attrd:
           # datos del atributo de relacion
           if attrScalar:
              data = toOneDictRow(attrd, columns)
           else:
              data = sqlResultToDic(attrd, columns, id)
        else:
           data = toOneDictRow(row, columns) 
   return rows, data
utils_Register_Function(sourceSearchOne)

# busca registros basado en una clase y filtros
# si pide atributo de relacion, especifica si
# el atributo es escalar o lista
def sourceSearchAll(source, fields, columns, sortField=None,
                     sortDir='ASC', attrScalar=False, attr='', id='id',
                    sessionDB=None):
   data = jsonDic([])
   rows = None
   q = makeSourceSearchQuery(source, fields, columns, sortField=None, sortDir='ASC',
                             sessionDB=sessionDB)
   if q:
      rows = q.all()
      # atributo del registro
      attrd = True
      if (len(rows) > 0) and (attr != ''):         
         attrd  = getattr(rows[0], attr, None)
      # si no pide atributo True, si pide devuelve atributo
      attrOk = True if attr == '' else attrd
      if (rows) and (attrOk):
        if attrd not in (True, None):
           # datos del atributo de relacion
           if attrScalar:
              data = toOneDictRow(attrd, columns)
           else:
              data = sqlResultToDic(attrd, columns, id)
        else:
           data = sqlResultToDic(rows, columns, id)               
   return rows, data
utils_Register_Function(sourceSearchAll)