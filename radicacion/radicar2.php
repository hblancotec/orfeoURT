<?php
session_start();

$ruta_raiz= "..";
if(!isset($_SESSION['dependencia']))	include "../rec_session.php";
//foreach ($_GET as $key => $valor)   ${$key} = $valor;
//foreach ($_POST as $key => $valor)   ${$key} = $valor;
extract($_GET, EXTR_SKIP);
extract($_POST, EXTR_OVERWRITE);
extract($_SESSION, EXTR_OVERWRITE);

$krd = $_SESSION["krd"];
$dependencia= $_SESSION["dependencia"];
$usua_doc	= $_SESSION["usua_doc"];
$codusuario = $_SESSION["codusuario"];
$tpNumRad	= $_SESSION["tpNumRad"];
$tpPerRad	= $_SESSION["tpPerRad"];
$tpDescRad	= $_SESSION["tpDescRad"];
$tip3Nombre = $_SESSION["tip3Nombre"];
$tip3img	= $_SESSION["tip3img"];
$tpDepeRad	= $_SESSION["tpDepeRad"];
$tip3desc	= $_SESSION["tip3desc"];
$ruta_raiz	= "..";


include_once "../include/db/ConnectionHandler.php";

$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

//include "crea_combos_universales.php";

if($nurad){
	$nurad=trim($nurad);
	$ent = substr($nurad,-1);
}

$no_tipo = "true";
$imgTp1 = str_replace(".jpg", "",$tip3img[1][$ent]);
$imgTp2 = str_replace(".jpg", "",$tip3img[2][$ent]);
$imgTp3 = str_replace(".jpg", "",$tip3img[3][$ent]);
$descTp1 = "alt='".$tip3desc[1][$ent]."' title='".$tip3desc[1][$ent]."'";
$descTp2 = "alt='".$tip3desc[2][$ent]."' title='".$tip3desc[2][$ent]."'";
$descTp3 = "alt='".$tip3desc[3][$ent]."' title='".$tip3desc[3][$ent]."'";
$nombreTp1 = $tip3Nombre[1][$ent];
$nombreTp2 = $tip3Nombre[2][$ent];
$nombreTp3 = $tip3Nombre[3][$ent];
?>

<HTML>
	<head>
		<title> .:: Orfeo Modulo de Radicaci&oacute;n ::. </title>
		<meta http-equiv="expires" content="99999999999">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="../estilos/tabber.css" TYPE="text/css" MEDIA="screen">
		<link rel="stylesheet" href="../estilos/orfeo.css" type="text/css">
		<SCRIPT Language="JavaScript" src="../js/crea_combos_2.js"></SCRIPT>
		<script type="text/javascript" src="../js/tabber.js"></script>
		<script type='text/javascript' src="../include/ajax/usuarios/usuariosServer.php?client=all"></script>
		<script type='text/javascript' src="../include/ajax/usuarios/usuariosServer.php?stub=usuarios"></script>
		<script type='text/javascript' src="../include/ajax/radicacion/radicacionServer.php?client=all"></script>
		<script type='text/javascript' src="../include/ajax/radicacion/radicacionServer.php?stub=radicacionAjax"></script>
		<script type='text/javascript'>
			// Objeto de HTML_AJAX pear para Traer usuarios
			var remote = new usuarios({}); // pass in an empty hash so were in async mode
			var remoteRad = new radicacionAjax({});
		</script>
		<script src="../pqr2/js/jquery-1.7.1.js"></script>
		<script src="../pqr2/js/ui/jquery.ui.core.js" type="text/javascript"></script>
		<script src="../pqr2/js/ui/jquery.ui.widget.js" type="text/javascript"></script>
		<script src="../pqr2/js/ui/jquery.ui.mouse.js" type="text/javascript"></script>  
		<script src="../pqr2/js/ui/jquery.ui.draggable.js" type="text/javascript"></script>
		<script src="../pqr2/js/jquery.alerts.js" type="text/javascript"></script>
		<link href="../pqr2/css/jquery.alerts.css" rel="stylesheet" type="text/css" media="screen" />
		<script> 
			radicando = 0;
			
			$(document).ready(function(){
				$("#coddepe").change(function(event){
					var id = $("#coddepe").find(':selected').val();
					$("#listaUsu").load('../include/tx/json/getInfoUsReasigna.php?id='+id);
				});
			});
			

			function trim (myString)
			{
				return myString.replace(/^\s+/g,'').replace(/\s+$/g,'');
			}
			document.write('<style type="text/css">.tabber{display:none;}<\/style>');

