#!/usr/bin/python
# -*- coding: iso-8859-15 -*-

import sys, os, types, copy
from time import localtime, time, strftime, ctime

from onetoproxy              import *
from database.ac_dynamicData import *
from utils.utils             import *

###########################################
#### DEFINICION DE RELACIONES  BASE  ######
###########################################
#/* Clase de EXPEDIENTES */  depe_codi
EXPEDIENTEPORDEPENDENCIA.r_dependencia = relation(DEPENDENCIA, foreign_keys = [EXPEDIENTEPORDEPENDENCIA.depe_codi, DEPENDENCIA.DEPE_CODI]
                                        ,primaryjoin = EXPEDIENTEPORDEPENDENCIA.depe_codi == DEPENDENCIA.DEPE_CODI
                                        ,lazy        = False)                          
# relaciones
#/* Nombre de la Dependencia */
#EXPEDIENTEPORDEPENDENCIA._dependencia = one_to_one(DEPENDENCIA, prefix='dependencia', fields=['DEPE_NOMB'])
OneToOneMapperProxy(EXPEDIENTEPORDEPENDENCIA, 'r_dependencia', 'dependencia_nombre', DEPENDENCIA, 'DEPE_NOMB')