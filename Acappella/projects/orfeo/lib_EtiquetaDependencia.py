#!/usr/bin/python
# -*- coding: iso-8859-15 -*-
from utils import utils, debug, sourceExtjs
from database import ac_dynamicData

#clase de datos para activemail
import dataClass_orfeo

## ventana forma para usuarios
@debug.runErrorHandler()
def EtiquetaDependenciaForm(args, kwargs):
   f = sourceForm(kwargs['_source'], kwargs['renderTo'], "", "", "")
   f['result']['autoWidth'] = False
   f['result']['width'] = 750
   f['result']['autoHeight'] = False
   f['result']['height'] = 300
   f['result']['autoScroll'] = True
   return f
_regRoute("EtiquetaDependenciaForm", EtiquetaDependenciaForm)

@debug.runErrorHandler()
def EtiquetaDependenciaReadRecord(args, kwargs):
   data = sourceReadRecord(args, kwargs)
   return data
_regRoute("EtiquetaDependenciaReadRecord", EtiquetaDependenciaReadRecord)

@debug.runErrorHandler()
def EtiquetaDependenciaUpdateRecord(args, kwargs):
   return sourceUpdateRecord(args, kwargs)
_regRoute("EtiquetaDependenciaUpdateRecord", EtiquetaDependenciaUpdateRecord)

@debug.runErrorHandler()
def EtiquetaDependenciaDeleteRecord(args, kwargs):
   return sourceDeleteRecord(args, kwargs)
_regRoute("EtiquetaDependenciaDeleteRecord", EtiquetaDependenciaDeleteRecord)

@debug.runErrorHandler()
def EtiquetaDependenciaInsertRecord(args, kwargs):
   return sourceInsertRecord(args, kwargs)
_regRoute("EtiquetaDependenciaInsertRecord", EtiquetaDependenciaInsertRecord)

def EtiquetaDependenciaSourceReport(args, kwargs):
   return SourceReport(args, kwargs)
_regRoute("EtiquetaDependenciaSourceReport", EtiquetaDependenciaSourceReport)

@debug.runErrorHandler()
def EtiquetaDependenciaGrid(args, kwargs):
   data = sourceGrid(kwargs['_source'], kwargs['renderTo'], kwargs['name'], "", "")
   data['result']['autoWidth'] = False
   data['result']['width'] = 1000
   return data
_regRoute("EtiquetaDependenciaGrid", EtiquetaDependenciaGrid)

@debug.runErrorHandler()
def EtiquetaDependenciaGridSortData(args, kwargs):
   data = gridSourceSortData(args, kwargs)
   return gridSourceSortData(args, kwargs)   
_regRoute("EtiquetaDependenciaGridSortData", EtiquetaDependenciaGridSortData)

@debug.runErrorHandler()
def EtiquetaDependenciaAll(args, kwargs):
   sort = {"field": "table_name", "order": "asc"}
   return allSourceSortData("EtiquetaDependencia", columns=["name", "id"], sort=[sort]) 
_regRoute("EtiquetaDependenciaAll", EtiquetaDependenciaAll)