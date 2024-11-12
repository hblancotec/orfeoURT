#!/usr/bin/python
# -*- coding: iso-8859-15 -*-

import __builtin__
import uuid
import pprint
import validate
from   types import *
from functools import partial

is_notBlank = partial(validate.is_string, min=1)

# carga nuevos valores en diccionario
def updateDic(target, source):
   for key, value in source.items():
      if not target.has_key(key):
         target[key] = value

# tooltip
def baseTooltip(tip, width=150):
   return { 'width': width
           ,'tip':tip}

# diccionario base de widget         
def baseWidget(type, name='', options={}):
   name   = name + "_@_@_" + str(uuid.uuid1())
   config = { 'xtype'  : type
             ,'_field' : '.'
             ,'_source': '.'
             ,'name'   : name
             ,'id'     : name}
   config.update(options)
   return config

# diccionario base de fecha
def baseDate(type, name=''):
   config = baseWidget('datefield', name)   
   config['showToday']   = True,
   config['format']      ='Y-m-d'
   config['width']       = 165
   config['allowBlank']  = True
   config['maxLength']   = 22   
   return config

# diccionario base de fecha
def baseStore(options={}):
   store = {'xtype'           : 'jsonstore',
            'autoLoad'        : False,
            'root'            : 'data.items',
            'versionProperty' : 'data.version',
            'totalProperty'   : 'data.total_count'              
           }
   store.update(options) 
   return store

# clase base para widget visuales
class guiWidgetBase():
   def __init__(self, config={}, items=[]):
      # diccionario opcion:validador
      self.validOptions = {'_field'     : is_notBlank,
                           '_source'    : is_notBlank,
                           '_register'  : validate.is_boolean,
                           'xtype'      : is_notBlank,
                           '_xtype'     : validate.is_string,
                           '_id'        : validate.is_string,
                           'items'      : validate.is_list,
                           'renderTo'   : validate.is_string,
                           'el'         : validate.is_string,
                           'addTo'      : validate.is_string,
                           'listeners'  : validate.is_dict}
      # lista de opciones obligatorias
      self.requiredOptions = ["_field", "_source"]
      # diccionario opcion:valor
      self.options      = {}
      # nombre del objeto asociado, ej: campo de la tabla
      self.options["_field"] = ""
      # nombre de la fuente de datos asociada
      self.options["_source"] = ""
      # sub items del widget
      self.options["items"]       = []
      self.options.update(config)
      # sub items del widget
      self.items        = items     

   # valida valores de cada opcion
   def check(self, config):
      # listado opciones del widget
      options = self.validOptions.keys()
      # diccionario de opciones invalidas
      invalid = {}

      # valida opciones requeridas
      for option in self.requiredOptions:
         if not option in config.keys():
            invalid[option] = "opcion obligatoria"
              
      # para cada opcion para este widget
      for key, value in config.items():
         # es una opcion de este widget?
         if key in options:
            # es valido el valor de esta opcion?
            validator = self.validOptions[key]
            try:
               value = validator(value=value)
            except Exception, e:
               invalid[key] = str(value) + " : valor invalido"
            
         else:
            invalid[key] = str(value) + " : opcion no valida"
      return invalid     

   # crea diccionario del widget
   def render(self):
      self.result = self.check(self.options)
      if len(self.result.keys()) > 0:
         return self.result
      
      # llama eventos de control 
      if self.options.has_key('listeners'):
         for k, v in self.options['listeners'].items():
            if v.find("_call_") == 0:
               call = getattr(__builtin__, v[6:], False)
               if (call) and (callable(call)):
                  self.options['listeners'][k] = call()
               
      
      # opciones generales
      render = self.options
      
      # items 
      render['items'] = []
      # render de cada sub item de este widget
      for item in self.items:
         if type(item) == DictType:
            render['items'].append(item)
         else:
            irender = item.render()
            if irender.has_key('xtype') and irender['xtype'] == '..':
               del irender['xtype']
            render['items'].append(irender)
            
      if len(render['items']) == 0:
         del render['items']

      # botones de la forma
      if self.__dict__.has_key('buttons'):
          render['buttons'] = self.buttons
      
      return render

   def addItem(self, widget, position=-1):
      self.items.insert(position, widget)

   def appendItem(self, widget):
      self.items.append(widget)

   def appendItems(self, widgets):
      for w in widgets:
         self.items.append(w)            

