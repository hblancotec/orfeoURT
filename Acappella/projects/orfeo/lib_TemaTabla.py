#!/usr/bin/python
# -*- coding: iso-8859-15 -*-
from utils import utils, debug, sourceExtjs
from database import ac_dynamicData

#clase de datos para activemail
import dataClass_orfeo

## ventana forma para TemaTablas
@debug.runErrorHandler()
def TemaTablaForm(args, kwargs):
   f = sourceForm(kwargs['_source'], kwargs['renderTo'], "", "", "")
   f['result']['autoWidth'] = False
   f['result']['width'] = 750
   f['result']['autoHeight'] = False
   f['result']['height'] = 500
   f['result']['autoScroll'] = True
   return f
_regRoute("TemaTablaForm", TemaTablaForm)

@debug.runErrorHandler()
def TemaTablaReadRecord(args, kwargs):
   data = sourceReadRecord(args, kwargs)
   return data
_regRoute("TemaTablaReadRecord", TemaTablaReadRecord)

@debug.runErrorHandler()
def TemaTablaUpdateRecord(args, kwargs):
   return sourceUpdateRecord(args, kwargs)
_regRoute("TemaTablaUpdateRecord", TemaTablaUpdateRecord)

@debug.runErrorHandler()
def TemaTablaDeleteRecord(args, kwargs):
   return sourceDeleteRecord(args, kwargs)
_regRoute("TemaTablaDeleteRecord", TemaTablaDeleteRecord)

@debug.runErrorHandler()
def TemaTablaInsertRecord(args, kwargs):
   return sourceInsertRecord(args, kwargs)
_regRoute("TemaTablaInsertRecord", TemaTablaInsertRecord)

def TemaTablaSourceReport(args, kwargs):
   return SourceReport(args, kwargs)
_regRoute("TemaTablaSourceReport", TemaTablaSourceReport)

@debug.runErrorHandler()
def TemaTablaGrid(args, kwargs):
   data = sourceGrid(kwargs['_source'], kwargs['renderTo'], kwargs['name'], "", "")
   data['result']['autoWidth'] = False
   data['result']['width'] = 1000
   return data
_regRoute("TemaTablaGrid", TemaTablaGrid)

@debug.runErrorHandler()
def TemaTablaGridSortData(args, kwargs):
   data = gridSourceSortData(args, kwargs)
   return gridSourceSortData(args, kwargs)   
_regRoute("TemaTablaGridSortData", TemaTablaGridSortData)

@debug.runErrorHandler()
def TemaTablaAll(args, kwargs):
   sort = {"field": "table_name", "order": "asc"}
   return allSourceSortData("TemaTabla", columns=["name", "id"], sort=[sort]) 
_regRoute("TemaTablaAll", TemaTablaAll)