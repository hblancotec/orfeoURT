#!/usr/bin/python
# -*- coding: iso-8859-15 -*-
import types, os
import simplejson
import cherrypy
from utils import utils, debug

resourcesPath = os.sep + "resources" + os.sep + "files"
htmlPath      = resourcesPath + os.sep + "html"
scriptPath    = resourcesPath + os.sep + "script"
stylePath     = resourcesPath + os.sep + "style"
imagePath     = resourcesPath + os.sep + "image"
xmlPath       = resourcesPath + os.sep + "xml"

def _getPath(args, type):
   path = ""
   if args.has_key("filename"):
      filename  = args["filename"]
      route     = args.get("route", None)
      routePath = _routes.get(route, "")
      basePath  = _appPath
      if routePath != "":
         basePath = _projectsPath + os.sep + routePath
      
      if   type == "script":
         path = basePath + scriptPath + os.sep + filename
      elif type == "image":
         path = basePath + imagePath  + os.sep + filename
      elif type == "css":
         path = basePath + stylePath  + os.sep + filename         
      
   return path

def remove_item(v):
   if type(v) in types.StringTypes:
      p = v.rfind("_@_@_")
      if p > -1:
        return v[0:p]
      else:
        return v
   else:
      return v

def remove_arrow(kwargs):
   #print "entra ", kwargs, type(kwargs)
   newDict = {}
   if  type(kwargs) == types.DictType:
      for k, v in kwargs.items():
         k = remove_item(k)
         #print v, type(v)
         if   type(v) == types.DictType:
            newDict[k] = remove_arrow(v)
         elif type(v) == types.ListType:
             #print "por lista:", v
             newDict[k] = [remove_arrow(i) for i in v]
         else:
            newDict[k] = remove_item(v)
   elif type(kwargs) in types.StringTypes:
      #print "remove item", kwargs
      return remove_item(kwargs)
   elif type(kwargs) == types.ListType:
      return [remove_arrow(i) for i in kwargs]
   else:
      newDict = kwargs

   return newDict

def deep_decode(kwargs):
   newDict = {}
   if type(kwargs) == types.DictType:
      for k, v in kwargs.items():         
         if type(v) in types.StringTypes:
            #print "<", k, v, type(v)
            try:
               v = simplejson.loads(v, encoding="ISO-8859-1")
               if type(v) in [types.IntType, types.LongType, types.FloatType ]:
                  v = u"" + str(v)
            except:
               pass
            newDict[k] = v
            #print ">", k, v, type(v)
         else:
            #print "decode>", k, v, type(v)
            newDict[k] = deep_decode(v)   
   else:
      newDict = kwargs
      
   return newDict

class Root:
   # pagina index.html
   def index(self):
      return utils.sendData(_sendFile(_appPath + htmlPath + os.sep + "index.html"))
   index.exposed = True

   # archivos javascript
   def script(self, *args, **kwargs):
      file = _getPath(kwargs, "script")
      return utils.sendData(_sendFile(file, True))        
   script.exposed = True

   # archivos de imagen
   def image(self, *args, **kwargs):
      file = _getPath(kwargs, "image")
      return utils.sendData(_sendFile(file, True))        
   image.exposed = True

   # pagina index.html - Extjs
   def images(self, *args, **kwargs):
      filename = os.sep.join(args)
      file = _getPath({"filename":filename}, "image")
      return utils.sendData(_sendFile(file, True))
   images.exposed = True   

   # hojas de estilo
   def css(self, *args, **kwargs):
      file = _getPath(kwargs, "css")
      return utils.sendData(_sendFile(file, True))        
   css.exposed = True   

   def call(self, *args, **kwargs):
      call = kwargs.get("call", "")
      data = dicResultFalse("funcion no encontrada [%s]" % call)
      try:
         callObj = eval(call)
         if callable(callObj):
            data = callObj(kwargs)
      except Exception, e:
          data = dicResultFalse(call + ":" + str(e))
      
      return utils.sendData(data)   
   call.exposed = True       
  
   def default(self, *args, **kwargs):    
    try:
       data = {}
       path = "" 
       if len(args) > 0:
          path = args[0]
          
       kwargs = deep_decode(kwargs)
       if kwargs.has_key('mode'):
          print "default mode :", args, kwargs
          data = _pathRoutes[path](args, kwargs)
          return data
       else:   
          data = _pathRoutes[path](args, kwargs)
          return utils.sendData(data)
       
    except Exception, e:
       print '##### ERROR > active document  ######'
       print args, kwargs
       print "ruta default " + debug.getShortError()
       return utils.sendData(dicResultFalse(str(e)))        
   default.exposed = True  