#################################      
# widget window                 #
#################################
class windowWidget(guiWidgetBase):    
   def __init__(self, config={}, items=[]):
      updateDic(config, { 'xtype'       : 'window'
                         ,'layout'      : 'fit'
                         ,'autoHeight'  : False
                         ,'autoWidth'   : False
                         ,'allowDomMove': False
                         ,'autoScroll'  : True
                         ,'draggable'   : False
                         ,'closable'    : False
                         ,'plain'       : True
                         ,'adjust'      : 0
                         ,'_field'      : '.'
                         ,'_source'     : '.'
                         #'info'        : {},
                         ,'_register'   : True})
      guiWidgetBase.__init__(self, config=config, items=items)
      self.validOptions.update({ 'id'          : is_notBlank
                                ,'title'       : validate.is_string
                                ,'layout'      : is_notBlank                                
                                ,'width'       : validate.is_integer
                                ,'height'      : validate.is_integer
                                ,'pageX'       : validate.is_integer
                                ,'pageY'       : validate.is_integer
                                ,'x'           : validate.is_integer
                                ,'y'           : validate.is_integer
                                ,'closable'    : validate.is_boolean
                                ,'plain'       : validate.is_boolean
                                ,'autoHeight'  : validate.is_boolean
                                ,'autoWidth'   : validate.is_boolean
                                ,'allowDomMove': validate.is_boolean
                                ,'autoScroll'  : validate.is_boolean
                                ,'draggable'   : validate.is_boolean
                                ,'adjust'      : validate.is_integer
                                #'info'        : validate.is_dict
                                })
      self.requiredOptions.extend(['id', 'title'])      

#################################      
# # widget forma                #
#################################
class formWidget(guiWidgetBase):    
   def __init__(self, config={}, items=[], buttons=[], Upload=False):
      updateDic(config, { 'xtype'      : 'formAcappella'
                         ,'plain'      : True
                         ,'labelAlign' : 'right'
                         ,'labelWidth' : 150
                         #,'layout'     : 'autotableform'
                         ,'autoHeight' : True
                         ,'autoWidth'  : True
                         ,'fileUpload' : Upload
                         ,'baseCls'    : 'x-plain'
                         ,'_field'     : '.'
                         ,'frame'      : True
                         ,'_register'  : True                         
                         })
      
      guiWidgetBase.__init__(self, config=config, items=items)
      self.validOptions.update({'autoHeight': validate.is_boolean,
                                'autoScroll': validate.is_boolean,
                                'autoHeight': validate.is_boolean,
                                'autoWidth' : validate.is_boolean,
                                'fileUpload': validate.is_boolean,
                                'layout'    : validate.is_string,
                                'baseCls'   : validate.is_string,
                                'plain'     : validate.is_boolean,
                                'pageX'     : validate.is_integer,
                                'pageY'     : validate.is_integer,
                                'frame'     : validate.is_boolean,
                                'url'       : validate.is_string,
                                '_submit'   : validate.is_string,
                                '_success'  : validate.is_string,
                                '_failure'  : validate.is_string,
                                'labelAlign': validate.is_string,
                                'labelWidth': validate.is_integer,
                                'id'        : is_notBlank,                                
                               })
      self.requiredOptions.extend(['id', 'url'])
      self.buttons = buttons

   def addItem(self, widget, position=-1):
      self.items.insert(position, widget)

   def appendItem(self, widget):
      self.items.append(widget)

   def appendItems(self, widgets):
      for w in widgets:
         self.items.append(w)                  
    
   def addField(self, field, position=-1):
      # validar tipo de field!!!!!
      self.addItem(field, position)

   def appendField(self, field):
      # validar tipo de field!!!!!
      self.appendItem(field)

   def appendFields(self, fields):
      for f in fields:
         self.appendItem(f)      

   def appendButton(self, button):
      self.buttons.append(button)


############################      
# widget tab panel         #
############################
class tabPanel(guiWidgetBase):    
   def __init__(self, config={}, items=[]):
      updateDic(config, { 'xtype'           : 'tabpanel'
                         ,'activeTab'       : 0
                         ,'border'          : True
                         ,'frame'           : True
                         ,'autoHeight'      : True
                         ,'autoShow'        : True 
                         ,'autoTabs'        : True
                         ,'deferredRender'  : False                     
                         ,'_field'          : '.'
                         ,'_source'         : '.'
                         ,'_register'       : False})
      guiWidgetBase.__init__(self, config=config, items=items)
      self.validOptions.update({ 'title'          : validate.is_string
                                ,'id'             : is_notBlank
                                ,'items'          : validate.is_list
                                ,'activeTab'      : validate.is_integer
                                ,'frame'          : validate.is_boolean
                                ,'autoHeight'     : validate.is_boolean
                                ,'border'         : validate.is_boolean
                                ,'plain'          : validate.is_boolean
                                ,'autoTabs'       : validate.is_boolean
                                ,'autoShow'       : validate.is_boolean 
                                ,'anchor'         : validate.is_string
                                ,'deferredRender' : validate.is_boolean
                               })
      self.requiredOptions.extend(['id', 'title'])

