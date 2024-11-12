#!/usr/bin/python
# -*- coding: iso-8859-15 -*-
from utils import utils, debug, sourceExtjs
from database import ac_dynamicData

#clase de datos para activemail
import dataClass_orfeo

## ventana forma para usuarios
@debug.runErrorHandler()
def RadicadoPorExpedienteForm(args, kwargs):
   f = sourceForm(kwargs['_source'], kwargs['renderTo'], "", "", "")
   f['result']['autoWidth'] = False
   f['result']['width'] = 750
   f['result']['autoHeight'] = False
   f['result']['height'] = 500
   f['result']['autoScroll'] = True
   return f
_regRoute("RadicadoPorExpedienteForm", RadicadoPorExpedienteForm)

@debug.runErrorHandler()
def RadicadoPorExpedienteReadRecord(args, kwargs):
   data = sourceReadRecord(args, kwargs)
   return data
_regRoute("RadicadoPorExpedienteReadRecord", RadicadoPorExpedienteReadRecord)

@debug.runErrorHandler()
def RadicadoPorExpedienteUpdateRecord(args, kwargs):
   return sourceUpdateRecord(args, kwargs)
_regRoute("RadicadoPorExpedienteUpdateRecord", RadicadoPorExpedienteUpdateRecord)

@debug.runErrorHandler()
def RadicadoPorExpedienteDeleteRecord(args, kwargs):
   return sourceDeleteRecord(args, kwargs)
_regRoute("RadicadoPorExpedienteDeleteRecord", RadicadoPorExpedienteDeleteRecord)

@debug.runErrorHandler()
def RadicadoPorExpedienteInsertRecord(args, kwargs):
   return sourceInsertRecord(args, kwargs)
_regRoute("RadicadoPorExpedienteInsertRecord", RadicadoPorExpedienteInsertRecord)

def RadicadoPorExpedienteSourceReport(args, kwargs):
   return SourceReport(args, kwargs)
_regRoute("RadicadoPorExpedienteSourceReport", RadicadoPorExpedienteSourceReport)

@debug.runErrorHandler()
def RadicadoPorExpedienteGrid(args, kwargs):
   data = sourceGrid(kwargs['_source'], kwargs['renderTo'], kwargs['name'], "", "")
   data['result']['autoWidth'] = False
   data['result']['width'] = 1000
   return data
_regRoute("RadicadoPorExpedienteGrid", RadicadoPorExpedienteGrid)

@debug.runErrorHandler()
def RadicadoPorExpedienteGridSortData(args, kwargs):
   data = gridSourceSortData(args, kwargs)
   return gridSourceSortData(args, kwargs)   
_regRoute("RadicadoPorExpedienteGridSortData", RadicadoPorExpedienteGridSortData)

@debug.runErrorHandler()
def RadicadoPorExpedienteAll(args, kwargs):
   sort = {"field": "table_name", "order": "asc"}
   return allSourceSortData("RadicadoPorExpediente", columns=["name", "id"], sort=[sort]) 
_regRoute("RadicadoPorExpedienteAll", RadicadoPorExpedienteAll)