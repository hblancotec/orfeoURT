#!/usr/bin/python
# -*- coding: iso-8859-15 -*-
from utils import utils, debug, sourceExtjs
from database import ac_dynamicData

#clase de datos para activemail
import dataClass_orfeo

## ventana forma para TiposDocumentaless
@debug.runErrorHandler()
def TiposDocumentalesForm(args, kwargs):
   f = sourceForm(kwargs['_source'], kwargs['renderTo'], "", "", "")
   f['result']['autoWidth'] = False
   f['result']['width'] = 750
   f['result']['autoHeight'] = False
   f['result']['height'] = 500
   f['result']['autoScroll'] = True
   return f
_regRoute("TiposDocumentalesForm", TiposDocumentalesForm)

@debug.runErrorHandler()
def TiposDocumentalesReadRecord(args, kwargs):
   data = sourceReadRecord(args, kwargs)
   return data
_regRoute("TiposDocumentalesReadRecord", TiposDocumentalesReadRecord)

@debug.runErrorHandler()
def TiposDocumentalesUpdateRecord(args, kwargs):
   return sourceUpdateRecord(args, kwargs)
_regRoute("TiposDocumentalesUpdateRecord", TiposDocumentalesUpdateRecord)

@debug.runErrorHandler()
def TiposDocumentalesDeleteRecord(args, kwargs):
   return sourceDeleteRecord(args, kwargs)
_regRoute("TiposDocumentalesDeleteRecord", TiposDocumentalesDeleteRecord)

@debug.runErrorHandler()
def TiposDocumentalesInsertRecord(args, kwargs):
   return sourceInsertRecord(args, kwargs)
_regRoute("TiposDocumentalesInsertRecord", TiposDocumentalesInsertRecord)

def TiposDocumentalesSourceReport(args, kwargs):
   return SourceReport(args, kwargs)
_regRoute("TiposDocumentalesSourceReport", TiposDocumentalesSourceReport)

@debug.runErrorHandler()
def TiposDocumentalesGrid(args, kwargs):
   data = sourceGrid(kwargs['_source'], kwargs['renderTo'], kwargs['name'], "", "")
   data['result']['autoWidth'] = False
   data['result']['width'] = 1000
   return data
_regRoute("TiposDocumentalesGrid", TiposDocumentalesGrid)

@debug.runErrorHandler()
def TiposDocumentalesGridSortData(args, kwargs):
   data = gridSourceSortData(args, kwargs)
   return gridSourceSortData(args, kwargs)   
_regRoute("TiposDocumentalesGridSortData", TiposDocumentalesGridSortData)

@debug.runErrorHandler()
def TiposDocumentalesAll(args, kwargs):
   sort = {"field": "table_name", "order": "asc"}
   return allSourceSortData("TiposDocumentales", columns=["name", "id"], sort=[sort]) 
_regRoute("TiposDocumentalesAll", TiposDocumentalesAll)