class itemPanel(guiWidgetBase):    
   def __init__(self, config={}, items=[]):
      updateDic(config, { 'xtype'         : '..'
                         ,'layout'        : 'form'
                         ,'frame'         : True 
                         ,'_field'        : '.'
                         ,'_source'       : '.'
                         #,'autoShow'      : True 
                         ,'hideMode'      : 'offsets'
                         ,'autoHeight'    : True
                         #,'deferredRender': False     
                         ,'listeners'     : {}
                         #,'bbar'         : []
                         ,'_register'     : False})
      guiWidgetBase.__init__(self, config=config, items=items)
      self.validOptions.update({ 'title'          : validate.is_string
                                ,'layout'         : validate.is_string
                                ,'hideMode'       : validate.is_string
                                ,'autoHeight'     : validate.is_boolean
                                ,'frame'          : validate.is_boolean
                                ,'autoShow'       : validate.is_boolean  
                                ,'id'             : is_notBlank
                                ,'bbar'           : validate.is_list
                                ,'listeners'      : validate.is_dict
                                 ,'deferredRender': validate.is_boolean
                               })
      self.requiredOptions.extend(['id', 'title'])
      
#######################      
# widget fields       #
#######################
class fieldWidget(guiWidgetBase):
   def __init__(self, config={}, items=[]):
      if not config.has_key('id') and config.has_key('name'):
        config['id'] = config['name']
      updateDic(config, { 'xtype'        : 'textfield'
                         ,'emptyText'    : ''
                         ,'disabled'     : False
                         #,'disabledClass': 'x-form-inline-field-disabled'
                         ,'listeners'    : {}})
                
      guiWidgetBase.__init__(self, config=config, items=items)      
      self.validOptions.update({'name'         : is_notBlank,
                                'fieldLabel'   : validate.is_string,
                                'value'        : validate.is_string,
                                'pageX'        : validate.is_integer,
                                'pageY'        : validate.is_integer,
                                'width'        : validate.is_integer,
                                'height'       : validate.is_integer,
                                'maxLength'    : validate.is_integer,
                                'minLength'    : validate.is_integer,
                                'id'           : is_notBlank,
                                'inputType'    : validate.is_string,
                                'anchor'       : validate.is_string,
                                'disabled'     : validate.is_boolean,
                                'allowBlank'   : validate.is_boolean,
                                'disabledClass': validate.is_string,
                                'emptyText'    : validate.is_string,
                                'labelStyle'   : validate.is_string,
                                'tooltip'      : validate.is_dict,
                                'listeners'    : validate.is_dict,
                                '_register'    : validate.is_boolean
                                })
      self.requiredOptions.extend(['id', 'name'])

   def addField(self, field, position=-1):
      # validar tipo de field!!!!!
      self.addItem(field, position)

   def appendField(self, field):
      # validar tipo de field!!!!!
      self.appendItem(field)

   def appendFields(self, fields):
      for f in fields:
         self.appendItem(f)        

class fieldSetWidget(fieldWidget):
   def __init__(self, config={}, items=[]):     
      updateDic(config, {'xtype'      : 'fieldset',
                         '_field'     : '.',
                         '_source'    : '.',                         
                         'labelWidth' : 200,
                         'title'      : '',
                         'defaults'   : {},
                         'defaultType': 'textfield',
                         'autoHeight' : True,
                         'bodyStyle'  : '',
                         'border'     : True})
                
      fieldWidget.__init__(self, config=config, items=items)      
      self.validOptions.update({'name'        : is_notBlank,                                
                                'labelWidth'  : validate.is_integer,
                                'title'       : validate.is_string,
                                'defaults'    : validate.is_dict,
                                'defaultType' : validate.is_string,
                                'autoHeight'  : validate.is_boolean,
                                'bodyStyle'   : validate.is_string,
                                'border'      : validate.is_boolean
                                })
      
class fieldText(fieldWidget):
   def __init__(self, config={}, items=[]):
      fieldWidget.__init__(self, config=config, items=items)

