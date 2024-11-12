#!/usr/bin/python
# -*- coding: iso-8859-15 -*-
from utils import utils, debug, sourceExtjs
from database import ac_dynamicData

#clase de datos para activemail
import dataClass_orfeo

## ventana forma para usuarios
@debug.runErrorHandler()
def DependenciaForm(args, kwargs):
   f = sourceForm(kwargs['_source'], kwargs['renderTo'], "", "", "")
   f['result']['autoWidth'] = False
   f['result']['width'] = 750
   f['result']['autoHeight'] = False
   f['result']['height'] = 500
   f['result']['autoScroll'] = True
   return f
_regRoute("DependenciaForm", DependenciaForm)

@debug.runErrorHandler()
def DependenciaReadRecord(args, kwargs):
   data = sourceReadRecord(args, kwargs)
   return data
_regRoute("DependenciaReadRecord", DependenciaReadRecord)

@debug.runErrorHandler()
def DependenciaUpdateRecord(args, kwargs):
   return sourceUpdateRecord(args, kwargs)
_regRoute("DependenciaUpdateRecord", DependenciaUpdateRecord)

@debug.runErrorHandler()
def DependenciaDeleteRecord(args, kwargs):
   return sourceDeleteRecord(args, kwargs)
_regRoute("DependenciaDeleteRecord", DependenciaDeleteRecord)

@debug.runErrorHandler()
def DependenciaInsertRecord(args, kwargs):
   return sourceInsertRecord(args, kwargs)
_regRoute("DependenciaInsertRecord", DependenciaInsertRecord)

def DependenciaSourceReport(args, kwargs):
   return SourceReport(args, kwargs)
_regRoute("DependenciaSourceReport", DependenciaSourceReport)

@debug.runErrorHandler()
def DependenciaGrid(args, kwargs):
   data = sourceGrid(kwargs['_source'], kwargs['renderTo'], kwargs['name'], "", "")
   data['result']['autoWidth'] = False
   data['result']['width'] = 1000
   return data
_regRoute("DependenciaGrid", DependenciaGrid)

@debug.runErrorHandler()
def DependenciaGridSortData(args, kwargs):
   data = gridSourceSortData(args, kwargs)
   return gridSourceSortData(args, kwargs)   
_regRoute("DependenciaGridSortData", DependenciaGridSortData)

@debug.runErrorHandler()
def DependenciaAll(args, kwargs):
   sort = {"field": "DEPE_NOMB", "order": "asc"}
   return allSourceSortData("Dependencia", columns=["DEPE_NOMB", "DEPE_CODI"], sort=[sort]) 
_regRoute("DependenciaAll", DependenciaAll)

@debug.runErrorHandler()
def dependenciaUsuario(args, kwargs):
   #print "dependenciaUsuario" ,args, kwargs
   session   = dySession()
   rows      = session.query(USUARIO).filter(USUARIO.DEPE_CODI == int(kwargs["depedencia"]))\
                                   .order_by(USUARIO.USUA_NOMB).all()
   
   dicData   = toDictColumns(rows, ["USUA_NOMB", "id"], USUARIO.__ID__)
   response  = {'items'      : dicData,
                'version'    : 0,
                'total_count': len(dicData)}   
   return dicResultTrue(response) 
_regRoute("dependenciaUsuario", dependenciaUsuario)