<?php
// Convertimos los vectores de los paises, dptos y municipios creados en crea_combos_universales.php a vectores en JavaScript.
//echo arrayToJsArray($vcontiv, 'vc');
//echo arrayToJsArray($vpaisesv, 'vp');
//echo arrayToJsArray($vdptosv, 'vd');
//echo arrayToJsArray($vmcposv, 'vm');
?>

			function cambIntgAp(valor)
			{	
				fecha_hoy =  '<?=date('d')."-".date('m')."-".date('Y')?>';
				if (valor!=0){
					if  (document.formulario.fecha_gen_doc.value.length==0){
						document.formulario.fecha_gen_doc.value=fecha_hoy;
					}
					else{
						document.formulario.fecha_gen_doc.value="";
					}
				}
			}

			function fechf(formulario,n)
			{
				var fechaActual = new Date();
				fecha_doc = document.formulario.fecha_gen_doc.value;
				dias_doc=fecha_doc.substring(0,2);
				mes_doc=fecha_doc.substring(3,5);
				ano_doc=fecha_doc.substring(6,10);
				var fecha = new Date(ano_doc,mes_doc-1, dias_doc);
				var tiempoRestante = fechaActual.getTime() - fecha.getTime();
				var dias = Math.floor(tiempoRestante / (1000 * 60 * 60 * 24));
				if (dias >60 && dias < 1500){
					jAlert('El documento tiene fecha anterior a 60 dias!!', 'Alerta');
				}
				else {
					if (dias > 1500) {
						jAlert('Verifique la fecha del documento!!', 'Alerta');
						fecha_doc = "";
					}
					else {
						fecha_doc = "ok";
						if (dias < 0) {
							jAlert('Verifique la fecha del documento !!, la fecha es superior al dia de hoy','Alerta');
							fecha_doc = "asdfa";
						}
					}
				}
				return fecha_doc;
			}

			function modificar_doc()
			{
				if (document.formulario.documento_us1.value) {
					document.formulario.submit();
				}
				else {
					jAlert('Remitente/Destinatario son obligatorios', 'Alerta');
				}
			}

			function pestanas(pestana)
			{
				<?php
				if($ent==1) $ver_pestana=""; else $ver_pestana="";
				?>
				document.getElementById('remitente').style.display = "";
				document.getElementById('predio').style.display = "<?=$ver_pestana?>";
				document.getElementById('empresa').style.display = "<?=$ver_pestana?>";
				if(pestana==1) {
					document.getElementById('pes1').style.display = "";
				}
				else {
					document.getElementById('pes1').style.display = "none";
				}
				if(pestana==2) {
					document.getElementById('pes2').style.display = "";
				}
				else {
					document.getElementById('pes2').style.display = "none";
				}
				if(pestana==3) {
					document.getElementById('pes3').style.display = "";
				}
				else {
					document.getElementById('pes3').style.display = "none";
				}
			}

			function pb1()
			{
			   dato1 = document.forma.no_documento.value;
			}

			function start(url, width, height) 
			{
				windowprops = "top=0,left=0,location=no,status=no, menubar=no,scrollbars=yes, resizable=yes,width=1100,height=550";
				preview = window.open(url , "preview", windowprops);
			}

			function doPopup() 
			{
				url = "popup.htm";
				width = 800; // ancho en pixels
				height = 320; // alto en pixels
				delay = 2; // tiempo de delay en segundos
				timer = setTimeout("start(url, width, height)", delay*1000);
			}

			function verDatosRad(noRad) 
			{
				url = "../verradicado.php?verrad="+noRad+"&krd=<?=$krd."&".session_name()."=".session_id()?>&datoVer=985&ruta_raiz=..";
				width = 800; // ancho en pixels
				height = 320; // alto en pixels
				delay = 2; // tiempo de delay en segundos
				timer = setTimeout("start(url, width, height)", delay*1000);
			}

			function buscar_usuario()
			{
				document.write('<form target=Buscar_Usuario name=formb action=buscar_usuario.php?envio_salida=true&ent=<?=$ent?> method=POST>');
				document.write("<input type='hidden' name=no_documento value='" + documento +"'>");
				document.write("</form> ");
			}

			function regresar()
			{
				i=1;
			}

			function radicar_doc()
			{
				debugger;
				if (radicando>0){
					alert ("Ya se esta procesando una radicaci\xf3n, por favor espere unos segundos mientras se atiende su solicitud.");
					return;
				}

				if (document.getElementById('cmbTema')) {
    				if (document.getElementById('cmbTema').value <= 0)
    				{
    					jAlert ("El tema es obligatorio.");
        				return;
    				}
    			} 
				
				if (document.getElementById('med').value == 0)
				{
					if (<?=$ent?>!=2) {
						jAlert ("El medio de Env\xedo es obligatorio.");
    					return;
					} else {
						jAlert ("El medio de Recepci\xf3n es obligatorio.");
    					return;
					}
				}
				if (document.formulario.documento_us1.value != 0 && document.formulario.muni_us1.value != 0 &&
					document.formulario.direccion_us1.value != 0 &&	document.formulario.coddepe.value != 0 &&
					document.getElementById('listaUsu').value != 0 && 
					document.getElementById('fecha_gen_doc').value 
				) {
					//Validamos tipo de radicacion, tipo de tercero, medio de envio y existencia de correo
					if  (<?=$ent?>!=2 && document.getElementById('tipo_emp_us1').value==1 && document.getElementById('med').value==4 && trim(document.getElementById('mail_us1').value)=='' ) {
						jAlert('La radicaci\xf3n para Entidades y Medio de Env\xedo Correo Electr\xf3nico requiere un email autorizado.', 'Alerta');
					} else {
						radicando++;
						radicar();
					}
				}
				else {
					jAlert('El tipo de tercero,\nRemitente/Destinatario,\nDireccion (incluido c\xf3d. postal),\nFecha doc y Dependencia son obligatorios', 'Alerta');
				}
			}

			function alertaVence(rad, fecha) {
				jAlert('Estimado usuario(a).\nEl radicado ' + rad + ' ser\xe1 anulado el ' + fecha + ' si no ha gestionado su destino.');
			}

			function radicar()
			{
				debugger;
				radicando++;
				var datosRad = new Array(20);
				datosRad['tipoRadicado'] = <?=$ent?>;
				datosRad['radiDepeRadi'] = <?=$dependencia?>;
				datosRad['radiDepeActu'] = document.getElementById('coddepe').value;
				datosRad['radiUsuaActu'] = document.getElementById('listaUsu').value;
				datosRad['radiUsuaRadi'] = <?=$codusuario?>;
				datosRad['usuaDoc'] = <?=$usua_doc?>;
				datosRad['dependenciaSecuencia'] = <?=$tpDepeRad[$ent]?>;
				datosRad['asunto'] = document.getElementById('asu').value;
				datosRad['cuentai'] = "'" + document.getElementById('cuentai').value + "'";
				datosRad['tipoRemitente'] = document.getElementById('tipo_emp_us1').value;
				datosRad['fechaOficio'] = document.getElementById('fecha_gen_doc').value;
				datosRad['med'] = document.getElementById('med').value;
				if(document.getElementById('tdoc')) {
					datosRad['tipoDocumento'] = document.getElementById('tdoc').value;
				}
				else {
					datosRad['tipoDocumento'] = '0';
				}
				if(document.getElementById('documento_us3').value>=1) {
					datosRad['documentoUs3']=document.getElementById('documento_us3').value;
				}
				else {
					datosRad['documentoUs3']="0";
				}
				datosRad['radiPais'] = document.getElementById('idpais1').value;

				<?php
				if(!$radicadopadre)
					$radicadopadre='0'; 
				$radicadopadre2 = ($rad1)? 0 : $radicadopadre;
				?>

				datosRad['radicadoPadre'] = '<?=$radicadopadre2?>';
				datosRad['carpetaPer'] = '0';

				<?php
				if(!$ent) 
					$ent="0"; 
				if($ent==2) {
					$carpetaCodi = '0';
				}
				else {
					$carpetaCodi = "'".$ent."'";
				}
				?>

				datosRad['carpetaCodi'] = <?=$carpetaCodi?>;
				datosRad['radiPath'] = '';
				datosRad['tDidCodi'] = '0';
				datosRad['ane'] = document.getElementById('ane').value;
				if (document.getElementById('cmbTema')) {
					datosRad['tema'] = document.getElementById('cmbTema').value;
				} else {
					datosRad['tema'] = 0;
				}

				remoteRad.newRadicadoAjax('noRadicado'
										,unescape( encodeURIComponent((datosRad['asunto'])))
										,datosRad['tipoRadicado'] 
										,datosRad['radiDepeRadi']
										,datosRad['radiDepeActu']
										,datosRad['dependenciaSecuencia']
										,datosRad['radiUsuaRadi']
										,datosRad['radiUsuaActu']
										,datosRad['usuaDoc']
										,datosRad['cuentai']
										,datosRad['documentoUs3']
										,datosRad['med']
										,datosRad['fechaOficio']
										,datosRad['radicadoPadre']
										,datosRad['radiPais']
										,datosRad['tipoDocumento']
										,datosRad['carpetaPer']
										,datosRad['carpetaCodi']
										,datosRad['tDidCodi']                            
										,datosRad['tipoRemitente']
										,datosRad['ane']
										,datosRad['radiPath']
										,datosRad['tema']
										);
			}

			function grabarDirecciones(radiNumeRadi)
			{
				var datosRad = new Array(20);
				nombre = document.getElementById('nombre_us1').value;
				apellido1 = document.getElementById('prim_apel_us1').value;
				apellido2 = document.getElementById('seg_apel_us1').value;
				grbNombresUs = trim(nombre) + ' '+ trim(apellido1) + ' ' + trim(apellido2);
				datosRad['grbNombresUs'] = grbNombresUs;
				datosRad['ccDocumento'] = document.getElementById('cc_documento_us1').value;
				ubicacion = document.getElementById('muni_us1').value;
				ubicacionM = ubicacion.split("-",4);
				datosRad['muniCodi'] = ubicacionM[2];
				datosRad['dptoCodi'] = ubicacionM[1];
				datosRad['idPais'] = ubicacionM[0];
				datosRad['idCont'] = document.getElementById('idcont1').value;
				funCodigo=0; oemCodigo=0; espCodigo=0; ciuCodigo=0;
				if(document.getElementById('tipo_emp_us1').value==0)
					ciuCodigo=document.getElementById('documento_us1').value;
				if(document.getElementById('tipo_emp_us1').value==1) 
					espCodigo=document.getElementById('documento_us1').value;
				if(document.getElementById('tipo_emp_us1').value==2) 
					oemCodigo=document.getElementById('documento_us1').value;
				if(document.getElementById('tipo_emp_us1').value==6) 
					funCodigo=document.getElementById('documento_us1').value;
				datosRad['direccion'] = document.getElementById('direccion_us1').value;
				datosRad['codpostal'] = document.getElementById('codpostal_us1').value;
				datosRad['dirTelefono'] = document.getElementById('telefono_us1').value;
				datosRad['dirMail'] = document.getElementById('mail_us1').value;
				datosRad['dirNombre'] = document.getElementById('otro_us1').value;
				datosRad['asunto'] = document.getElementById('asu').value;
				datosRad['cuentai'] = "'" + document.getElementById('cuentai').value + "'";
				datosRad['fechaOficio'] = document.getElementById('fecha_gen_doc').value;
				datosRad['med'] = document.getElementById('med').value;
				datosRad['ane'] = document.getElementById('ane').value;
				remoteRad.insertDireccionAjax(radiNumeRadi,1,0,datosRad['grbNombresUs'],datosRad['ccDocumento'],
					datosRad['muniCodi'],datosRad['dptoCodi'],datosRad['idPais'],datosRad['idCont'],
					funCodigo,oemCodigo,ciuCodigo, espCodigo, 
					datosRad['direccion'],datosRad['codpostal'],datosRad['dirTelefono'],datosRad['dirMail'],
					datosRad['dirNombre'], unescape( encodeURIComponent((datosRad['asunto']))),datosRad['cuentai'],datosRad['fechaOficio'],datosRad['med'],datosRad['ane']
				 );

				// Aqui se graba el Segundo Destinatario

				if(document.getElementById('cc_documento_us2').value) {
					var datosRad = new Array(20);
					nombre = ""; apellido1=""; apellido2="";
					nombre = document.getElementById('nombre_us2').value;
					apellido1 = document.getElementById('prim_apel_us2').value;
					apellido2 = document.getElementById('seg_apel_us2').value;
					grbNombresUs = trim(nombre) + ' '+ trim(apellido1) + ' ' + trim(apellido2);
					datosRad['grbNombresUs'] = grbNombresUs;
					datosRad['ccDocumento'] = document.getElementById('cc_documento_us2').value;
					ubicacion = document.getElementById('muni_us2').value;
					ubicacionM = ubicacion.split("-",4);
					datosRad['muniCodi'] = ubicacionM[2];
					datosRad['dptoCodi'] = ubicacionM[1];
					datosRad['idPais'] = ubicacionM[0];
					datosRad['idCont'] = document.getElementById('idcont2').value;
					funCodigo=0; oemCodigo=0; espCodigo=0; ciuCodigo=0;
					if(document.getElementById('tipo_emp_us2').value==0) 
						ciuCodigo=document.getElementById('documento_us2').value;
					if(document.getElementById('tipo_emp_us2').value==1) 
						espCodigo=document.getElementById('documento_us2').value;
					if(document.getElementById('tipo_emp_us2').value==2) 
						oemCodigo=document.getElementById('documento_us2').value;
					if(document.getElementById('tipo_emp_us2').value==6) 
						funCodigo=document.getElementById('documento_us2').value;
					datosRad['direccion'] = document.getElementById('direccion_us2').value;
					datosRad['codpostal'] = document.getElementById('codpostal_us2').value;
					datosRad['dirTelefono'] = document.getElementById('telefono_us2').value;
					datosRad['dirMail'] = document.getElementById('mail_us2').value;
					datosRad['dirNombre'] = document.getElementById('otro_us2').value;
					remoteRad.insertDireccionAjax(radiNumeRadi,2,0,datosRad['grbNombresUs'],datosRad['ccDocumento'],
						datosRad['muniCodi'],datosRad['dptoCodi'],datosRad['idPais'],datosRad['idCont'],funCodigo,oemCodigo,
						ciuCodigo, espCodigo,datosRad['direccion'],datosRad['codpostal'], datosRad['dirTelefono'],datosRad['dirMail'],datosRad['dirNombre']
					);
				}
			}
		</script>
	</head>
	<body bgcolor="#FFFFFF" onLoad="document.getElementById('grabarDir').style.visibility='hidden';">
		<div id="spiffycalendar" class="text"></div>
		<link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
		<script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>
		<link rel="stylesheet" href="../estilos/tabber.css" TYPE="text/css" MEDIA="screen">
		<script type="text/javascript" src="../js/tabber.js"></script>

