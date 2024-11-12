<?php
session_start();
//empieza Anexos   por  Julian Rolon
//lista los documentos del radicado y proporciona links para ver historicos de cada documento
//este archivo se incluye en la pagina verradicado.php
if (!$ruta_raiz) $ruta_raiz= ".";
include_once "$ruta_raiz/class_control/anexo.php";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
require_once "$ruta_raiz/class_control/TipoDocumento.php";
include_once "$ruta_raiz/class_control/firmaRadicado.php";
include "$ruta_raiz/config.php";
require_once "$ruta_raiz/class_control/ControlAplIntegrada.php";
include_once "$ruta_raiz/class_control/AplExternaError.php";
require_once $ruta_raiz.'/include/class/mime.class.php';

$db = new ConnectionHandler(".");
$objTipoDocto = new TipoDocumento($db);
$objTipoDocto->TipoDocumento_codigo($tdoc);
$objFirma = new  FirmaRadicado($db);
$objMime = new Mime($db->conn);
$objCtrlAplInt = new ControlAplIntegrada($db);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$num_archivos=0;

$anex = new Anexo($db);
$sqlFechaDocto = $db->conn->SQLDate("Y-m-D H:i:s A","sgd_fech_doc");
$sqlFechaAnexo = $db->conn->SQLDate("Y-m-D H:i:s A","anex_fech_anex");
$sqlSubstDesc =  $db->conn->substr."(anex_desc, 0, 50)";
include_once "include/query/busqueda/busquedaPiloto1.php";

$isql = "	SELECT	ANEX_CODIGO AS DOCU
					,ANEX_TIPO_EXT AS EXT
					,ANEX_TAMANO AS TAMA
					,ANEX_SOLO_LECT AS RO
					,USUA_NOMB AS CREA
					,DEPE_CODI AS DEPENDENCIA
					,$sqlSubstDesc DESCR
					,ANEX_NOMB_ARCHIVO AS NOMBRE
					,SGD_PNUFE_CODI
					,ANEX_CREADOR
					,ANEX_ORIGEN
					,ANEX_SALIDA
					,$radi_nume_salida as RADI_NUME_SALIDA
					,ANEX_ESTADO
					,ANEX_ESTADO_EMAIL
					,SGD_DOC_SECUENCIA
					,SGD_DIR_TIPO
					,SGD_DOC_PADRE
					,SGD_TPR_CODIGO
					,SGD_APLI_CODI
					,SGD_TRAD_CODIGO
					,SGD_TPR_CODIGO
					,ANEX_TIPO
					,$sqlFechaDocto as FECDOC
					,$sqlFechaAnexo as FEANEX
					,ANEX_TIPO as NUMEXTDOC
					,ANEX_BORRADO
					,ANEX_ORDEN
					, dbo.VALIDAR_ACCESO_RADEXP (RADI_NUME_SALIDA, '', '$krd') AS PERMISO
                    ,anex_marcar_envio_email
			FROM	ANEXOS
					,ANEXOS_TIPO
					,USUARIO
			WHERE	ANEX_RADI_NUME = $verrad 
					AND ANEX_TIPO = ANEX_TIPO_CODI
					AND ANEX_CREADOR = USUA_LOGIN
			ORDER BY RADI_NUME_SALIDA DESC, ANEX_SALIDA DESC, ANEX_ORDEN, ANEX_CODIGO";

$datos_envio = preg_replace("[^A-Za-z0-9 .,:()&����������=#�]"," ", $datos_envio);
?>
<script>

debugger;
swradics=0;
radicando=0;
function verDetalles(anexo,tpradic,aplinteg,num)
{
	optAsigna = "";
	if (swradics==0){
		optAsigna="&verunico=1";
	}
	contadorVentanas=contadorVentanas+1;
	nombreventana="ventanaDetalles"+contadorVentanas;
	url="detalle_archivos.php?usua=<?=$krd?>&radi=<?=$verrad?>&anexo="+anexo;
	url="<?=$ruta_raiz?>/nuevo_archivo.php?codigo="+anexo+"&<?="krd=$krd&".session_name()."=".trim(session_id()) ?>&usua=<?=$krd?>&numrad=<?=$verrad ?>&contra=<?=$drde?>&radi=<?=$verrad?>&tipo=<?=$tipo?>&ent=<?=$ent?><?=$datos_envio?>&ruta_raiz=<?=$ruta_raiz?>"+"&tpradic="+tpradic+"&aplinteg="+aplinteg+optAsigna;
	window.open(url,nombreventana,'top=0,height=480,width=640,scrollbars=yes,resizable=yes');
	return;
}

