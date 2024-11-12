#!/usr/bin/python
# -*- coding: iso-8859-15 -*-
from utils import utils, debug, sourceExtjs
from database import ac_dynamicData

#clase de datos para activemail
import dataClass_orfeo

## ventana forma para usuarios
@debug.runErrorHandler()
def nombreTablaForm(args, kwargs):
   f = sourceForm(kwargs['_source'], kwargs['renderTo'], "", "", "")
   return f
_regRoute("nombreTablaForm", nombreTablaForm)

@debug.runErrorHandler()
def nombreTablaReadRecord(args, kwargs):
   return sourceReadRecord(args, kwargs)
_regRoute("nombreTablaReadRecord", nombreTablaReadRecord)

@debug.runErrorHandler()
def nombreTablaUpdateRecord(args, kwargs):   
   return sourceUpdateRecord(args, kwargs)
_regRoute("nombreTablaUpdateRecord", nombreTablaUpdateRecord)

@debug.runErrorHandler()
def nombreTablaDeleteRecord(args, kwargs):
   return sourceDeleteRecord(args, kwargs)
_regRoute("nombreTablaDeleteRecord", nombreTablaDeleteRecord)

@debug.runErrorHandler()
def nombreTablaInsertRecord(args, kwargs):
   return sourceInsertRecord(args, kwargs)
_regRoute("nombreTablaInsertRecord", nombreTablaInsertRecord)

def nombreTablaSourceReport(args, kwargs):
   return SourceReport(args, kwargs)
_regRoute("nombreTablaSourceReport", nombreTablaSourceReport)

@debug.runErrorHandler()
def nombreTablaGrid(args, kwargs):   
   data = sourceGrid(kwargs['_source'], kwargs['renderTo'], kwargs['name'], "", "")
   data['result']['autoWidth'] = False
   data['result']['width'] = 1000
   return data
_regRoute("nombreTablaGrid", nombreTablaGrid)

@debug.runErrorHandler()
def nombreTablaGridSortData(args, kwargs):
   return gridSourceSortData(args, kwargs)   
_regRoute("nombreTablaGridSortData", nombreTablaGridSortData)

@debug.runErrorHandler()
def nombreTablaAll(args, kwargs):
   sort = {"field": "table_name", "order": "asc"}
   return allSourceSortData("nombreTabla", columns=["name", "id"], sort=[sort]) 
_regRoute("nombreTablaAll", nombreTablaAll)