<?php
error_reporting(7);
$ddate	= date('d');
$mdate	= date('m');
$adate	= date('Y');
$nurad	= trim($nurad);
$hora	= date('H:i:s');
$fechaf = $date.$mdate.$adate.$hora;

// aqui se busca el radicado para editar si viene la variable $Buscar
if($Buscar) {
	$docDia = $db->conn->SQLDate('d','a.RADI_FECH_OFIC');
	$docMes = $db->conn->SQLDate('m','a.RADI_FECH_OFIC');
	$docAno = $db->conn->SQLDate('Y','a.RADI_FECH_OFIC');
	$fRad = $db->conn->SQLDate('Y-m-d','a.RADI_FECH_RADI');
	if (!$nurad || strlen(trim($nurad))==0)
		$nurad="NULL";
	$query = "	SELECT 	A.*
						,$docDia AS DOCDIA
						,$docMes AS DOCMES
						,$docAno AS DOCANO
						,a.EESP_CODI
						,a.RA_ASUN
						,$fRad AS FECHA_RADICADO
				FROM	RADICADO A
				WHERE	A.RADI_NUME_RADI=$nurad";
	$rs=$db->conn->Execute($query);
	$varQuery = $query;
	$busqueda=$nurad;
	if(!$rs->EOF and is_numeric($busqueda))	{
		if($cursor)	{
			$Submit4 = "Modificar";
		}
		$asu=$rs->fields["RA_ASUN"];
		$tip_doc =$rs->fields["TDID_CODI"];
		$radicadopadre=$rs->fields["RADI_NUME_DERI"];
		$ane= $rs->fields["RADI_DESC_ANEX"];
		$codep=$rs->fields["DEPTO_CODI"];
		$pais=$rs->fields["RADI_PAIS"];
		$carp_codi = $rs->fields["CARP_CODI"];
		$cuentai = $rs->fields["RADI_CUENTAI"];
		$carp_per = $rs->fields["CARP_PER"];
		$depende=$rs->fields["RADI_DEPE_ACTU"];
		$tip_rem=$rs->fields["TRTE_CODI"]+1;
		$tdoc=$rs->fields["TDOC_CODI"];
		$med =$rs->fields["MREC_CODI"];
		$cod=$rs->fields["MUNI_CODI"];
		$coddepe=$rs->fields["RADI_DEPE_ACTU"];
		$codusuarioActu=$rs->fields["RADI_USUA_RADI"];
		$coddepe=$rs->fields["RADI_DEPE_ACTU"];
		$fechproc12=$rs->fields["DOCDIA"];
		$fechproc22=$rs->fields["DOCMES"];
		$fechproc32=$rs->fields["DOCANO"];
		$fechaRadicacion=$rs->fields["FECHA_RADICADO"];
		$espcodi =$rs->fields["EESP_CODI"];
		$fecha_gen_doc = "$fechproc12/$fechproc22/$fechproc32";
		include "busca_direcciones.php";
	}
	else {
		echo "<p><center>
			<table width='90%' class=borde_tab celspacing=5>
				<tr>
					<td class=titulosError>
						<center>No se han encontrado registros con numero de radicado <font color=blue>$nurad</font> 
						<br>Revise el radicado escrito, solo pueden ser Numeros de 15 digitos 
						<br><p><hr>
						<a href='edtradicado.php?fechaf=$fechaf&krd=$krd&drde=$drde'><font color=red>Intente de Nuevo</a></center>
					</td>
				</tr>
			</table></center>";
		if(!$rsJHLC) die("<hr>");
	}
}
// Fin de Busqueda del Radicado para editar
?>

  <script language="javascript">
  
<?php
if(!$fecha_gen_doc || $fecha_gen_doc=='//')
{	$fecha_busq = date("d-m-Y");
	if($ent!=2)$fecha_gen_doc = $fecha_busq;
}
?>

  //if($ent==2) $fecha_gen_doc="01-01-2010";
  var dateAvailable1 = new ctlSpiffyCalendarBox("dateAvailable1", "formulario", "fecha_gen_doc","btnDate1","<?=$fecha_gen_doc?>",scBTNMODE_CUSTOMBLUE);
  </script>

<?php
if($rad1 or $rad0 or $rad2) {
	if($rad1) $tpRadicado = "1";
	if($rad2) $tpRadicado = "2";
	if($rad0) $tpRadicado = "0";
	echo "<input type=hidden name=tpRadicado value=$tpRadicado>";
	$docDia = $db->conn->SQLDate('D','a.RADI_FECH_OFIC');
	$docMes = $db->conn->SQLDate('M','a.RADI_FECH_OFIC');
	$docAno = $db->conn->SQLDate('Y','a.RADI_FECH_OFIC');
	if (!$radicadopadre || strlen(trim($radicadopadre))==0)
		$radicadopadre="NULL";
	$query = "	select	A.*
						,$docDia AS DOCDIA
						,$docMes AS DOCMES
						,$docAno AS DOCANO
						,a.EESP_CODI
				FROM	RADICADO A
				WHERE	A.RADI_NUME_RADI=$radicadopadre";
	$varQuery = $query;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$rs=$db->conn->Execute($query);
	if(!$rs->EOF) {
		echo "<!-- No hay datos: $query -->";
	}
	if(!$Buscar and !$Submit4) {
		$varQuery = $query;
		$comentarioDev = 'Entro a Anexar un radicado ';
		$cuentaii =$rs->fields["RADI_CUENTAI"];
		if($cuentaii){
			$cuentai=$cuentaii;
		}
		$pnom=$rs->fields["RADI_NOMB"];
		$papl=$rs->fields["RADI_PRIM_APEL"];
		$sapl=$rs->fields["RADI_SEGU_APEL"];
		$numdoc=$rs->fields["RADI_NUME_IDEN"];
		$asu=$rs->fields["RA_ASUN"];
		$tel=$rs->fields["RADI_TELE_CONT"];
		$rem2=$rs->fields["RADI_REM"];
		$adress=$rs->fields["RADI_DIRE_CORR"];
	}
	$depende=$rs->fields["RADI_DEPE_ACTU"];
	$radi_usua_actu_padre=$rs->fields["RADI_USUA_ACTU"];
	$radi_depe_actu_padre=$rs->fields["RADI_DEPE_ACTU"];
	$tip_doc =$rs->fields["TDID_CODI"];
	$ane= $rs->fields["RADI_DESC_ANEX"];
	$cod=$rs->fields["MUNI_CODI"];
	$codep=$rs->fields["DPTO_CODI"];
	$pais=$rs->fields["RADI_PAIS"];
	$espcodi=$rs->fields["EESP_CODI"];
	if($noradicar2) {
		$fecha_gen_doc = $rs->fields["DOCDIA"] ."-".$rs->fields["DOCMES"] ."-".$rs->fields["DOCANO"];
		$fechproc12=$rs->fields["DOCDIA"];
		$fechproc22=$rs->fields["DOCMES"];
		$fechproc32=$rs->fields["DOCANO"];
	}
	$ruta_raiz = "..";
	$no_tipo = "true";
	include "busca_direcciones.php";
}
IF($rad1) {
	$encabezado = "<center><b>Copia de datos del Radicado  $radicadopadre ";
    $radicadopadre = 0;
	$tipoanexo = "1";
}
IF($rad0){
	$encabezado = "<center><b>Anexo de $radicadopadre ";
	$tipoanexo = "0";
	$radicadopadre_exist=1;
}
IF($rad2) {
	$encabezado = "<center><b>Documento Asociado de $radicadopadre ";
	if(!$Submit4 and !$Submit3){
		$cuentai = "";
	}
	$tipoanexo = "2";
 	$radicadopadre_exist=1;
}
IF($noradicar1)
	$radicadopadre_exist=0;