class fieldNumber(fieldWidget):
   def __init__(self, config={}, items=[]):
      updateDic(config, {'xtype' : 'numberfield'})
      fieldWidget.__init__(self, config=config, items=items)
      self.validOptions.update({'allowDecimals'   : validate.is_boolean,
                                'allowNegative'   : validate.is_boolean,
                                'decimalPrecision': validate.is_integer                                
                                })      

class fieldDateTime(fieldWidget):
   def __init__(self, config={}, items=[]):
      updateDic(config, {'xtype'    : 'DateField',
                         'showToday': True})
                         #'format'   : 'Y-m-d H:i:s'})                         
      fieldWidget.__init__(self, config=config, items=items)
      self.validOptions.update({'showToday': validate.is_boolean,
                                'format'   : validate.is_string})      
      
class fieldTextArea(fieldWidget):
   def __init__(self, config={}, items=[]):
      updateDic(config, {'xtype' : 'textarea'})
      fieldWidget.__init__(self, config=config, items=items)
      self.validOptions.update({'anchor' : validate.is_string})

class fieldComboBox(fieldWidget):
   def __init__(self, config={}, items=[]):
      updateDic(config, { 'xtype'         : 'combo'
                         ,'forceSelection': True
                         ,'mode'          : 'local'
                         ,'pageSize'      : 300
                         ,'width'         : 400
                         ,'loadingText'   : 'Cargando datos..'
                         ,'selectOnFocus' : True
                         ,'triggerAction' : 'all'
                         ,'fieldLabel'    : ''
                         ,'tpl'           : '' 
                         ,'displayField'  : ''
                         ,'valueField'    : ''
                         ,'hiddenName'    : ''
                         ,'editable'      : False
                         ,'emptyText'     : ''
                         ,'typeAhead'     : True
                         ,'reload'        : False
                         ,'allowBlank'    : False
                         ,'value'         : ''
                         ,'autoLoad'      : True                         
                         })
      
      fieldWidget.__init__(self, config=config, items=items)
      self.validOptions.update({'forceSelection' : validate.is_boolean,
                                'mode'           : validate.is_string,
                                'pageSize'       : validate.is_integer,
                                'loadingText'    : validate.is_string,
                                'selectOnFocus'  : validate.is_boolean,
                                'emptyText'      : validate.is_string,
                                'triggerAction'  : validate.is_string,
                                'fieldLabel'     : validate.is_string,
                                'tpl'            : validate.is_string,
                                'displayField'   : validate.is_string,
                                'valueField'     : validate.is_string,
                                'hiddenName'     : validate.is_string,
                                'editable'       : validate.is_boolean,
                                'emptyText'      : validate.is_string,
                                'typeAhead'      : validate.is_boolean,
                                'reload'         : validate.is_boolean,
                                'autoLoad'       : validate.is_boolean,
                                'store'          : validate.is_dict,
                                'fields'         : validate.is_list,
                                'url'            : validate.is_string
                                })


class fieldComboRelation(fieldComboBox):
   def __init__(self, config={}, items=[]):
      updateDic(config, {'xtype': 'comboboxex',
                         'store': self.localStore(config)})
      fieldComboBox.__init__(self, config=config, items=items)
      self.validOptions.update({})
        
   def localStore(self, configOri):      
      config = {} 
      config['xtype'] = 'datastore'
       
      config['proxy'] = {}
      config['proxy']['xtype'] = 'httpproxy'
      config['proxy']['url']   = configOri.get('url', '')
       
      config['reader'] = {}
      config['reader']['xtype']         = 'jsonreader'
      config['reader']['totalProperty'] = 'data.total_count'
      config['reader']['root']          = 'data.items'
      config['reader']['fields']        = configOri.get('fields', '')
       
      return config

class fieldComboLov(fieldComboRelation):
   def __init__(self, config={}, items=[]):
      updateDic(config, {'xtype': 'comboboxlov',
                         'mode' : 'remote'})
      fieldComboRelation.__init__(self, config=config, items=items)
      self.validOptions.update({})   

class fieldRadioGroup(fieldWidget):
   def __init__(self, config={}, items=[]):
      items = self.verItems(config)
      updateDic(config, {'xtype'   : 'radiogroup',
                         'vertical': True})
                
      fieldWidget.__init__(self, config=config, items=items)
      self.validOptions.update({'radio'   : validate.is_dict,
                                'vertical' : validate.is_boolean})
        
   def verItems(self, config):
      items = []
      name  = config['name']
      i = 0
      for k, v in config['radio'].items():
         checked = False
         tooltip = config.get('tooltip', {'tip': 'click', 'width': 50})
         if config.has_key("value") and (v == config["value"]):
            checked = True
            
         items.append({'boxLabel'   : k
                       ,'id'        : name + '_' + str(i)
                       ,'name'      : name
                       ,'inputValue': v
                       ,'tooltip'   : tooltip
                       ,'checked'   : checked})
         i += 1 
      return items

