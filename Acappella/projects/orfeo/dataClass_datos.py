#!/usr/bin/python
# -*- coding: iso-8859-15 -*-

import sys, os, types, copy
from time import localtime, time, strftime, ctime

from database.ac_dynamicData   import *
from utils.utils               import *

from  datetime import datetime

# /* TABLAS BASICAS DEPENDENCIA*/
class Dependencia(ac_DBBase, ac_ExtDbBase):
    __ID__         = 'id'
    __tablename__  = 'DEPENDENCIA'
    __dataName__   = 'Dependencia'
    __dataType__   = 'APLICACION'
    __columnsAttrs__ = {'DEPE_CODI': {'name'    : 'Codigo Dependencia',
                                      'nullable'      : True,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Codigo Dependencia',
                                      'description'   : 'Codigo Dependencia',                                        
                                      'sortDefault'   : True,
                                      'sort'          : 'ASC'},
                        
                        'DEPE_NOMB': {'name'    : 'Nombre de la Dependencia',
                                      'nullable'      : True,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Nombre de la Dependencia',
                                      'description'   : 'Nombre de la Dependencia',
                                      'sort'          : 'ASC'},

                        'ID_CONT': {'name'    : 'Codigo Continente',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Codigo Continente',
                                      'description'   : 'Codigo Continente'},

                        'ID_PAIS': {'name'    : 'Codigo Pais',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Codigo Pais',
                                      'description'   : 'Codigo Pais'},

                        'DPTO_CODI': {'name'    : 'Codigo Departamento',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Codigo Departamento',
                                      'description'   : 'Codigo Departamento',
                                      'sort'          : 'ASC',                                        
                                      'sortDefault'   : True},
                        
                        'DEPE_CODI_PADRE': {'name'    : 'Dependencia Padre',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Dependencia Padre',
                                      'description'   : 'Dependencia Padre'},
                        
                        'MUNI_CODI': {'name'    : 'Codigo Municipio',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Codigo Municipio',
                                      'description'   : 'Codigo Municipio'},
                        
                        'DEPE_CODI_TERRITORIAL': {'name'    : 'Codigo Territorial',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Codigo Territorial',
                                      'description'   : 'Codigo Territorial'},

                        'DEP_SIGLA': {'name'    : 'Sigla Dependencia',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Sigla Dependencia',
                                      'description'   : 'Sigla Dependencia'},
                        
                        'DEP_CENTRAL': {'name'    : 'Numero de Central',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Numero de Central',
                                      'description'   : 'Numero de Central'},
                        
                        'DEP_DIRECCION': {'name'    : 'Dirección',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Dirección',
                                      'description'   : 'Dirección'},
                        
                        'DEPE_NUM_INTERNA': {'name'    : 'Numero Interno',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Numero Interno',
                                      'description'   : 'Numero Interno'},
                        
                        'DEPE_NUM_RESOLUCION': {'name'    : 'Numero de Resolucion',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Numero de Resolucion',
                                      'description'   : 'Numero de Resolucion'},
                        
                        'DEPE_RAD_TP1': {'name'    : 'Tipo Radicado 1',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Tipo Radicado 1',
                                      'description'   : 'Tipo Radicado 1'},

                        'DEPE_RAD_TP2': {'name'    : 'Tipo Radicado 2',
                                      'nullable'      : True,
                                      'length'        : 40,                                          
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Tipo Radicado 2',
                                      'description'   : 'Tipo Radicado 2'},
                        
                        'DEPE_RAD_TP3': {'name'    : 'Tipo Radicado 3',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Tipo Radicado 3',
                                      'description'   : 'Tipo Radicado 3'},
                        
                        'DEPE_RAD_TP4': {'name'    : 'Tipo Radicado 4',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Tipo Radicado 4',
                                      'description'   : 'Tipo Radicado 4'},
                        
                        'DEPE_RAD_TP5': {'name'    : 'Tipo Radicado 5',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Tipo Radicado 5',
                                      'description'   : 'Tipo Radicado 5'},
                        
                        'DEPE_RAD_TP6': {'name'    : 'Tipo Radicado 6',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Tipo Radicado 6',
                                      'description'   : 'Tipo Radicado 6'},
                        
                        'DEPE_RAD_TP7': {'name'    : 'Tipo Radicado 7',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Tipo Radicado 7',
                                      'description'   : 'Tipo Radicado 7'},
                        
                        'DEPE_RAD_TP8': {'name'    : 'Tipo Radicado 8',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Tipo Radicado 8',
                                      'description'   : 'Tipo Radicado 8'},
                        
                        'DEPE_RAD_TP9': {'name'    : 'Tipo Radicado 9',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Tipo Radicado 9',
                                      'description'   : 'Tipo Radicado 9'},
                        
                        'DEPENDENCIA_ESTADO': {'name'    : 'Estado Dependencia',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Estado Dependencia',
                                      'description'   : 'Estado Dependencia'}
                       }
    
    __relations__  = {}
    __filters__    = {}
    __validators__ = {}
    __dataCache__  =  ['DEPE_CODI', 'DEPE_NOMB']
    __visualorder__ = ['DEPE_CODI', 'DEPE_NOMB', 'ID_CONT', 'ID_PAIS', 'DPTO_CODI', 'MUNI_CODI',
                       'DEPE_CODI_PADRE', 'DEPE_CODI_TERRITORIAL', 'DEP_SIGLA', 'DEP_CENTRAL',
                       'DEP_DIRECCION', 'DEPE_NUM_INTERNA', 'DEPE_NUM_RESOLUCION',
                       'DEPE_RAD_TP1', 'DEPE_RAD_TP2', 'DEPE_RAD_TP3', 'DEPE_RAD_TP4', 'DEPE_RAD_TP5',
                       'DEPE_RAD_TP6', 'DEPE_RAD_TP7', 'DEPE_RAD_TP8', 'DEPE_RAD_TP9', 'DEPENDENCIA_ESTADO'
                       ]

    id         = Column('id', Integer, Sequence('Dependencia_seq', optional=True),
                        primary_key=True, nullable=False)

    DEPE_CODI             = Column('DEPE_CODI',             Integer, nullable=True, default=0)     
    DEPE_NOMB             = Column('DEPE_NOMB',             Unicode(70), nullable=True, default='')
    ID_CONT               = Column('ID_CONT',               Integer, nullable=True, default=0)
    ID_PAIS               = Column('ID_PAIS',               Integer, nullable=True, default=0)
    DPTO_CODI             = Column('DPTO_CODI',             Integer, nullable=True, default=0)
    MUNI_CODI             = Column('MUNI_CODI',             Numeric(4,0), nullable=True, default=0)
    DEPE_CODI_PADRE       = Column('DEPE_CODI_PADRE',       Integer, nullable=True, default=0)
    DEPE_CODI_TERRITORIAL = Column('DEPE_CODI_TERRITORIAL', Numeric(4,0), nullable=True, default=0)
    DEP_SIGLA             = Column('DEP_SIGLA',             Unicode(70), nullable=True, default='')   
    DEP_CENTRAL           = Column('DEP_CENTRAL',           Numeric(1,0), nullable=True, default=0)
    DEP_DIRECCION         = Column('DEP_DIRECCION',         Unicode(100), nullable=True, default='')
    DEPE_NUM_INTERNA      = Column('DEPE_NUM_INTERNA',      Numeric(4,0), nullable=True, default=0)
    DEPE_NUM_RESOLUCION   = Column('DEPE_NUM_RESOLUCION',   Numeric(4,0), nullable=True, default=0)
    DEPE_RAD_TP1          = Column('DEPE_RAD_TP1',          Integer, nullable=True, default=0)
    DEPE_RAD_TP2          = Column('DEPE_RAD_TP2',          Integer, nullable=True, default=0)
    DEPE_RAD_TP3          = Column('DEPE_RAD_TP3',          Integer, nullable=True, default=0)
    DEPE_RAD_TP4          = Column('DEPE_RAD_TP4',          Integer, nullable=False, default=0)
    DEPE_RAD_TP5          = Column('DEPE_RAD_TP5',          Integer, nullable=True, default=0)
    DEPE_RAD_TP6          = Column('DEPE_RAD_TP6',          Integer, nullable=True, default=0)
    DEPE_RAD_TP7          = Column('DEPE_RAD_TP7',          Integer, nullable=True, default=0)
    DEPE_RAD_TP8          = Column('DEPE_RAD_TP8',          Integer, nullable=True, default=0)
    DEPE_RAD_TP9          = Column('DEPE_RAD_TP9',          Integer, nullable=True, default=0)    
    DEPENDENCIA_ESTADO    = Column('DEPENDENCIA_ESTADO',    Numeric(18,0), nullable=True, default=0)
    
    __mapper_args__     = {'extension': [FormatDecimal('MUNI_CODI'), FormatDecimal('DEPE_CODI_TERRITORIAL'),
                                         FormatDecimal('DEP_CENTRAL'), FormatDecimal('DEPE_NUM_INTERNA'),
                                         FormatDecimal('DEPE_NUM_RESOLUCION'), FormatDecimal('DEPENDENCIA_ESTADO')]} 