?>

  <script>
  function procEst2(formulario,tb)
  {
	var lista = document.formulario.codep.value;
	i = document.formulario.codep.value;
	if (i != 0) {
		var dropdownObjectPath = document.formulario.tip_doc;
		var wichDropdown = "tip_doc";
		var d=tb;
		var withWhat = document.formulario.codep.value;
		populateOptions2(wichDropdown, withWhat,tb);
	}
  }
  
  function populateOptions2(wichDropdown, withWhat,tbres)
  {
	r = new Array;
	i=0;
	if (withWhat == "2") {
		r[i++]=new Option("NIT", "1");
	}
	if (withWhat == "1") {
		document.formulario.submit();
		r[i++]=new Option("NIT","4");
		r[i++]=new Option("NUIR","5");
	}
	if (withWhat == "3") {
		r[i++]=new Option("CC", "0");
		r[i++]=new Option("CE", "2");
		r[i++]=new Option("TI", "1");
		r[i++]=new Option("PASAPORTE", "3");
	}
	if (i==0) {
		alert(i + " " + "Error!!!");
	}
	else {
		dropdownObjectPath = document.formulario.tip_doc;
		eval(document.formulario.tip_doc.length=r.length);
		largestwidth=0;
		for (i=0; i < r.length; i++) {
			eval(document.formulario.tip_doc.options[i]=r[i]);
			if (r[i].text.length > largestwidth) {
				largestwidth=r[i].text.length; 
			}
		}
		eval(document.formulario.tip_doc.length=r.length);
		//eval(document.myform.cod.options[0].selected=true);
	}
  }

  function vnum(formulario,n)
  {
	valor = formulario.elements[n].value;
	if (isNaN(valor)) {
		alert ("Dato incorrecto..");
		formulario.elements[n].value="";
		formulario.elements[n].focus();
		return false;
	}
	else
		return true;
  }

  function fech(formulario,n)
  {
	m=n-1;
	s=m-1;
	var f=document.formulario.elements[n].value;
	var meses=parseInt(document.formulario.elements[m].value);
	eval(lona=document.formulario.elements[n].length);
	eval(lonm=document.formulario.elements[m].length);
	eval(lond=document.formulario.elements[s].length);
	if(lona==44 || lonm==44 || lond==44) {
		jAlert('Fecha incorrecta  debe ser DD/MM/AAAA !!!', 'Alerta');
		document.formulario.elements[s].value="";
		document.formulario.elements[m].value="";
		document.formulario.elements[n].value="";
		document.formulario.elements[s].focus();
	}
	else {
		if ((f%4)==0){
			if(document.formulario.elements[m].value<13){
				switch(meses){
					case 12 : if(document.formulario.elements[s].value>31){
								alert ("Fecha incorrecta..");
								document.formulario.elements[s].value="";
								document.formulario.elements[m].value="";
								document.formulario.elements[n].value="";
								document.formulario.elements[s].focus();
								return false;
							  }
							  break;
					case 11 : if(document.formulario.elements[s].value>30){
								alert ("Fecha incorrecta..");
								document.formulario.elements[s].value="";
								document.formulario.elements[m].value="";
								document.formulario.elements[n].value="";
								document.formulario.elements[s].focus();
								return false;
							  }
							  break;
					case 10 : if(document.formulario.elements[s].value>31){
								alert ("Fecha incorrecta..");
								document.formulario.elements[s].value="";
								document.formulario.elements[m].value="";
								document.formulario.elements[n].value="";
								document.formulario.elements[s].focus();
								return false;
							  }
					  		  break;
					case 9 : if(document.formulario.elements[s].value>30){
								alert ("Fecha incorrecta..");
								document.formulario.elements[s].value="";
								document.formulario.elements[m].value="";
								document.formulario.elements[n].value="";
								document.formulario.elements[s].focus();
								return false;
							  }
					  		  break;
					case 8 : if(document.formulario.elements[s].value>31){
								alert ("Fecha incorrecta..");
								document.formulario.elements[s].value="";
								document.formulario.elements[m].value="";
								document.formulario.elements[n].value="";
								document.formulario.elements[s].focus();
								return false;
							  }
							  break;
					case 7 : if(document.formulario.elements[s].value>31){
								alert ("Fecha incorrecta..");
								document.formulario.elements[s].value="";
								document.formulario.elements[m].value="";
								document.formulario.elements[n].value="";
								document.formulario.elements[s].focus();
								return false;
							  }
					  		  break;
					case 6 : if(document.formulario.elements[s].value>30){
								alert ("Fecha incorrecta..");
								document.formulario.elements[s].value="";
								document.formulario.elements[m].value="";
								document.formulario.elements[n].value="";
								document.formulario.elements[s].focus();
								return false;
							  }
					  		  break;
					case 5 : if(document.formulario.elements[s].value>31){
								alert ("Fecha incorrecta..");
								document.formulario.elements[s].value="";
								document.formulario.elements[m].value="";
								document.formulario.elements[n].value="";
								document.formulario.elements[s].focus();
								return false;
							  }
					  		  break;
					case 4 : if(document.formulario.elements[s].value>30){
								alert ("Fecha incorrecta..");
								document.formulario.elements[s].value="";
								document.formulario.elements[m].value="";
								document.formulario.elements[n].value="";
								document.formulario.elements[s].focus();
								return false;
							  }
					  		  break;
					case 3 : if(document.formulario.elements[s].value>31){
								alert ("Fecha incorrecta..");
								document.formulario.elements[s].value="";
								document.formulario.elements[m].value="";
								document.formulario.elements[n].value="";
								document.formulario.elements[s].focus();
								return false;
							  }
					  		  break;
					case 2 : if(document.formulario.elements[s].value>29){
								alert ("Fecha incorrecta..");
								document.formulario.elements[s].value="";
								document.formulario.elements[m].value="";
								document.formulario.elements[n].value="";
								document.formulario.elements[s].focus();
								return false;
							  }
					  		  break;
					case 1 : if(document.formulario.elements[s].value>31){
								alert ("Fecha incorrecta..");
								document.formulario.elements[s].value="";
								document.formulario.elements[m].value="";
								document.formulario.elements[n].value="";
								document.formulario.elements[s].focus();
								return false;
							  }
					  		  break;
				}
			}
			else {
				alert("Fecha mes inexistente!!");
				document.formulario.elements[s].value="";
				document.formulario.elements[m].value="";
				document.formulario.elements[n].value="";
				document.formulario.elements[s].focus();
			}
		}
		else {
			if(document.formulario.elements[m].value<13){
				switch(meses){
					case 12 : if(document.formulario.elements[s].value>31){
								alert ("Fecha incorrecta..");
								document.formulario.elements[s].value="";
								document.formulario.elements[m].value="";
								document.formulario.elements[n].value="";
								document.formulario.elements[s].focus();
								return false;
							  }
					  		  break;
					case 11 : if(document.formulario.elements[s].value>30){
								alert ("Fecha incorrecta..");
								document.formulario.elements[s].value="";
								document.formulario.elements[m].value="";
								document.formulario.elements[n].value="";
								document.formulario.elements[s].focus();
								return false;
							  }
					  		  break;
					case 10 : if(document.formulario.elements[s].value>31){
								alert ("Fecha incorrecta..");
								document.formulario.elements[s].value="";
								document.formulario.elements[m].value="";
								document.formulario.elements[n].value="";
								document.formulario.elements[s].focus();
								return false;
							  }
							  break;
					case 9 : if(document.formulario.elements[s].value>30){
								alert ("Fecha incorrecta..");
								document.formulario.elements[s].value="";
								document.formulario.elements[m].value="";
								document.formulario.elements[n].value="";
								document.formulario.elements[s].focus();
								return false;
							  }
					  		  break;
					case 8 : if(document.formulario.elements[s].value>31){
								alert ("Fecha incorrecta..");
								document.formulario.elements[s].value="";
								document.formulario.elements[m].value="";
								document.formulario.elements[n].value="";
								document.formulario.elements[s].focus();
								return false;
							  }
							  break;
					case 7 : if(document.formulario.elements[s].value>31){
								alert ("Fecha incorrecta..");
								document.formulario.elements[s].value="";
								document.formulario.elements[m].value="";
								document.formulario.elements[n].value="";
								document.formulario.elements[s].focus();
								return false;
							  }
							  break;
					case 6 : if(document.formulario.elements[s].value>30){
								alert ("Fecha incorrecta..");
								document.formulario.elements[s].value="";
								document.formulario.elements[m].value="";
							  	document.formulario.elements[n].value="";
								document.formulario.elements[s].focus();
								return false;
							  }
					  		  break;
					case 5 : if(document.formulario.elements[s].value>31){
								alert ("Fecha incorrecta..");
								document.formulario.elements[s].value="";
								document.formulario.elements[m].value="";
								document.formulario.elements[n].value="";
								document.formulario.elements[s].focus();
							  	return false;
							  }
							  break;
					case 4 : if(document.formulario.elements[s].value>30){
								alert ("Fecha incorrecta..");
								document.formulario.elements[s].value="";
								document.formulario.elements[m].value="";
								document.formulario.elements[n].value="";
								document.formulario.elements[s].focus();
								return false;
							  }
					  		  break;
					case 3 : if(document.formulario.elements[s].value>31){
								alert ("Fecha incorrecta..");
								document.formulario.elements[s].value="";
								document.formulario.elements[m].value="";
								document.formulario.elements[n].value="";
								document.formulario.elements[s].focus();
								return false;
							  }
					  		  break;
					case 2 : if(document.formulario.elements[s].value>28){
								alert ("Fecha incorrecta..");
								document.formulario.elements[s].value="";
								document.formulario.elements[m].value="";
								document.formulario.elements[n].value="";
								document.formulario.elements[s].focus();
								return false;
							  }
							  break;
					case 1 : if(document.formulario.elements[s].value>31){
								alert ("Fecha incorrecta..");
								document.formulario.elements[s].value="";
								document.formulario.elements[m].value="";
								document.formulario.elements[n].value="";
								document.formulario.elements[s].focus();
								return false;
							  }
							  break;
				}
			}
			else {
				alert("Fecha mes inexistente!!");
				document.formulario.elements[s].value="";
				document.formulario.elements[m].value="";
				document.formulario.elements[n].value="";
				document.formulario.elements[s].focus();
			}
		}
	}
  }
  var contadorVentanas=0
  </script>