class fieldCheckBoxGroup(fieldWidget):
   def __init__(self, config={}, items=[]):
      items = self.verItems(config)
      updateDic(config, {'xtype'   : 'checkboxgroup',                         
                         'vertical': True})
                
      fieldWidget.__init__(self, config=config, items=items)
      self.validOptions.update({'vertical': validate.is_boolean,
                                'checkbox': validate.is_dict})
        
   def verItems(self, config):
      items = []
      name  = config['name']
      i = 0
      for k, v in config['checkbox'].items():
         checked = False
         tooltip = config.get('tooltip', {'tip': 'click', 'width': 50})
         if config.has_key("value") and (v == config["value"]):
            checked = True
            
         items.append({'boxLabel'   : k
                       ,'id'        : name + '_' + str(i)
                       ,'name'      : name
                       ,'inputValue': v
                       ,'tooltip'   : tooltip
                       ,'checked'   : checked})
         i += 1 
      return items

class fieldUpLoad(fieldWidget):
   def __init__(self, config={}, items=[]):
      updateDic(config, { 'xtype'    : 'fileuploadfield'
                         ,'_field'     : '.'
                         ,'_source'    : '.'
                         ,'buttonText'  : 'Buscar Archivo'
                        })
      fieldWidget.__init__(self, config=config, items=items)
      self.validOptions.update({ 'buttonCfg' : validate.is_dict
                                ,'buttonText': validate.is_string})

class uploadPanelWidget(guiWidgetBase):
   def __init__(self, config={}, items=[]):
      updateDic(config, { 'xtype'          : 'uploadpanel'
                         ,'_field'         : '.'
                         ,'_source'        : '.'
                         ,'buttonsAt'      : 'tbar'
                         ,'url'            : ''
                         ,'path'           :  ''
                         ,'addText'        : 'Vincular Documento (Archivo)'
                         ,'clickRemoveText': 'Click para eliminar'
                         ,'fileQueuedText' : 'Archivo <b>{0}</b> pendiente para enviar'
                         ,'fileFailedText' : 'Archivo <b>{0}</b> envio fallido'
                         ,'uploadText'     : '' 
                         ,'maxFileSize'    : 1048576})
                
      guiWidgetBase.__init__(self, config=config, items=items)      
      self.validOptions.update({ 'name'            : is_notBlank
                                ,'buttonsAt'       : validate.is_string
                                ,'url'             : validate.is_string
                                ,'path'            : validate.is_string
                                ,'addText'         : validate.is_string
                                ,'clickRemoveText' : validate.is_string
                                ,'fileQueuedText'  : validate.is_string
                                ,'fileFailedText'  : validate.is_string
                                ,'uploadText'      : validate.is_string
                                ,'maxFileSize'     : validate.is_integer
                                ,'listeners'       : validate.is_dict
                                ,'_register'       : validate.is_boolean
                                ,'id'              : is_notBlank,                                
                                })
      self.requiredOptions.extend(['id', 'name'])   
         
# carga el tipo de input de acuerdo a definicion
def inputType(data, editable=True, sufix=''):
   w = {}
   
   if    data['xtype'] == 'textfield':
      w = fieldText(data, items=[])
   
   if    data['xtype'] == 'textarea':
      w = fieldTextArea(data, items=[])

   if    data['xtype'] == 'datefield':      
      w = fieldDateTime(data, items=[])   
   
   elif  data['xtype'] == 'numberfield':
      w = fieldNumber(data, items=[])
   
   elif  data['xtype'] == 'comboboxex':      
      w =  fieldComboRelation(data, items=[])
      
   elif  data['xtype'] == 'comboboxlov':
      w =  fieldComboLov(data, items=[])    
   
   elif  data['xtype'] == 'radiogroup':
      w =  fieldRadioGroup(data, items=[])      

   elif  data['xtype'] == 'checkboxgroup':
      w =  fieldCheckBoxGroup(data, items=[])      
   
   elif  data['xtype'] == 'fieldset':
      w = fieldRadioSet(data, items=[])

   if not editable:      
      w.options['disabled'] = True

   if w.options.has_key('_register') and \
      w.options.has_key('_id')       and \
      w.options['_register'] == True and sufix != '':      
      w.options['_id'] =  w.options['_id'] + sufix      
      
   return w   
 