dataClass_register("Dependencia", Dependencia)
    

# /* TABLAS BASICAS USUARIOS*/
class Usuario(ac_DBBase, ac_ExtDbBase):
    __ID__         = 'id'
    __tablename__  = 'USUARIO'
    __dataName__   = 'Usuarios'
    __dataType__   = 'APLICACION'
    __columnsAttrs__ = {'USUA_CODI': {'name'    : 'Codigo Del Usuario',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Codigo Del Usuario',
                                      'description'   : 'Codigo Del Usuario',                                        
                                      'sortDefault'   : True,
                                      'sort'          : 'ASC'},
                        
                        'DEPE_CODI': {'name'    : 'Codigo de la Dependencia',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Codigo de la Dependencia',
                                      'description'   : 'Codigo de la Dependencia',
                                      'sort'          : 'ASC'},

                        'USUA_NOMB': {'name'    : 'Nombre Usuario',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Nombre Usuario',
                                      'description'   : 'Nombre Usuario'}
                       }
    
    __relations__  = {}
    __filters__    = {}
    __validators__ = {}
    __dataCache__  =  ['USUA_NOMB', 'USUA_CODI', 'DEPE_CODI']
    __visualorder__ = ['USUA_NOMB', 'USUA_CODI', 'DEPE_CODI']

    id         = Column('id', Integer, Sequence('Usuario_seq', optional=True),
                        primary_key=True, nullable=False)

    USUA_CODI             = Column('USUA_CODI',             Integer, nullable=True, default=0)
    DEPE_CODI             = Column('DEPE_CODI',             Integer, nullable=True, default=0)    
    USUA_NOMB             = Column('USUA_NOMB',             Unicode(45), nullable=True, default='')
