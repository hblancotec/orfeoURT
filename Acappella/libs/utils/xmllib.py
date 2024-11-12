import __builtin__
import sys, os, string, inspect, threading, Queue, pprint
import md5, time, base64, datetime, random, uuid
from time  import localtime, time, strftime, ctime, sleep
from types import *
import debug

from xml.etree.cElementTree import Element , SubElement , ElementTree

# convierte resultados de row proxy de class a xml
def sqlToXml(rows, dictra, xmlname=""):
   root = Element("root")
   
   for n in rows:
      data = Element("data")
      for k, v in dictra.items():
         data.attrib[v] = unicode(getattr(n, v))           
      root.append(data)
            
   Path = _rootPath + os.sep + 'reportes' + os.sep + xmlname      
   ElementTree(root).write(Path)   
utils_Register_Function(sqlToXml)



