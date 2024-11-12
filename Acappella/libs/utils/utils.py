import __builtin__
import sys, os, string, inspect, threading, Queue, pprint
import md5, time, base64, datetime, random, uuid
from time  import localtime, time, strftime, ctime, sleep
from types import *
import exceptions, mimetypes
mimetypes.init()
from cgi import FieldStorage

import cherrypy
from   cherrypy.lib.static import serve_file, staticfile

import simplejson
import debug #, globalObjects
from configobj import configobj, validate

###########################################################
######## FUNCIONES GENERALES  #############################
###########################################################
# retorna id unico para este proceso
def idProcess():
   return threading.currentThread()

# registra funciones en diccionario builtin, de la aplicacion
def utils_Register_Function(f):
    setattr(__builtin__, f.func_name, f)
__builtin__.utils_Register_Function = utils_Register_Function

# registra objeto en diccionario builtin, de la aplicacion
def utils_Register_Object(name, o):
    setattr(__builtin__, name,o)
__builtin__.utils_Register_Object = utils_Register_Object

# impresion indentada de objetos python
def nicePrint(obj):
   pp = pprint.PrettyPrinter(indent=0, depth=100)      
   pp.pprint(obj)
utils_Register_Function(nicePrint)

# conversion segura a string
def utils_str(value):
    if type(value) in [UnicodeType, StringType]:
        value = value.encode("utf-8")
    return str(value)    
__builtin__.utils_str = utils_str

# determina el nombre de la funcion
def whoami():
    import sys
    return sys._getframe(1).f_code.co_name
__builtin__.whoami = whoami
   
# importa modulos de manera dinamica.
def get_module(name):
    module = False
    if not string.strip(name) == "":#si no es blanco
      lstName = name.split('.')
      for m in lstName:
        if not sys.modules.has_key(m) : #si no esta cargado el modulo
          try:
            module = __import__(m) #importa el modulo
          except:
            module = False
            cherrypy.log("error cargando modulo: " + name, 'RUN', 1)          
        else:
          module = True
    else:
      cherrypy.log("modulo sin nombre", 'RUN', 1)
      
    return module
   
# carga archivos de configuracion
def _loadConfig(filename=""):
   config = configobj.ConfigObj(filename+".ini", configspec=filename+".cfg")
   config.validate(validate.Validator())
   return config
utils_Register_Function(_loadConfig)

# extrae la extension de un archivo
def extFile(filename):
   extFile = 'html'
   pos     = string.rfind(filename, '.')#ultimo texto separado por .
   if pos > -1:#encontro uno
      extFile =  filename[pos+1:]
   return extFile
utils_Register_Function(extFile)

# determina sin es un archivo
def isFile(filename="", message=""):
   if os.path.isfile(filename): #existe archivo        
      return dicResult(True, "", filename)
   else:
      return dicResult(False, message + "- archivo no existe - " + filename)

# lee archivo para envio
def _sendFile(filename="", strict=False):
  if _isOK(isFile(filename, "sendFile:")):
    return dicResult(True, "", serve_file(_getR(), \
                     mimetypes.types_map.get('.'+extFile(filename), "text/plain")), mode="file")     
  else:
    print "_sendFile: archivo no existe [" + filename + "]" 
    if strict:
       print "_sendFile: archivo no existe [" + filename + "]"
    return ""
utils_Register_Function(_sendFile)

# lee archivo para envio
def fileText(name):
   f = file(name)
   return f.read()
utils_Register_Function(fileText)

def getUuid(base='ui_'):
   return base + "_@_@_" + str(uuid.uuid1())
utils_Register_Function(getUuid)
   
########################################################
######## MANEJO DE DICCIONARIO TAREAS GLOBALES #########
########################################################
# diccionario GLOBAL a indexar por numero de tarea-thread
dicTask = {}

# retorna diccionario de resultados
def dicResult(ok=True, message="", result=None, mode="", level=0):
   return {'ok':ok, 'message':message, 'result':result, 'mode':mode, 'level':level}
utils_Register_Function(dicResult)

# retorna diccionario de resultados con valor true
def dicResultTrue(result=None, mode="", level=0):
   return {'ok':True, 'message':"", 'result':result, 'mode':mode, 'level':level}
utils_Register_Function(dicResultTrue)

# retorna diccionario de resultados con valor false
def dicResultFalse(message="", mode="", level=0):
   return {'ok':False, 'message':message, 'result':None, 'mode':mode, 'level':level}
utils_Register_Function(dicResultFalse)