# forma window widget
class formWindowWidget(windowWidget):    
   def __init__(self, window={}, form={}, items=[], buttons=[], Upload=False):
      window['_xtype'] = 'formWindow'
      windowWidget.__init__(self, config=window, items=items)
      self.form = formWidget(config=form, items=[], buttons=buttons, Upload=Upload)
      self.appendItem(self.form)

   def addField(self, field, position=-1):
      # validar tipo de field!!!!!
      self.form.addField(field, position)

   def appendField(self, field):
      # validar tipo de field!!!!!
      self.form.appendField(field)

   def appendButton(self, button):
      self.form.appendButton(button)              

# boton widget
class botonWidget(guiWidgetBase):    
   def __init__(self, config={}, items=[]):
      updateDic(config, {'xtype'         : 'button',
                         'text'          : '',
                         'iconCls'       : '',
                         'icon'          : '',
                         'fieldLabel'    : '',
                         '_field'        : '.',
                         '_source'       : '.',
                         'handler'       : '',
                         #'pressed'       : False,
                         'enableToggle'  : False,
                         '_register'     : False})
      guiWidgetBase.__init__(self, config=config, items=items)
      self.validOptions.update({'id'          : is_notBlank,
                                'html'        : validate.is_string,
                                'text'        : validate.is_string,
                                'iconCls'     : validate.is_string,
                                'icon'        : validate.is_string,
                                'handler'     : validate.is_string,
                                'fieldLabel'  : validate.is_string, # botonform
                                'autoHeight'  : validate.is_boolean,
                                'autoWidth'   : validate.is_boolean,
                                'pressed'     : validate.is_boolean,
                                'enableToggle': validate.is_boolean,
                                'width'       : validate.is_integer,
                                'height'      : validate.is_integer,
                                'children'    : validate.is_list
                                })
      self.requiredOptions.extend(['id', 'text'])
      

# simple panel
class panelWidget(guiWidgetBase):    
   def __init__(self, config={}, items=[]):
      updateDic(config, {'xtype'      : 'panel',
                         'title'      : '',
                         'autoHeight' : True,
                         'frame'      : False,
                         'collapsible': False,
                         'autoWidth'  : True,
                         'autoScroll' : True,
                         'autoShow'   : True,
                         #'bbar'       : [],    
                         '_field'     : '.',
                         '_source'    : '.',
                         '_register'  : False})
      guiWidgetBase.__init__(self, config=config, items=items)
      self.validOptions.update({'id'          : is_notBlank,
                                'html'        : validate.is_string,
                                'title'       : validate.is_string,
                                'autoHeight'  : validate.is_boolean,
                                'frame'       : validate.is_boolean,
                                'collapsible' : validate.is_boolean,
                                'autoWidth'   : validate.is_boolean,
                                'autoShow'    : validate.is_boolean,
                                'width'       : validate.is_integer,
                                'height'      : validate.is_integer,
                                'layout'      : validate.is_string,
                                'autoScroll'  : validate.is_boolean,
                                'bbar'        : validate.is_list
                                })
      self.requiredOptions.extend(['id', 'title'])

# acordeon panel
class accordionWidget(panelWidget):    
   def __init__(self, config={}, items=[]):
      updateDic(config, {'layout'    : 'accordion',
                         'collapsed' : False})
      panelWidget.__init__(self, config=config, items=items)
      self.validOptions.update({'id'          : is_notBlank,
                                'html'        : validate.is_string,
                                'layout'      : validate.is_string,
                                'collapsed'   : validate.is_boolean,
                                'children'    : validate.is_list
                                })