dataClass_register("Usuario", Usuario)

# /* TABLAS TIPOS DE DATOS PARA PQR*/
class TiposDocumentales(ac_DBBase, ac_ExtDbBase):
    __ID__         = 'id'
    __tablename__  = 'SGD_TPR_TPDCUMENTO'
    __dataName__   = 'Tipos Documentales TRD'
    __dataType__   = 'APLICACION'
    __columnsAttrs__ = {'SGD_TPR_CODIGO': {'name'    : 'Tipo Documental TRD',
                                      'nullable'      : True,
                                      'length'        : 30,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Tipo Documental TRD',
                                      'description'   : 'Tipo Documental TRD',                                        
                                      'sortDefault'   : True,
                                      'sort'          : 'ASC'},
                        
                        'SGD_TPR_DESCRIP': {'name'    : 'Nombre Tipo Documental',
                                      'nullable'      : True,
                                      'length'        : 160,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Codigo de la Dependencia',
                                      'description'   : 'Nombre Tipo Documental',
                                      'sort'          : 'ASC'},

                        'SGD_TPR_TERMINO': {'name'    : 'Termino En Dias',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Termino En Dias',
                                      'description'   : 'Termino En Dias'},

                        'SGD_TPR_NUMERA': {'name'    : 'Numeración',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Numeración',
                                      'description'   : 'Numeración'},

                        'SGD_TPR_RADICA': {'name'    : 'Se Radica?',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Se Radica?',
                                      'description'   : 'Se Radica?'},

                        'SGD_TERMINO_REAL': {'name'    : 'Termino Real En Dias',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Termino Real En Dias',
                                      'description'   : 'Termino Real En Dias'},

                        'SGD_TPR_ALERTA': {'name'    : 'Alertar Dias',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Alertar Dias',
                                      'description'   : 'Alertar Dias'},

                        'SGD_TPR_NOTIFICA': {'name'    : 'Notifica En Dias',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Notifica En Dias',
                                      'description'   : 'Notifica En Dias'},
                        
                        'SGD_TPR_PQR': {'name'    : 'Tipo Para PQR',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Tipo Para PQR',
                                      'description'   : 'Tipo Para PQR'},
                        
                        'SGD_TPR_TP1': {'name'    : 'Tipo Radicado 1',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Tipo Radicado 1',
                                      'description'   : 'Tipo Radicado 1'},

                        'SGD_TPR_TP2': {'name'    : 'Tipo Radicado 2',
                                      'nullable'      : True,
                                      'length'        : 40,                                          
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Tipo Radicado 2',
                                      'description'   : 'Tipo Radicado 2'},
                        
                        'SGD_TPR_TP3': {'name'    : 'Tipo Radicado 3',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Tipo Radicado 3',
                                      'description'   : 'Tipo Radicado 3'},
                        
                        'SGD_TPR_TP4': {'name'    : 'Tipo Radicado 4',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Tipo Radicado 4',
                                      'description'   : 'Tipo Radicado 4'},
                        
                        'SGD_TPR_TP5': {'name'    : 'Tipo Radicado 5',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Tipo Radicado 5',
                                      'description'   : 'Tipo Radicado 5'},
                        
                        'SGD_TPR_TP6': {'name'    : 'Tipo Radicado 6',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Tipo Radicado 6',
                                      'description'   : 'Tipo Radicado 6'},
                        
                        'SGD_TPR_TP7': {'name'    : 'Tipo Radicado 7',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Tipo Radicado 7',
                                      'description'   : 'Tipo Radicado 7'},
                        
                        'SGD_TPR_TP8': {'name'    : 'Tipo Radicado 8',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Tipo Radicado 8',
                                      'description'   : 'Tipo Radicado 8'},
                        
                        'SGD_TPR_TP9': {'name'    : 'Tipo Radicado 9',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Tipo Radicado 9',
                                      'description'   : 'Tipo Radicado 9'},
                                                 
                       }
    
    __relations__  = {}
    __filters__    = {}
    __validators__ = {}
    __dataCache__  =  ['SGD_TPR_CODIGO', 'SGD_TPR_DESCRIP']
    __visualorder__ = ['SGD_TPR_CODIGO', 'SGD_TPR_DESCRIP', 'SGD_TPR_TERMINO', 'SGD_TPR_NUMERA', 'SGD_TPR_RADICA',
                       'SGD_TPR_ESTADO', 'SGD_TERMINO_REAL', 'SGD_TPR_ALERTA', 'SGD_TPR_NOTIFICA', 'SGD_TPR_PQR',
                       'SGD_TPR_TP1', 'SGD_TPR_TP2', 'SGD_TPR_TP3', 'SGD_TPR_TP4', 'SGD_TPR_TP5', 'SGD_TPR_TP6',
                       'SGD_TPR_TP7', 'SGD_TPR_TP8', 'SGD_TPR_TP9']

    id         = Column('id', Integer, Sequence('Usuario_seq', optional=True),
                        primary_key=True, nullable=False)

    SGD_TPR_CODIGO   = Column('SGD_TPR_CODIGO',    Integer, nullable=True, default=0)     
    SGD_TPR_DESCRIP  = Column('SGD_TPR_DESCRIP',   Unicode(150), nullable=True, default='')
    SGD_TPR_TERMINO  = Column('SGD_TPR_TERMINO',   Numeric(4,0), nullable=True, default=0)
    SGD_TPR_NUMERA   = Column('SGD_TPR_NUMERA',    Unicode(1), nullable=True, default=0)
    SGD_TPR_RADICA   = Column('SGD_TPR_RADICA',    Unicode(1), nullable=True, default=0)    
    SGD_TPR_ESTADO   = Column('SGD_TPR_ESTADO',    Integer, nullable=True, default=0)
    SGD_TERMINO_REAL = Column('SGD_TERMINO_REAL',  Integer, nullable=True, default=0)
    SGD_TPR_ALERTA   = Column('SGD_TPR_ALERTA',    Numeric(18,0), nullable=True, default=0)
    SGD_TPR_NOTIFICA = Column('SGD_TPR_NOTIFICA',  Integer, nullable=True, default=0)
    SGD_TPR_PQR      = Column('SGD_TPR_PQR',       Integer, nullable=True, default=0)
    SGD_TPR_TP1      = Column('SGD_TPR_TP1',       Numeric(18,0), nullable=True, default=0)
    SGD_TPR_TP2      = Column('SGD_TPR_TP2',       Numeric(18,0), nullable=True, default=0)
    SGD_TPR_TP3      = Column('SGD_TPR_TP3',       Numeric(18,0), nullable=True, default=0)
    SGD_TPR_TP4      = Column('SGD_TPR_TP4',       Integer, nullable=False, default=0)
    SGD_TPR_TP5      = Column('SGD_TPR_TP5',       Numeric(18,0), nullable=True, default=0)
    SGD_TPR_TP6      = Column('SGD_TPR_TP6',       Integer, nullable=True, default=0)
    SGD_TPR_TP7      = Column('SGD_TPR_TP7',       Integer, nullable=True, default=0)
    SGD_TPR_TP8      = Column('SGD_TPR_TP8',       Integer, nullable=True, default=0)
    SGD_TPR_TP9      = Column('SGD_TPR_TP9',       Numeric(18,0), nullable=True, default=0)
    
    __mapper_args__  = {'extension': [FormatDecimal('SGD_TPR_TERMINO'), FormatDecimal('SGD_TPR_ALERTA'),
                                      FormatDecimal('SGD_TPR_TP1'), FormatDecimal('SGD_TPR_TP2'),
                                      FormatDecimal('SGD_TPR_TP3'), FormatDecimal('SGD_TPR_TP5'),
                                      FormatDecimal('SGD_TPR_TP9')]} 