# retorna si el resultado es correcto o no, y coloca el diccionario de resultados
# en el diccionario global indexado por numero de tarea
def _isOK(data, log=False):
   dicTask[idProcess()] = data
   if (log and not data['ok']): #registra error      
      cherrypy.log(data['message'], 'ERROR')
   return data['ok']
utils_Register_Function(_isOK)

# retorna un atributo del resultado
def _getAttr(data=None, attribute=''):
   if data == None:  
      return dicTask[idProcess()][attribute]
   else:
      return data[attribute]
utils_Register_Function(_getAttr)
    
# retorna solo el resultado del diccionario de resultados
def _getR(data=None):
   return _getAttr(data, 'result')
utils_Register_Function(_getR)

# retorna mensaje del diccionario de resultados
def _getM(data=None):
   return _getAttr(data, 'message')
utils_Register_Function(_getM)

# retorna nivel de error del diccionario de resultados
def _getL(data=None):
   return _getAttr(data, 'level')
utils_Register_Function(_getL)

# retorna ok del diccionario de resultados
def _getOK(data=None):
   if data == None:   
      return dicTask[idProcess()]['ok']
   else:
      return data['result']
utils_Register_Function(_getOK)

# retorna todo el diccionario de resultado
def _getAll(data=None):
   if data == None:  
      return dicTask[idProcess()]
   else:
      return data
utils_Register_Function(_getAll)

# lee fecha FORMATEADA ANO/MES/DIA
def getDate():
   return strftime("%Y/%m/%d", localtime())

# lee mes
def getMonth():
   return strftime("%m", localtime())

# lee ano
def getYear():
   return strftime("%Y", localtime())

# lee hora FORMATEADA HORA:MINUTOS:SEGUNDOS
def getTime():
   return strftime("%H:%M:%S", localtime())

# lee fecha - hora FORMATEADA ANO/MES/DIA - HORA:MINUTOS:SEGUNDOS
def getDateTime():    
   return getDate() + "-" + getTime()

# retorna diccionario,
# formado con keys de diccionarios fuente (dicSource) 
def newDicFrom(dicSource, keys):    
   items = [dicSource[k] for k in keys]
   return dict(zip(keys, items))
utils_Register_Function(newDicFrom)

###########################################################
######## ESTRUCTURAS DE MANEJO ############################
###########################################################
# Diccionario que funciona como objeto.
class objectDic(dict):
     """
     Xs is a container object to hold information about cross sections.
     """
     # dict interface support, to be extended if needed
     def __setitem__(self, key, value):
         setattr(self, key, value)

     def __getitem__(self, key):
         return getattr(self, key)

     def keys(self):
         return self.__dict__.keys()
        
     def values(self):
         return self.__dict__.values()

     def has_key(self, key):
         return self.__dict__.has_key(key)        
        
     def items(self):
         return zip(self.keys(), self.values())

     def __iter__(self):
         for k in self.keys():
             yield k
         raise StopIteration

     def iteritems(self):         
         for k in self.items():
             yield k
         raise StopIteration       

     def __contains__(self, key):
         return key in self.keys()

     def __repr__(self):
         return repr(dict(self.items()))

# convienrte valores a listas
def valueToListIntegers(value):
   list = []
   if   type(value) in StringTypes:
      if str(value) != "":
         list = value.split(',')
   elif type(value) in [ListType, TupleType]:
      list = value
   elif type(value) != NoneType:
      list = [value]
   list = [int(v) for v in list]   
   return list
utils_Register_Function(valueToListIntegers)

# formato basico para datos de source
def sourceDict():
   return {'items'      : [],
           'total_count': 0,
           'version'    : 0}
utils_Register_Function(sourceDict)

   
#######################################################
######## MANEJO DE ENVIO DE DATOS AL NAVEGADOR  #######
#######################################################
# procesa respuesta enviada al cliente widgets remotos
def sendData(data={}):
   # diccionario de respuesta
   result = { 
     "success" : True,
     "error"   : "",
     "data"    : {}
   }
   # es ok
   if _isOK(data):
      # es modo texto
      if data['mode'] == '':                 
         result["data"]    = data['result']
         result["success"] = data['ok']
         result["error"]   = data['message']
         #nicePrint(result)
         # a formato json
         return simplejson.dumps(result, encoding="ISO-8859-1")
      else:
         # es objeto iterador
         return data['result']
   else:
      result["success"] = False
      result["error"]   = _getM()
      return simplejson.dumps(result, encoding="ISO-8859-1")