# widget tree
class treeWidget(guiWidgetBase):    
   def __init__(self, config={}, items=[]):
      updateDic(config, {'xtype'          : 'treepanel',
                         'useArrows'      : True,
                         'autoScroll'     : True,
                         'animate'        : True,
                         'enableDD'       : False,
                         'containerScroll': False,
                         'rootVisible'    : False,
                         'autoHeight'     : True,
                         'allowDomMove'   : False,
                         'frame'          : True,
                         'root'           : {},
                         '_field'         : '.',
                         '_source'        : '.',
                         '_register'      : True})
      guiWidgetBase.__init__(self, config=config, items=items)
      self.validOptions.update({'id'             : is_notBlank,
                                'title'          : validate.is_string,
                                'layout'         : is_notBlank,                                
                                'width'          : validate.is_integer,
                                'height'         : validate.is_integer,
                                'pageX'          : validate.is_integer,
                                'pageY'          : validate.is_integer,
                                'x'              : validate.is_integer,
                                'y'              : validate.is_integer,
                                'autoHeight'     : validate.is_boolean,
                                'autoWidth'      : validate.is_boolean,
                                'useArrows'      : validate.is_boolean,
                                'autoScroll'     : validate.is_boolean,
                                'animate'        : validate.is_boolean,
                                'enableDD'       : validate.is_boolean,
                                'frame'          : validate.is_boolean,
                                'containerScroll': validate.is_boolean,
                                'rootVisible'    : validate.is_boolean,
                                'root'           : validate.is_dict
                                })
      self.requiredOptions.extend(['id', 'title'])

# widget tree node
class treeNode(guiWidgetBase):    
   def __init__(self, config={}, items=[]):
      updateDic(config, {'xtype'          : 'treenode',
                         'allowChildren'  : True,
                         'expandable'     : True,
                         'leaf'           : False,
                         'text'           : "Nodo",
                         'children'       : [],
                         '_field'         : '.',
                         '_source'        : '.',
                         '_register'      : False})
      guiWidgetBase.__init__(self, config=config, items=items)
      self.validOptions.update({'id'             : is_notBlank,
                                'allowChildren'  : validate.is_boolean,
                                'checked'        : validate.is_boolean,
                                'expandable'     : validate.is_boolean,
                                'leaf'           : validate.is_boolean,
                                'text'           : validate.is_string,
                                'children'       : validate.is_list
                                })
      self.requiredOptions.extend(['id', 'text'])

###############################################
######## RUTINAS DE GRID DE NAVEGACION ########
###############################################
# widget BufferedJsonReader
class bufferedJsonReader(guiWidgetBase):    
   def __init__(self, config={}, items=[]):
      updateDic(config, {'xtype'           : 'bufferedjsonreader',
                         'root'            : 'data.items',
                         'versionProperty' : 'data.version',
                         'totalProperty'   : 'data.total_count',
                         '_field'          : '.',
                         '_source'         : '.',
                         '_register'       : False})
      guiWidgetBase.__init__(self, config=config, items=items)
      self.validOptions.update({'id'              : is_notBlank,
                                'root'            : validate.is_string,
                                'versionProperty' : validate.is_string,
                                'totalProperty'   : validate.is_string,
                                'fields'          : validate.is_list
                                })
      self.requiredOptions.extend(['id'])

# widget BufferedStore
class bufferedStore(guiWidgetBase):    
   def __init__(self, config={}, items=[]):
      updateDic(config, {'xtype'        : 'bufferedstore',
                         'autoLoad'     : True,
                         'bufferSize'   : 300,                         
                         '_field'       : '.',
                         '_source'      : '.',
                         '_register'    : False})
      guiWidgetBase.__init__(self, config=config, items=items)
      self.validOptions.update({'id'            : is_notBlank,
                                'autoLoad'      : validate.is_boolean,
                                'bufferSize'    : validate.is_integer,
                                'reader'        : validate.is_dict,
                                'sortInfo'      : validate.is_dict,
                                'columns'       : validate.is_list,
                                'source'        : validate.is_string,
                                'url'           : validate.is_string                                
                                })
      self.requiredOptions.extend([])

# widget bufferedGridView
class bufferedGridView(guiWidgetBase):    
   def __init__(self, config={}, items=[]):
      updateDic(config, {'xtype'        : 'bufferedgridview',
                         'nearLimit'    : 100,
                         'loadMask'     : {'msg': 'Por favor espere...'},
                         '_field'       : '.',
                         '_source'      : '.',
                         '_register'    : False})
      guiWidgetBase.__init__(self, config=config, items=items)
      self.validOptions.update({'id'        : is_notBlank,
                                'nearLimit' : validate.is_integer,
                                'loadMask'  : validate.is_dict
                                })
      self.requiredOptions.extend([])

# widget bufferedGridToolbar
class bufferedGridToolbar(guiWidgetBase):    
   def __init__(self, config={}, items=[]):
      updateDic(config, {'xtype'        : 'bufferedgridtoolbar',
                         'displayInfo'  : True,
                         '_field'       : '.',
                         '_source'      : '.',
                         '_register'    : False})
      guiWidgetBase.__init__(self, config=config, items=items)
      self.validOptions.update({'id'           : is_notBlank,
                                'view'         : validate.is_dict,
                                'displayInfo'  : validate.is_boolean
                                })
      self.requiredOptions.extend([])

