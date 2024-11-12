#!/usr/bin/python
# -*- coding: iso-8859-15 -*-

import sys, os, types, copy
from time import localtime, time, strftime, ctime

from database.ac_dynamicData   import *
from utils.utils               import *

configGlobal = _loadConfig(_projectsPath + os.sep + "orfeo" + os.sep + "orfeo")
utils_Register_Object('configGlobal', configGlobal)

configDB   = configGlobal["database"]
strconnect = configDB["sqlname"] + "://" + \
             configDB["user"] + ":" + configDB["password"] + "@" + \
             configDB["adrress"] + "/" + configDB["name"]

#engine  = create_engine('mssql:///?dsn=dnpPrueba&has_window_funcs=1',
#                        echo=False, encoding='latin1')

print strconnect
engine  = create_engine(strconnect+'?has_window_funcs=1', echo=False, encoding='latin1')
                        
ac_DBBase.metadata.bind = engine

DBSession = dynamicData_DbSession()

### CLASES DE DATOS ########
from dataClass_expediente import *
from dataClass_datos      import *

try:
  ac_DBBase.metadata.create_all()
except:
  pass
DBSession.clear()