<?php
if ($Buscar1) {
	include "busca_direcciones.php";
}
$var_envio=session_name()."=".trim(session_id())."&ent=$ent&carp_per=$carp_per&carp_codi=$carp_codi&rad=$nurad&coddepe=$coddepe&depende=$depende";
?>

  <form action='radicar.php?<?=$var_envio?>'  method="post" name="formulario" id="formulario" class="borde_tab">
	<INPUT TYPE=HIDDEN NAME=radicadopadre value='<?=$radicadopadre ?>'>
	<input type=hidden name=tipoanexo value='<?=$tipoanexo ?>'>
	<input type=hidden name='noradicar' value='<?=$noradicar ?>'>
	<input type=hidden name='noradicar1' value='<?=$noradicar1 ?>'>
	<input type=hidden name='noradicar2' value='<?=$noradicar2 ?>'>
	<input type=hidden name='atrasRad0' value='<?=$rad0 ?>'>
	<input type=hidden name='atrasRad1' value='<?=$rad1 ?>'>
	<input type=hidden name='atrasRad2' value='<?=$rad2 ?>'>
	<input type=hidden name='faxPath' value='<?=$faxPath ?>'>

<?php
if($tpRadicado){
	echo "<input type=hidden name=tpRadicado value=$tpRadicado>";
}
?>

	<table width="99%"  border="0" align="center" cellpadding="1" cellspacing="1" class="borde_tab">
	 <tr>
	  <td width="6" class="titulos2">
	  	<a href='./radicar.php?<?=session_name()."=".session_id()?>&rad2=Asociado&krd=<?=$krd?>&ent=<?=$ent?>&rad1=<?=$atrasRad1?>
	  		&rad2=<?=$atrasRad2?>&rad0=<?=$atrasRad0?>&radicadopadre=<?=$radicadopadre?>&noradicar=<?=$noradicar?>
	  		&noradicar1=<?=$noradicar1?>&noradicar2=<?=$noradicar2?>'>Atras</a>
	  </td>
	  <td width="94%" align="center"  valign="middle" class="titulos2"> <b>
      
<?php
$query = "	SELECT	SGD_TRAD_CODIGO
					,SGD_TRAD_DESCR 
			FROM	sgd_trad_tiporad
			WHERE	SGD_TRAD_CODIGO=$ent";
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
$rs=$db->conn->Execute($query);
$tRadicacionDesc = $rs->fields["SGD_TRAD_DESCR"];
?>

	   MODULO DE RADICACION
      
<?=$tRadicacionDesc?>
	   (Dep
<?=$dependencia ?>
	   --
<?=$tpDepeRad[$ent]?>
	   ) </b>

<?php
if($nurad){
	echo "<b>Rad No" . $nurad;
	$ent = substr($nurad,-1);
}
?>
  
	   <br>
<?=$encabezado ?>
	  </td>
	 </tr>
	</table>
	<table  width=99% border="0" align="center" cellspacing="1" cellpadding="1" class="borde_tab" >
	 <tr valign="middle">
	  <td class="titulos5" width="15%" align="right">
		<font color="" face="Arial, Helvetica, sans-serif">
		 <span class="titulos5">Fecha: dd/mm/aaaa</span>
		</font>
	  </td>
	  <td class="listado5" width="15%">
	   <font color="" face="Arial, Helvetica, sans-serif">
		<input name="fechproc1" type="text" readonly id="fechproc13" size="2" maxlength="2" value="<?php echo $ddate;?>" class="tex_area">/
		<input name="fechproc2" type="text" readonly id="fechproc23" size="2" maxlength="2" value="<?php echo $mdate;?>" class="tex_area">/
		<input name="fechproc3" type="text" readonly id="fechproc33" size="4" maxlength="4" value="<?php echo $adate;?>" class="tex_area">
	   </font>
	  </td>
	  <td width="15%" class="titulos5" align="right">
	   <font color="" face="Arial, Helvetica, sans-serif" class="titulos5">Fecha Doc. dd/mm/aaaa </font>
	  </td>
	  <td width="15%" class="listado5">
	   <font color="" face="Arial, Helvetica, sans-serif">
		<script language="javascript">
			dateAvailable1.date = "<?=date('Y-m-d');?>";
			dateAvailable1.writeControl();
			dateAvailable1.dateFormat="dd-MM-yyyy";
		</script>
	   </font>
	   <font color="" face="Arial, Helvetica, sans-serif">&nbsp;</font>
	  </td>
	  <td width="15%" class="titulos5" align="right">Cuenta Interna, Oficio, Referencia</td>
	  <td width="15%" class="listado5">
	   <font face="Arial, Helvetica, sans-serif">
		<input name="cuentai" id="cuentai" type="text"  maxlength="20" class="tex_area" value='<?php echo $cuentai; ?>' >
	   </font>
	  </td>
	 </tr>
	</table>
	<table width="600" border="0" cellspacing="0" cellpadding="0">
	 <tr>
	  <td height="0"> 
	   <input name="VERIFICAR" type='hidden' class="ebuttons2" value="Verifique Radicaci&oacute;n">
	  </td>
	 </tr>
	</table>
  
	<table width="99%" align="center" border="0" cellspacing="0" cellpadding="0">
	 <tr valign="bottom">
	  <td height="10">
	   <table width="99%" align="center" border="0" cellspacing="0" cellpadding="0">
		<tr>
<?php
if($ent!=2)
	$img_remitente = "destinatario";
else
	$img_remitente = "remitente";
?>

		 <td width="523"  valign="bottom" >	</td>
		
<?php
if($ent!=2)
	$busq_salida="true";
else
	$busq_salida=""; 
?>

		</tr>
	   </table>
	  </td>
	 </tr>
	 <tr valign="top">
	  <td class="titulos5">
	   <div class="tabber" id="tab1">

