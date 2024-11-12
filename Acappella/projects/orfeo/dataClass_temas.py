#!/usr/bin/python
# -*- coding: iso-8859-15 -*-

import sys, os, types, copy
from decimal import *
from time import localtime, time, strftime, ctime

from database.ac_dynamicData   import *
from utils.utils               import *

from  datetime import datetime

# extension para formateo de datos
class FormatDate(MapperExtension):
    def __init__(self, field):
        self.field = field

    def changeValue(self, instance):
        value   = getattr(instance, self.field)
        if value != None:
           newdate = datetime.strptime(value, '%Y-%m-%d %H:%M:%S')
           value   = newdate.strftime("%d/%m/%Y %H:%M:%S")        
           value   = newdate.strftime("%d/%m/%Y %H:%M:%S")
           setattr(instance, self.field, value)
        return value

    def before_update(self, mapper, connection, instance):
         self.changeValue(instance)         

    def before_insert(self, mapper, connection, instance):
         self.changeValue(instance)

    def append_result(self, mapper, selectcontext, row, instance, result, **flags):
        value = getattr(instance, self.field)
        if value != None:           
           value = value.strftime("%Y-%m-%d %H:%M:%S")
           setattr(instance, self.field, value)        
        return EXT_CONTINUE
ac_register_object("FormatDate", FormatDate)    

# extension para formateo de datos
class FormatDecimal(MapperExtension):
    def __init__(self, field):
        self.field = field

    def changeValue(self, instance):
        value   = getattr(instance, self.field)
        if value != None:           
           value   = Decimal(value)
           setattr(instance, self.field, value)
        return value

    def before_update(self, mapper, connection, instance):
         self.changeValue(instance)         

    def before_insert(self, mapper, connection, instance):
         self.changeValue(instance)

    def append_result(self, mapper, selectcontext, row, instance, result, **flags):
        value = getattr(instance, self.field)
        if value != None:           
           value = int(value)
           setattr(instance, self.field, value)        
        return EXT_CONTINUE
ac_register_object("FormatDecimal", FormatDecimal)        

#/* Nombre de Tablas */
class TemaTabla(ac_DBBase, ac_ExtDbBase):
    __ID__         = 'id'
    __tablename__  = 'SGD_TEM_NOMBRES'
    __dataName__   = 'Nombre de Tablas'
    __dataType__   = 'APLICACION'
    __columnsAttrs__ = {'SGD_TEMA_NOMBRE': {'name'    : 'Nombre',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Nombre del Tema',
                                      'description'   : 'Nombre del Tema',
                                      'sort'          : 'ASC',                                        
                                      'sortDefault'   : True}
                       }
    
    __relations__  = {}
    __filters__    = {}
    __validators__ = {}
    __dataCache__  =  ['SGD_TEMA_NOMBRE']
    __visualorder__ = ['SGD_TEMA_NOMBRE']

    id              = Column('id', Integer, Sequence('Nombre_Tabla_seq', optional=True),
                             primary_key=True, nullable=False)
    SGD_TEMA_NOMBRE = Column('SGD_TEMA_NOMBRE', Unicode(120), nullable=False, default='')        
Index('TemaTablaUnique', TemaTabla.__table__.c.SGD_TEMA_NOMBRE, unique=True)        
dataClass_register("TemaTabla", TemaTabla)