function borrarArchivo(anexo,linkarch,radicar_a,procesoNumeracionFechado)
{
	if (confirm('Estas seguro de borrar este archivo anexo ?'))	{
		contadorVentanas=contadorVentanas+1;
		nombreventana="ventanaBorrar"+contadorVentanas;
		//url="borrar_archivos.php?usua=<?=$krd?>&contra=<?=$drde?>&radi=<?=$verrad?>&anexo="+anexo+"&linkarchivo="+linkarch;
		url="lista_anexos_seleccionar_transaccion.php?borrar=1&usua=<?=$krd?>&numrad=<?=$verrad?>&&contra=<?=$drde?>&radi=<?=$verrad?>&anexo="+anexo+"&linkarchivo="+linkarch+"&numfe="+procesoNumeracionFechado+"&dependencia=<?=$dependencia?>&codusuario=<?=$codusuario?>";
		window.open(url,nombreventana,'height=100,width=180');
	}
	return;
}

function verHistoricoAnexo(radicado, anexo)
{
	nombreventana= "ventHistAnex";
	url="historico_anexo.php?radi="+radicado+"&anex="+anexo;
	window.open(url,nombreventana,'height=400,width=600');
}

function verHistoricoImagen(radicado, anexo)
{
	nombreventana= "ventHistAnexDoc";
	url="ver_hist_imagen.php?type=a&id="+anexo+"&rad="+radicado;
	window.open(url,nombreventana,'height=400,width=630');
}

function radicarArchivo(anexo,linkarch,radicar_a,procesoNumeracionFechado,tpradic,aplinteg,numextdoc)
{
	if (radicando>0){
	 	alert ("Ya se esta procesando una radicaci\xf3n, para re-intentarlo hagla click sobre la pesta\xf1a de documentos");
	 	return;
	}

	if (confirm('Se asignar\xe1 un n\xfamero de radicado a este documento. Esta seguro  ?')) {
		radicando++;
		contadorVentanas=contadorVentanas+1;
		nombreventana="mainFrame";
		url="<?=$ruta_raiz?>/lista_anexos_seleccionar_transaccion.php?radicar=1&radicar_a="+radicar_a+"&vp=n&<?="krd=$krd&".session_name()."=".trim(session_id()) ?>&radicar_documento=<?=$verrad?>&numrad=<?=$verrad?>&anexo="+anexo+"&linkarchivo="+linkarch+"<?=$datos_envio?>"+"&ruta_raiz=<?=$ruta_raiz?>&numfe="+procesoNumeracionFechado+"&tpradic="+tpradic+"&aplinteg="+aplinteg+"&numextdoc="+numextdoc;
		window.open(url,nombreventana,'height=450,width=600');
	}
	return;
}


function numerarArchivo(anexo,linkarch,radicar_a,procesoNumeracionFechado)
{
	if (confirm('Se asignar\xe1 un n\xfamero a este documento. Esta seguro ?'))	{
		contadorVentanas=contadorVentanas+1;
		nombreventana="mainFrame";
		url="<?=$ruta_raiz?>/lista_anexos_seleccionar_transaccion.php?numerar=1"+"&vp=n&<?="krd=$krd&".session_name()."=".trim(session_id()) ?>&radicar_documento=<?=$verrad?>&numrad=<?=$verrad?>&anexo="+anexo+"&linkarchivo="+linkarch+"<?=$datos_envio?>"+"&ruta_raiz=<?=$ruta_raiz?>&numfe="+procesoNumeracionFechado;
		window.open(url,nombreventana,'height=450,width=600');
	}
	return;
}

function asignarRadicado(anexo,linkarch,radicar_a,numextdoc)
{
	if (radicando>0){
		alert ("Ya se esta procesando una radicaci\xf3n, para re-intentarlo hagla click sobre la pesta\xf1a de documentos");
		return;
	}

	radicando++;

	if (confirm('Est\xe1 seguro de asignarle el n\xfamero de Radicado a este archivo ?')) {
		contadorVentanas=contadorVentanas+1;
		nombreventana="mainFrame";
		url="<?=$ruta_raiz?>/genarchivo.php?generar_numero=no&radicar_a="+radicar_a+"&vp=n&<?="krd=$krd&".session_name()."=".trim(session_id()) ?>&radicar_documento=<?=$verrad?>&numrad=<?=$verrad?>&anexo="+anexo+"&linkarchivo="+linkarch+"<?=$datos_envio?>"+"&ruta_raiz=<?=$ruta_raiz?>"+"&numextdoc="+numextdoc;
		window.open(url,nombreventana,'height=450,width=600');
	}
	return;
}

function ver_tipodocuATRD(anexo,codserie,tsub)
{
	<?php
	$isqlDepR = "	SELECT	RADI_DEPE_ACTU
							,RADI_USUA_ACTU
					FROM	RADICADO
					WHERE	RADI_NUME_RADI = $numrad";
	$rsDepR = $db->conn->Execute($isqlDepR);
	$coddepe = $rsDepR->fields['RADI_DEPE_ACTU'];
	$codusua = $rsDepR->fields['RADI_USUA_ACTU'];
	$ind_ProcAnex="S";
	?>
  	window.open("./radicacion/tipificar_documento.php?krd=<?=$krd?>&nurad="+anexo+"&ind_ProcAnex=<?=$ind_ProcAnex?>&codusua=<?=$codusua?>&coddepe=<?=$coddepe?>&tsub="+tsub+"&codserie="+codserie+"&texp=<?=$texp?>","Tipificacion_Documento_Anexos","height=500,width=750,scrollbars=yes");
}

