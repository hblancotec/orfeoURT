#!/usr/bin/python
# -*- coding: iso-8859-15 -*-

import sys, os, types, copy
from decimal import *
from time import localtime, time, strftime, ctime

from database.ac_dynamicData   import *
from utils.utils               import *

from  datetime import datetime

# extension para formateo de datos
class FormatDate(MapperExtension):
    def __init__(self, field):
        self.field = field

    def changeValue(self, instance):
        value   = getattr(instance, self.field)
        if value != None:
           newdate = datetime.strptime(value, '%Y-%m-%d %H:%M:%S')
           value   = newdate.strftime("%d/%m/%Y %H:%M:%S")        
           value   = newdate.strftime("%d/%m/%Y %H:%M:%S")
           setattr(instance, self.field, value)
        return value

    def before_update(self, mapper, connection, instance):
         self.changeValue(instance)         

    def before_insert(self, mapper, connection, instance):
         self.changeValue(instance)

    def append_result(self, mapper, selectcontext, row, instance, result, **flags):
        value = getattr(instance, self.field)
        if value != None:           
           value = value.strftime("%Y-%m-%d %H:%M:%S")
           setattr(instance, self.field, value)        
        return EXT_CONTINUE
ac_register_object("FormatDate", FormatDate)    

# extension para formateo de datos
class FormatDecimal(MapperExtension):
    def __init__(self, field):
        self.field = field

    def changeValue(self, instance):
        value   = getattr(instance, self.field)
        if value != None:           
           value   = Decimal(value)
           setattr(instance, self.field, value)
        return value

    def before_update(self, mapper, connection, instance):
         self.changeValue(instance)         

    def before_insert(self, mapper, connection, instance):
         self.changeValue(instance)

    def append_result(self, mapper, selectcontext, row, instance, result, **flags):
        value = getattr(instance, self.field)
        if value != None:           
           value = int(value)
           setattr(instance, self.field, value)        
        return EXT_CONTINUE
ac_register_object("FormatDecimal", FormatDecimal)        

#/* Nombre de Tablas */
class NombreTabla(ac_DBBase, ac_ExtDbBase):
    __ID__         = 'id'
    __tablename__  = 'SGD_NOMBRE_TABLAS'
    __dataName__   = 'Nombre de Tablas'
    __dataType__   = 'APLICACION'
    __columnsAttrs__ = {'table_name': {'name'    : 'Nombre',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Nombre de la Tabla',
                                      'description'   : 'Nombre de la Tabla',
                                      'sort'          : 'ASC',                                        
                                      'sortDefault'   : True}
                       }
    
    __relations__  = {}
    __filters__    = {}
    __validators__ = {}
    __dataCache__  =  ['table_name']
    __visualorder__ = ['table_name']

    id         = Column('id', Integer, Sequence('Nombre_Tabla_seq', optional=True),
                        primary_key=True, nullable=False)
    table_name = Column('table_name', Unicode(120), nullable=False, default='')        
Index('nombreTablaUnique', NombreTabla.__table__.c.table_name, unique=True)        
dataClass_register("NombreTabla", NombreTabla)

