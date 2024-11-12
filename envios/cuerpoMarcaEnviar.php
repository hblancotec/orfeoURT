<?php
session_start();
$ruta_raiz = "..";
if (count($_SESSION) == 0) {
    die(include $ruta_raiz . "/sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}

if ($_SESSION['usua_perm_impresion'] == 0){
	die(include "../sinpermiso.php");
	exit;
}

$ruta_raiz = "..";
include_once "$ruta_raiz/_conf/constantes.php";
include_once ORFEOPATH . "class_control/usuario.php";
require_once ORFEOPATH . "include/db/ConnectionHandler.php";
include_once ORFEOPATH . "class_control/TipoDocumento.php";
include_once ORFEOPATH . "class_control/firmaRadicado.php";

$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

if (isset($_POST['EnviarFax']) && $_POST['EnviarFax'])
    include("$ruta_raiz/webServices/appClient/modulosCliente/envioFaxServer.php");

//$db->conn->debug = true;
//Se crea el objeto de analisis de firmas
$objFirma = new FirmaRadicado($db);

if (!$_SESSION['dependencia'])
    include "../rec_session.php";
$nombusuario = $_SESSION['usua_nomb'];
if (!$dep_sel)
    $dep_sel = $_SESSION['dependencia'];
$rs_modenv = $db->conn->cacheExecute(60, "select mrec_desc, mrec_codi from medio_recepcion order by mrec_desc");
$sqlFechaHoy = $db->conn->DBDate($fecha_hoy);
$sqlcorreo = "SELECT USUA_EMAIL as emailuno, USUA_EMAIL_1 as emaildos, USUA_EMAIL_2 as emailtres
		FROM USUARIO WHERE (USUA_LOGIN = '$krd')";

$resuCorreo = $db->conn->Execute($sqlcorreo);

$emailuno = $resuCorreo->fields["emailuno"];
$emaildos = $resuCorreo->fields["emaildos"];
$emailtres = $resuCorreo->fields["emailtres"];
//variable con la fecha formateada
$fechah = date("dmy") . "_" . time("h_m_s");
//variable con elementos de sesion
$encabezado = session_name() . "=" . session_id() . "&krd=$krd";
?>
<html>
    <head>
        <script>
            pedientesFirma="";
            function back() {
                history.go(-1);
            }

            function recargar(){
                window.location.reload();	
            }

            function editFirmantes(rad){
                nombreventana="EdiFirms";
                url="<?= $ruta_raiz ?>/firma/editarFirmates.php?radicado=" + rad +"&<?= "&" . session_name() . "=" . trim(session_id()) ?>";
                window.open(url,nombreventana,'height=480,width=1200,scrollbars=yes,resizable=yes');
                return; 
            }

            function markAll(){
                var nodoCheck = document.getElementsByTagName("input");
                var varCheck = document.getElementById("checkall").checked;
                for (i=0; i<nodoCheck.length; i++){
                    if (nodoCheck[i].type == "checkbox" && nodoCheck[i].name != "checkall" && nodoCheck[i].disabled == false) {
                        nodoCheck[i].checked = varCheck;
                        //nodoCheck[i].click();
                    }
                }
            }

            function solicitarFirma () {
                marcados = 0;
                radicados = "";
                radiMalos = "";
                markMalos = 0;
				debugger;
                for(i=0;i<document.formEnviar.elements.length;i++){
                    if(document.formEnviar.elements[i].checked==1)	{
                    	if (!isNaN(document.formEnviar.elements[i].value)) {
                        	if (document.formEnviar.elements[i].value.length == 15) {
                    			marcados++;
                    			radicados += (document.formEnviar.elements[i].value) + ",";
                        	}
                        }
                    }
                }
                
                if(marcados>=1)	{

					url = "<?= $ruta_raiz ?>/firma/seleccFirmantes.php";
					radicados = radicados.substring(0, radicados.length-1);
					var winName='MyWindow';
					var winURL=url;
					var windowoption='resizable=yes,height=300,width=800,location=0,menubar=0,scrollbars=1';
					var params = { 'usua_nomb' : '<?php echo $_SESSION['usua_nomb']; ?>','depe_nomb' :'<?php echo $_SESSION['depe_nomb']; ?>', 'krd' : '<?php echo $krd; ?>', '<?php echo session_name(); ?>' : '<?php echo session_id() ?>', 'radicados' : radicados};
					var form = document.createElement("form");
					form.setAttribute("method", "post");
					form.setAttribute("action", winURL);
					form.setAttribute("target",winName);
					for (var i in params) {
						if (params.hasOwnProperty(i)) {
                	      var input = document.createElement('input');
                	      input.type = 'hidden';
                	      input.name = i;
                	      input.value = params[i];
                	      form.appendChild(input);
                	    }
                	  }              
                	  document.body.appendChild(form);                       
                	  window.open('', winName,windowoption);
                	  form.target = winName;
                	  form.submit();                 
                	  document.body.removeChild(form); 
                	  	  
                }else{
                    alert("Debe seleccionar al menos un radicado");
                }	
            }

            function valPendsFirma (){
                for(i=0;i<document.formEnviar.elements.length;i++){
                    if(document.formEnviar.elements[i].checked==1)	{
                        if (pedientesFirma.indexOf(document.formEnviar.elements[i].value)!=-1){
                            alert ("No se puede enviar el radicado " + document.formEnviar.elements[i].value + " pues se encuentra pendiente de firma ");
                            return false;
                        }
				
                    }
                }
	
                return true;
            }

        </script>
        <link rel="stylesheet" href="../estilos/orfeo.css">
        <?php
        if (!$_GET['carpeta'])
            $carpeta = 0;

        if (!$estado_sal) {
            $estado_sal = 2;
        }

        if (!$_GET['estado_sal_max'])
            $estado_sal_max = 3;

        if ($estado_sal == 2) {
            $accion_sal = "Marcar Documentos Como Impresos";
            $nomcarpeta = "Documentos Para Impresion";

            $pagina_sig = "cuerpoMarcaEnviar.php";
            if ($_SESSION['usua_perm_impresion'] == 2) {
                $swBusqDep = "S";
            }
            $dependencia_busq1 = " and c.radi_depe_radi = $dep_sel ";
            $dependencia_busq2 = " and c.radi_depe_radi = $dep_sel";
        }

        //variable que indica la accion a ejecutar en el formulario
        $accion_sal = "Marcar Documentos como Impresos";
        //variable que indica la accion a ejecutar en el formulario
        $nomcarpeta = "Marcar Documentos como Impresos";
        //variable que indica la accion a ejecutar en el formulario email 
        $accion_sal_email = "Enviar Documentos E-mail";
        //variable que indica la accion a ejecutar en el formulario
        $nomcarpeta_email = "Enviar Documentos E-mail";
        $carpeta = "nada";
        $pagina_sig = "../envios/marcaEnviar.php";
        $pagina_sig_email = "../envios/marcaEnviar_Email.php";
        $pagina_actual = "../envios/cuerpoMarcaEnviar.php";
        $varBuscada = "radi_nume_salida";
        $swListar = "si";

        if ($orden_cambio == 1) {
            if (!$_GET['orderTipo']) {
                $orderTipo = " DESC";
            } else {
                $orderTipo = "";
            }
        }

        //var de formato para la tabla
        $tbbordes = "#CEDFC6";
        //var de formato para la tabla
        $tbfondo = "#FFFFCC";

        //le pone valor a la variable que maneja el criterio de ordenamiento inicial
        if (!$_GET['orno']) {
            $orno = 1;
            $ascdesc = $orderTipo;
        }
        $imagen = "flechadesc.gif";
        ?> 
        <script>
            <!-- Esta funcion esconde el combo de las dependencia e inforados Se activan cuando el menu envie una seoal de cambio.-->
 
            function window_onload() {
                form1.depsel.style.display = '';
                form1.enviara.style.display = '';
                form1.depsel8.style.display = 'none';
                form1.carpper.style.display = 'none';
                setVariables();
                setupDescriptions();
            }


<?php

function tohtml($strValue) {
    return htmlspecialchars($strValue);
}
?>
        </script>
        <script>
            var strSeleccion="";
            function cambioDependecia (dep){
                document.formDep.action="cuerpo_masiva.php?krd=<?= $krd ?>&dep_sel="+dep;
                //alert(document.formDep.action);
                document.formDep.submit();
            }

            function marcar()
            {
            	debugger;
                marcados = 0;

                for(i=0;i<document.formEnviar.elements.length;i++)
                {
                    if(document.formEnviar.elements[i].checked==1)
                    {
                        marcados++;
                    }
                }
                if(marcados>=1) {
                    if (valPendsFirma())
				
                    document.formEnviar.action="<?= $pagina_sig ?>?<?= session_name() . "=" . session_id() . "&krd=$krd&fechah=$fechah&dep_sel=$dep_sel&estado_sal=$estado_sal&usua_perm_impresion=$usua_perm_impresion&estado_sal_max=$estado_sal_max" ?>";				
                document.formEnviar.submit();
            } else {
                alert("Debe seleccionar un radicado");
            }
        }

        function marcar_Email()
        {
            marcados = 0;
 
            for(i=0;i<document.formEnviar.elements.length;i++)
            {
                if(document.formEnviar.elements[i].checked==1)
                {
                    marcados++;
                }
            }
            if(marcados>=1) {
                if (valPendsFirma())
                    document.formEnviar.action="<?= $pagina_sig_email ?>?<?= session_name() . "=" . session_id() . "&krd=$krd&fechah=$fechah&dep_sel=$dep_sel&estado_sal=$estado_sal&usua_perm_impresion=$usua_perm_impresion&estado_sal_max=$estado_sal_max" ?>";				
                document.formEnviar.submit();
            } else {
                alert("Debe seleccionar un radicado para enviar por E-mail o");
            }
        }

        function selecionEspecifica(obj,str){
            if(obj.checked){
                strSeleccion+=","+str;
            }
            else{
                strSeleccion = strSeleccion.toString().replace(","+str.toString(),""); 
            }
        }

        function enviaFax()
        {
            marcados = 0;
            var msg="";
            var band=true;
            if(document.getElementById('txtNFax').value==""){
            
                msg="Debe digitar un numero de Fax destino";
                band= false;
            }
            if(document.getElementById('txtNFax').value.length<7){
            
                msg +="\nDebe digitar un n\xFAmero de Fax v\xE1lido, al menos de 7 d\xEDgitos";
                band= false;
            }
	
            if(strSeleccion.toString().length<=0){
            
                msg +="\nDebe seleccionar un radicado para el env\xEDo de Fax";
                band= false;
            }
            if(band){
                document.formEnviar.hidRadsFax.value=strSeleccion;
                //document.formEnviar.action="<?= $pagina_sig ?>?<?= session_name() . "=" . session_id() . "&krd=$krd&fechah=$fechah&dep_sel=$dep_sel&estado_sal=$estado_sal&usua_perm_impresion=$usua_perm_impresion&estado_sal_max=$estado_sal_max" ?>";				
                document.formEnviar.submit();
            }
            else{
                alert(msg);
                return band;
            
            }
	
        }
        function verMedioEnvio(rad,tip)
        {
            var anchoPantalla = screen.availWidth;
            var altoPantalla  = screen.availHeight;
            window.open('<?= $ruta_raiz ?>/envios/cambiarMedioEnvio.php?krd=<?= $krd ?>&numRad='+rad+'&tip='+tip,'Cambio Medio Envio', 'top='+(altoPantalla/3)+',left='+(anchoPantalla/3)+', height='+(altoPantalla*0.20)+', width='+(anchoPantalla*0.37)+', scrollbars=yes,resizable=yes')
        }

        function regresar()
        {	
            window.location.reload();
        }
        
        function actModEnvio(cmb, rad){
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.open("POST", "actualizaModEnvioAjax.php", true);
            xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            xmlhttp.send("codigoe=" + cmb + "&codigod=" + rad);
        }
        </script>
        <style type="text/css">
            <!--
            .textoOpcion {  font-family: Arial, Helvetica, sans-serif; font-size: 8pt; color: #000000; text-decoration: underline}
            -->
        </style>
    </head>

    <body  topmargin="0" >
        <div id="object1" style="position:absolute; visibility:show; left:10px; top:-50px; width:80%; z-index:2" > 
            <p>Cuadro de Historico</p>
        </div>
        <?php
        $sqlFecha = $db->conn->SQLDate("d/m/Y", "r.SGD_RENV_FECH");
        $img1 = "";
        $img2 = "";
        $img3 = "";
        $img4 = "";
        $img5 = "";
        $img6 = "";
        $img7 = "";
        $img8 = "";
        $img9 = "";
        if ($ordcambio) {
            if ($ascdesc == "") {
                $ascdesc = "DESC";
                $imagen = "flechadesc.gif";
            } else {
                $ascdesc = "";
                $imagen = "flechaasc.gif";
            }
        } else
        if ($ascdesc == "DESC")
            $imagen = "flechadesc.gif";
        else
            $imagen = "flechaasc.gif";



        if ($orno == 1) {
            $order = " a.radi_nume_salida  $ascdesc";
            $img1 = "<img src='../iconos/$imagen' border=0 alt='$data'>";
        }
        if ($orno == 2) {
            $order = " 6  $ascdesc";
            $img2 = "<img src='../iconos/$imagen' border=0 alt='$data'>";
        }
        if ($orno == 3) {
            $order = " a.anex_radi_nume $ascdesc";
            $img3 = "<img src='../iconos/$imagen' border=0 alt='$data'>";
        }
        if ($orno == 4) {
            $order = " c.radi_fech_radi  $ascdesc";
            $img4 = "<img src='../iconos/$imagen' border=0 alt='$data'>";
        }
        if ($orno == 5) {
            $order = " a.anex_desc  $ascdesc";
            $img5 = "<img src='../iconos/$imagen' border=0 alt='$data'>";
        }
        if ($orno == 6) {
            $order = " a.sgd_fech_impres  $ascdesc";
            $img6 = "<img src='../iconos/$imagen' border=0 alt='$data'>";
        }
        if ($orno == 7) {
            $order = " a.anex_creador $ascdesc";
            $img7 = "<img src='../iconos/$imagen' border=0 alt='$data'>";
        }
        if ($orno == 8) {
            $order = " a.anex_creador $ascdesc";
            $img7 = "<img src='../iconos/$imagen' border=0 alt='$data'>";
        }

        $encabezado = session_name() . "=" . session_id() . "&dep_sel=$dep_sel&krd=$krd&estado_sal=$estado_sal&usua_perm_impresion=$usua_perm_impresion&fechah=$fechah&estado_sal_max=$estado_sal_max&ascdesc=$ascdesc&orno=";
        $fechah = date("dmy") . "_" . time("h_m_s");
        $check = 1;
        $fechaf = date("dmy") . "_" . time("hms");
        $row = array();
        ?>
        <br>
        <table border=0 width='100%' class='t_bordeGris' align='center'>
            <tr >
                <td height="20" > 
                    <TABLE width="98%" align="center" cellspacing="0" cellpadding="0">
                        <tr> 
                            <td height="73"> 
                                <?php
                                include "../envios/paEncabeza.php";
                                include "../envios/paBuscar.php";

                                /*
                                 * GENERAR LISTADO ENTREGA FISICOS
                                 */

                                $accion_sal2 = "Generar Listado de Entrega";
                                /*  GENERACION LISTADO DE RADICADOS
                                 *  Aqui utilizamos la clase adodb para generar el listado de los radicados
                                 *  Esta clase cuenta con una adaptacion a las clases utiilzadas de orfeo.
                                 *  el archivo original es adodb-pager.inc.php la modificada es adodb-paginacion.inc.php
                                 */
                                ?>
                            </td>
                        </tr>
                    </table>		
                    <table BORDER=0 cellpad=2 cellspacing='2' WIDTH=98% align='center' valign='top' class="borde_tab" cellpadding="2">
                        <tr>
                        <form name='formListado' action='../envios/paramListaImpresos.php?<?= $encabezado ?>' method='post'>
                            <td align="center" class="titulos2">
                            <a href='<?= $pagina_actual ?>?<?= $encabezado ?> '></a>
                            	<input type='submit' value='"<?= $accion_sal2 ?>"' name='Enviar' id='Enviar' valign='middle' class='botones_largo'>
                            </td>
                        </form>

                        <td  class="titulos2" align="center">	
                            <a href='<?= $pagina_sig ?>?<?= $encabezado ?> '></a>	
                            <input type='submit' value="<?= $accion_sal ?>" name='Enviar' id='Enviar' class='botones_largo' onclick="marcar();">					
                        </td>

                        <?php
                        include "$ruta_raiz/include/query/envios/queryCuerpoMarcaEnviar.php";
                        $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
                        //$db->conn->debug = true;
                        $rs = $db->conn->Execute($isql);
                        if ($_SESSION['usua_perm_firma'] == 2 || $_SESSION['usua_perm_firma'] == 3) {
                        ?>    

                            <td  align="center"  class="titulos2" align="center"> 
                                <input type=button value='Solicitar Firma' name=solicfirma valign='middle' class='botones_largo' onclick="solicitarFirma();" >
                            </td>

                        <?php
                        }
                        ?>	      				
            </tr>
        </table>
    </td>
</tr>
</table>			 	
<form name='formEnviar'  method='post'>	
    <input type="hidden" name="hidRadsFax" id="hidRadsFax"/>
    <table BORDER=0  cellpad=2 width="98%" align="center" cellspacing='2' class='borde_tab' valign='top' >
        <tr>
            <td  align='left'  class="titulos2"  >

                <!-- <b>Listar Por </b>
                        <a href='<?= $pagina_actual ?>?<?= $encabezado ?>98&ordcambio=1' alt='Ordenar Por Leidos' >
                        <span class='leidos'>Impresos</span></a> 
                <?= $img7 ?> <a href='<?= $pagina_actual ?>?<?= $encabezado ?>99&ordcambio=1'  alt='Ordenar Por Leidos'><span class='no_leidos'>
                        Por Imprimir</span></a>	
                -->
                <img src="<?= $ruta_raiz ?>/imagenes/estadoDocInfo.gif">
            </td>		

            <td class="titulos2" >
                <?php include("$ruta_raiz/webServices/appClient/modulosCliente/envioFaxServer.php"); ?>
            </td>

        </tr>
    </table>

    <TABLE width="98%" align="center" cellspacing="0" cellpadding="0" valign='top'>
        <tr> 
            <td class="grisCCCCCC"> 
                <table width="100%"  border="0"  cellpadding="0" cellspacing="3" class="borde_tab">
                    <tr class='titulos3' > 
                        <td  align="center"> <img src='<?= $ruta_raiz ?>/imagenes/estadoDoc.png'  border=0 > 
                        </td>
                        <td align="center">
                            <a href='<?= $PHP_SELF . "?" . $encabezado ?>1&ordcambio=1&orno=1' class='textoOpcion' alt='Ordenamiento'>
                                <?= $img1 ?>
                                Radicado Salida 
                            </a>
                        </td>
                        <td align="center">
                            <a href='<?= $PHP_SELF . "?" . $encabezado ?>1&ordcambio=1&orno=2' class='textoOpcion' alt='Ordenamiento'>
                                <?= $img2 ?>
                                Copia 
                            </a>
                        </td>
                        <td align="center"> 
                            <a href='<?= $PHP_SELF . "?" . $encabezado ?>1&ordcambio=1&orno=3' class='textoOpcion' alt='Ordenamiento'>
                                <?= $img3 ?>
                                Radicado Padre
                            </a>
                        </td>
                        <td align="center"> 
                            <a href='<?= $PHP_SELF . "?" . $encabezado ?>1&ordcambio=1&orno=4' class='textoOpcion' alt='Ordenamiento'>
                                <?= $img4 ?>
                                Fecha Radicado
                            </a>
                        </td>
                        <td align="center"> <a href='<?= $PHP_SELF . "?" . $encabezado ?>1&ordcambio=1&orno=5' class='textoOpcion' alt='Ordenamiento'> 
                                <?= $img5 ?>
                                Descripcion </a> </td>
                        <td align="center"> <a href='<?= $PHP_SELF . "?" . $encabezado ?>1&ordcambio=1&orno=6' class='textoOpcion' alt='Ordenamiento'>	
                                <?= $img6 ?>
                            </a> Fecha Impresion </td>
                        <td align="center"> <a href='<?= $PHP_SELF . "?" . $encabezado ?>1&ordcambio=1&orno=7' class='textoOpcion' alt='Ordenamiento'>	
                                <?= $img7 ?>
                                Generado Por </a> </td>
                        <td  width='10%' align="center"> <a href='<?= $PHP_SELF . "?" . $encabezado ?>1&ordcambio=1&orno=8' class='textoOpcion' alt='Ordenamiento'>
                                <?= $img8 ?>
                                Medio env&iacute;o</a> </td><!--Medio env&iacute;o -->
                        <td align="center" ><img src='<?= $ruta_raiz ?>/imagenes/imprim.jpg' border='0'><input type="checkbox"  id="checkall" onClick="markAll();">  </td>				
                    </tr>
                    <?php
                    $i = 1;
                    $ki = 0;
                    $registro = $pagina * 100;
                    while ($rs && !$rs->EOF) {
                        if ($ki >= $registro and $ki < ($registro + 100)) {

                            $swEsperaFirma = false;
                            $estado = $rs->fields['CHU_ESTADO'];
                            $estado_email = $rs->fields['CHU_ESTADO_EMAIL'];
                            $copia = $rs->fields['COPIA'];
                            $documentos = $rs->fields['DOCUMENTOS'];
                            $rad_salida = $rs->fields['IMG_RADICADO_SALIDA'];
                            $rad_padre = $rs->fields['RADICADO_PADRE'];
                            $cod_dev = $rs->fields['HID_DEVE_CODIGO'];
                            $fech_radicado = $rs->fields['FECHA_RADICADO'];
                            $descripcion = $rs->fields['DESCRIPCION'];
                            $fecha_impre = $rs->fields['FECHA_IMPRESION'];
                            $fecha_dev = $rs->fields['HID_SGD_DEVE_FECH'];
                            $generadoPor = $rs->fields['GENERADO_POR'];
                            $path_imagen = $rs->fields['HID_RADI_PATH'];
                            $dirTipo = $rs->fields['HID_SGD_DIR_TIPO'];
                            $medio_envio = $rs->fields['MEDIO_ENVIO'];
                            $idmedenvio = $rs->fields['IDMEDIO'];
                            $destinatario = $rs->fields['DESTINATARIO'];
                            $SgdDirTipo = $rs->fields['COPIA'];
                            $estadoFAX = $rs->fields['ESTADO_ENVIO_FAX'];
                            $sdircodigo = $rs->fields['SGDDIRCODIGO'];
                            $codienvio = $rs->fields['HID_SGD_FENV_CODIGO'];
                            $tipoRad = substr($rs->fields['IMG_RADICADO_SALIDA'], -1);
                            if ($copia > 0) {
                                $radcopia = $rad_salida . '_' . $copia;
                            } else {
                                $radcopia = $rad_salida;
                            }
                            //***********************************************
                            $edoDev = 0;

                            if ($cod_dev == 0 OR $cod_dev == NULL) {
                                $edoDev = 97;
                            } else {
                                if ($cod_dev > 0)
                                    $edoDev = 98;
                            }
                            if ($cod_dev == 99)
                                $edoDev = 99;

                            switch ($edoDev) {
                                case 99:
                                    $imgEstado = "<img src='$ruta_raiz/imagenes/docDevuelto_tiempo.gif'  border=0 alt='Fecha Devolucionyy :$fecha_dev' title='Fecha Devolucion :$fecha_dev'>";
                                    //INICIO	Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico) 					
                                    if ($estado_email == '1') {
                                        $imgEstado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo.gif border=0>";
                                    }else
                                        $imgEstado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo2.gif border=0>";
                                    //FIN: Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico)

                                    break;
                                case 98:
                                    $imgEstado = "<img src='$ruta_raiz/imagenes/docDevuelto.gif'  border=0 alt='Fecha Devolucion :$fecha_dev' title='Fecha Devolucion :$fecha_dev'>";

                                    //INICIO	Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico) 					
                                    if ($estado_email == '1') {
                                        $imgEstado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo.gif border=0>";
                                    }else
                                        $imgEstado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo2.gif border=0>";
                                    //FIN: Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico)


                                    break;
                                case 97:
                                    $fecha_dev = $rs->fields["HID_SGD_DEVE_FECH"];
                                    if ($rs->fields["HID_DEVE_CODIGO1"] == 99) {
                                        $imgEstado = "<img src='$ruta_raiz/imagenes/docDevuelto_tiempo.gif'  border=0 alt='Fecha Devolucionx :$fecha_dev' title='Devolucion por Tiempo de Espera'>";
                                        $noCheckjDevolucion = "enable";
                                        break;
                                    }
                                    if ($rs->fields["HID_DEVE_CODIGO"] >= 1 and $rs->fields["HID_DEVE_CODIGO"] <= 98) {
                                        $imgEstado = "<img src='$ruta_raiz/imagenes/docDevuelto.gif'  border=0 alt='Fecha Devolucionn :$fecha_dev' title='Fecha Devolucion :$fecha_dev'>";
                                        $noCheckjDevolucion = "disable";
                                        break;
                                    }

                                    //INICIO	Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico) 					
                                    if ($estado_email == '1') {
                                        $imgEstado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo.gif border=0>";
                                    }else
                                        $imgEstado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo2.gif border=0>";
                                    //FIN: Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico)


                                    switch ($estado) {
                                        case 2:
                                            $estadoFirma = $objFirma->firmaCompleta($rad_salida);
                                            if ($estadoFirma == "NO_SOLICITADA") {
                                                $imgEstado = "<img src=$ruta_raiz/imagenes/docRadicado.gif  border=0>";

                                                //INICIO	Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico) 					
                                                if ($estado_email == '1') {
                                                    $imgEstado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo.gif border=0>";
                                                }else
                                                    $imgEstado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo2.gif border=0>";
                                                //FIN: Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico)				
                                            }else if ($estadoFirma == "MODIFICACION") {
                                                $imgEstado = "<a  href='javascript:editFirmantes($rad_salida)' > <img src=$ruta_raiz/imagenes/docEsperaCambio.gif  border=0></a>";
                                                
                                                //INICIO	Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico)
                                                if ($estado_email == '1') {
                                                    $imgEstado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo.gif border=0>";
                                                }else
                                                    $imgEstado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo2.gif border=0>";
                                                    //FIN: Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico)
                                            }else if ($estadoFirma == "COMPLETA") {
                                                $imgEstado = "<img src=$ruta_raiz/imagenes/docFirmado.gif  border=0>";

                                                //INICIO	Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico) 					
                                                if ($estado_email == '1') {
                                                    $imgEstado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo.gif border=0>";
                                                }else
                                                    $imgEstado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo2.gif border=0>";
                                                //FIN: Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico)
                                            }else if ($estadoFirma == "RECHAZADA") {
                                                $imgEstado = "<a href='javascript:editFirmantes($rad_salida)'><img src=$ruta_raiz/imagenes/docFirmadoMal.png border=0></a>";

                                                //INICIO	Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico) 					
                                                if ($estado_email == '1') {
                                                    $imgEstado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo.gif border=0>";
                                                }else
                                                    $imgEstado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo2.gif border=0>";
                                                //FIN: Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico)
                                            }else if ($estadoFirma == "INCOMPLETA") {
                                                $imgEstado = "<a href='javascript:editFirmantes($rad_salida)'><img src=$ruta_raiz/imagenes/docEsperaFirma.gif border=0></a>";
                                                
                                                //INICIO	Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico)
                                                if ($estado_email == '1') {
                                                    $imgEstado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo.gif border=0>";
                                                }else
                                                    $imgEstado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo2.gif border=0>";
                                                    //FIN: Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico)
                                                    
                                                    $swEsperaFirma = true;
                                            }
                                            break;
                                        case 3:
                                            $imgEstado = "<img src=$ruta_raiz/imagenes/docImpreso.gif  border=0>";

                                            //INICIO	Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico) 					
                                            if ($estado_email == '1') {
                                                $imgEstado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo.gif border=0>";
                                            }else
                                                $imgEstado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo2.gif border=0>";
                                            //FIN: Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico)

                                            break;
                                        case 4:
                                            $imgEstado = "<img src=$ruta_raiz/imagenes/docEnviado.gif  border=0>";

                                            //INICIO	Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico) 					
                                            if ($estado_email == '1') {
                                                $imgEstado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo.gif border=0>";
                                            }else
                                                $imgEstado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo2.gif border=0>";
                                            //FIN: Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico)					

                                            break;
                                    }
                                    break;
                            }
                            switch ($estadoFAX) {
                                case '':
                                case null:
                                    $imgEstado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo2.gif border=0>";
                                    break;
                                case 0:
                                    $imgEstado.="<img src=$ruta_raiz/imagenes/docEnvioFaxEspera.gif title='En espera de env&iacute;o definitivo del FAX'border=0>";
                                    break;
                                case 1:
                                    $imgEstado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo.gif title='Documento enviado v&iacute;a FAX'border=0>";
                                    break;
                                case -1:
                                case -2:
                                    $imgEstado.="<img src=$ruta_raiz/imagenes/docEnvioFaxError.gif title='Ocurri&oacute; un error al enviar el FAX'border=0>";
                            }
                            if ($data == "")
                                $data = "NULL";
                            if ($i == 1) {
                                $formato = "listado2";

                                $i = 2;
                            } else {
                                $formato = "listado1";

                                $i = 1;
                            }
                            ?>
                            <tr class='<?= $formato ?>'> 
                                <td class='<?= $leido ?>' align="center" width="14%"> 
                                    <?= $imgEstado ?>
                                </td>
                                <td class='<?= $leido ?>' width="8%"> 
                                    <a href='<?= $ruta_raiz ?>/bodega/<?= $path_imagen . "?time=" . time() ?>' >
                                        <?= $rad_salida ?> </a>
                                </td>
                                <td class='<?= $leido ?>' width="5%">
                                    <?= $copia ?></td>
                                <td class='<?= $leido ?>' width="9%"> 
                                    <?= $rad_padre ?>
                                </td>
                                <td  class='<?= $leido ?>' width="9%"> 
                                    <?= $fech_radicado ?>
                                </td>
                                <td class='<?= $leido ?>' width="30%"> 
                                    <?= $descripcion ?>
                                </td>
                                <td class='<?= $leido ?>' width="12%"> &nbsp; 
                                    <?= $fecha_impre; ?>
                                </td>
                                <td class='<?= $leido ?>' width="10%" > 
                                    <?= $generadoPor ?>
                                </td>
                                <td class='<?= $leido ?>' width="10%" > 
                                    <span id="<?= $rad_salida ?>-<?= $SgdDirTipo ?>"></span>
                                    <?php
                                    $habilt="";
                                    $envio=0;
                                    if ($idmedenvio) {
                                        $envio = $idmedenvio;
                                    }
                                    if ($codienvio) {
                                        $envio = $codienvio;
                                        //$habilt="disabled";
                                    }
                                    $rs_modenv = $db->conn->Execute("  SELECT F.SGD_FENV_DESCRIP, F.SGD_FENV_CODIGO 
                                                                    FROM SGD_FENV_FRMENVIO F INNER JOIN SGD_FRMENVIO_TIPOSRAD T ON F.SGD_FENV_CODIGO = T.SGD_FENV_CODIGO 
                                                                    WHERE T.SGD_TRAD_CODIGO = $tipoRad AND SGD_FENV_ESTADO = 1 ORDER BY 1");
                                    echo $rs_modenv->GetMenu2("cmbModEnv_".$rad_salida."-".$dirTipo, $envio, false, false, 0, "id='cmbModEnv_$rad_salida' $habilt");  //"onChange=\"actModEnvio(this.value, '$sdircodigo')\"
                                        $rs_modenv->Move(0);
                                    ?>
                                </td>
                                <td align='center' class='<?= $leido ?>' width="3%"> 
                                    <?php if ($swEsperaFirma) { ?>
                                        <script>
                                        pedientesFirma = pedientesFirma + <?= $rad_salida ?> + "," ;
                                        </script>
                                    <?php } ?>                      
                                    <input type=checkbox name='checkValue[<?= $rad_salida ?>]' value='<?= $radcopia ?>' onclick="JavaScript:selecionEspecifica(this,'<?php echo $dircodigo ?>');" >                    
                                </td>                  
                            </tr>
                            <?php
                        }
                        $ki = $ki + 1;
                        $rs->MoveNext();
                    }
                    ?>
                </table>
            </TD>
        </tr>
    </TABLE>
</form>
<table border="0" cellspace="2" cellpad="2" WIDTH="98%" class='t_bordeGris' align='center'>
    <tr align="center"> 
        <td>
            <?php
            $numerot = $ki;

// Se calcula el numero de | a mostrar
            $paginas = ($numerot / 100);
            ?>
            <span class='leidos'> Paginas</span>
            <?php
            if (intval($paginas) <= $paginas) {
                $paginas = $paginas;
            } else {
                $paginas = $paginas - 1;
            }
// Se imprime el numero de Paginas.
            for ($ii = 0; $ii < $paginas; $ii++) {
                if ($pagina == $ii) {
                    $letrapg = "<font color=green size=3>";
                } else {
                    $letrapg = "<font color=blue size=2>";
                }
                echo " <a  class=paginacion  href='$PHP_SELF?dep_sel=$dep_sel&pagina=$ii&$encabezado&orno=$orno'>$letrapg" . ($ii + 1) . "</font></a>\n";
            }
            ?>
        </td>
    </tr>
</table>
</body>
</html>