function ver_tipodocuAnex(cod_radi,codserie,tsub)
{
	window.open("./radicacion/tipificar_anexo.php?krd=<?=$krd?>&nurad="+cod_radi+"&ind_ProcAnex=<?=$ind_ProcAnex?>&codusua=<?=$codusua?>&coddepe=<?=$coddepe?>&tsub="+tsub+"&codserie="+codserie,"Tipificacion_Documento_Anexos","height=300,width=750,scrollbars=yes");
}

function vistaPreliminar(anexo,linkarch,linkarchtmp)
{
	contadorVentanas=contadorVentanas+1;
	nombreventana="mainFrame";
	url="<?=$ruta_raiz?>/genarchivo.php?vp=s&<?="krd=$krd&".session_name()."=".trim(session_id()) ?>&radicar_documento=<?=$verrad?>&numrad=<?=$verrad?>&anexo="+anexo+"&linkarchivo="+linkarch+"&linkarchivotmp="+linkarchtmp+"<?=$datos_envio?>"+"&ruta_raiz=<?=$ruta_raiz?>";
	window.open(url,nombreventana,'height=450,width=600');
	return;
}

function nuevoArchivo(asigna)
{
	debugger;
	contadorVentanas=contadorVentanas+1;
	optAsigna="";
	if (asigna==1){
		optAsigna="&verunico=1";
	}
	nombreventana="ventanaNuevo"+contadorVentanas;
	url="<?=$ruta_raiz?>/nuevo_archivo.php?codigo=&<?="krd=$krd&".session_name()."=".trim(session_id()) ?>&usua=<?=$krd?>&numrad=<?=$verrad ?>&contra=<?=$drde?>&radi=<?=$verrad?>&tipo=<?=$tipo?>&ent=<?=$ent?>"+"<?=$datos_envio?>"+"&ruta_raiz=<?=$ruta_raiz?>&tdoc=<?=$tdoc?>"+optAsigna;
	window.open(url,nombreventana,'height=700,width=600,scrollbars=yes,resizable=yes');
	return;
}

function Plantillas(plantillaper1)
{
	if(plantillaper1==0){
		plantillaper1="";
	}
	contadorVentanas=contadorVentanas+1;
	nombreventana="ventanaNuevo"+contadorVentanas;
	urlp="plantilla.php?<?="krd=$krd&".session_name()."=".trim(session_id()); ?>&verrad=<?=$verrad ?>&numrad=<?=$numrad ?>&plantillaper1="+plantillaper1;
	window.open(urlp,nombreventana,'top=0,left=0,height=800,width=850');
	return;
}

function Plantillas_pb(plantillaper1)
{
	if(plantillaper1==0)
	{
	  plantillaper1="";
	}
	contadorVentanas=contadorVentanas+1;
	nombreventana="ventanaNuevo"+contadorVentanas;
	urlp="crea_plantillas/plantilla.php?<?="krd=$krd&".session_name()."=".trim(session_id()); ?>&verrad=<?=$verrad ?>&numrad=<?=$numrad ?>&plantillaper1="+plantillaper1;
	window.open(urlp,nombreventana,'top=0,left=0,height=800,width=850');
	return;
}

function regresar()
{
	//window.history.go(0);
	window.location.reload();
	window.close();
}

</script>

<link rel="stylesheet" href="estilos/orfeo.css">
<body bgcolor="#FFFFFF">
 <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
   <td height="25" class="titulos4" colspan="10"> GENERACI&Oacute;N DE DOCUMENTOS </td>
  </tr>
 </table>
 <table WIDTH="100%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab" >
  <tr class="t_bordeGris">
   <td colspan="15" class="timpar"> 
    <img src="<?=$ruta_raiz?>/imagenes/estadoDocInfo.gif" width="320" height="35">
   </td>
  </tr>
  <tr bgcolor='#6699cc' class='etextomenu' align='middle'>
   <th width='10%' class="titulos2" align="left">
	<img src="<?=$ruta_raiz?>/imagenes/estadoDoc.png" border=0  height="32">
   </th>
   <th width='15%' class="titulos2"> RADICADO </th>
   <th width='15%' class="titulos2"> LOG IMAGEN </th>
   <th width='5%'  class="titulos2"> TIPO </th>
   <th width='5%'  class="titulos2"> TRD </th>
   <th width='1%'  class="titulos2"> </th>
   <th width='5%'  class="titulos2"> TAMA&Ntilde;O (Kb) </th>
   <th width='5%'  class="titulos2"> SOLO LECTURA </th>
   <th width='20%' class="titulos2"> CREADOR </th>
   <th width='20%' class="titulos2"> DESCRIPCI&Oacute;N </th>
   <th width='12%' class="titulos2"> ANEXADO </th>
   <th width='1%'  class="titulos2"> <img src='/img/silk/icons/email_attach.png' alt='Marcar para env&iacute;o correo electr&ocute;nico' /></th>
   <th width='35%' class="titulos2" colspan="5"> ACCI&Oacute;N </th>
  </tr>
  