dataClass_register("TiposDocumentales", TiposDocumentales)

# /* TABLAS TIPOS DE DATOS PARA PQR*/
class TiposPqr(ac_DBBase, ac_ExtDbBase):
    __ID__         = 'id'
    __tablename__  = 'SGD_PQR_MASTER'
    __dataName__   = 'Tipos Documentales para PQR'
    __dataType__   = 'APLICACION'
    __columnsAttrs__ = {'SGD_PQR_TPD': {'name'    : 'Tipo Documental',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Codigo Del Usuario',
                                      'description'   : 'Codigo Del Usuario',
                                      'sort'          : 'ASC'},
                        
                        'SGD_PQR_LABEL': {'name'    : 'Label Para Web',
                                      'nullable'      : True,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Label Para Web',
                                      'description'   : 'Label Para Web',
                                      'sortDefault'   : True,
                                      'sort'          : 'ASC'},                                                

                        'SGD_PQR_DEPE': { 'name'         : 'Dependencia Responsable',
                                         'length'        : 50,
                                         'description'   : 'Dependencia Responsable',                                         
                                         'emptyText'     : 'Seleccione Dependencia',
                                         '_id'           : 'pqr_Dependencia',
                                         '_register'     :  True,
                                         'comborelation' : {'displayField'  : 'DEPE_NOMB',
                                                            'valueField'    : 'DEPE_CODI',
                                                            'hiddenName'    : 'SGD_PQR_DEPE',
                                                            'emptyText'     : 'Seleccione Dependencia',
                                                            'reload'        : False,
                                                            'autoLoad'      : True,
                                                            'url'           : 'DependenciaAll',
                                                            'fields'        : ['DEPE_NOMB', 'DEPE_CODI'],
                                                            'listeners'     : {'select': '_call_dependencia_select'}
                                                           } 
                                       },

                         'SGD_PQR_USUA': { 'name'        : 'Usuario Responsable',
                                         'length'        : 50,
                                         'description'   : 'Usuario Responsable',                                         
                                         'emptyText'     : 'Seleccione Responsable',
                                         '_id'           : 'pqr_Usuario',
                                         '_register'     :  True,
                                         'comborelation' : {'displayField'  : 'USUA_NOMB',
                                                            'valueField'    : 'id',
                                                            'hiddenName'    : 'SGD_PQR_USUA',
                                                            'emptyText'     : 'Usuario Responsable',
                                                            'reload'        : False,
                                                            'autoLoad'      : True,
                                                            'url'           : 'UsuarioAll',
                                                            'fields'        : ['USUA_NOMB', 'id'],
                                                            'listeners'     : {'show': 'alert(0)'}
                                                            #'listeners'     : {'show': "alert(0);ac_reset_combo(pqr_Usuario, 'UsuarioAll', {})"}                                                            
                                                           } 
                                       }                        
                        
                       }
    
    __relations__  = {}
    __filters__    = {}
    __validators__ = {}
    __dataCache__  =  ['SGD_PQR_LABEL']
    __visualorder__ = ['SGD_PQR_TPD', 'SGD_PQR_LABEL', 'SGD_PQR_DEPE', 'SGD_PQR_USUA']

    id         = Column('id', Integer, Sequence('Usuario_seq', optional=True),
                        primary_key=True, nullable=False)

    SGD_PQR_TPD    = Column('SGD_PQR_TPD',             Integer, nullable=True, default=0)
    SGD_PQR_LABEL  = Column('SGD_PQR_LABEL',             Unicode(50), nullable=True, default='')    
    SGD_PQR_DEPE      = Column('SGD_PQR_DEPE',             Integer, nullable=True, default=0)
    SGD_PQR_USUA      = Column('SGD_PQR_USUA',             Integer, nullable=True, default=0)
    
    SGD_PQR_LABEL  = Column('SGD_PQR_LABEL',             Unicode(50), nullable=True, default='')
dataClass_register("TiposPqr", TiposPqr)