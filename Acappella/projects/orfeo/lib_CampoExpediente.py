#!/usr/bin/python
# -*- coding: iso-8859-15 -*-
from utils import utils, debug, sourceExtjs
from database import ac_dynamicData

#clase de datos para activemail
import dataClass_orfeo

## ventana forma para usuarios
@debug.runErrorHandler()
def CampoExpedienteForm(args, kwargs):
   f = sourceForm(kwargs['_source'], kwargs['renderTo'], "", "", "")
   f['result']['autoWidth'] = False
   f['result']['width'] = 750
   f['result']['autoHeight'] = False
   f['result']['height'] = 400
   f['result']['autoScroll'] = True
   return f
_regRoute("CampoExpedienteForm", CampoExpedienteForm)

@debug.runErrorHandler()
def CampoExpedienteReadRecord(args, kwargs):
   data = sourceReadRecord(args, kwargs)
   return data
_regRoute("CampoExpedienteReadRecord", CampoExpedienteReadRecord)

@debug.runErrorHandler()
def CampoExpedienteUpdateRecord(args, kwargs):
   return sourceUpdateRecord(args, kwargs)
_regRoute("CampoExpedienteUpdateRecord", CampoExpedienteUpdateRecord)

@debug.runErrorHandler()
def CampoExpedienteDeleteRecord(args, kwargs):
   return sourceDeleteRecord(args, kwargs)
_regRoute("CampoExpedienteDeleteRecord", CampoExpedienteDeleteRecord)

@debug.runErrorHandler()
def CampoExpedienteInsertRecord(args, kwargs):
   return sourceInsertRecord(args, kwargs)
_regRoute("CampoExpedienteInsertRecord", CampoExpedienteInsertRecord)

def CampoExpedienteSourceReport(args, kwargs):
   return SourceReport(args, kwargs)
_regRoute("CampoExpedienteSourceReport", CampoExpedienteSourceReport)

@debug.runErrorHandler()
def CampoExpedienteGrid(args, kwargs):
   data = sourceGrid(kwargs['_source'], kwargs['renderTo'], kwargs['name'], "", "")
   data['result']['autoWidth'] = False
   data['result']['width'] = 1000
   return data
_regRoute("CampoExpedienteGrid", CampoExpedienteGrid)

@debug.runErrorHandler()
def CampoExpedienteGridSortData(args, kwargs):
   data = gridSourceSortData(args, kwargs)
   return gridSourceSortData(args, kwargs)   
_regRoute("CampoExpedienteGridSortData", CampoExpedienteGridSortData)

@debug.runErrorHandler()
def CampoExpedienteAll(args, kwargs):
   sort = {"field": "table_name", "order": "asc"}
   return allSourceSortData("CampoExpediente", columns=["name", "id"], sort=[sort]) 
_regRoute("CampoExpedienteAll", CampoExpedienteAll)