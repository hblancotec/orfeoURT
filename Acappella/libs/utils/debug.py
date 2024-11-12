import sys, os, string, types
import inspect
import threading, Queue
import md5, time, base64, datetime
from time import localtime, time, strftime, ctime

#import datetext

##### debug
import exceptions
import mimetypes
mimetypes.init()

import utils

###########################################################
######## MANEJO DE MENSAJES DE ERROR ######################
###########################################################
def outputDebug(msgs=[]):
    """
    funcion que escribe una lista de mensajes a la salida de debug.
    """
    global debugfile
    msg = ''
    for txt in msgs:      
      msg = msg + str(txt)

    print msg

def outputLocalVarsDebug(locals):
    """
    funcion que envia a la salida de debug la lista de variables que se definen
    en __debugvar__ en cada funcion o clase, si esta (__debugvar__) existe
    """
    if locals.has_key('__debugvar__'):
       dbug = locals['__debugvar__']
       outputDebug(['>>>>>>DebugVars>>>>>>'])       
       for i in dbug :
         if locals.has_key(i):
           outputDebug(['>>', i, ':', locals[i]])
           
       outputDebug(['>>>>>>>>>>>>>>>>>>>>>'])

def outputFrame(w, frame):
    """
    funcion que envia a la salida de debug la informacion del frame 
    para determinar en que linea y archivo se produjo el error.
    """
    lastTrace = w + " - " + frame.f_code.co_name + " - File:" + frame.f_code.co_filename
    outputDebug(['En:', w, lastTrace])    
    outputLocalVarsDebug(frame.f_locals)
    outputDebug(['Llamado desde la linea:', str(frame.f_lineno)])    
    return lastTrace
  
def debugFrame(w):
    """
    funcion que controla el seguimiento de errores controlados por try.
    """
    frame = inspect.currentframe()
    index = 0
    lT    = ""
    outputDebug(["TIME:"+utils.getDate()+"-"+utils.getTime()])
    while frame:
      try:
         if index > 0 and frame.f_back:
            outputDebug(['----- trace index:', index, ''])
            if index == 1:               
               stack = sys.exc_info()
               lT = outputTrace(stack[0], stack[1], stack[2].tb_lineno)
               del stack                    
            wi = ''
            lF = outputFrame(wi, frame)
            if index == 1: lT = lT + " " + lF
         index += 1
      except: pass   
      frame = frame.f_back
      
    del frame
    
    return lT

def outputTrace(e, t, l, f):
    """
    funcion que envia a la salida de debug la informacion de excepciones que no
    se previeron a traves de try. 
    """
    #if e == exceptions.SystemExit : sys.exit(0)
    outputDebug(['Excepcion:',e])
    outputDebug(['Tipo de error :', t])
    outputDebug(['Linea del error:', l])    
    #lastTrace = "Excepcion:" + str(e) + " - " +  "Tipo:" + str(t) + " - " + "Linea:" + str(l) + " - File:" + f.f_code.co_filename
    lastTrace = "Tipo:" + str(t) + " - " + "Linea:" + str(l) + " - File:" + f.f_code.co_filename
    return lastTrace    

def debugTrace(w, type, value, tb):
    """
    funcion que controla el seguimiento de errores NO controlados por try.
    """
    index = 0
    outputDebug(["TIME:"+utils.getDate()+"-"+utils.getTime()])
    lastTrace = ''
    while tb:
      frame = tb.tb_frame  
      try:
         if index > 0 and frame.f_back:
           outputDebug(['----- trace index:', index, ''])
           if not tb.tb_next:
             lastTrace = outputTrace(type, value, tb.tb_lineno, frame)            
           wi = ''
           outputFrame(wi, frame)
         index = index + 1
      except: pass
      del frame  
      tb = tb.tb_next

    del tb
    return lastTrace

def findFrame(tb):
    """
    funcion que busca linea de error
    """
    index = 0
    call = ""
    while tb:
      frame = tb.tb_frame  
      if not tb.tb_next:
        call = frame.f_code.co_filename + ' - ' + frame.f_code.co_name + ' - ' + str(tb.tb_lineno)
      index = index + 1
      del frame  
      tb = tb.tb_next

    del tb
    return call

def debugHook(type, value, tb, message=""):
    """
    funcion invoca el seguimiento de errores controlados por try.
    """
    if message == "" : message = value
    return debugTrace(message, type, value, tb)

def outputMessage(tpyemsg, msg):
    outputDebug(['**** ', tpyemsg, ' ****************'])
    outputDebug(["TIME:"+utils.getDate()+"-"+utils.getTime()])
    outputDebug(msg)

#sys.excepthook = debugHook    

def runError(msg, modulo):    
    return debugHook(sys.exc_info()[0], sys.exc_info()[1], sys.exc_info()[2], modulo)    
    
def runErrorHandler():
    """ manejador para funciones invocadas de manera interna"""
    def wrapper(f):
      def newfunc(*pargs, **kwargs):       
        try: return f(*pargs, **kwargs)                
        except Exception, exception:
          lastTrace = runError('error: '+ str(f.func_doc) + '  [' +
                                      str(sys.exc_info()[0])+']', f.__name__)#carga error
          return dicResult(False, lastTrace)
                    
      return newfunc

    return wrapper

def getShortError():
    stack = sys.exc_info()
    return str(stack[0]) + " - " + str(stack[1]) + " - " + str(stack[2].tb_lineno)

def verError(stack, msg):
  message = str(stack[0]) + " - " + str(stack[1])
  message = findFrame(stack[2]) + " - " + message + " - " + msg
  return message