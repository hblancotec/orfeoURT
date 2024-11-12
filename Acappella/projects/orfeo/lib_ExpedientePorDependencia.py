#!/usr/bin/python
# -*- coding: iso-8859-15 -*-
from utils import utils, debug, sourceExtjs
from database import ac_dynamicData

#clase de datos para activemail
import dataClass_orfeo

## ventana forma para usuarios
@debug.runErrorHandler()
def ExpedientePorDependenciaForm(args, kwargs):
   f = sourceForm(kwargs['_source'], kwargs['renderTo'], "", "", "")
   return f
_regRoute("ExpedientePorDependenciaForm", ExpedientePorDependenciaForm)

@debug.runErrorHandler()
def ExpedientePorDependenciaReadRecord(args, kwargs):
   data = sourceReadRecord(args, kwargs)
   return data
_regRoute("ExpedientePorDependenciaReadRecord", ExpedientePorDependenciaReadRecord)

@debug.runErrorHandler()
def ExpedientePorDependenciaUpdateRecord(args, kwargs):
   return sourceUpdateRecord(args, kwargs)
_regRoute("ExpedientePorDependenciaUpdateRecord", ExpedientePorDependenciaUpdateRecord)

@debug.runErrorHandler()
def ExpedientePorDependenciaDeleteRecord(args, kwargs):
   return sourceDeleteRecord(args, kwargs)
_regRoute("ExpedientePorDependenciaDeleteRecord", ExpedientePorDependenciaDeleteRecord)

@debug.runErrorHandler()
def ExpedientePorDependenciaInsertRecord(args, kwargs):
   return sourceInsertRecord(args, kwargs)
_regRoute("ExpedientePorDependenciaInsertRecord", ExpedientePorDependenciaInsertRecord)

def ExpedientePorDependenciaSourceReport(args, kwargs):
   return SourceReport(args, kwargs)
_regRoute("ExpedientePorDependenciaSourceReport", ExpedientePorDependenciaSourceReport)

@debug.runErrorHandler()
def ExpedientePorDependenciaGrid(args, kwargs):
   data = sourceGrid(kwargs['_source'], kwargs['renderTo'], kwargs['name'], "", "")
   data['result']['autoWidth'] = False
   data['result']['width'] = 1000
   return data
_regRoute("ExpedientePorDependenciaGrid", ExpedientePorDependenciaGrid)

@debug.runErrorHandler()
def ExpedientePorDependenciaGridSortData(args, kwargs):
   return gridSourceSortData(args, kwargs)   
_regRoute("ExpedientePorDependenciaGridSortData", ExpedientePorDependenciaGridSortData)

@debug.runErrorHandler()
def ExpedientePorDependenciaAll(args, kwargs):
   sort = {"field": "table_name", "order": "asc"}
   return allSourceSortData("ExpedientePorDependencia", columns=["name", "id"], sort=[sort]) 
_regRoute("ExpedientePorDependenciaAll", ExpedientePorDependenciaAll)


@debug.runErrorHandler()
def ExpedientePorDependenciaBuscaGrid(args, kwargs):
   print "ExpedientePorDependenciaBuscaGrid"
   nicePrint(kwargs)
   gwindow = sourceGrid(kwargs['_source'], kwargs['renderTo'], kwargs['name'], "", "")
   gwindow['result']['title'] = 'Asignar Expediente'
   grid    = gwindow['result']['items'][0]
   del grid['tbar'][0]
   del grid['tbar'][0]
   
   anular = """
      var sm = anulaExpedientePorDependenciaGridWindow_grid.getSelectionModel();

      var ok = returnOk(function(response) {                  
                 ac_reset_grid(anulaExpedientePorDependenciaGridWindow_grid);
               });    
      
      var fAnular = function(btn) {
         if (btn == 'yes') {
            var records = sm.getSelections();
            var anular  = [];
            for (var i = 0; i < records.length; i++){
              anular[anular.length] = records[i].id;         
            }         
            var params = {'anular': anular};
            ac_request('anularExpedientePorDependencias', params, ok);
         }                                      
      };
         
      if (sm.getCount() > 0) {
         Ext.Msg.confirm('Anular ExpedientePorDependencias', 'Desea anular documentos?', fAnular);         
      }                  
   """
   archiveButton = {'text'   : 'Anular ExpedientePorDependencias',
                    'iconCls': 'icon_arhive',
                    'handler': anular,
                    'id'     : 'anularExpedientePorDependencia'}
   grid['tbar'].append(archiveButton)
   grid['listeners'] = {}
   return gwindow
_regRoute("ExpedientePorDependenciaBuscaGrid", ExpedientePorDependenciaBuscaGrid)

@debug.runErrorHandler()
def anularExpedientePorDependencias(args, kwargs):
   listIds = kwargs['anular']
   return anularRadicado(ExpedientePorDependencia, listIds)    
_regRoute("anularExpedientePorDependencias", anularExpedientePorDependencias)