<?php
for($i=1;$i<=3;$i++){
	if($i==1){
		$nombre = $nombre_us1;
		$documento = $documento_us1;
		$papel = $prim_apel_us1;
		$grbNombresUs1 = trim($nombre_us1) . " " . trim($prim_apel_us1) . " ". trim($seg_apel_us1);
		$sapel = $seg_apel_us1;
		$tel = $telefono_us1;
		$dir = $direccion_us1;
		$codPos = $codigo_postal_us1;
		if (!$codPos){
			$codPos = $codpostal_us1;
		}
	  	$mail = $mail_us1;
	  	if($mailFrom)
	  		$mail = $mailFrom;
		$muni = $muni_us1;
		$codep = $codep_us1;
		$idp = $idpais1;
		$idc = $idcont1;
		$tipo = $tipo_emp_us1;
		$cc_documento = $cc_documento_us1;
		$otro = $otro_us1;
	}
	if($i==2) {
		$nombre = $nombre_us2;
		$documento = $documento_us2;
		$cc_documento = $cc_documento_us2;
		$papel = $prim_apel_us2;
		$sapel = $seg_apel_us2;
		$grbNombresUs2 = trim($nombre_us2) . " " . trim($prim_apel_us2) . " ". trim($seg_apel_us2);
		$tel = $telefono_us2;
		$dir = $direccion_us2;
		$codPos = $codigo_postal_us2;
		$mail = $mail_us2;
		$muni = $muni_us2;
		$codep = $codep_us2;
		$idp = $idpais2;
		$idc = $idcont2;
		$tipo = $tipo_emp_us2;
		$otro = $otro_us2;
	}
	if($i==3){
		$nombre = $nombre_us3;
		$documento = $documento_us3;
		$cc_documento = $cc_documento_us3;
		$grbNombresUs3 = trim($nombre_us3) . " " . trim($prim_apel_us3) . " ".trim($seg_apel_us3);
		$papel = $prim_apel_us3;
		$sapel = $seg_apel_us3;
		$tel = $telefono_us3;
		$dir = $direccion_us3;
		$codPos = $codigo_postal_us3;
		$mail = $mail_us3;
		$muni = $muni_us3;
		$codep = $codep_us3;
		$idp = $idpais3;
		$idc = $idcont3;
		$tipo = $tipo_emp_us3;
		$otro = $otro_us3;
	}
	if($tipo==1 or $i==3){
		$lbl_nombre = "Raz&oacute;n Social";
		$lbl_apellido = "Sigla";
		$lbl_nombre2 = "Rep. Legal";
	}
	else{
		$lbl_nombre = "Nombres";
		$lbl_apellido = "Primer Apellido";
		$lbl_nombre2 = "Segundo Apellido";
	}
	$bloqEdicion="";
	if ($i==3){
		$bloqEdicion = "readonly='true'";
	}
	$titulo = $tip3Nombre[$i][$ent];
	if(!$titulo)
		$titulo = "?? $i";
?>

	   <div class="tabbertab" title="<?=$titulo?>">
	   <table width=100%  id='pes<?=$i?>' class="t_bordeGris" align="center" cellpadding="0" cellspacing="1">
		<tr class=listado2>
		 <td class="titulos5"  align="right">Documento</td>
		 <td bgcolor="#FFFFFF"  class="listado5">
		  <input type=text name='cc_documento_us<?=$i?>' id='cc_documento_us<?=$i?>' value='<?=$cc_documento?>' readonly class="tex_area">
		  <input type=text name='documento_us<?=$i ?>' id='documento_us<?=$i ?>' value='<?=$documento?>' readonly class="tex_area" size="3">
		 </td>
		 <td class="titulos5"  align="right">
		  <font face="Arial, Helvetica, sans-serif" class="etextomenu">Tipo</font>
		 </td>
		 <td width="45%"  bgcolor="#FFFFFF" class="listado5">
		  <select name="tipo_emp_us<?=$i?>" id="tipo_emp_us<?=$i?>" class="select" onfocus="this.defaultIndex=this.selectedIndex;" onchange="this.selectedIndex=this.defaultIndex;">
	
<?php
	if($i==1){ if($tipo_emp_us1==0){ $datos = " selected "; } else{ $datos= ""; } }
	if($i==2){ if($tipo_emp_us2==0){ $datos = " selected "; } else{ $datos= ""; } }
	if($i==3){ if($tipo_emp_us3==0){ $datos = " selected "; } else{ $datos= ""; } }
?>
		  <option value=0 <?=$datos ?>>USUARIO  </option>
	
<?php
	if($i==1){ if($tipo_emp_us1==1){ $datos = " selected "; } else{ $datos= ""; } }
	if($i==2){ if($tipo_emp_us2==1){ $datos = " selected "; } else{ $datos= ""; } }
	if($i==3){ if($tipo_emp_us3==1){ $datos = " selected "; } else{ $datos= ""; } }
?>

		  <option value=1 <?=$datos ?>>ENTIDADES  </option>

<?php
	if($i==1){ if($tipo_emp_us1==2){ $datos = " selected "; } else{ $datos= ""; } }
	if($i==2){ if($tipo_emp_us2==2){ $datos = " selected "; } else{ $datos= ""; } }
	if($i==3){ if($tipo_emp_us3==2){ $datos = " selected "; } else{ $datos= ""; } }
?>
		  <option value=2 <?=$datos ?>>EMPRESAS  </option>

<?php
	if($i==1){ if($tipo_emp_us1==6){ $datos = " selected "; } else{ $datos= ""; } }
	if($i==2){ if($tipo_emp_us2==6){ $datos = " selected "; } else{ $datos= ""; } }
	if($i==3){ if($tipo_emp_us3==6){ $datos = " selected "; } else{ $datos= ""; } }
?>

		  <option value=6 <?=$datos ?>>FUNCIONARIOS  </option>
		  </select>
		 </td>
		 <td align="right">
		  <input type="button" name="Button" value="BUSCAR" class="botones_funcion" onClick="start('buscar_usuario.php?krd=<?=$krd?>&nombreTp1=<?=$nombreTp1?>&nombreTp2=<?=$nombreTp2?>&nombreTp3=<?=$nombreTp3?>&busq_salida=<?=$busq_salida?>&ent=<?=$ent?>',1024,400);" align="right">
		  <input type='hidden' name='depende22' value="<?php echo $depende;?>">
		 </td>
		</tr>
		<tr class=e_tablas>
		 <td width="13%" class="titulos5" align="right">
		  <font face="Arial, Helvetica, sans-serif" class="etextomenu"><?=$lbl_nombre ?> </font>
		 </td>
		 <td width="30%" bgcolor="#FFFFFF" class="listado5">
		  <INPUT type=text name='nombre_us<?=$i ?>' id='nombre_us<?=$i ?>' value='<?=$nombre ?>' readonly class="tex_area" size=40>
		 </td>
		 <td width="10%" class="titulos5" align="right">
		  <font face="Arial, Helvetica, sans-serif" class="etextomenu"><?=$lbl_apellido ?></font>
		 </td>
		 <td colspan="3" bgcolor="#FFFFFF" class="listado5">

<?php
	if($i==4){
		$ADODB_COUNTRECS = true;
		$query = "	SELECT	PAR_SERV_NOMBRE
							,PAR_SERV_CODIGO 
					FROM 	PAR_SERV_SERVICIOS
					ORDER BY PAR_SERV_NOMBRE";
		$rs=$db->conn->Execute($query);
		$numRegs = "! ".$rs->RecordCount();
		$varQuery = $query;
		print $rs->GetMenu2("sector_us$i", "sector_us$i", "0:-- Seleccione --", false,"","onChange='procEst(formulario,18,$i )' class='ecajasfecha'");
		$ADODB_COUNTRECS = false;
		if($mailFrom and $i==1){
			$mail = $mailFrom;
		}
?>
		  <select name="sector_us<?=$i ?>"  id="sector_us<?=$i ?>" class="select">
<?php

		while(!$rs->EOF){
			$codigo_sect = $rs->fields["PAR_SERV_CODIGO"];
			$nombre_sect = $rs->fields["PAR_SERV_NOMBRE"];
			echo "<option value=$codigo_sect>$nombre_sect</option>";
			$rs->MoveNext();
		}
?>

		  </select>

<?php
	}
	else{
?>

		  <INPUT type=text name='prim_apel_us<?=$i ?>' id='prim_apel_us<?=$i ?>' value='<?=$papel ?>' class="tex_area"  readonly  size="35">
<?php
	}
?>
		 </td>
		</tr>
		<tr class=e_tablas>
		 <td width="10%" class="titulos5"  align="right"><font face="Arial, Helvetica, sans-serif" class="etextomenu"><?=$lbl_nombre2 ?></font></td>
		 <td width="30%" bgcolor="#FFFFFF" class="listado5">
		  <input type=text name='seg_apel_us<?=$i ?>' id='seg_apel_us<?=$i ?>' value='<?=$sapel ?>' readonly class="tex_area" size=40>
		 </td>
		 <td width="10%" class="titulos5"  align="right">
		  <font face="Arial, Helvetica, sans-serif" class="etextomenu">Tel&eacute;fono</font>
		 </td>
		 <td colspan="3" bgcolor="#FFFFFF"  class="listado5">
		  <input type=text name='telefono_us<?=$i?>' id='telefono_us<?=$i?>' value='<?=$tel?>' <?=$bloqEdicion?> class="tex_area" size=35>
		 </td>
		</tr>
		<tr class=e_tablas>
		 <td width="10%" class="titulos5"  align="right">
		  <font face="Arial, Helvetica, sans-serif" class="etextomenu">Direcci&oacute;n</font>
		 </td>
		 <td width="30%" bgcolor="#FFFFFF"  class="listado5">
		  <INPUT type=text name='direccion_us<?=$i?>' id='direccion_us<?=$i?>' value='<?=$dir?>' <?=$bloqEdicion?> class="tex_area" size=40>
		 </td>
		 <td width="10%" class="titulos5"  align="right">
		  <font face="Arial, Helvetica, sans-serif" class="etextomenu">C&oacute;digo Postal </font></td>
		 <td bgcolor="#FFFFFF"  class="listado5">
		  <INPUT type=text name='codpostal_us<?=$i ?>'  id='codpostal_us<?=$i ?>' value='<?=$codPos ?>' readonly class="tex_area" size="7" maxlength="7">
		 </td>
		 </tr>
		 <tr class=e_tablas>
		 <td width="10%" class="titulos5"  align="right">
		  <font face="Arial, Helvetica, sans-serif" class="etextomenu">Mail </font>
		 </td>
		 <td bgcolor="#FFFFFF"  class="listado5">
		  <INPUT type='text' name='mail_us<?=$i ?>'  id='mail_us<?=$i ?>' value='<?=$mail ?>' <?=$bloqEdicion?> readonly class="tex_area" size=35>
		 </td>

<?php
	if($i!=3){
?>

		<tr class=e_tablas>
		 <td width="13%" class="titulos5"  align="right" >
		  <font face="Arial, Helvetica, sans-serif" class="etextomenu">Dignatario</font>
		 </td>
		 <td bgcolor="#FFFFFF"  class="listado5" colspan="3">

		  <INPUT type='text' name='otro_us<?=$i?>' id='otro_us<?=$i?>' value="<?php echo htmlspecialchars(stripcslashes($otro));?>" class='tex_area' size='40' maxlength='50'>
		 </td>
		</tr>

<?php
	}
?>

		<tr class=e_tablas>
		 <td width="10%" class="titulos5"  align="right">
		  <font face="Arial" class="etextomenu">Continente</font>
		 </td>
		 <td width="20%" bgcolor="#FFFFFF"  class="listado5">
<?php
	/*  En este segmento trabajaremos macrosusticion, lo que en el argot php se denomina Variables variables.
	*	El objetivo es evitar realizar codigo con las mismas asignaciones y comparaciones cuya diferencia es el
	*	valor concatenado de una variable + $i.
	*/
	$var_cnt = "idcont".$i;
	$var_pai = "idpais".$i;
	$var_dpt = "codep_us".$i;
	$var_mcp = "muni_us".$i;

	/*	Se crean las variables cuyo contenido es el valor por defecto para cada combo, esto segun el siguiente orden:
	*	1. Se pregunta si existe idcont1, idcont2 e idcont3 (segun iteraccion del ciclo), si es asi se asigna a $contcodi.
	*	2. Sino existe (osea que no viene de buscar_usuario.php) se pregunta si existe "localidad" y se asigna el
	*	   respectivo codigo; de ser negativa la "localidad", $contcodi toma el valor de 0. Esto para cada
	*	   variable de continente, pais, dpto y mncpio respectivamente.
	*/

	(${$var_cnt}) ? $contcodi = ${$var_cnt} : ($_SESSION['cod_local'] ? $contcodi = (substr($_SESSION['cod_local'],0,1)*1) : $contcodi = 0 ) ;
	(${$var_pai}) ? $paiscodi = ${$var_pai} : ($_SESSION['cod_local'] ? $paiscodi = (substr($_SESSION['cod_local'],2,3)*1) : $paiscodi = 0 ) ;
	(${$var_dpt}) ? $deptocodi = ${$var_dpt} : ($_SESSION['cod_local'] ? $deptocodi = $paiscodi."-".(substr($_SESSION['cod_local'],6,3)*1) : $deptocodi = 0 ) ;
	(${$var_mcp}) ? $municodi = ${$var_mcp} : ($_SESSION['cod_local'] ? $municodi = $deptocodi."-".substr($_SESSION['cod_local'],10,3)*1 : $municodi = 0 ) ;

	//	Visualizamos el combo de continentes.
	echo "<SELECT NAME=\"idcont$i\" ID=\"idcont$i\" CLASS=\"select\" onchange=\"cambia(this.form, 'idpais$i', 'idcont$i')\">";
	/*foreach ($vcontiv as $key => $value) {
		echo "<option value='".$key."'>".$value."</option>";
	}*/
	echo "</SELECT>";
?>
		 </td>
		 <td width="10%" class="titulos5"  align="right"><font face="Arial" class="etextomenu">Pa&iacute;s</font></td>
		 <td  colspan="3" bgcolor="#FFFFFF"  class="listado5">
<?php
	//Visualizamos el combo de paises.
	echo "<SELECT NAME=\"idpais$i\" ID=\"idpais$i\" CLASS=\"select\" onchange=\"cambia(this.form, 'codep_us$i', 'idpais$i')\"></SELECT>";
?>	

		 </td>
		</tr>
		<tr>
		 <td width="10%" class="titulos5"  align="right"> <font face="Arial" class="etextomenu">Departamento</font> </td>
		 <td width="20%" bgcolor="#FFFFFF"  class="listado5">

<?php
	echo "<SELECT NAME=\"codep_us$i\" ID=\"codep_us$i\" CLASS=\"select\" onchange=\"cambia(this.form, 'muni_us$i', 'codep_us$i')\"></SELECT>";
?>
		 </td>
		 <td width="10%" class="titulos5"  align="right"><font face="Arial" class="etextomenu">Municipio</font></td>
		 <td  colspan="3" bgcolor="#FFFFFF"  class="listado5">

<?php
	echo "<SELECT NAME=\"muni_us$i\" ID=\"muni_us$i\" CLASS=\"select\" ></SELECT>";
	$municodi=0;$muninomb="";$deptocodi=0;
?>

		 </td>
		</tr>
	   </table>
	   </div>

<?php
}
unset($contcodi);
unset($paiscodi);
unset($deptocodi);
unset($municodi);
if($tipoMedio=="eMail"){
	$asu = trim($mailAsunto);
}
?>

	   </div>
	   <table width=100% border="0" class="borde_tab" align="center">
		<tr>
		 <td class="titulos5" width="25%" align="right"> 
		  <font color="" face="Arial, Helvetica, sans-serif" class="etextomenu">Asunto</font>
		 </td>
		 <td width="75%" class="listado5">
		  <textarea name="asu" id="asu" cols="70" class="tex_area" rows="2"><?php echo htmlspecialchars(stripcslashes($asu));?></textarea>
		 </td>
		</tr>
	   </table>
	   <table width=100% border="0" cellspacing="1" cellpadding="1" class="borde_tab" align="center">
		<!--DWLayoutTable-->
		<tr>
		 <td width="150" class="titulos5" align="right">
		  <font color="" face="Arial, Helvetica, sans-serif" class="etextomenu">
		