#/* CAMPOS PARA EXPEDIENTE */
class CampoExpediente(ac_DBBase, ac_ExtDbBase):
    __ID__         = 'id'
    __tablename__  = 'SGD_CAMEXP_CAMPOEXPEDIENTE'
    __dataName__   = 'Campos para Expedientes'
    __dataType__   = 'APLICACION'
    __columnsAttrs__ = {'SGD_CAMEXP_CODIGO': {'name'    : 'Codigo de Campo Expediente',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Codigo de Campo Expediente',
                                      'description'   : 'Codigo de Campo Expediente',
                                      'sort'          : 'ASC'},
                        
                        'SGD_CAMEXP_CAMPO': {'name'    : 'Nombre de Campo Para Expediente',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Nombre de Campo Para Expediente',
                                      'description'   : 'Nombre de Campo Para Expediente',
                                      'sort'          : 'ASC'},

                        'SGD_PAREXP_CODIGO': {'name'    : 'Codigo Para Expediente',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Codigo Para Expediente',
                                      'description'   : 'Codigo Para Expediente'},

                        'SGD_CAMEXP_FK': {'name'    : 'Campo Es LLave Foranea',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Campo Es LLave Foranea',
                                      'description'   : 'Campo Es LLave Foranea'},

                        'SGD_CAMEXP_TABLAFK': {'name'    : 'Nombre de la Tabla',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Nombre de la Tabla',
                                      'description'   : 'Nombre de la Tabla',
                                      'sort'          : 'ASC',                                        
                                      'sortDefault'   : True},
                        
                        'SGD_CAMEXP_CAMPOFK': {'name'    : 'Nombre del Campo Llave Foranea',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Nombre del Campo Llave Foranea',
                                      'description'   : 'Nombre del Campo Llave Foranea'},
                        
                        'SGD_CAMEXP_CAMPOVALOR': {'name'    : 'Nombre del Campo Valor',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Nombre del Campo Valor',
                                      'description'   : 'Nombre del Campo Valor'},
                        
                        'SGD_CAMEXP_ORDEN': {'name'    : 'Orden Del Campo',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Orden Del Campo',
                                      'description'   : 'Orden Del Campo'}
                       }
    
    __relations__  = {}
    __filters__    = {}
    __validators__ = {}
    __dataCache__  =  ['SGD_CAMEXP_TABLAFK', 'SGD_CAMEXP_CAMPO', 'SGD_CAMEXP_CODIGO']
    __visualorder__ = ['SGD_CAMEXP_CODIGO', 'SGD_CAMEXP_CAMPO', 'SGD_PAREXP_CODIGO',
                       'SGD_CAMEXP_FK', 'SGD_CAMEXP_TABLAFK', 'SGD_CAMEXP_CAMPOFK',
                       'SGD_CAMEXP_CAMPOVALOR', 'SGD_CAMEXP_ORDEN']

    id         = Column('id', Integer, Sequence('CampoExpediente_seq', optional=True),
                        primary_key=True, nullable=False)

    SGD_CAMEXP_CODIGO     = Column('SGD_CAMEXP_CODIGO',     Integer, nullable=False, default=0)     
    SGD_CAMEXP_CAMPO      = Column('SGD_CAMEXP_CAMPO',      Unicode(30), nullable=False, default='')
    SGD_PAREXP_CODIGO     = Column('SGD_PAREXP_CODIGO',     Integer, nullable=False, default=0)
    SGD_CAMEXP_FK         = Column('SGD_CAMEXP_FK',         Integer, nullable=False, default=0)
    SGD_CAMEXP_TABLAFK    = Column('SGD_CAMEXP_TABLAFK',    Unicode(30), nullable=False, default='')
    SGD_CAMEXP_CAMPOFK    = Column('SGD_CAMEXP_CAMPOFK',    Unicode(30), nullable=False, default='')
    SGD_CAMEXP_CAMPOVALOR = Column('SGD_CAMEXP_CAMPOVALOR', Unicode(30), nullable=False, default='')
    SGD_CAMEXP_ORDEN      = Column('SGD_CAMEXP_ORDEN',      Integer, nullable=False, default=0)
dataClass_register("CampoExpediente", CampoExpediente)

