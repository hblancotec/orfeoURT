import copy, types, datetime

from cherrypy.lib.static import serve_file, staticfile, serve_download
import os

import simplejson
from database import ac_dynamicData
import extjs

# forma basica de ventana
def basicForm(sourceForm="", render="", namewin="", titlewin="", title="", Upload=False, options={}):
   wform  = extjs.formWindowWidget(window  = { 'width'     : 750 
                                              ,'autoHeight': True
                                              ,'x'         : 5
                                              ,'y'         : 5
                                              ,'id'        : namewin
                                              ,'title'     : titlewin
                                              ,'addTo'     : render
                                              ,'_xtype'    : 'formWindow'
                                              ,'_source'   : sourceForm                                                                                            
                                              }
                                   
                                   ,form   =  { 'id'        : namewin + 'Form'
                                               ,'_source'   : sourceForm
                                               ,'fileUpload': Upload 
                                               ,'url'       : ''} # este no tiene funcion?
                                   
                                   ,items  =  []
                                   
                                   ,buttons=  [])
   return wform
utils_Register_Function(basicForm)

# genera la forma partiendo del source
def sourceForm(sourceForm="", render="", namewin="", titlewin="", title="", Upload=False, options={}):
   dataClass = dataClass_getSource(sourceForm, whoami())
   if titlewin == "":
      titlewin = dataClass.__dataName__

   if title == "":
      title = sourceForm

   if namewin == "":
      namewin = "%sGridWindowFormWindow" % sourceForm     
      
   wform = basicForm(sourceForm, render, namewin, titlewin, title, Upload)   

   if len(dataClass.__visualorder__) > 0:
      visuals  = dataClass.__visualorder__
   else:   
      visuals  = dataClass.__columnsAttrs__.keys()

   for v in visuals:
      w = extjs.inputType(dataClass.getFieldExtjs(v))
      wform.appendField(w)
      
   update = """
     var ok    = returnOkRequest(object, true, null, true, null);
     var error = returnErrorRequest(null, null); 
     updateSource('%s', object, ok, error);    
   """ % (sourceForm)

   delete = """
     var ok    = returnOkRequest(object, true, null, true, null);
     var error = returnErrorRequest(null, null);        
     deleteSource('%s', object, ok, error);          
   """  % (sourceForm)
   
   wform.appendButton({'text'    : 'Salvar',
                       'id'      : namewin + "_button_save",
                       'iconCls' : 'icon_new',
                       'handler' : update})
   
   wform.appendButton({'text'    : 'Borrar',
                       'id'      : namewin + "_button_delete",
                       'iconCls' : 'icon_delete',
                       'handler' : delete})
   regresa = """
     regresaSource(object);
   """
   
   wform.appendButton({'text'   : 'Regresar',
                       'id'     : namewin + "_button_regresa",
                       'iconCls': 'icon_return',
                       'handler': regresa})

   show = """
     var ok    = returnShowOk(object);   
     var error = returnErrorRequest(null, null);
     readRecordSource('%s', object, ok, error);         
   """ % (sourceForm)
   
   w = wform.render()
   w['listeners'] = {'show': show}
   return dicResultTrue(w)
_regRoute("sourceForm", sourceForm)
utils_Register_Function(sourceForm)

