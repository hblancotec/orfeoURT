#!/usr/bin/python
# -*- coding: iso-8859-15 -*-

from __future__ import with_statement
import sys, os, types, datetime, uuid, copy

from sqlalchemy import *
from utils.utils import *


# crea un registro basado en una instanciaextiende one to one proxy,
def classSaveRecord(DATACLASS, fields):
   objectData = DATACLASS(**fields)
   error      = objectData.save()
   return error if error else objectData
utils_Register_Function(classSaveRecord)
