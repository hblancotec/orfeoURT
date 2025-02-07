// Version: 11. August 2008

// AutoTableFormLayout
// Based on http://extjs.com/forum/showthread.php?t=39342 by mbajema and Animal

Ext.namespace('Ext.ux.layout');

// Make the fieldTpl available to ALL layouts
Ext.override(Ext.layout.ContainerLayout, {
    fieldTpl: (function() {
        var t = new Ext.Template(
            '<div class="x-form-item {5}" tabIndex="-1">',
                '<label for="{0}" style="{2}" class="x-form-item-label">{1}{4}</label>',
                '<div class="x-form-element" id="x-form-el-{0}" style="{3}">',
                '</div><div class="{6}"></div>',
            '</div>'
        );
        t.disableFormats = true;
        return t.compile();
    })()
});

Ext.ux.layout.AutoTableFormLayout = Ext.extend(Ext.layout.TableLayout, 
{
	labelSeparator: ':',
	
    setContainer: function(ct) 
    {
        // Creating all subitems, figure out how many columns we need
		this.columns = 1;
        var aCols = new Array();
        for(var i = 0, len = ct.items.length; i < len; i++) 
        {	var mainItem = ct.items.itemAt(i);
        	if (mainItem.fieldLabel == "") mainItem.labelSeparator = "";
        	mainItem.labelWidth = mainItem.labelWidth || ct.labelWidth || 100;
        	mainItem.colspan = mainItem.colspan || 1;
        	
        	var cols = mainItem.colspan;
        	if (mainItem.items != undefined)
       		{	// All subitems of this row
       			for(var j = 0, lensubItems = mainItem.items.length; j < lensubItems; j++) 	
            	{	var subItem = mainItem.items[j];
            		Ext.applyIf(subItem, ct.defaults);
            		subItem = Ext.ComponentMgr.create(subItem, subItem.xtype || ct.defaultType || "textfield");
            		if (subItem.fieldLabel == "") subItem.labelSeparator = "";
            		
            		subItem.labelWidth = subItem.labelWidth || ct.subLabelWidth || 0;
            		mainItem.items[j] = subItem;
           	 		subItem.colspan = subItem.colspan || 1;
           	 		cols += subItem.colspan;
           	 		ct.getForm().add(subItem);
            	}
           	 	this.columns = Math.max(this.columns, cols);
           	 }
        	aCols[i] = cols;
        }
       
        // Set colspan
        for(var i = 0, len = ct.items.length; i < len; i++) 
        {	var mainItem = ct.items.itemAt(i);
        	mainItem.colspan += this.columns-aCols[i];
        }
        
       Ext.layout.FormLayout.prototype.setContainer.apply(this, arguments);
       
       this.currentRow = 0;
       this.currentColumn = 0;
       this.cells = [];
    },

    renderItem : function(c, position, target) 
    {
        if (c && !c.rendered)
		{			
        		
			// Render mainItem
            this.doRender(c, 0, Ext.get(this.getNextCell(c)), false);
         		
         	// Render subitems
         	for (var i = 0; i < this.columns-1; i++) 
         	{	if (c.items != undefined && i < c.items.length)
            	{	// Subitem available: Render it
            		var subItem = c.items[i];
            		this.doRender(subItem, 0, Ext.get(this.getNextCell(subItem)), true);
            	}
            }

         	// Hide row?
         	if (c.hideRow) this.showRow(this.currentRow, false);
        
        }
    },
    
    doRender : function (c, position, target, isChild)
    {	// We need this in order to be able to set the labelWidth for mainitems and subitems differently
    	if(typeof c.labelWidth == 'number')
        {	var pad = 5; // (typeof c.ownerCt.labelPad == 'number' ? c.ownerCt.labelPad : 5); // How to get the container here?
    		this.labelStyle = "width:"+c.labelWidth+"px;";
    		if (isChild) this.labelStyle += "width:"+(c.labelWidth-pad)+"px; padding-left:"+pad+"px;";
            this.elementStyle = "padding-left:"+(c.labelWidth+pad)+'px;';
    	}
    	
    	// Call Render
    	Ext.layout.FormLayout.prototype.renderItem.call(this, c, 0, target);
    },
    
    showRow : function (index, show)
    {	var row = this.getRow(index);
		if (show)
				row.style.display = "";
		else	row.style.display = "none";
    }
    
});

Ext.Container.LAYOUTS['autotableform'] = Ext.ux.layout.AutoTableFormLayout;

// ButtonField 
// based on http://extjs.com/forum/showthread.php?t=6099&page=2 by jgarcia@tdg-i.com

Ext.namespace('Ext.ux.form');

Ext.ux.form.ButtonField = Ext.extend(Ext.form.Field,  
{
	defaultAutoCreate : 
	{ 
		tag: 'div' 
	},
	
	value : '',
	
	onRender: function (ct, position) 
	{
            if(!this.el)
            {
                var cfg = this.getAutoCreate();
                if(!cfg.name)
                {
                    cfg.name = this.name || this.id;
                }
                if(this.inputType)
                {
                    cfg.type = this.inputType;   
				}
                this.el = ct.createChild(cfg, position);
            }

		this.button = new Ext.Button(
		{
			renderTo : this.el,
			text     : this.text,
			iconCls  : this.iconCls || null,
			handler  : this.handler || Ext.emptyFn,
			scope    : this.scope   || this
		})
	},
	getValue : function()
	{	return this.button.text;
	},
	setValue : function(value)
	{	this.button.text = value;
		this.value = value;
	}
});

Ext.ComponentMgr.registerType("formBtn", Ext.ux.form.ButtonField );

// SimpleHtml 
// based on Ext.ux.form.ButtonField

Ext.ux.form.SimpleHtml = Ext.extend(Ext.form.Field,  
{
	defaultAutoCreate : 
	{ 
		tag: 'div' 
	},
	
	value : '',
	
	onRender: function (ct, position) 
	{
            if(!this.el)
            {
                var cfg = this.getAutoCreate();
                if(!cfg.name)
                {
                    cfg.name = this.name || this.id;
                }
                if(this.inputType)
                {
                    cfg.type = this.inputType;   
				}
                this.el = ct.createChild(cfg, position);
            }
           
		this.component = new Ext.Component({
			renderTo : this.el,
			autoEl	 : { html: this.value },
			scope    : this.scope || this,
			style    : "padding-top:2px;"+(this.bodyStyle || "") // Correct the aligning with the label
		})
		
		this.hiddenName = this.name; // Make findField working
			
	},
	getValue : function()
	{	return this.component.autoEl.html;
	},
	setValue : function(value)
	{	this.component.getEl().dom.innerHTML = value;
		this.value = value;
	}
});

Ext.ComponentMgr.registerType("formHtml", Ext.ux.form.SimpleHtml );