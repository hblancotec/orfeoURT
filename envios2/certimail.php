<?php
require "index.php";

require_once "../config.php";
require "adodb/adodb.inc.php";
$dsn = $driver . "://$usuario:$contrasena@$servidor/$servicio";
$conn = NewADOConnection($dsn);
if ($conn) {
    $conn->SetFetchMode(ADODB_FETCH_ASSOC);
} else
    die("Error al conectar BD");

$str = $conn->Concat( "cast(d.depe_codi as varchar) ", "' '", "d.depe_nomb");
$rs = $conn->Execute("select $str, depe_codi from dependencia d order by depe_codi");
$cmbDependencias = $rs->GetMenu2('cmbDepe', $_SESSION['dependencia'], false, false, 0, "id='cmbDepe' class='select'");
$rs = $conn->Execute("select SGD_FENV_DESCRIP, SGD_FENV_CODIGO from SGD_FENV_FRMENVIO where SGD_FENV_CODIGO = 106 order by SGD_FENV_DESCRIP");
$cmbMenvios = $rs->GetMenu2('cmbFenvio', ":&lt;seleccione&gt;", false, false, 0, "id='cmbFenvio' class='select'");
?>
<script type="text/javascript">
    jQuery(document).ready(function(){

        var dlg=$('#dialog').dialog({
            title: 'Enviar radicados seleccionados',
            resizable: true,
            autoOpen:false,
            modal: true,
            hide: 'fade',
            width: 370,
            height: 200,
            close: function( event, ui ) {$('#msjErr').html("");},
            position: {
                my: "center",
                at: "center"
            }
        });
		     
        jQuery("#list").jqGrid({ 
            url:'listar.php?q=2&dep='+$('#cmbDepe option:selected').val(), 
            datatype: "json", 
            colNames:[	'Radicado', 'Copia', 'Destinatario', 'Direccion', 'Cod. Postal', 'Correo E.', 'Municipio', 'Departamento', 'Pais', 'Ruta', 'Dircodigo' ], 
            colModel:[	{name:'radi_nume_salida', index:'radi_nume_salida', width:110, searchtype:"integer", formatter:returnHyperLink, searchrules:{"required":true, "number":true}, frozen: true },
                {name:'copia', index:'copia', width:35, frozen: true},
                {name:'sgd_dir_nomremdes', index:'sgd_dir_nomremdes', width:200}, 
                {name:'sgd_dir_direccion', index:'sgd_dir_direccion', width:200}, 
                {name:'sgd_dir_codpostal', index:'sgd_dir_codpostal', width:70, searchtype:"integer", align:"right"},
                {name:'sgd_dir_mail', index:'sgd_dir_mail', width:120, editable:true, editoptions: { size: 35}, editrules:{custom:true, custom_func:valemail} }, 
                {name:'muni_nomb', index:'muni_nomb', width:100}, 
                {name:'dpto_nomb', index:'dpto_nomb', width:100, align:"right"}, 
                {name:'nombre_pais', index:'nombre_pais', width:90},
                {name:'ruta', index:'ruta', hidden:true},
                {name:'dircodigo', index:'dircodigo', hidden:true},
            ], 
            pager: "#pager",
            rowNum: 25,
            rowList: [25, 100, 250, 500, 1000, 1500],
            id: "idr",
            sortname: "radi_nume_salida",
            sortorder: "asc",
            viewrecords: true,
            gridview: true,
            autoencode: true,
            multiselect: true,
            caption: "", 
            repeatitems: true,
            height: 'auto',
            sortable: true,
            showError: true,
            width: '100%',
            //ondblClickRow: function(rowid,iRow,iCol,e){ alert('double clicked\nROWID:'+rowid+'\nIROW:'+iRow+'\nICOL:'+iCol); },
            editurl: "actDatosEnvio.php?<?= session_name() . '=' . session_id() ?>"
        });
        jQuery("#list").jqGrid(	'navGrid',
        '#pager',
        {add:false, edit:true, del:false},
        {}, // edit parameters 
        {}, // add parameters 
        {reloadAfterSubmit:false}, //delete parameters
        {multipleSearch:true}
    );
        $("#cmbDepe").change(function(){jQuery("#list").jqGrid('setGridParam',{url:"listar.php?q=2&dep="+$('#cmbDepe option:selected').val()}).trigger("reloadGrid");});
        $("#btnEnviar").click(function(){
            var s = jQuery("#list").jqGrid('getGridParam','selarrrow');
            if (s.length == 0)
            {
                alert('No hay elementos seleccionados');
            } else {
                dlg.dialog('open');
            }
        });
				
        function returnHyperLink(cellValue, options, rowdata, action)
        {
            return "<a href='../bodega/" + rowdata[9] + "' >" + rowdata[0]  + "</a>";
        };
        
        function valemail(value, colname) {
            var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if (!filter.test(value))
                return [false,"Digite un correo electr\xf3nico v\xe1lido"];
            else 
                return [true,""];
        }
				
        $("#btnValidar").click(	function cambiarFondo() {
            var s = jQuery("#list").jqGrid('getGridParam','selarrrow');
            var msjError = "";
            $.each( s, function( key, value ) {
                var reg = jQuery("#list").jqGrid('getRowData',s[key]);   //get the selected row
                $.ajax({
                    url: 'enviarRadicado.php',
                    type: 'post',
                    dataType: 'json',
                    async: false,
                    data: "tipEnvio="+$('#cmbFenvio option:selected').val()+"&rad="+value+"&observa="+$('#txtObs').val()+"&destino="+reg.muni_nomb+"&nombre="+reg.sgd_dir_nomremdes+
                        "&dircod="+reg.dircodigo+"<?= '&' . session_name() . '=' . session_id() ?>",
                    success: function (data) {
                        if ( data && data.success ) {
                            $('#list').jqGrid('delRowData', value);
                        } else {
                            $("#" + value).css("background", "#F6CED8");
                            msjError = msjError + data.errors.reason + "\n";
                            //console.log(data.errors.reason);
                        }
                    }
                });
            });
            if (msjError.length>0) {
                $("#msjErr").html(msjError);
            } else { dlg.dialog("close"); }
        });
    });
			
</script>
<table width="100%">
    <tr>
        <td width="33%" height="20" class="titulo1">LISTADO DE: </td>
        <td width="33%" class="titulo1">USUARIO </td>
        <td width="34%" class="titulo1">DEPENDENCIA</td>
    </tr>
    <tr width="33%">
        <td height="20" class="info">Radicados Para Env&iacute;o</td>
        <td class="info"><?= $_SESSION['depe_nomb'] ?></td>
        <td class="info"><?= $cmbDependencias ?></td>
    </tr>
</table>
<table id="list" width="100%"><tr><td></td></tr></table> 
<div id="pager"></div>
<div id="dialog" title="Confirmaci&oacute;n de Env&iacute;o" style="font-size: 75%;font-family: verdana">
    Los radicados seleccionados ser&aacute;n enviados a trav&eacute;s del medio <?= $cmbMenvios ?>.<br/>
    <b>Observaci&oacute;n:</b><br />
    <input type="text" name="txtObs" id="txtObs" maxlength="100" size="45"/>
    <div id="msjErr"></div>
    <p align="center"><input type="button" name="btnValidar" id="btnValidar" value="Enviar" class="botones_largo"></p>
</div>
<p align="center"><input type="button" name="btnEnviar" id="btnEnviar" value="Generar Registro de Env&iacute;o" class="botones_largo"></p>
</body>
</html>