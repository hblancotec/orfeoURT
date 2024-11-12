
if(!fileAcepts){
  var  fileAcepts=['pdf', 'jpg', 'png', 'gif', 'doc', 'docx', 'xls', 'xlsx', 'bmp', 'tif', 'zip'];
}

Ext.define("Ext.ux.form.Multiupload", {
    extend: 'Ext.form.Panel',
    border: 0,
    alias: 'widget.multiupload',
    margins: '2 2 2 2',
    accept: fileAcepts,
    fileslist: [],
    //frame: false,
    frame: false,
    bodyStyle: 'background:transparent;',
    items: [
        {
            xtype: 'filefield',
            buttonOnly: true,
            listeners: {
                change: function (view, value, eOpts) {
                    //  alert(value);
                    var parent = this.up('form');
                    parent.onFileChange(view, value, eOpts);
                }
            },
            buttonConfig: {
                text: 'Adjuntar',
                icon: 'icons/attach.png'
            }
        }
    ],
    onFileChange: function (view, value, eOpts) {
        // debugger;
        var fileNameIndex = value.lastIndexOf("/") + 1;
        if (fileNameIndex == 0) {
            fileNameIndex = value.lastIndexOf("\\") + 1;
        }
        var filename = value.substr(fileNameIndex);

        var IsValid = this.fileValidiation(view, filename);
        if (!IsValid) {
            return;
        }

        
        this.fileslist.push(filename);
        var addedFilePanel = Ext.create('Ext.form.Panel', {
            frame: false,
            border: 0,
            padding: 2,
            margin: '0 10 0 0',
             bodyStyle: 'background:transparent;',
            layout: {
                type: 'hbox',
                align: 'middle'
            },
            items: [
                {
                    xtype: 'image',
                    src: 'icons/attach.png'
                }
                ,
                {
                    xtype: 'label',
                    padding: 5,
                    listeners: {
                        render: function (me, eOpts) {
                            me.setText(filename);
                        }
                    }
                },{
                    xtype: 'button',
                    text: null,
                    border: 0,
                    frame: false,
                     icon: 'icons/bullet_delete.png',
                    tooltip: 'Quitar',
                    listeners: {
                        click: function (me, e, eOpts) {
                            var currentform = me.up('form');
                            var mainform = currentform.up('form');
                            var lbl = currentform.down('label');
                            mainform.fileslist.pop(lbl.text);
                            mainform.remove(currentform);
                            currentform.destroy();
                            mainform.doLayout();
                        }
                    }
                }
                
            ]
        });

        var newUploadControl = Ext.create('Ext.form.FileUploadField', {
            buttonOnly: true,
            listeners: {
                change: function (view, value, eOpts) {

                    var parent = this.up('form');
                    parent.onFileChange(view, value, eOpts);
                }
            },
            buttonConfig: {
                text: 'Adjuntar',
                icon: 'icons/attach.png'
            }
        });
        view.hide();
        addedFilePanel.add(view);
        this.insert(0, newUploadControl);
        this.add(addedFilePanel);


        // alert(filename);
    },

    fileValidiation: function (me, filename, size) {

        var isValid = true;
        var indexofPeriod = me.getValue().lastIndexOf("."),
            uploadedExtension = me.getValue().substr(indexofPeriod + 1, me.getValue().length - indexofPeriod);
        if (!Ext.Array.contains(this.accept, uploadedExtension)) {
            isValid = false;
            // Add the tooltip below to
            // the red exclamation point on the form field
            me.setActiveError('Por favor adjunte archivos con una de la siguientes extensiones  permitidas:  (' + this.accept.join() + ').');
            // Let the user know why the field is red and blank!
            Ext.MessageBox.show({
                title: 'Erro en tipo de archivo',
                msg: 'Por favor adjunte archivos con una de la siguientes extensiones  permitidas:  (' + this.accept.join() + ').',
                buttons: Ext.Msg.OK,
                icon: Ext.Msg.ERROR
            });
            // Set the raw value to null so that the extjs form submit
            // isValid() method will stop submission.
            me.setRawValue(null);
            me.reset();
        }

        if (Ext.Array.contains(this.fileslist, filename)) {
            isValid = false;
            me.setActiveError('El archivo seleccionado ' + filename + ' ya fue adjuntado!');
            Ext.MessageBox.show({
                title: 'Error',
                msg: 'El archivo seleccionado ' + filename + ' ya fue adjuntado!',
                buttons: Ext.Msg.OK,
                icon: Ext.Msg.ERROR
            });
            // Set the raw value to null so that the extjs form submit
            // isValid() method will stop submission.
            me.setRawValue(null);
            me.reset();
        }


        return isValid;
    }
});