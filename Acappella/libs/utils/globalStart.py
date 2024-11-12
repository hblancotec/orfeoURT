"""
El path se envia indicando toda la ruta incluyendo hasta el directorio Acappella
NO se coloca el separador de directorios (/ o \) al final de la ruta
Ej:
  start-xxx x:/dir/Acappella para windows
  start-xxx /dir/Acappella para linux

El directorio projects se incluye siempre al mismo nivel thirdparty
"""

import __builtin__
import os, cherrypy


#registra funciones en diccionario builtin, de la aplicacion
def ac_register_function(f):
   setattr(__builtin__, f.func_name, f)
   return getattr(__builtin__, f.func_name)
__builtin__.ac_register_function = ac_register_function

#registra objeto en diccionario builtin, de la aplicacion
def ac_register_object(name, o):
   setattr(__builtin__, name, o)
__builtin__.ac_register_object = ac_register_object


# ruta asociadas a cada peticion web
ac_register_object("_pathRoutes",   {})

def _regRoute(route, func):
   _pathRoutes[route] = func
ac_register_function(_regRoute)

def defServer(appName):
   import utils

   basePath = _appPath + os.sep 
   cherrypy.lowercase_api   = True
   cherrypy.log.error_file  = basePath + 'log.txt'
   cherrypy.log.access_file = cherrypy.log.error_file

   config = _loadConfig(basePath + "server")
   utils_Register_Object("_config", config)
   
   cherrypy.config.update(config['http'])
         
   run = __import__(appName + '.run')
   cherrypy.tree.mount(run.run.Root(), '/')
   cherrypy.tree.mount(run.run.Root(), '/' + appName)

   modules = config.get('modules', default=None)
 
   if modules != None:
      for k,v in modules.items():
         _routes[k] = v
         module = utils.get_module(v)  
         if module:
            run = __import__(v + '.run')         
      
def runServer():
   cherrypy.engine.start()
   cherrypy.engine.block()
   #cherrypy.quickstart()
