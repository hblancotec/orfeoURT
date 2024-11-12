#!/usr/bin/python
# -*- coding: iso-8859-15 -*-
from utils import utils, debug, sourceExtjs
from database import ac_dynamicData

#clase de datos para activemail
import dataClass_orfeo

## ventana forma para TiposPqrs
@debug.runErrorHandler()
def TiposPqrForm(args, kwargs):
   f = sourceForm(kwargs['_source'], kwargs['renderTo'], "", "", "")
   f['result']['autoWidth'] = False
   f['result']['width'] = 750
   f['result']['autoHeight'] = False
   f['result']['height'] = 500
   f['result']['autoScroll'] = True
   return f
_regRoute("TiposPqrForm", TiposPqrForm)

@debug.runErrorHandler()
def TiposPqrReadRecord(args, kwargs):
   data = sourceReadRecord(args, kwargs)
   return data
_regRoute("TiposPqrReadRecord", TiposPqrReadRecord)

@debug.runErrorHandler()
def TiposPqrUpdateRecord(args, kwargs):
   return sourceUpdateRecord(args, kwargs)
_regRoute("TiposPqrUpdateRecord", TiposPqrUpdateRecord)

@debug.runErrorHandler()
def TiposPqrDeleteRecord(args, kwargs):
   return sourceDeleteRecord(args, kwargs)
_regRoute("TiposPqrDeleteRecord", TiposPqrDeleteRecord)

@debug.runErrorHandler()
def TiposPqrInsertRecord(args, kwargs):
   return sourceInsertRecord(args, kwargs)
_regRoute("TiposPqrInsertRecord", TiposPqrInsertRecord)

def TiposPqrSourceReport(args, kwargs):
   return SourceReport(args, kwargs)
_regRoute("TiposPqrSourceReport", TiposPqrSourceReport)

@debug.runErrorHandler()
def TiposPqrGrid(args, kwargs):
   data = sourceGrid(kwargs['_source'], kwargs['renderTo'], kwargs['name'], "", "")
   data['result']['autoWidth'] = False
   data['result']['width'] = 1000
   return data
_regRoute("TiposPqrGrid", TiposPqrGrid)

@debug.runErrorHandler()
def TiposPqrGridSortData(args, kwargs):
   data = gridSourceSortData(args, kwargs)
   return gridSourceSortData(args, kwargs)   
_regRoute("TiposPqrGridSortData", TiposPqrGridSortData)

@debug.runErrorHandler()
def TiposPqrAll(args, kwargs):
   sort = {"field": "table_name", "order": "asc"}
   return allSourceSortData("TiposPqr", columns=["name", "id"], sort=[sort]) 
_regRoute("TiposPqrAll", TiposPqrAll)

# funciones de javascript cuando cambia pais
def dependencia_select():
   text = """
      ac_reset_combo(pqr_Usuario, 'dependenciaUsuario', {depedencia: pqr_Dependencia.getValue()});      
   """ 
   return text   
utils_Register_Function(dependencia_select)  