<?php
$rowan = array();
//echo $isql;
$rs=$db->conn->Execute($isql);
if (!$ruta_raiz_archivo)
	$ruta_raiz_archivo = $ruta_raiz;
$directoriobase="$ruta_raiz_archivo/bodega/";
include $ruta_raiz_archivo.'/include/class/DatoContacto.php';
//Flag que indica si el radicado padre fue generado desde esta area de anexos
$swRadDesdeAnex=$anex->radGeneradoDesdeAnexo($verrad);
$objDir=new DatoContacto($db->conn);
while(!$rs->EOF){
	$aplinteg	= $rs->fields["SGD_APLI_CODI"];
	$numextdoc	= $rs->fields["NUMEXTDOC"];
	$tpradic	= $rs->fields["SGD_TRAD_CODIGO"];
	$coddocu	= $rs->fields["DOCU"];
	$origen		= $rs->fields["ANEX_ORIGEN"];
	$descrip	= $rs->fields["DESCR"];
	$depecrea	= $rs->fields["DEPENDENCIA"];
	if (($rs->fields["ANEX_SALIDA"]==1 ) && ($rs->fields["ANEX_BORRADO"]=='N'))
		$num_archivos++;
	$tamanexo = $rs->fields["TAMA"];
    $acumtamanexos += $tamanexo;
	$puedeRadicarAnexo = $objCtrlAplInt->contiInstancia($coddocu,$MODULO_RADICACION_DOCS_ANEXOS,2);
	if (strlen($coddocu) == 19) {
    	$linkarchivo=substr(trim($coddocu),0,4)."/".substr(trim($coddocu),4,3)."/docs/".trim($rs->fields["NOMBRE"]);
    	$linkarchivo_vista="$ruta_raiz/bodega/".substr(trim($coddocu),0,4)."/".substr(trim($coddocu),4,3)."/docs/".trim($rs->fields["NOMBRE"])."?time=".time();
    	$linkarchivotmp=substr(trim($coddocu),0,4)."/".substr(trim($coddocu),4,3)."/docs/tmp".trim($rs->fields["NOMBRE"]);
	} elseif (strlen($coddocu) == 20) {
	    $strDep = ltrim(substr(trim($coddocu),4,4), '0');
	    $linkarchivo=substr(trim($coddocu),0,4)."/".$strDep."/docs/".trim($rs->fields["NOMBRE"]);
	    $linkarchivo_vista="$ruta_raiz/bodega/".substr(trim($coddocu),0,4)."/".$strDep."/docs/".trim($rs->fields["NOMBRE"])."?time=".time();
	    $linkarchivotmp=substr(trim($coddocu),0,4)."/".$strDep."/docs/tmp".trim($rs->fields["NOMBRE"]);
	}
	if(!trim($rs->fields["NOMBRE"])) 
		$linkarchivo = "";
        if($rs->fields["RADI_NUME_SALIDA"]){
            $datosDir=$objDir->obtieneDatosDir(false, $rs->fields["RADI_NUME_SALIDA"],$rs->fields["SGD_DIR_TIPO"] );
            $estadoFAX=$datosDir[0]['ESTADO_ENVIO_FAX'];
        }
?>
   <tr>

<?php
	if($origen==1){
		echo " class='timpar' ";
		if ($rs->fields["NOMBRE"]=="No"){
			$linkarchivo= "";
		}
		echo "";
	}
	if($rs->fields["RADI_NUME_SALIDA"]!=0){
		$cod_radi =$rs->fields["RADI_NUME_SALIDA"];	
	}
	else {
		$cod_radi =$coddocu;
	}
	$anex_estado = $rs->fields["ANEX_ESTADO"];
	$anex_estado_email = $rs->fields["ANEX_ESTADO_EMAIL"];
	if($anex_estado<=1) {
		$img_estado = "<img src=$ruta_raiz/imagenes/docRecibido.gif ";
		//INICIO Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico)
		if($anex_estado_email=='1'){
			$img_estado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo.gif border=0>";
		}
		else
			$img_estado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo2.gif border=0>";
		//FIN: Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico)
	}
	if($anex_estado==2){
		$estadoFirma = $objFirma->firmaCompleta($cod_radi);
		if ($estadoFirma == "NO_SOLICITADA"){
			$img_estado = "<img src=$ruta_raiz/imagenes/docRadicado.gif  border=0>";
			//INICIO	Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico)
			if($anex_estado_email=='1'){
				$img_estado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo.gif border=0>";
			}
			else 
				$img_estado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo2.gif border=0>";
			//FIN: Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico)
		}
		else if ($estadoFirma == "COMPLETA"){
			$img_estado = "<img src=$ruta_raiz/imagenes/docFirmado.gif  border=0>";
			//INICIO	Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico)
			if($anex_estado_email=='1'){
				$img_estado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo.gif border=0>";
			}
			else 
				$img_estado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo2.gif border=0>";
			//FIN: Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico)
		}
		else if ($estadoFirma == "INCOMPLETA") {
			$img_estado = "<img src=$ruta_raiz/imagenes/docEsperaFirma.gif border=0>";
			//INICIO	Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico)
			if($anex_estado_email=='1'){
				$img_estado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo.gif border=0>";
			}
			else 
				$img_estado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo2.gif border=0>";
			//FIN: Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico)
		}
	}
	if($anex_estado==3) {
		$img_estado = "<img src=$ruta_raiz/imagenes/docImpreso.gif>";
		//INICIO	Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico)
		if($anex_estado_email=='1'){
			$img_estado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo.gif border=0>";
		}
		else 
			$img_estado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo2.gif border=0>";
		//FIN: Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico)
	}
	if($anex_estado==4) {
		$img_estado = "<img src=$ruta_raiz/imagenes/docEnviado.gif>";
		//INICIO	Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico)
		if($anex_estado_email=='1'){
			$img_estado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo.gif border=0>";
		}
		else
			$img_estado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo2.gif border=0>";
			//FIN: Linea de verificacion de activacion de bandera para el envio por correo E-mail (correo electronico)
	}
        switch($estadoFAX){    
                    case '':
                    case null:
                        $img_estado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo2.gif border=0>";
                        break;
                    case 0:  
                        $img_estado.="<img src=$ruta_raiz/imagenes/docEnvioFaxEspera.gif title='En espera de env&iacute;o definitivo del FAX'border=0>";
                        break;
                    case 1: 
                        $img_estado.="<img src=$ruta_raiz/imagenes/docEnviadoCorreo.gif title='Documento enviado v&iacute;a FAX'border=0>";
                        break;
                    case -1:
                    case -2:
                        $img_estado.="<img src=$ruta_raiz/imagenes/docEnvioFaxError.gif title='Ocurri&oacute; un error al enviar el FAX'border=0>";
                    
                }
?>

	<td height="21" class='listado2'> 
	 <font size=1> <?=$img_estado?> </font>
	</td>
	<td class='listado2'>
	 <font size=1>

<?php
	if ($rs->fields["ANEX_BORRADO"] == 'N') {
		if(trim($linkarchivo)){
			### SE VALIDA SI EL USUARIO QUE CONSULTA PUEDE ACCEDER AL RADICADO CONSULTADO
			if($rs->fields['PERMISO'] == 0 || !$rs->fields['RADI_NUME_SALIDA']){
				echo "<b><a class=vinculos href='".trim(strtolower($linkarchivo_vista))."'>".trim(strtolower($cod_radi));
			}
			else{
				echo "<span>".trim(strtolower($cod_radi))."</span>";
			}
		}
		else {
			echo trim(strtolower($cod_radi));
		}
	}
	else {
		echo $cod_radi;
	}
?>

	 </font>
	</td>
	<td class='listado2'>
	<?php 
	if ($rs->fields["ANEX_BORRADO"] == 'N') {
		if(trim($linkarchivo)) {
			echo "<center><a href=\"javascript:verHistoricoImagen('".$verrad."','".$coddocu."')\" class='vinculos'><img border='0' src='imagenes/log.png' alt='Log del documento' title='Log del documento' height='12' width='12' /></a></center>";
		}
	}
	?>
	</td>
    <td class='listado2'>
     <font size=1> <?php
	if(trim($linkarchivo)) {
		echo $rs->fields["EXT"];
	}
	else {
		echo $msg;
	}
	if($rs->fields["SGD_DIR_TIPO"]==7) 
		$msg = "Otro Destinatario";
	else 
		$msg="Otro Destinatario";?>
	 </font>
	</td>
	<td class='listado2' width='1%' valign='middle'>
	 <font face="Arial, Helvetica, sans-serif" class="etextomenu">
  
<?php
	// Indica si el Radicado Ya tiene asociado algun TRD
	$isql_TRDA = "SELECT *
					FROM SGD_RDF_RETDOCF
					WHERE RADI_NUME_RADI = $cod_radi ";
	$rs_TRA = $db->conn->Execute($isql_TRDA);
	$radiNumero = $rs_TRA->fields["RADI_NUME_RADI"];
	if ($radiNumero !='') {
		$msg_TRD = "S";
	}
	else {
		$msg_TRD = "";
	}
?>
	  <center>

<?php
	 echo $msg_TRD;
?>

	  </center>
	 </font>
	</td>
	<td class='listado2' width="1%" valign="middle">
	 <font face="Arial, Helvetica, sans-serif" class="etextomenu">

<?php
	/**  $perm_radi_sal  Viene del campo PER_RADI_SAL y Establece permiso en la rad. de salida
	  *  1 Radicar documentos,  2 Impresion de Doc's, 3 Radicacion e Impresion. (Por. Jh)
	  *  Ademas verifica que el documento no este radicado con $rowwan[9] y [10]
	  *  El jefe con $codusuario=1 siempre podra radicar
	  */
	if( ( $rs->fields["EXT"]=="rtf" or $rs->fields["EXT"]=="doc" or $rs->fields["EXT"]=="odt" or $rs->fields["EXT"]=="xml" or $rs->fields["EXT"]=="docx") AND 
	  	($rs->fields["ANEX_ESTADO"]<=3) AND	($rs->fields["ANEX_BORRADO"] == 'N') ) {
		echo "<a class=vinculos href=javascript:vistaPreliminar('$coddocu','$linkarchivo','$linkarchivotmp')>";
?>

	  <img src="<?=$ruta_raiz?>/iconos/vista_preliminar.gif" alt="Vista Preliminar" border="0">
      <font face="Arial, Helvetica, sans-serif" class="etextomenu">
	   <font face="Arial, Helvetica, sans-serif" class="etextomenu">
		<font face="Arial, Helvetica, sans-serif" class="etextomenu">

<?php
		echo "</a>";
		$radicado = "false";
		$anexo = $cod_radi;
	}
?>

		</font>
	   </font>
	  </font>
	 </font>
	</td>
    <td class='listado2'>
     <font size=1> <?=$tamanexo?> </font>
    </td>
    <td class='listado2'>
     <font size=1> <?=$rs->fields["RO"]?> </font>
    </td>
    <td class='listado2'>
     <font size=1> <?=$rs->fields["CREA"]?> </font>
    </td>
    <td class='listado2'>
     <font size=1> <?=$rs->fields["DESCR"]?> </font>
    </td>
    <td class='listado2'>
     <font size=1> <?=$rs->fields["FEANEX"]?> </font>
    </td>   
    <td class='listado2'>
		<?php
    	if (strlen($rs->fields["NOMBRE"]) > 1) {
    		//Checked Object ?
    		$co = ($rs->fields["anex_marcar_envio_email"] == 1) ? 'checked': '';
    		//Read Only Checkbox Anexos
    		$roca = (trim($rs->fields["ANEX_CREADOR"])==trim($krd))? "javascript:validartamanexos('".$verrad."', '".$coddocu."');" : "return false;";
    		?>
    		<input type='checkbox' name='<?php echo $coddocu ?>' id='<?php echo $coddocu ?>' <?php echo $co ?> onclick="<?php echo $roca ?>" />
    	<?php
		}
		?>
	</td> 
<?php 
	if ($rs->fields["ANEX_BORRADO"] == 'N') {
?>

	<td class='listado2'>
     <font size=1>

<?php
		//echo "compara dependencia:  [" . $_SESSION["dependencia"] . "=" . $depecrea . "]";
if($origen!=1 and $linkarchivo and $verradPermisos == "Full" ) {
			if ($anex_estado<4) {
				//if (($codusuario == $radi_usua_actu) || ($depeCodi == $coddepe)) {
				if ($_SESSION["dependencia"] == $depecrea) {
					echo "<a class=vinculos href=javascript:verDetalles('$coddocu','$tpradic','$aplinteg')>Modificar</a> ";
				}
			}
		}
?>

	 </font>
	</td>

<?php
		//Estas variables se utilizan para verificar si se debe mostrar la opción de tipificación de anexo .TIF
		$anexTipo = $rs->fields["ANEX_TIPO"];
	    $anexTPRActual = $rs->fields["SGD_TPR_CODIGO"];
        $dataMime = $objMime->GetData($anexTipo);
	   	if ($verradPermisos == "Full") {
?>
	<td class='listado2'>
	 <font size=1>
 
<?php
			$radiNumeAnexo = $rs->fields["RADI_NUME_SALIDA"];
			if($radiNumeAnexo>0 and trim($linkarchivo)) {
				if(!$codserie) 
					$codserie="0";
				if(!$tsub) 
					$tsub="0";
				echo "<a class=vinculos href=javascript:ver_tipodocuATRD($radiNumeAnexo,$codserie,$tsub);>Tipificar</a> ";
			}
			elseif ($perm_tipif_anexo == 1 && (($dataMime['ANEX_PERM_TIPIF_ANEXO']==1)) && $anexTPRActual == '') {
				//Es un anexo de tipo tif (4) y el usuario tiene permiso para Tipificar, ademas el anexo no ha sido tipificado
				if(!$codserie) 
					$codserie="0";
				if(!$tsub)
					$tsub="0";
				echo "<a class=vinculoTipifAnex href=javascript:ver_tipodocuAnex('$cod_radi','$anexo',$codserie,$tsub);> Tipificar </a> ";
			}
			elseif ($perm_tipif_anexo == 1 && (($dataMime['ANEX_PERM_TIPIF_ANEXO']==1)) && $anexTPRActual != '') {
				//Es un anexo de tipo tif (4) y el usuario tiene permiso para Tipificar, ademas el anexo YA ha sido tipificado antes
				if(!$codserie) 
					$codserie="0";
				if(!$tsub) 
					$tsub="0";
				echo "<a class=vinculoTipifAnex href=javascript:ver_tipodocuAnex('$cod_radi','$anexo',$codserie,$tsub);> Re-Tipificar </a> ";
			}
?>

	 </font>
	</td>
	<td class='listado2'>
	 <font size=1>

<?php
	if ($rs->fields["RADI_NUME_SALIDA"]=="" and $ruta_raiz != ".." and (trim($rs->fields["ANEX_CREADOR"])==trim($krd) OR $codusuario==1) and
	  (strlen($rs->fields["SGD_DOC_SECUENCIA"])=="" and strcmp ($cod_radi,$rs->fields["DOCU"])==0)) {
		if($origen!=1  and $linkarchivo) {
			echo "<a class=vinculos href=javascript:borrarArchivo('$coddocu','$linkarchivo','$cod_radi','".$rs->fields["SGD_PNUFE_CODI"]."')>Borrar</a> ";
		}
	}
?>

	 </font>
	</td>
	<td class='listado2'>
	 <font size=1>

<?php
			/**  $perm_radi_sal  Viene del campo PER_RADI_SAL y Establece permiso en la rad. de salida
			  *  1 Radicar documentos,  2 Impresion de Doc's, 3 Radicacion e Impresiï¿½n. (Por. Jh)  
			  *  Ademas verifica que el documento no este radicado con $rowwan[9] y [10]
			  *  El jefe con $codusuario=1 siempre podrï¿½ radicar
			  */
			if  ($rs->fields["ANEX_SALIDA"]==1 AND ($codusuario==1 OR $perm_radi_sal==1 OR $perm_radi_sal==3) and 
					($ruta_raiz != ".." OR ($rs->fields["SGD_PNUFE_CODI"] AND
					$rs->fields["SGD_DOC_SECUENCIA"] AND $rs->fields["SGD_DOC_SECUENCIA"]>0 )   )) {
				if (!$rs->fields["RADI_NUME_SALIDA"]){
					if(substr($verrad,-1)==2 && $puedeRadicarAnexo==1 ) {
						$rs->fields["SGD_PNUFE_CODI"]=0;
						echo "<a class=vinculos href=javascript:radicarArchivo('$coddocu','$linkarchivo','si',".$rs->fields["SGD_PNUFE_CODI"].",'$tpradic','$aplinteg','$numextdoc')>Radicar(-$tpradic)</a> ";
						$radicado = "false";
						$anexo = $cod_radi;
					}
					elseif ($puedeRadicarAnexo!=1) {
						$objError = new AplExternaError();
						$objError->setMessage($puedeRadicarAnexo);
						echo ($objError->getMessage());
					}
					else {
						if((substr($verrad,-1)!=2) and $num_archivos==1 and !$rs->fields["SGD_PNUFE_CODI"] and $swRadDesdeAnex==false ) {
							echo "<a class=vinculos href=javascript:asignarRadicado('$coddocu','$linkarchivo','$cod_radi','$numextdoc')>Asignar Rad</a> ";
							$radicado = "false";
							$anexo = $cod_radi;
						}
						else if ($rs->fields["SGD_PNUFE_CODI"]&& strcmp($cod_radi,$rs->fields["SGD_DOC_PADRE"])==0 && !$anex->seHaRadicadoUnPaquete($rs->fields["SGD_DOC_PADRE"])) {
							echo "<a class=vinculos href=javascript:radicarArchivo('$coddocu','$linkarchivo','si',".$rs->fields["SGD_PNUFE_CODI"].",'$tpradic','$aplinteg','$numextdoc')>Radicar(-$tpradic)</a> ";
							$radicado = "false";
							$anexo = $cod_radi;
						}
						else if ($puedeRadicarAnexo==1) {
							$rs->fields["SGD_PNUFE_CODI"]=0;
							echo "<a class=vinculos href=javascript:radicarArchivo('$coddocu','$linkarchivo','si',".$rs->fields["SGD_PNUFE_CODI"].",'$tpradic','$aplinteg',$numextdoc)>Radicar(-$tpradic)</a> ";
							$radicado = "false";
							$anexo = $cod_radi;
						}
					}
				}
				else {
					if (!$rs->fields["SGD_PNUFE_CODI"]) $rs->fields["SGD_PNUFE_CODI"]=0;
					if ($anex_estado<=3) {
					    if ($_SESSION["dependencia"] == $depecrea) {
						    echo "<a class=vinculos href=javascript:radicarArchivo('$coddocu','$linkarchivo','$cod_radi',".$rs->fields["SGD_PNUFE_CODI"].",'','',$numextdoc)>Re-Generar</a> ";
							$radicado = "true";
					    }
					}
				}
			}
			else if ( $usua_perm_numera_res==1 && $ruta_raiz != ".." && !$rs->fields["SGD_DOC_SECUENCIA"] && strcmp($cod_radi,$rs->fields["SGD_DOC_PADRE"])==0) {
				// SI ES PAQUETE DE DOCUMENTOS Y EL USUARIO TIENE PERMISOS
				echo "<a class=vinculos href=javascript:numerarArchivo('$coddocu','$linkarchivo','si',".$rs->fields["SGD_PNUFE_CODI"].")>Numerar</a> ";
			}
			if($rs->fields["RADI_NUME_SALIDA"]) {
				$radicado="true";
			}
?>

	 </font>
	</td>

<?php
		}
		else {
?>

	<td class='listado2'>
	 <font size=1>

<?php
			if ( $origen!=1  and $linkarchivo and $perm_borrar_anexo == 1 && $rs->fields["ANEX_SALIDA"]==0 && ($dataMime['ANEX_PERM_TIPIF_ANEXO']==1) ) {
				echo "<a class=vinculoTipifAnex href=javascript:borrarArchivo('$coddocu','$linkarchivo','$cod_radi','".$rs->fields["SGD_PNUFE_CODI"]."')>Borrar</a> ";
			}
			if ( $perm_tipif_anexo == 1 && ($dataMime['ANEX_PERM_TIPIF_ANEXO']==1) && $anexTPRActual == '' ) {
				//Es un anexo de tipo tif (4) y el usuario tiene permiso para Tipificar, además el anexo no ha sido tipificado
				if(!$codserie) 
					$codserie="0";
				if(!$tsub) 
					$tsub="0";
				echo "<a class=vinculoTipifAnex href=javascript:ver_tipodocuAnex('$cod_radi','$anexo',$codserie,$tsub);> Tipificar </a> ";
			}
			elseif ( $perm_tipif_anexo == 1 && ($dataMime['ANEX_PERM_TIPIF_ANEXO']==1) && $anexTPRActual != '' ) {
				//Es un anexo de tipo tif (4) y el usuario tiene permiso para Tipificar, además el anexo YA ha sido tipificado antes
				if(!$codserie) 
					$codserie="0";
				if(!$tsub) 
					$tsub="0";
				echo "<a class=vinculoTipifAnex href=javascript:ver_tipodocuAnex('$cod_radi','$anexo',$codserie,$tsub);> Re-Tipificar </a> ";
			}
?>

	 </font>
	</td>

<?php
		}
	}
?>
	
	<td class='listado2'><?php echo "<a class=vinculos href=javascript:verHistoricoAnexo('$verrad','$coddocu');>Hist&oacute;rico</a> "; ?></td>
   </tr>

<?php
	$rs->MoveNext();
}
/*
$mostrar_lista = 0;
if($mostrar_lista==1){
?>
  </TABLE>
<?php
}*/
?>

  </table>
  <table  width="100%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
   <tr align="center">
	<td align=center>
