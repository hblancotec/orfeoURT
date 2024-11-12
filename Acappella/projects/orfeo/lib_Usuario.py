#!/usr/bin/python
# -*- coding: iso-8859-15 -*-
from utils import utils, debug, sourceExtjs
from database import ac_dynamicData

#clase de datos para activemail
import dataClass_orfeo

## ventana forma para usuarios
@debug.runErrorHandler()
def UsuarioForm(args, kwargs):
   f = sourceForm(kwargs['_source'], kwargs['renderTo'], "", "", "")
   f['result']['autoWidth'] = False
   f['result']['width'] = 750
   f['result']['autoHeight'] = False
   f['result']['height'] = 500
   f['result']['autoScroll'] = True
   text = """
      ac_reset_combo(pqr_Usuario, 'dependenciaUsuario', {depedencia: pqr_Dependencia.getValue()});      
   """ 
   return f
_regRoute("UsuarioForm", UsuarioForm)

@debug.runErrorHandler()
def UsuarioReadRecord(args, kwargs):
   data = sourceReadRecord(args, kwargs)
   return data
_regRoute("UsuarioReadRecord", UsuarioReadRecord)

@debug.runErrorHandler()
def UsuarioUpdateRecord(args, kwargs):
   return sourceUpdateRecord(args, kwargs)
_regRoute("UsuarioUpdateRecord", UsuarioUpdateRecord)

@debug.runErrorHandler()
def UsuarioDeleteRecord(args, kwargs):
   return sourceDeleteRecord(args, kwargs)
_regRoute("UsuarioDeleteRecord", UsuarioDeleteRecord)

@debug.runErrorHandler()
def UsuarioInsertRecord(args, kwargs):
   return sourceInsertRecord(args, kwargs)
_regRoute("UsuarioInsertRecord", UsuarioInsertRecord)

def UsuarioSourceReport(args, kwargs):
   return SourceReport(args, kwargs)
_regRoute("UsuarioSourceReport", UsuarioSourceReport)

@debug.runErrorHandler()
def UsuarioGrid(args, kwargs):
   data = sourceGrid(kwargs['_source'], kwargs['renderTo'], kwargs['name'], "", "")
   data['result']['autoWidth'] = False
   data['result']['width'] = 1000
   return data
_regRoute("UsuarioGrid", UsuarioGrid)

@debug.runErrorHandler()
def UsuarioGridSortData(args, kwargs):
   data = gridSourceSortData(args, kwargs)
   return gridSourceSortData(args, kwargs)   
_regRoute("UsuarioGridSortData", UsuarioGridSortData)

@debug.runErrorHandler()
def UsuarioAll(args, kwargs):
   sort = {"field": "USUA_NOMB", "order": "asc"}
   return allSourceSortData("Usuario", columns=["USUA_NOMB", "id"], sort=[sort]) 
_regRoute("UsuarioAll", UsuarioAll)