#/* CAMPOS POR DEPENDENCIA */
class EtiquetaDependencia(ac_DBBase, ac_ExtDbBase):
    __ID__         = 'id'
    __tablename__  = 'SGD_PAREXP_PARAMEXPEDIENTE'
    __dataName__   = 'Etiquetas Para Expedientes por Dependencia'
    __dataType__   = 'APLICACION'
    __columnsAttrs__ = {'SGD_PAREXP_CODIGO': {'name'    : 'Codigo de Campo Expediente',
                                      'nullable'      : False,
                                      'length'        : 30,
                                      'inputValidator': 'NOTBLANKNULL',
                                      'label'         : 'Codigo de Campo Expediente',
                                      'description'   : 'Codigo de Campo Expediente'},
                        
                        'DEPE_CODI': {'name'    : 'Codigo Dependencia',
                                      'nullable'      : False,
                                      'length'        : 30,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Codigo Dependencia',
                                      'description'   : 'Codigo Dependencia',                                        
                                      'sortDefault'   : True,
                                      'sort'          : 'ASC'},
                       
                        'SGD_PAREXP_TABLA': {'name'    : 'Nombre de la Tabla',
                                      'nullable'      : False,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Nombre de la Tabla',
                                      'description'   : 'Nombre de la Tabla',
                                      'sort'          : 'ASC'},
                        
                        'SGD_PAREXP_ETIQUETA': {'name'    : 'Etiqueta del Campo',
                                      'nullable'      : False,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Etiqueta del Campo',
                                      'description'   : 'Etiqueta del Campo'},
                        
                        'SGD_PAREXP_ORDEN': {'name'    : 'Orden Visual Del Campo',
                                      'nullable'      : False,
                                      'length'        : 30,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Orden Visual Del Campo',
                                      'description'   : 'Orden Visual Del Campo'}
                       }
    
    __relations__  = {}
    __filters__    = {} 
    __validators__ = {}
    __dataCache__  =  ['DEPE_CODI', 'SGD_PAREXP_CODIGO', 'SGD_PAREXP_TABLA', 'SGD_PAREXP_ETIQUETA', 'SGD_PAREXP_ORDEN']
    __visualorder__ = ['DEPE_CODI', 'SGD_PAREXP_CODIGO', 'SGD_PAREXP_TABLA', 'SGD_PAREXP_ETIQUETA', 'SGD_PAREXP_ORDEN']

    id         = Column('id', Integer, Sequence('EtiquetaDependencia_seq', optional=True),
                        primary_key=True, nullable=False)

    SGD_PAREXP_CODIGO   = Column('SGD_PAREXP_CODIGO',   Integer, nullable=False, default=0)
    DEPE_CODI           = Column('DEPE_CODI',           Integer, nullable=False, default=0)
    SGD_PAREXP_TABLA    = Column('SGD_PAREXP_TABLA',    Unicode(30), nullable=False, default='')    
    SGD_PAREXP_ETIQUETA = Column('SGD_PAREXP_ETIQUETA', Unicode(30), nullable=False, default='')
    SGD_PAREXP_ORDEN    = Column('SGD_PAREXP_ORDEN',    Integer, nullable=False, default=0)
dataClass_register("EtiquetaDependencia", EtiquetaDependencia)

