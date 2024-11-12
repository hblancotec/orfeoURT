(function(){
 
  Ext.namespace("com.succinctllc.form");
  var NS = com.succinctllc.form;
  NS.ComboBox = function(cfg){
    if(!cfg) cfg = {};
    if(!cfg.store && cfg.url){
      cfg.store = new Ext.data.Store({
        baseParams:cfg.baseParams || {},
        url: cfg.url,
        reader : new Ext.data.JsonReader({}, [cfg.valueField, cfg.displayField || cfg.valueField])
      });
    } else {
      if (!cfg.store || Ext.type(cfg.store) == 'array' || cfg.store instanceof Ext.data.SimpleStore) {
        cfg.mode = "local";
      }
      else {
        cfg.mode = "remote";
      }
    }
 
    if(cfg.transform) {
      this.clearValueOnRender = !Ext.fly(cfg.transform).first("[selected=true]");
    }
 
    /*
     * If we have a valueField this will make
     * form.getValues return the correct value
     */
   if(cfg.valueField) {
       var extraCfg = {
         hiddenName : cfg.name,
         hiddenId : cfg.name+"Id"
       };
   } else {
     var extraCfg = {};
   }
 
    NS.ComboBox.superclass.constructor.call(this, Ext.apply(extraCfg, {
      minListWidth : cfg.width
    },cfg));
  };
 
  Ext.extend(NS.ComboBox, Ext.form.ComboBox, {
    editable:     false,
    triggerAction: 'all',
    autoLoad : true,
    forceReload:false,
    forceSelection:true,
    clearValueOnRender:false,
 
    initComponent : function(){
		  //alert("initComponent:");
      if (this.clearValueOnRender) {
        this.on("render", function(){
          this.clearValue();
        }, this);
      }
      /*
       * If width is set to 'auto' and minListWidth is not set then we need
       * to set a minListWidth so the list is guranteed to at least be the
       * same size as the combo box
       */
      if (((!this.width || this.width == 'auto') && !this.minListWidth)) {
        this.on("render", function(){
          this.minListWidth = this.wrap.getWidth();
        }, this);
      }
 
      if (this.mode == "remote") {
        this.store.on('load', this.assureValueEntry, this);
        if (this.autoLoad) {
          this.on("render", function(){
            if (this.store.getCount() == 0) {
              if(this.triggerAction == 'all') {
                        this.doQuery(this.allQuery, true);
                    } else {
                        this.doQuery(this.getRawValue());
                    }
            }
          }, this);
        }
      } else {
        this.assureValueEntry(this.store);
      }
 
      NS.ComboBox.superclass.initComponent.apply(this, arguments);
    },
 
    assureValueEntry: function(){		
		  //alert("assureValueEntry:" + this.value);  
      if(this.forceSelection)
        this.setValue(this.value);
    },
 
    setValue : function(v){
		  //alert("set:" + v);
          var text = v;
      if(this.valueField){
              var r = this.findRecord(this.valueField, v);
              if(r){
                  text = r.data[this.displayField];
              }else if(this.valueNotFoundText !== undefined){
                  text = this.valueNotFoundText;
              }
          }
          this.lastSelectionText = text;
          if(this.hiddenField){ 
					              this.hiddenField.value = v;
          }
          Ext.form.ComboBox.superclass.setValue.call(this, text);
          this.value = v;
      },
 
    /*
     * If you load via this method then we assume we don't need to run doQuery again.
     */
    load : function(options){
		  //alert("load:" + options);
      this.store.load(options);
      var q = (this.triggerAction == 'all')?this.allQuery:this.getRawValue();
      if(q === undefined || q === null)
              q = '';
      this.lastQuery = q;
    },
 
    doQuery: function(){
		  //alert("doQuery:");
      if (this.forceReload) {
        this.store.reload({
          callback: NS.ComboBox.superclass.doQuery.createDelegate(this, arguments, false)
        });
      } else {
        NS.ComboBox.superclass.doQuery.apply(this, arguments);
      }
    },
	
	initList : function(){
	      //alert("list:" + this.list);
        if(!this.list){
            var cls = 'x-combo-list';

            this.list = new Ext.Layer({
                shadow: this.shadow, cls: [cls, this.listClass].join(' '), constrain:false
            });

            var lw = this.listWidth || Math.max(this.wrap.getWidth(), this.minListWidth);
            this.list.setWidth(lw);
            this.list.swallowEvent('mousewheel');
            this.assetHeight = 0;

            if(this.title){
                this.header = this.list.createChild({cls:cls+'-hd', html: this.title});
                this.assetHeight += this.header.getHeight();
            }

						// boton de reload 
						if(this.reload){
                if(!this.header){
                    this.header = this.list.createChild({cls:cls+'-hd',html:' '});
                }

                this.refreshTool = Ext.DomHelper.insertFirst(this.header,'<div class="x-tool x-tool-refresh"> </div>',true);

                this.refreshTool.addClassOnOver('x-tool-refresh-over');
                this.refreshTool.dom.qtip = 'Reload list';
                this.refreshTool.on('click',function(){
                    this.refreshTool.removeClass('x-tool-refresh-over');
                    this.store.load();
                },this);

                this.assetHeight += this.header.getHeight();

            }
            // boton de reload						
						
            this.innerList = this.list.createChild({cls:cls+'-inner'});
            this.innerList.on('mouseover', this.onViewOver, this);
            this.innerList.on('mousemove', this.onViewMove, this);
            this.innerList.setWidth(lw - this.list.getFrameWidth('lr'));

            if(this.pageSize){
                this.footer = this.list.createChild({cls:cls+'-ft'});
                this.pageTb = new Ext.PagingToolbar({
                    store:this.store,
                    pageSize: this.pageSize,
                    renderTo:this.footer
                });
                this.assetHeight += this.footer.getHeight();
            }

            if(!this.tpl){
                /**
                * @cfg {String/Ext.XTemplate} tpl The template string, or {@link Ext.XTemplate}
                * instance to use to display each item in the dropdown list. Use
                * this to create custom UI layouts for items in the list.
                * <p>
                * If you wish to preserve the default visual look of list items, add the CSS
                * class name <pre>x-combo-list-item</pre> to the template's container element.
                * <p>
                * <b>The template must contain one or more substitution parameters using field
                * names from the Combo's</b> {@link #store Store}. An example of a custom template
                * would be adding an <pre>ext:qtip</pre> attribute which might display other fields
                * from the Store.
                * <p>
                * The dropdown list is displayed in a DataView. See {@link Ext.DataView} for details.
                */
                this.tpl = '<tpl for="."><div class="'+cls+'-item">{' + this.displayField + '}</div></tpl>';
                /**
                 * @cfg {String} itemSelector
                 * <b>This setting is required if a custom XTemplate has been specified in {@link #tpl}
                 * which assigns a class other than <pre>'x-combo-list-item'</pre> to dropdown list items</b>.
                 * A simple CSS selector (e.g. div.some-class or span:first-child) that will be
                 * used to determine what nodes the DataView which handles the dropdown display will
                 * be working with.
                 */
            }

            /**
            * The {@link Ext.DataView DataView} used to display the ComboBox's options.
            * @type Ext.DataView
            */
            this.view = new Ext.DataView({
                applyTo: this.innerList,
                tpl: this.tpl,
                singleSelect: true,
                selectedClass: this.selectedClass,
                itemSelector: this.itemSelector || '.' + cls + '-item'
            });

            this.view.on('click', this.onViewClick, this);

            this.bindStore(this.store, true);

            if(this.resizable){
                this.resizer = new Ext.Resizable(this.list,  {
                   pinned:true, handles:'se'
                });
                this.resizer.on('resize', function(r, w, h){
                    this.maxHeight = h-this.handleHeight-this.list.getFrameWidth('tb')-this.assetHeight;
                    this.listWidth = w;
                    this.innerList.setWidth(w - this.list.getFrameWidth('lr'));
                    this.restrictHeight();
                }, this);
                this[this.pageSize?'footer':'innerList'].setStyle('margin-bottom', this.handleHeight+'px');
            }
        }
    }

	  }
	);
 
}())