# widget ColumnModel
class columnModel(guiWidgetBase):    
   def __init__(self, config={}, items=[]):
      updateDic(config, {'xtype'        : 'columnmodel',
                         '_field'       : '.',
                         '_source'      : '.',
                         '_register'    : False})
      guiWidgetBase.__init__(self, config=config, items=items)
      self.validOptions.update({'id'      : is_notBlank,
                                'columns' : validate.is_list
                                })
      self.requiredOptions.extend([])

# widget Grid Panel
class gridPanel(guiWidgetBase):    
   def __init__(self, config={}, items=[]):
      updateDic(config, {'xtype'          : 'grid',
                         'enableDragDrop' : False,
                         'loadMask'       : {'msg': 'Cargando...'},
                         'title'          : '.',
                         #'width'          : 500,
                         #'height'         : 300,
                         '_field'         : '.',
                         '_source'        : '.',
                         '_register'      : True})
      guiWidgetBase.__init__(self, config=config, items=items)
      self.validOptions.update({'id'             : is_notBlank,
                                'ds'             : validate.is_dict,
                                'enableDragDrop' : validate.is_boolean,
                                'cm'             : validate.is_dict,
                                'sm'             : validate.is_string,
                                'loadMask'       : validate.is_dict,
                                'view'           : validate.is_dict,
                                'title'          : validate.is_string,
                                'bbar'           : validate.is_dict,
                                'tbar'           : validate.is_list,
                                'width'          : validate.is_integer,
                                'height'         : validate.is_integer,
                                'buttons'        : validate.is_list
                                })
      self.requiredOptions.extend(['id'])

# widget Grid Panel
class dataView(guiWidgetBase):           
   def __init__(self, config={}, items=[]):
      updateDic(config, {'xtype'          : 'dataview',
                         'enableDragDrop' : False,
                         'loadingText'    : 'Cargando...',
                         'title'          : '.',
                         'autoHeight'     : True,
                         'multiSelect'    : True,
                         'width'          : 650,
                         'height'         : 200,
                         'overClass'      : 'x-view-over',
                         'emptyText'      : 'Sin datos',
                         'itemSelector'   : 'div.thumb-wrap',
                         'tpl'            : '',
                         'title'          : '.',
                         '_field'         : '.',
                         '_source'        : '.',
                         '_register'      : True})
      
      guiWidgetBase.__init__(self, config=config, items=items)
      self.validOptions.update({'id'             : is_notBlank,
                                'title'          : validate.is_string,
                                'store'          : validate.is_dict,
                                'tpl'            : validate.is_string,
                                'enableDragDrop' : validate.is_boolean,
                                'loadingText'    : validate.is_string,
                                'autoHeight'     : validate.is_boolean,
                                'multiSelect'    : validate.is_boolean,
                                'overClass'      : validate.is_string,
                                'emptyText'      : validate.is_string,
                                'itemSelector'   : validate.is_string,
                                'width'          : validate.is_integer,
                                'height'         : validate.is_integer,
                                })
      self.requiredOptions.extend(['id'])

"""
class fieldComboRelation(fieldComboBox):
   def __init__(self, config={}, items=[]):
      config['xtype'] = 'combo'
      updateDic(config, {'mode'         : 'remote',
                         'triggerAction': 'all',
                         'queryDelay'   : 500,
                         'query'        : '',
                         'store'        : self.localStore(),
                         })
      fieldComboBox.__init__(self, config=config, items=items)
      self.validOptions.update({'mode'         : validate.is_string,
                                'triggerAction': validate.is_string,
                                'queryDelay'   : validate.is_integer,
                                'query'        : validate.is_string,
                                'store'        : validate.is_dict
                                })
        
   def localStore(self):
      config = {} 
      config['xtype'] = 'datastore'
       
      config['proxy'] = {}
      config['proxy']['xtype']       = 'remoteproxy'
      config['proxy']['url']         = 'urlremoteproxy'
      config['proxy']['paramModify'] = [{'param': 'query', 'from': 'local', 'to': 'remote', 'operation': 'move'}]
      config['proxy']['matchColumn'] = 'name'
      config['proxy']['reloadOnNewQuery'] = True
       
      config['reader'] = {}
      config['reader']['xtype']  = 'arrayreader'
      config['reader']['fields'] = [{'name':'name'}]
       
      return config
"""