#/* RADICADOS POR EXPEDIENTE */
class RadicadoPorExpediente(ac_DBBase, ac_ExtDbBase):
    __ID__         = 'id'
    __tablename__  = 'SGD_EXP_EXPEDIENTE'
    __dataName__   = 'Radicados Por Expediente'
    __dataType__   = 'APLICACION'
    __columnsAttrs__ = {'sgd_exp_numero': {'name'    : 'Codigo del Expediente',
                                      'nullable'      : False,
                                      'length'        : 40,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Codigo del Expediente',
                                      'description'   : 'Codigo del Expediente',
                                      'sort'          : 'ASC',                                        
                                      'sortDefault'   : True},
                        
                        'RADI_NUME_RADI': {'name'    : 'Numero de Radicado',
                                      'nullable'      : False,
                                      'length'        : 50,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Numero de Radicado',
                                      'description'   : 'Numero de Radicado',
                                      'sort'          : 'ASC'},
                       
                        'SGD_EXP_FECH': {'name'    : 'Fecha De Creación',
                                      'nullable'      : True,
                                      'length'        : 120,
                                      'format'        : 'Y-m-d H:i:s',    
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Fecha De Creación',
                                      'description'   : 'Fecha De Creación',
                                      'sort'          : 'ASC'},
                        
                        'SGD_EXP_FECH_MOD': {'name'    : 'Fecha De Modificación',
                                      'nullable'      : True,
                                      'format'        : 'Y-m-d H:i:s',        
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Fecha De Modificación',
                                      'description'   : 'Fecha De Modificación'},
                        
                        'DEPE_CODI': {'name'    : 'Codigo Dependencia',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Codigo Dependencia',
                                      'description'   : 'Codigo Dependencia',
                                      'sort'          : 'ASC'},
                        
                        'USUA_CODI': {'name'    : 'Codigo Usuario',
                                      'nullable'      : True,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Codigo Usuario',
                                      'description'   : 'Codigo Usuario'},

                         'USUA_DOC': {'name'    : 'Documento Usuario',
                                      'nullable'      : True,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Documento Usuario',
                                      'description'   : 'Documento Usuario'},

                         'SGD_EXP_ESTADO': {'name'    : 'Estado del Expediente',
                                      'nullable'      : True,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Estado del Expediente',
                                      'description'   : 'Estado del Expediente'},

                         'SGD_EXP_TITULO': {'name'    : 'Titulo del Expediente',
                                      'nullable'      : True,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Titulo del Expediente',
                                      'description'   : 'Titulo del Expediente'},

                         'SGD_EXP_ASUNTO': {'name'    : 'Asunto del Expediente',
                                      'nullable'      : True,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Asunto del Expediente',
                                      'description'   : 'Asunto del Expediente'},

                         'SGD_EXP_CARPETA': {'name'    : 'Carpeta',
                                      'nullable'      : True,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Carpeta',
                                      'description'   : 'Carpeta'},

                         'SGD_EXP_UFISICA': {'name'    : 'Ubicación Fisica',
                                      'nullable'      : True,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Ubicación Fisica',
                                      'description'   : 'Ubicación Fisica'},

                         'SGD_EXP_ISLA': {'name'    : 'Ubicación Isla',
                                      'nullable'      : True,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Ubicación Isla',
                                      'description'   : 'Ubicación Isla'},
                        
                        'SGD_EXP_ESTANTE': {'name'    : 'Ubicación Estante',
                                      'nullable'      : True,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Ubicación Estante',
                                      'description'   : 'Ubicación Estante'},
                        
                        'SGD_EXP_CAJA': {'name'    : 'Ubicación Caja',
                                      'nullable'      : True,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Ubicación Caja',
                                      'description'   : 'Ubicación Caja'},
                        
                        'SGD_EXP_FECH_ARCH': {'name'    : 'Fecha Archivado',
                                      'nullable'      : True,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Fecha Archivado',
                                      'description'   : 'Fecha Archivado'},
                        
                        'SGD_EXP_SUBEXPEDIENTE': {'name'    : 'Sub Expediente',
                                      'nullable'      : True,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Sub Expediente',
                                      'description'   : 'Sub Expediente'},
                        
                        'SGD_EXP_NOMBRESUBEXP': {'name'    : 'Nombre SubExpediente',
                                      'nullable'      : True,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Nombre SubExpediente',
                                      'description'   : 'Nombre SubExpediente'}                        
                       }
    
    __relations__  = {}
    __filters__    = {}
    __validators__ = {}
    __dataCache__  =  ['sgd_exp_numero', 'RADI_NUME_RADI', 'DEPE_CODI', 'USUA_CODI']
    __visualorder__ = ['sgd_exp_numero', 'RADI_NUME_RADI', 'SGD_EXP_FECH', 'SGD_EXP_FECH_MOD',
                       'DEPE_CODI', 'USUA_CODI', 'USUA_DOC', 'SGD_EXP_ESTADO', 'SGD_EXP_TITULO',
                       'SGD_EXP_ASUNTO', 'SGD_EXP_CARPETA', 'SGD_EXP_UFISICA', 'SGD_EXP_ISLA',
                       'SGD_EXP_ESTANTE', 'SGD_EXP_CAJA', 'SGD_EXP_FECH_ARCH', 'SGD_EXP_SUBEXPEDIENTE',
                       'SGD_EXP_NOMBRESUBEXP']

    id         = Column('id', Integer, Sequence('DependenciaExpediente_seq', optional=True),
                        primary_key=True, nullable=False)

    sgd_exp_numero        = Column('sgd_exp_numero',        Unicode(25), nullable=False, default='')
    RADI_NUME_RADI        = Column('RADI_NUME_RADI',        Numeric(15,0), nullable=False, default=0)
    SGD_EXP_FECH          = Column('SGD_EXP_FECH',          DateTime, nullable=True, default=datetime.now)
    SGD_EXP_FECH_MOD      = Column('SGD_EXP_FECH_MOD',      DateTime, nullable=True, default=datetime.now)
    DEPE_CODI             = Column('DEPE_CODI',             Numeric(4,0), nullable=True, default=0)
    USUA_CODI             = Column('USUA_CODI',             Numeric(4,0), nullable=True, default=0)
    USUA_DOC              = Column('USUA_DOC',              Unicode(15), nullable=True, default='')
    SGD_EXP_ESTADO        = Column('SGD_EXP_ESTADO',        Integer, nullable=True, default=0)
    SGD_EXP_TITULO        = Column('SGD_EXP_TITULO',        Unicode(50), nullable=True, default='')
    SGD_EXP_ASUNTO        = Column('SGD_EXP_ASUNTO',        Unicode(150), nullable=True, default='')
    SGD_EXP_CARPETA       = Column('SGD_EXP_CARPETA',       Unicode(30), nullable=True, default='')    
    SGD_EXP_UFISICA       = Column('SGD_EXP_UFISICA',       Unicode(20), nullable=True, default='')    
    SGD_EXP_ISLA          = Column('SGD_EXP_ISLA',          Unicode(10), nullable=True, default='')    
    SGD_EXP_ESTANTE       = Column('SGD_EXP_ESTANTE',       Unicode(10), nullable=True, default='')    
    SGD_EXP_CAJA          = Column('SGD_EXP_CAJA',          Unicode(10), nullable=True, default='')
    SGD_EXP_FECH_ARCH     = Column('SGD_EXP_FECH_ARCH',     DateTime, nullable=True, default=datetime.now)
    SGD_EXP_SUBEXPEDIENTE = Column('SGD_EXP_SUBEXPEDIENTE', Integer, nullable=True, default=0)
    SGD_EXP_NOMBRESUBEXP  = Column('SGD_EXP_NOMBRESUBEXP',  Unicode(255), nullable=True, default='')

    __mapper_args__     = {'extension': [FormatDate('SGD_EXP_FECH'), FormatDate('SGD_EXP_FECH_MOD'),
                                         FormatDate('SGD_EXP_FECH_ARCH'),
                                         FormatDecimal('RADI_NUME_RADI'), FormatDecimal('DEPE_CODI'),
                                         FormatDecimal('USUA_CODI'), FormatDecimal('SGD_EXP_ESTADO')]}    