# genera el grid partiendo del source
def sourceGrid(sourceGrid, render="", namewin="", titlewin="", title="", options={}, addData=[]):   
   if title == "":
      title = sourceGrid

   dataClass = dataClass_getSource(sourceGrid, whoami())

   if titlewin == "":
      titlewin = dataClass.__dataName__

   if namewin == "":
      namewin = "%sGridWindow" % sourceGrid
      
   baseFields   = copy.deepcopy(dataClass.__dataCache__)
   baseFieldsId = copy.deepcopy(baseFields)
   baseFieldsId.append(dataClass.__ID__)
   baseFieldsReader = baseFieldsId;
   baseFieldsReader.extend(addData)
   dicFields    = [{'name': b} for b in baseFieldsReader]
   bjr  = extjs.bufferedJsonReader({'id'    : dataClass.__ID__,
                                    'fields': dicFields});

   sortDefault = {}
   sortables   = []
   for f in baseFields:
      if dataClass.__columnsAttrs__.has_key(f):
         attr = dataClass.__columnsAttrs__[f]
         if attr.has_key("sort"):
            sortables.append(f)
         if attr.has_key("sortDefault") and attr.has_key("sort"):
            sortDefault["field"]     = f
            sortDefault["direction"] = attr["sort"]         
   
   bs = extjs.bufferedStore({'reader'  : bjr.render(),
                             'sortInfo': sortDefault,
                             'columns' : baseFieldsId,
                             '_source' : sourceGrid,
                             'url'     : '%sGridSortData'%title});
   bgw = extjs.bufferedGridView({});
   
   bgt = extjs.bufferedGridToolbar({'view':  bgw.render()});   

   cmList = []
   for f in baseFields:
      fExt = dataClass.getFieldExtjs(f)
      dm   = {}
      dm['header'] = fExt['fieldLabel']
      dm['align']  = 'left'
      if fExt['xtype'] != "textfield":
         dm['align']  = 'rigth'
      dm['width']     = int(fExt['width'] * 0.7)
      dm['dataIndex'] = f
      dm['sortable']  = f in sortables
                     
      cmList.append(dm)

   cm  = extjs.columnModel({'columns': cmList})

   newRecord = """
      getRemoteComponentPlugin({url   : '%sForm',
      
                                params: {'renderTo': '%s',
                                         'name'    : '%sFormWindow',
                                         '_source' : '%s'},
                                         
                                info  : {'call'  : object._widgetBase.getId(),
                                         'mode'  : 'new',
                                         'record': null}
                               }, false, true);
   """ % (title, render, namewin, title)                       
   
   editRecord = """
      var r = object.getSelectionModel().getSelected();
      getRemoteComponentPlugin({url   : '%sForm',
                                params: {'renderTo': '%s',
                                         'name'    : '%sFormWindow',
                                         '_source' : '%s'},
                                         
                                info  : {'call'  : object._widgetBase.getId(),
                                         'mode'  : 'edit',
                                         'record': r.id}
                               }, false, true);
   """ % (title, render, namewin, title)

   genReport = """      
       url_wind_direct({url: '%sSourceReport',  params: {'report':'%s'}});                      
   """ % (title,title)


   newButton = {'text'   : 'Nuevo %s'  % title,
                'iconCls': 'icon_new',
                'handler': newRecord,
                'id'     : '%s_grid_tbar' % namewin}

   reportButton = {'text'   : 'Reporte',
                   'iconCls': 'icon_new',
                   'handler': genReport,
                   'id'     : '%s_grid_tbar' % namewin}
   
   tbar = [newButton]
   #tbar = [newButton, reportButton]
            
   gp = extjs.gridPanel({'id'   : '%s_grid' % namewin
                         ,'ds'   : bs.render()
                         ,'cm'   : cm.render()
                         ,'sm'   : 'bufferedselectionmodel'
                         ,'view' : bgw.render()
                         ,'title': ''
                         ,'bbar' : bgt.render()
                         ,'tbar' : tbar
                        })
   
   gp.options['listeners'] = {'dblclick': editRecord}                           

   w = extjs.windowWidget(config={'id'    : namewin,
                                  'x'     : 4,
                                  'y'     : 4,
                                  'width' : 750,
                                  'height': 420,
                                  'title' : titlewin,
                                  'addTo' : render,
                                  'adjust': 10},
                          items=[])
   w.addItem(gp);     

   dicW = w.render()   
   return dicResultTrue(dicW)
_regRoute("sourceGrid", sourceGrid)
utils_Register_Function(sourceGrid)

# retorna los datos ordeandos para grid
def gridSourceSortData(args, kwargs):
   preWhere = kwargs.get("preWhere", [])   
   response = getProfileCacheSortData(kwargs['_source'],
                                      kwargs["sort"],
                                      kwargs["dir"],
                                      int(kwargs["start"]),
                                      int(kwargs["start"])+int(kwargs["limit"]),
                                      kwargs["columns"],
                                      preWhere = preWhere)
   return dicResultTrue(response) 
_regRoute("gridSourceSortData", gridSourceSortData)
utils_Register_Function(gridSourceSortData)

# crea un nuevo registro
def sourceInsertRecord(args, kwargs):
   dataClass = dataClass_getSource(kwargs['_source'], whoami())
   #fields    = simplejson.loads(kwargs['fields'], encoding="ISO-8859-1")
   fields    = kwargs['fields']
   fieldsStr = {}
   for k, v in fields.items():
      fieldsStr[str(k)] = v
   
   u = dataClass(**fieldsStr)
   error = u.save() # retorna false si no hay errores
   if not error: 
      return dicResultTrue("Registro Creado")
   else:
      return dicResultFalse(str(error))