<?php
if($ent==2)	{
	echo "Medio Recepci&oacute;n";
}
else {
	echo "Medio Env&iacute;o";
}

// Si la variable $faxPath viene significa que el tipo de recepcion es fax Por eso $med se coloca en 2
if($faxPath)
	$med=2;
if($tipoMedio)
	$med=4;
?>
		  </font>
		 </td>
		 <td valign="top" class="listado5">

<?php
if ($ent == 2) {
    $query = "SELECT M.MREC_DESC, M.MREC_CODI
                   FROM MEDIO_RECEPCION M WHERE MREC_ESTADO = 1 ORDER BY 1 ";
    $opcMenu = "0:<< Seleccione >>";
} else {
    $query = "SELECT F.SGD_FENV_DESCRIP, F.SGD_FENV_CODIGO
                   FROM SGD_FENV_FRMENVIO F INNER JOIN SGD_FRMENVIO_TIPOSRAD T ON F.SGD_FENV_CODIGO = T.SGD_FENV_CODIGO
                    WHERE F.SGD_FENV_ESTADO = 1 AND T.SGD_TRAD_CODIGO = $ent ORDER BY 1 ";
    $opcMenu = "0:<< Seleccione >>";
}
$rs=$db->conn->Execute($query);
$varQuery = $query;
if($rs){
	print $rs->GetMenu2("med", $med, "$opcMenu", false,""," id=med  class='select' " );
}
?>

		 </td>
		 <td  class="titulos5" align="center"> <font face="Arial, Helvetica, sans-serif" class="etextomenu">Tipo Doc</font> </td>
		 <td valign="top" class="listado5"> 
		  <font color="">
		   <input name="hoj" type=hidden value="<?php echo $hoj; ?>">

<?php
$query = "	SELECT	SGD_TPR_DESCRIP
					,SGD_TPR_CODIGO 
			FROM	SGD_TPR_TPDCUMENTO 
			WHERE	SGD_TPR_TP$ent='1'
				 	and SGD_TPR_RADICA='1' 
			ORDER BY SGD_TPR_DESCRIP ";
$opcMenu = "0:-- Seleccione un tipo --";
$fechaHoy = date("Y-m-d");
$fechaHoy = $fechaHoy . "";
$ADODB_COUNTRECS = true;
//$db->conn->debug=true;
$rs=$db->conn->Execute($query);
if ($rs && !$rs->EOF ){
	$numRegs = "!".$rs->RecordCount();
	$varQuery = $query;
	print $rs->GetMenu2("tdoc", $tdoc, "$opcMenu", false,"","class='ecajasfecha' " );
}
else {
	$tdoc = 0;
}
$ADODB_COUNTRECS = false;

$rst = $db->conn->Execute("SELECT SGD_DCAU_DESCRIP, SGD_DCAU_CODIGO, SGD_DCAU_PQRDESC FROM SGD_DCAU_CAUSAL WHERE SGD_DCAU_PQR=1 AND SGD_DCAU_ESTADO = 1 ORDER BY SGD_DCAU_ORDEN");
$cmbTemas = "<select class='form-control' name='cmbTema' id='cmbTema' required>";
$cmbTemas.= "<option value=''>Seleccione Tema</option>";
while ($vector = $rst->fetchRow()) {
    $cmbTemas .= "<option value='" . $vector['SGD_DCAU_CODIGO'] . "'>" . $vector['SGD_DCAU_DESCRIP'] . "</option>";
}
$cmbTemas .= "</select>";

?>

		  </font>
		 </td>
		</tr>
		<tr>
		 <td  class="titulos5" width="150" align="right" colspan=1>
		  <font color="" face="Arial, Helvetica, sans-serif" class="etextomenu"> Desc Anexos</font>
		 </td>
		 <td>
		  <input name="ane" id="ane" type="text" size="72" class="tex_area" value="<?php echo htmlspecialchars(stripcslashes($ane));?>">
		 </td>
		</tr>
		 <!-- /** Modificado Supersolidaria 01-Nov-2006
        		* Datos del funcionario que tiene a cargo una entidad y la dependencia a la
        		* que pertenece.
				*/-->
		<?php 
		if ($ent == 2) {
		?>
		<tr>
			<td class="titulos5" width="150" align="right" colspan=1>
				 <font color="" face="Arial, Helvetica, sans-serif" class="etextomenu"> Tema</font>
			</td>
			<td>
				<?php echo $cmbTemas; ?>
			</td>
		</tr>
		<?php
		}
		?>
<?php
switch( $db->entidad ) {
	case 'SES':
?>
		<tr>
		 <td  class="titulos5" align="center">
		  <font color="" face="Arial, Helvetica, sans-serif" class="etextomenu"> Funcionario Encargado </font>
		  <font color="" face="Arial, Helvetica, sans-serif">
		   <input id="supervisor_us" name="supervisor_us" type="text" size="80" class="tex_area" value="<?=$supervisor_us;?>" readonly>
		  </font>
		 </td>
		</tr>
    
<?php
	break;
}
?>

		<tr>
		 <td class="titulos5" align="right" >
		  <font color="" face="Arial, Helvetica, sans-serif" class="etextomenu"> Dependencia</font>
		 </td>
		 <td>
		  <font color="" face="Arial, Helvetica, sans-serif">

<?php
// Busca las dependencias existentes en la Base de datos...
if($radi_depe_actu_padre and $tipoanexo==0 and !$coddepeinf)
	$coddepe = $radi_depe_actu_padre;
if(!$coddepe){
	$coddepe=$dependencia;
}

/** Solo los documentos de entrada (ent=2) muestra la posibilidad de redireccion a otras dependencias
  * @queryWhere String opcional para la consulta.
  */
	