dataClass_register("RadicadoPorExpediente", RadicadoPorExpediente)        

#/* EXPEDIENTES POR DEPENDENCIA */
class ExpedientePorDependencia(ac_DBBase, ac_ExtDbBase):
    __ID__         = 'id'
    __tablename__  = 'SGD_SEXP_SECEXPEDIENTES'
    __dataName__   = 'Expediente Por Dependencia'
    __dataType__   = 'APLICACION'
    __columnsAttrs__ = {'sgd_exp_numero': {'name'    : 'Numero del Expediente',
                                      'nullable'      : False,
                                      'length'        : 30,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Numero del Expediente',
                                      'description'   : 'Numero del Expediente',
                                      'sort'          : 'ASC',                                        
                                      'sortDefault'   : True},
                        
                        'sgd_srd_codigo': {'name'    : 'Codigo de la Serie',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Codigo de la Serie',
                                      'description'   : 'Codigo de la Serie',
                                      'sort'          : 'ASC'},
                       
                        'sgd_sbrd_codigo': {'name'    : 'Codigo de la SubSerie',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Codigo de la SubSerie',
                                      'description'   : 'Codigo de la SubSerie',
                                      'sort'          : 'ASC'},
                        
                        'sgd_sexp_secuencia': {'name'    : 'Secuencia',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Secuencia',
                                      'description'   : 'Secuencia'},
                        
                        'depe_codi': {'name'    : 'Codigo Dependencia',
                                      'nullable'      : False,
                                      'length'        : 20,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Codigo Dependencia',
                                      'description'   : 'Codigo Dependencia',
                                      'sort'          : 'ASC'},
                        
                         'usua_doc': {'name'    : 'Documento Usuario Creador',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Documento Usuario Creador',
                                      'description'   : 'Documento Usuario Creador'},

                         'sgd_sexp_fech': {'name'    : 'Fecha del Expediente',
                                      'nullable'      : False,
                                      'format'        : 'Y-m-d H:i:s', 
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Fecha del Expediente',
                                      'description'   : 'Fecha del Expediente'},

                         'sgd_fexp_codigo': {'name'    : 'Codigo?',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Codigo?',
                                      'description'   : 'Codigo?'},

                         'sgd_sexp_ano': {'name'    : 'Año',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Año',
                                      'description'   : 'Año'},

                         'usua_doc_responsable': {'name'    : 'Documento Usuario Responsable',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULLŽ',
                                      'label'         : 'Documento Usuario Responsable',
                                      'description'   : 'Documento Usuario Responsable'},

                         'sgd_pexp_codigo': {'name'    : 'Codigo?',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULL',
                                      'label'         : 'Codigo?',
                                      'description'   : 'Codigo?'},

                         'SGD_SEXP_PAREXP1': {'name'    : 'Etiqueta 1',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULL',
                                      'sort'          : 'ASC',  
                                      'label'         : 'Etiqueta 1',
                                      'description'   : 'Etiqueta 1'},

                        'SGD_SEXP_PAREXP2': {'name'    : 'Etiqueta 2',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULL',
                                      'sort'          : 'ASC',         
                                      'label'         : 'Etiqueta 2',
                                      'description'   : 'Etiqueta 2'},
                        
                        'SGD_SEXP_PAREXP3': {'name'    : 'Etiqueta 3',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULL',
                                      'sort'          : 'ASC',         
                                      'label'         : 'Etiqueta 3',
                                      'description'   : 'Etiqueta 3'},

                        'SGD_SEXP_PAREXP4': {'name'    : 'Etiqueta 4',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULL',
                                      'sort'          : 'ASC',         
                                      'label'         : 'Etiqueta 4',
                                      'description'   : 'Etiqueta 4'},

                        'SGD_SEXP_PAREXP5': {'name'    : 'Etiqueta 5',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULL',
                                      'label'         : 'Etiqueta 5',
                                      'description'   : 'Etiqueta 5'},                         
                         
                        
                        'SGD_SEXP_NOMBRE': {'name'    : 'Nombre Del Expediente',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULL',
                                      'label'         : 'Nombre Del Expediente',
                                      'description'   : 'Nombre Del Expediente'},

                       'dependencia_nombre': {'name'    : 'Nombre De la Dependencia',
                                      'nullable'      : False,
                                      'length'        : 120,
                                      'inputValidator': 'NOTBLANKNULL',
                                      'sort'          : 'ASC',  
                                      'label'         : 'Nombre De la Dependencia',
                                      'description'   : 'Nombre De la Dependencia'}                            
                       }
    
    __relations__  = {}
    __filters__    = {}
    __validators__ = {}
    __dataCache__  =  ['sgd_exp_numero', 'depe_codi', 'dependencia_nombre',
                       'SGD_SEXP_PAREXP1', 'SGD_SEXP_PAREXP2', 'SGD_SEXP_PAREXP3', 'SGD_SEXP_PAREXP4']
    __visualorder__ = ['sgd_exp_numero', 'sgd_srd_codigo', 'sgd_sbrd_codigo', 'sgd_sexp_secuencia', 'depe_codi', 'usua_doc',
                        'sgd_sexp_fech', 'sgd_sexp_ano', 'usua_doc_responsable', 'SGD_SEXP_PAREXP1',
                        'SGD_SEXP_PAREXP2', 'SGD_SEXP_PAREXP3', 'SGD_SEXP_PAREXP4', 'SGD_SEXP_PAREXP5']

    id         = Column('id', Integer, Sequence('DependenciaExpediente_seq', optional=True),
                        primary_key=True, nullable=False)

    sgd_exp_numero       = Column('sgd_exp_numero',       Unicode(25), nullable=False, default='')
    sgd_srd_codigo       = Column('sgd_srd_codigo',       Integer, nullable=False, default=0)
    sgd_sbrd_codigo      = Column('sgd_sbrd_codigo',      Integer, nullable=False, default=0)
    sgd_sexp_secuencia   = Column('sgd_sexp_secuencia',   Integer, nullable=False, default=0)
    depe_codi            = Column('depe_codi',            Integer, nullable=False, default=0)
    usua_doc             = Column('usua_doc',             Unicode(25), nullable=False, default='')
    sgd_sexp_fech        = Column('sgd_sexp_fech',        DateTime, nullable=False, default=datetime.now)
    sgd_fexp_codigo      = Column('sgd_fexp_codigo',      Integer, nullable=False, default=0)
    sgd_sexp_ano         = Column('sgd_sexp_ano',         Integer, nullable=False, default=0)
    usua_doc_responsable = Column('usua_doc_responsable', Unicode(18), nullable=False, default='')
    sgd_pexp_codigo      = Column('sgd_pexp_codigo',      Integer, nullable=False, default=0)
    SGD_SEXP_PAREXP1     = Column('SGD_SEXP_PAREXP1',     Unicode(160), nullable=False, default='')
    SGD_SEXP_PAREXP2     = Column('SGD_SEXP_PAREXP2',     Unicode(160), nullable=False, default='')
    SGD_SEXP_PAREXP3     = Column('SGD_SEXP_PAREXP3',     Unicode(160), nullable=False, default='')
    SGD_SEXP_PAREXP4     = Column('SGD_SEXP_PAREXP4',     Unicode(160), nullable=False, default='')
    SGD_SEXP_PAREXP5     = Column('SGD_SEXP_PAREXP5',     Unicode(160), nullable=False, default='')
    SGD_SEXP_NOMBRE      = Column('SGD_SEXP_NOMBRE',      Unicode(160), nullable=False, default='')

    __mapper_args__     = {'extension': FormatDate('sgd_sexp_fech')}
    
dataClass_register("ExpedientePorDependencia", ExpedientePorDependencia)