_regRoute("sourceInsertRecord", sourceInsertRecord)
utils_Register_Function(sourceInsertRecord)

# lee un  registro
def sourceReadRecord(args, kwargs):
   session   = dySession()
   record    = {}
   fields    = kwargs['fields']
   # si envia una lista con element, por alguna razon que no se
   # la convierte en un item solo !!!
   if type(fields) not in [types.ListType, types.TupleType]:
      fields = [fields]
   dataClass = dataClass_getSource(kwargs['_source'], whoami())
   u = dataClass.getOneById(int(kwargs['_id']), session=session)   
   if u:
      for f in fields:
         value = getattr(u, str(f))
         if type(value) == datetime.datetime:
            value = str(value)            
         record[f] = value
         
   return dicResultTrue(record)  
_regRoute("sourceReadRecord", sourceReadRecord)
utils_Register_Function(sourceReadRecord)

def sourceUpdateRecord(args, kwargs):
   session   = dySession()
   dataClass = dataClass_getSource(kwargs['_source'], whoami())
   u = dataClass.getOneById(int(kwargs['_id']), session=session)   
   if u:
      #fields = simplejson.loads(kwargs['fields'], encoding="ISO-8859-1")
      fields    = kwargs['fields']
      error = dataClass.simpleUpdate(u, fields, sessionDB=session)      
      if not error: 
         return dicResultTrue("Registro Modificado")
      else:
         return dicResultFalse(str(error))
   else:
      return dicResultFalse("!!Registro no encontrado!!")
_regRoute("sourceUpdateRecord", sourceUpdateRecord)
utils_Register_Function(sourceUpdateRecord)

def sourceDeleteRecord(args, kwargs):
   session   = dySession()
   dataClass = dataClass_getSource(kwargs['_source'], whoami())
   u = dataClass.getOneById(int(kwargs['_id']), session=session)
   if u:
      dataClass.simpleDelete(u, sessionDB=session)

   return dicResultTrue("Registro Eliminado")   
_regRoute("sourceDeleteRecord", sourceDeleteRecord)
utils_Register_Function(sourceDeleteRecord)

# retorna todos ordenados
def allSourceSortData(source, columns=[], filters=[], sort=[]):
   session   = dySession()
   dataClass = dataClass_getSource(source, whoami())
   filters   = [{'field':'status', 'operator': '=', 'value':u'activo'}]
   rows      = dataClass.allSelect(columns=columns, filters=filters, sort=sort)
   dicData   = toDictColumns(rows, columns, dataClass.__ID__)
   response  = {'items'      : dicData,
                'version'    : 0,
                'total_count': cacheGetSize(source)}    
   return dicResultTrue(response) 
_regRoute("allSourceSortData", allSourceSortData)
utils_Register_Function(allSourceSortData)

#  retona lista de campos de una fuente
def getSourceListFields(source, listNames, editable=True,
                        sufix='', register={}):
   items     = []
   dataClass = dataClass_getSource(source, whoami())
   for ln in listNames:
      field = extjs.inputType(dataClass.getFieldExtjs(ln),
                              editable, sufix)
      if ln in register.keys():
         field.options['_register'] = True
         field.options['_id']       = register[ln]         
      items.append(field)
      
   return items
utils_Register_Function(getSourceListFields)

# lee un  registro por un campo
def sourceReadRecordByField(args, kwargs):
   session   = dySession()
   record    = {}
   fields    = kwargs['fields']
   # si envia una lista con element, por alguna razon que no se
   # la convierte en un item solo !!!
   if type(fields) not in [types.ListType, types.TupleType]:
      fields = [fields]
   dataClass = dataClass_getSource(kwargs['_source'], whoami())
   values    = {kwargs['field']:kwargs['value']}
   u = dataClass.getOneByEqual(values=values, session=session)   
   if u:
      for f in fields:
         value = getattr(u, str(f))
         if type(value) == datetime.datetime:
            value = str(value)
         record[f] = value
         
   return dicResultTrue(record)  
_regRoute("sourceReadRecordByField", sourceReadRecordByField)
utils_Register_Function(sourceReadRecordByField)