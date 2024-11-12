#!/usr/bin/python
# -*- coding: iso-8859-15 -*-
from __future__ import with_statement
import threading, uuid, random, datetime, time, os
import types

from cherrypy.lib.static import serve_file, staticfile, serve_download
import cherrypy

from utils import utils, debug, extjs
from sqlalchemy import *
import simplejson

#clase de datos para activemail
import dataClass_orfeo
import dataClass_temas

# clases para relaciones
import dataClass_relations

# manejo de logica
import lib_NombreTabla
import lib_ExpedientePorDependencia
import lib_RadicadoPorExpediente
import lib_CampoExpediente
import lib_EtiquetaDependencia
import lib_Dependencia
import lib_Usuario
import lib_TiposDocumentales
import lib_TiposPqr
import lib_TemaTabla

##### IMPORTA LIBRERIAS DE MANEJO DE FUENTES DE DATOS #####

nameFileBase = _projectsPath + os.sep + 'orfeo' + os.sep + "index.html"

# TABLA DE NOMBRE DE TABLAS A PUBLICAR
@debug.runErrorHandler()
def tablas_name_manejo(args, kwargs):
   #print "tablas_name_manejo:", kwargs
   for k, v in kwargs.items(): exec(k + " = v")
   p = """
      getRemoteComponentPlugin({url:'nombreTablaGrid',
                                params:{'renderTo': 'center', '_source': 'nombreTabla', 'name': 'nombreTablaGridWindow'}},                                
                                false, true)
   """
   index = fileText(nameFileBase)
   index = index % (p)   
   return index
_regRoute("tablas_name_manejo", tablas_name_manejo)

# TABLA MANEJO DE EXPEDIENTES POR DEPENDENCIA
@debug.runErrorHandler()
def ExpedientePorDependencia_manejo(args, kwargs):
   #print "tablas_name_manejo:", kwargs
   for k, v in kwargs.items(): exec(k + " = v")
   p = """
      getRemoteComponentPlugin({url:'ExpedientePorDependenciaGrid',
                                params:{'renderTo': 'center', '_source': 'ExpedientePorDependencia', 'name': 'ExpedientePorDependenciaGridWindow'}},                                
                                false, true)
   """
   index = fileText(nameFileBase)
   index = index % (p)   
   return index
_regRoute("ExpedientePorDependencia_manejo", ExpedientePorDependencia_manejo)

# TABLA MANEJO RADICADOS POR EXPEDIENTE
@debug.runErrorHandler()
def RadicadoPorExpediente_manejo(args, kwargs):
   #print "tablas_name_manejo:", kwargs
   for k, v in kwargs.items(): exec(k + " = v")
   p = """
      getRemoteComponentPlugin({url:'RadicadoPorExpedienteGrid',
                                params:{'renderTo': 'center', '_source': 'RadicadoPorExpediente', 'name': 'RadicadoPorExpedienteGridWindow'}},                                
                                false, true)
   """
   index = fileText(nameFileBase)
   index = index % (p)   
   return index
_regRoute("RadicadoPorExpediente_manejo", RadicadoPorExpediente_manejo)

# TABLA MANEJO CAMPOS PARA EXPEDIENTES
@debug.runErrorHandler()
def CampoExpediente_manejo(args, kwargs):
   #print "tablas_name_manejo:", kwargs
   for k, v in kwargs.items(): exec(k + " = v")
   p = """
      getRemoteComponentPlugin({url:'CampoExpedienteGrid',
                                params:{'renderTo': 'center', '_source': 'CampoExpediente', 'name': 'CampoExpedienteGridWindow'}},                                
                                false, true)
   """
   index = fileText(nameFileBase)
   index = index % (p)   
   return index
_regRoute("CampoExpediente_manejo", CampoExpediente_manejo)


# Etiquetas Para Expedientes por Dependencia
@debug.runErrorHandler()
def EtiquetaDependencia_manejo(args, kwargs):
   #print "tablas_name_manejo:", kwargs
   for k, v in kwargs.items(): exec(k + " = v")
   p = """
      getRemoteComponentPlugin({url:'EtiquetaDependenciaGrid',
                                params:{'renderTo': 'center', '_source': 'EtiquetaDependencia', 'name': 'EtiquetaDependenciaGridWindow'}},                                
                                false, true)
   """
   index = fileText(nameFileBase)
   index = index % (p)   
   return index