<?php
$tipoRad = substr($numrad, -1);
if($verradPermisos == "Full" || (($_SESSION["dependencia"] == 663 || $_SESSION["dependencia"] == 900) && $tipoRad == 2)){
	
?>
	
	<a class="vinculos" href='javascript:nuevoArchivo(<?php if($num_archivos==0 && $swRadDesdeAnex==false) echo"1"; else echo"0";  ?>)' class="timpar">Anexar Archivo  </a> &nbsp;

<?php 
}
$permOrd=0;
$cons ="SELECT	USUA_PERM_ORDENAR
		FROM 	USUARIO
		WHERE	USUA_LOGIN = '$krd'";
$rsCon = $db->conn->Execute($cons);
$permOrd = $rsCon->fields['USUA_PERM_ORDENAR'];
if ($permOrd==1){
?>

	<a class=vinculos href='./ordenarAnexos/index.php?&numrad=<?=$verrad?>' > Ordenar Anexos</a>

<?php
}
?>
	</td>
	<script>
		swradics=<?=$num_archivos?>;
    </script>
   </tr>
  </table>
  <script type="text/javascript">
	function validartamanexos(radi, idanex) {
		var checkBox = document.getElementById(idanex);
		var acutamanex = (checkBox.checked == true) ? 1 : 0;
		var url="anexosarchivo.php?<?php echo session_name()."=".trim(session_id()); ?>&radi="+radi+"&idanex="+idanex+"&acutamanex="+acutamanex;

		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				if (this.responseText.substring(0,1) == 0)
					checkBox.checked = false;
				else
					checkBox.checked = true;
				alert(this.responseText.substring(2));
			}
		};
		xhttp.open("GET", url, true);
		xhttp.send();
	}

	swradics=<?=$num_archivos?>;
    </script>
 </body>
</html>