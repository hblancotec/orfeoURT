import sys, os, string

"""
El path se envia indicando toda la ruta incluyendo hasta el directorio Acappella
NO se coloca el separador de directorios (/ o \) al final de la ruta
Ej:
  start-xxx x:/dir/Acappella para windows
  start-xxx /dir/Acappella para linux

El directorio projects se incluye siempre al mismo nivel thirdparty
"""
appPath = os.getcwd() #lee path en el cual corre este script
if len(sys.argv) > 1:
  rootPath = sys.argv[1] #path enviado por comando de linea
else:  
  rootPath = string.join(appPath.split(os.sep)[:-2:], os.sep)#ubica directorio base de Acapella


sys.path.insert(0, rootPath + os.sep+'libs')
sys.path.insert(0, rootPath + os.sep+'thirdparty')
sys.path.insert(0, rootPath + os.sep + 'projects')

from utils import utils
utils_Register_Object("_rootPath",     rootPath)
utils_Register_Object("_projectsPath", rootPath + os.sep + 'projects')
utils_Register_Object("_appPath",      appPath)
utils_Register_Object("_routes",       {})

from utils import globalStart
globalStart.defServer('activedocument')
globalStart.runServer()