_regRoute("EtiquetaDependencia_manejo", EtiquetaDependencia_manejo)

# Manejo de Dependencia
@debug.runErrorHandler()
def Dependencia_manejo(args, kwargs):
   #print "tablas_name_manejo:", kwargs
   for k, v in kwargs.items(): exec(k + " = v")
   p = """
      getRemoteComponentPlugin({url:'DependenciaGrid',
                                params:{'renderTo': 'center', '_source': 'Dependencia', 'name': 'DependenciaGridWindow'}},                                
                                false, true)
   """
   index = fileText(nameFileBase)
   index = index % (p)   
   return index
_regRoute("Dependencia_manejo", Dependencia_manejo)


# Manejo de Usuario
@debug.runErrorHandler()
def Usuario_manejo(args, kwargs):
   #print "tablas_name_manejo:", kwargs
   for k, v in kwargs.items(): exec(k + " = v")
   p = """
      getRemoteComponentPlugin({url:'UsuarioGrid',
                                params:{'renderTo': 'center', '_source': 'Usuario', 'name': 'UsuarioGridWindow'}},                                
                                false, true)
   """
   index = fileText(nameFileBase)
   index = index % (p)   
   return index
_regRoute("Usuario_manejo", Usuario_manejo)

# Manejo de Tipos Documentales
@debug.runErrorHandler()
def TiposDocumentales_manejo(args, kwargs):
   #print "tablas_name_manejo:", kwargs
   for k, v in kwargs.items(): exec(k + " = v")
   p = """
      getRemoteComponentPlugin({url:'TiposDocumentalesGrid',
                                params:{'renderTo': 'center', '_source': 'TiposDocumentales', 'name': 'TiposDocumentalesGridWindow'}},                                
                                false, true)
   """
   index = fileText(nameFileBase)
   index = index % (p)   
   return index
_regRoute("TiposDocumentales_manejo", TiposDocumentales_manejo)

# Manejo de Tipos Documentales
@debug.runErrorHandler()
def TiposPqr_manejo(args, kwargs):
   #print "tablas_name_manejo:", kwargs
   for k, v in kwargs.items(): exec(k + " = v")
   p = """
      getRemoteComponentPlugin({url:'TiposPqrGrid',
                                params:{'renderTo': 'center', '_source': 'TiposPqr', 'name': 'TiposPqrGridWindow'}},                                
                                false, true)
   """
   index = fileText(nameFileBase)
   index = index % (p)   
   return index
_regRoute("TiposPqr_manejo", TiposPqr_manejo)


# Manejo de Tabla de Temas
@debug.runErrorHandler()
def TemaTabla_manejo(args, kwargs):
   #print "tablas_name_manejo:", kwargs
   for k, v in kwargs.items(): exec(k + " = v")
   p = """
      getRemoteComponentPlugin({url:'TemaTablaGrid',
                                params:{'renderTo': 'center', '_source': 'TemaTabla', 'name': 'TemaTablaGridWindow'}},                                
                                false, true)
   """
   index = fileText(nameFileBase)
   index = index % (p)   
   return index
_regRoute("TemaTabla_manejo", TemaTabla_manejo)


# Manejo de Tipos Documentales
#@debug.runErrorHandler()
def asignaRadicadoPorExpediente_manejo(args, kwargs):   
   kwargs['renderTo'] = 'center'
   kwargs['_source']  = 'ExpedientePorDependencia'
   kwargs['name']     = 'ExpedientePorDependenciaBuscaGrid'   
   for k, v in kwargs.items(): exec(k + " = v")
   params = simplejson.dumps(kwargs, encoding="ISO-8859-1")
   
   p = """
      getRemoteComponentPlugin({url:'ExpedientePorDependenciaBuscaGrid',
                                params:%s},                                
                                false, true)
   """ % (params)
   
   index = fileText(nameFileBase)
   index = index % (p)
   print "............................."
   return index
_regRoute("asignaRadicadoPorExpediente_manejo", asignaRadicadoPorExpediente_manejo)