if($ent!=2){
	$queryWhere =" where depe_codi=$dependencia";
}
else{
	$queryWhere = "where dependencia_estado = 2";
}
// Modificado SGD 11-Jul-2007
//$query = "select DEPE_NOMB,DEPE_CODI from dependencia $queryWhere order by depe_nomb";
switch( $GLOBALS['entidad'] ) {
	case 'SGD':
		$query = "	SELECT	".$db->conn->Concat( "DEPE_CODI", "'-'", "DEPE_NOMB" ).", DEPE_CODI
					FROM 	DEPENDENCIA
					$queryWhere
					ORDER BY DEPE_CODI, DEPE_NOMB";
		break;
	default:
		$query = "	SELECT	DEPE_NOMB
							,DEPE_CODI
					FROM	DEPENDENCIA
					$queryWhere
					ORDER BY DEPE_NOMB";
}
						
						

$ADODB_COUNTRECS = true;
$rs=$db->conn->Execute($query);
$numRegs = "!".$rs->RecordCount();
$varQuery = $query;
$comentarioDev = "Muestra las dependencias";

if($ent!=2){
	$opcion = '<option value="' .$_SESSION["codusuario"]. '">' .$_SESSION["usua_nomb"]. '</option>'; 
	
}

if ($ent!=2){
	print $rs->GetMenu2("coddepe",$_SESSION["codusuario"],"", false,false," id=coddepe class='select'");
}
else{
	print $rs->GetMenu2("coddepe","", "0:-- Seleccione una Dependencia --", false,false," id=coddepe class='select' ");
}
$ADODB_COUNTRECS = false;
 
?>
				</font><br>
			</td>
		</tr>
		<tr>
			<td class="titulos5" align="right" >
				<font color="" face="Arial, Helvetica, sans-serif" class="etextomenu"> Usuario: </font>
			</td>
			<td>
				<select name=listaUsu id=listaUsu>	
					<?php
					if ($ent!=2){
						echo $opcion;
					}
					?>
				</select>
			</td>
		</tr>
		
		<tr>
			
		</tr>

<?php
// Comprueba si el documento es una radicacion nueva de entrada....
if($tipoanexo==0 and $radicadopadre and !$radicadopadreseg and (!$Submit3  and !$Submit4)){
?>

		<tr>
		 <td class="titulos5" align="center" colspan=2>

<?php
	if($radi_depe_actu_padre==999){
		echo "<font color=red >Documento padre se encuentra en Archivo</font>";
	}
	elseif($radi_depe_actu_padre and $rad0)	{
		$query= "	SELECT 	USUA_NOMB
							,USUA_CODI
					FROM	USUARIO
					WHERE	DEPE_CODI=$radi_depe_actu_padre
             				AND USUA_CODI=$radi_usua_actu_padre";
		$ADODB_COUNTRECS = true;
    	//$db->conn->debug = true;
		$rs=$db->conn->Execute($query);
		$numRegs = "!".$rs->RecordCount();
		$ADODB_COUNTRECS = false;
		$varQuery = $query;
		$comentarioDev = "Muestra las dependencias";
		$usuario_padre = $rs->fields["USUA_NOMB"];
		$cod_usuario_inf = $rs->fields["USUA_CODI"];
		echo "</b></font>El Usuario Actual de $radicadopadre es $usuario_padre";
		$coddepeinf = $radi_depe_actu_padre;
		$informar_rad = "Informar";
		$observa_inf = "(Se ha generado un anexo pero ha sido enviado a la dependencia $coddepe)";
	}
?>

		 </td>
		</tr>
<?PHP
}
?>

		<tr>
		 <td colspan="4" class="listado5" >
		  <center>
<?php
if(!$Submit3 and !$Submit4){
?>

		  <input type='button' id='Submit33' name='Submit33' value='Radicar' class="botones_largo" onClick="radicar_doc();"> <br>
		  <input type='button' id='grabarDir' name='grabarDir' value='Modificar f' class="botones_largo" onClick="grabarDirecciones(document.getElementById('numeroRadicado').value);">
		  <input type=hidden  name=numeroRadicado id=numeroRadicado><br>
		  <div id="noRadicado" style="border: 3px coral solid; width: 400px;" > </div>
<?php
}
else{
	$varEnvio = session_name()."=".session_id()."&faxPath&leido=no&krd=$krd&faxPath=$faxPath&verrad=$nurad&nurad=$nurad&ent=$ent";
?>

		  <a href="hojaResumenRad.php?<?=$varEnvio?>" target="HojaResumen<?=$nurad?>" class=vinculos>Ver Hoja Resumen </a> <br>
		  <a href="stickerWeb/index.php?<?=$varEnvio?>&alineacion=left" target="Sticker<?=$nurad?>" class=vinculos>Ver Sticker </a> - 
		  <a href="stickerWeb/index.php?<?=$varEnvio?>&alineacion=Center" target="Sticker<?=$nurad?>" class=vinculos>StickerCentrado </a>
		   <input type='hidden' id='Submit4'  name='Submit4' value='MODIFICAR DATOS' class='ebuttons2'>
<?php
}
?>

		  </center>
		 </td>
		</tr>

<?php
// Aqui valida si ya radico para dejar informar o Anexat archivos para ras. de Salida.
//if(($Submit4 or $Submit3) AND !$Buscar){
if($ent==1 and !$Submit3) {
?>

		<tr bgcolor=white>
		 <td class="titulos5" colspan="5" align="center">
		  <font color="" face="Arial, Helvetica, sans-serif" class="etextomenu"> </font>
		 </td>
		</tr>
		<tr>
		 <td colspan="5">

<?php
	$ruta_raiz = "..";
	$radicar_documento = "true";
	if($num_archivos==1 and $radicado=="false")	{
		$generar_numero = "no";
		$vp = "n";
		$radicar_a="$nurad";
		error_reporting(0);
	}
?>
		 </td>
		</tr>

<?php
}
/** if que evita que salga Informar a Usuario si no es un Radicado de entrada
  * La razon es Muy simple, en salida, memos o cualquier otro el documento puede estar
  * en proyeccion y solo debe informarse cuando el usuario este deacuerdo por el modulo de carpetas normal
  * @autor Jairo Losada - Rq DNP 09/2009
  */
if($ent==2) {
	$scriptCargarUsuarios = " onClick=".'"'."remote.getUsuarios('usuariosInformar',document.getElementById('coddepeinf').value,document.getElementById('accionReasignar').checked)".'";';
?>

		<tr>
		 <td class="titulos5" width=10 colspan=3 >
		  <table class='borde_tab'>
		   <tr>
			<td class=listado2>
			 <input type='radio' name='accionIR' checked id='accionInformar' value='InformarOtrosD' <?=$scriptCargarUsuarios ?>> Informar a:
			</td>
			<td> </td>
			<td class=listado2>
			 <input TYPE='radio' name='accionIR' id='accionReasignar' value='ReasignarOtrosD' <?=$scriptCargarUsuarios ?>> Derivar a:
			</td>
		   </tr>
		  </table>
		 </td>
		</tr>
	   </table>
	   <table width=100% class=borde_tab>
		<tr>
		 <td>

<?php
	$query = "	SELECT	DEPE_NOMB
						,DEPE_CODI
           		FROM	DEPENDENCIA
           		WHERE	dependencia_estado=2
           		ORDER BY DEPE_NOMB";
	$rs=$db->conn->Execute($query);
	$varQuery = $query;
	print $rs->GetMenu2("coddepeinf", $coddepeinf, false, true,5," $scriptCargarUsuarios class='select' id='coddepeinf'");
?>

		 </td>
         <td align=center class=titulos2 colspan=3>	Seleccione los Usuarios<br>
		  <select name="usuariosInformar" id="usuariosInformar" size="5" onclick="remote.informarUsuario('usuariosInformados'
    			,document.getElementById('numeroRadicado').value,'<?=$krd?>','<?=$dependencia?>','<?=$codusuario ?>'
    			,document.getElementById('coddepeinf').value,document.getElementById('usuariosInformar').value, 'Doc Radicado'
    			,document.getElementById('accionInformar').checked, document.getElementById('accionReasignar').checked );"
    			class="select" >
		  </select>
		 </td>	
		</tr>

<?php
}
// Fin de if que evita que salga si no es un Radicado de entrada
?>
	   </table>
       <div id="usuariosInformados"> </div>
       <div id="usuariosReasignados"></div>
	   <input type='hidden' name='depende' value='<?php echo $depende; ?>'><BR>
	  </td>
	 </tr>
	</table>
   <br>
  </form>

<?php

$verrad = $nurad;
$radi = $nurad;
$contra = $drde;
$tipo = 1;
if($Submit3 or $Submit4 or $rad0 or $rad1 or $rad2) { 
	echo "<script language='JavaScript'>";
	for ($i=1; $i<=3; $i++){
		$var_pai = "idpais".$i;
		$var_dpt = "codep_us".$i;
		$var_mcp = "muni_us".$i;
		$muni_tmp = ${$var_mcp};
		if (!(is_null($muni_tmp))){
			echo "\n";
			echo "cambia(document.formulario, 'idpais$i', 'idcont$i');
			formulario.idpais$i.value = ${$var_pai};
			cambia(document.formulario, 'codep_us$i', 'idpais$i');
			formulario.codep_us$i.value = '${$var_dpt}';
			cambia(document.formulario, 'muni_us$i', 'codep_us$i');
			formulario.muni_us$i.value = '${$var_mcp}';";
		}
	}
	echo "</script>";
}
?>
 </body>
</html>