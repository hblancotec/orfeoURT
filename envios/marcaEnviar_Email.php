<?php
    session_start();
    $ruta_raiz = "..";


    if (!$_SESSION['dependencia'] || !$_SESSION['usua_doc'])   include "../rec_session.php";
?>
<html>
<head>
<title>Orfeo.  Envio de Documentos por E-Mail</title>
<link href="../estilos/orfeo.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php

include "../config.php";
include_once("$ruta_raiz/class_control/firmaRadicado.php");
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
require_once("$ruta_raiz/class_control/Radicado.php");
require_once("$ruta_raiz/class_control/CombinaError.php");


$db = new ConnectionHandler("$ruta_raiz");
$objFirma = new  FirmaRadicado($db);
$radObjeto = new Radicado($db);
$servBodega = $servWebOrfeo . "/bodega";
$servBodega = str_replace("/", "|", $servBodega);

$servidorSmtp 	= "172.16.1.92:25";
//$servidorSmtp 	= "poseidon.dnp.ad:25";
include "class.phpmailer.php";

// historico
include ("$ruta_raiz/include/tx/Tx.php");
$hist = new Historico($db);

function enviarCorreo($verradicado2,
			 			$correo,
			 			$nombreEmisor,
			 			$Nomb_usua,
			 			$Email_usua,
						$servidorSmtp,
						$adjuntos,
						$ext,
						$correocopia,
						$nurad){

		$mail = new PHPMailer();

		$cuerpo = "<br>El Departamento Nacional de Planeaci&oacute;n <br> ha dado respuesta a su solicitud No. " . $nurad . " mediante el oficio No.
		 " . $verradicado2 . ", la cual tambi�n puede ser consultada en el portal Web del DNP.</p><br>";
		$mail->Mailer   = "smtp";
		$mail->From 	= $correo;
		$mail->FromName = $nombreEmisor;
		$mail->Host 	= $servidorSmtp;
		$mail->Mailer   = "smtp";
		$mail->SMTPAuth = "true";
		$mail->Subject 	= "Respuesta al radicado ".$nurad." Departamento de Planeacion Nacional";
		$mail->AltBody = "Para ver el mensaje, por favor use un visor de E-mail compatible!";
		$mail->Body 	= $cuerpo;
		$mail->SMTPOptions = array(
		    'ssl' => array(
		        'verify_peer' => false,
		        'verify_peer_name' => false,
		        'allow_self_signed' => true
		    )
		);
		$mail->IsHTML(true);
		if($correocopia !="")$mail->AddCC($correocopia);
		$mail->AddAddress($Email_usua,$Nomb_usua);
		$mail->AddReplyTo($correo,$nombreEmisor);
		$mail->AddAttachment($adjuntos, "Radi".$verradicado2.$ext);

		if (!$mail->Send()) {
			return false;
		}

		$mail->ClearAddresses();
		$mail->ClearAttachments();

		return true;
	}

?>
<table class="borde_tab" width="100%" cellspacing="5">
<tr><td class="titulos5" align="center" valign="middle"><B>ENVIO DE DOCUMENTOS POR E-MAIL</B></td></tr>
</table>
<form name='forma' id="forma" action='marcaEnviar.php?<?=session_name()."=".session_id()."&krd=$krd&fecha_h=$fechah&dep_sel=$dep_sel&estado_sal=$estado_sal&estado_sal_max=$estado_sal_max&no_planilla=$no_planilla"?>' method=post>
<table border=0 width=100% class=borde_tab cellspacing="5">
<tr class="titulos2">
	<td valign="center" align="center">Estado</td>
	<td valign="center" align="center">Radicado</td>
	<td valign="center" align="center">Radicado Padre</td>
	<td valign="center" align="center">Destinatario</td>
	<td valign="center" align="center">Direcci&oacute;n</td>
	<td valign="center" align="center">Pa&iacute;s</td>
	<td valign="center" align="center">Depto</td>
	<td valign="center" align="center">Municipio</td>
	<td valign="center" align="center">&nbsp;</td>
	<td valign="center" align="center">Asunto</td>
</tr>
<?php
if ($checkValue) {
    $num = count($checkValue);
	$i = 0;
	while ($i < $num) {
		$record_id = key($checkValue);
		$radicadosSel[$i] = $record_id;
		$setFiltroSelect .= $record_id ;
		if($i<=($num-2)) {
			$setFiltroSelect .= ",";
		}
		$estadoFirma = $objFirma->firmaCompleta($record_id);
		if ($estadoFirma == "COMPLETA")
		{	$swValido=false;
			include ("http://$servFirma?radicado=$record_id&servbase=java:$usuario&servbodega=$servBodega");

			if ($swValido==false)
			{	echo ("<span class='alarmas'>");
				echo ("El conjunto de firmas carece de validez en el radicado $record_id  para: " );
				for ($k=0;$k<count ($docNoValida) ; $k++)
				{	$objFirma->anularFirmaRad($record_id);
					echo ("<BR>$docNoValida[$k] ----> $nombNoValida[$k]  ");
				}
				echo ("<BR>Por favor verifique si el documento anexo se modifico o fue regenerado.  ");
				echo ("</span>");
				die;
			}
			else
			{	//Archivo donde se le indica al servidor de Open Office como llevar a cabo la combinaci�n
				$firmasRad = $objFirma->nombresFirmsRad($record_id);
				$hora=date("H")."_".date("i")."_".date("s");
				// var que almacena el dia de la fecha
				$ddate=date('d');
				// var que almacena el mes de la fecha
				$mdate=date('m');
				// var que almacena el a� de la fecha
				$adate=date('Y');
				// var que almacena  la fecha formateada
				$fechaArchivo=$adate."_".$mdate."_".$ddate;
				//var que almacena el nombre que tendr�la pantilla
				$archInsumo="tmp_".$usua_doc."_".$fechaArchivo."_".$hora.".txt";
				$radObjeto->radicado_codigo($record_id);
				$linkarchivo = $radObjeto->getRadi_path();
				$directoriobase="$ruta_raiz_archivo/bodega/";
				$linkarchivo = "$directoriobase/".$linkarchivo;
				$linkArchSimple=$linkarchivo;
				$trozosPath= explode("/",$linkarchivo);
				$nombreArchivo = $trozosPath[count($trozosPath)-1];
				copy("$ruta_raiz/$linkarchivo","$ruta_raiz/bodega/masiva/$nombreArchivo");
				$fp=fopen("$ruta_raiz/bodega/masiva/$archInsumo",'w+');
				fputs ($fp,"archivoInicial=$linkArchSimple"."\n");
				fputs ($fp,"archivoFinal=$linkArchSimple"."\n");
				fputs ($fp,"*FIRMANTES*=$firmasRad"."\n");
				fclose($fp);
				$estadoTransaccion=-1;
				$vp = "x";
				include ("http://$servProcDocs/docgen/servlet/WorkDistributor?accion=1&ambiente=$ambiente&archinsumo=$archInsumo&vp=$vp");
				if ($estadoTransaccion!=0)
				{ 	$objError = new CombinaError (NO_DEFINIDO);
					echo ($objError->getMessage());
					die;
			 	}
				//echo ("($linkarchivo)($archInsumo)");
		}	}
  		next($checkValue);
		$i++;
	}

	if ($radicadosSel) 	$whereFiltro = " and b.radi_nume_radi in($setFiltroSelect)";
} // FIN  if ($checkValue)

if ($setFiltroSelect) $filtroSelect = $setFiltroSelect;
if ($filtroSelect)
{	// En este proceso se utilizan las variabels $item, $textElements, $newText que son temporales para esta operacion.
	$filtroSelect = trim($filtroSelect);
	$textElements = split (",", $filtroSelect);
	$newText = "";
	foreach ($textElements as $item)
	{	$item = trim ( $item );
		if ( strlen ( $item ) != 0)
		{	if (strlen($item)<=6)  $sec = str_pad($item,6,"0",STR_PAD_left);	}
	}
} // FIN if ($filtroSelect)

$carp_codi = substr($dep_radicado,0,2);

$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

$isql = "SELECT
			convert(varchar(15),b.RADI_NUME_RADI) as RADI_NUME_RADI,a.ANEX_NOMB_ARCHIVO,a.ANEX_DESC,a.SGD_REM_DESTINO,a.SGD_DIR_TIPO
  		   	,convert(varchar(15),a.ANEX_RADI_NUME) as ANEX_RADI_NUME, convert(varchar(15),a.RADI_NUME_SALIDA) as RADI_NUME_SALIDA
		FROM
		 	ANEXOS a,
			RADICADO b
		WHERE
			a.radi_nume_salida=b.radi_nume_radi
		AND
			a.RADI_NUME_SALIDA in(".$setFiltroSelect.")
		AND
			a.sgd_dir_tipo <>7";

$rsMarcar = $db->conn->Execute($isql);
//$no_registros = $rsMarcar->recordcount();
$verRegistros = "";

$verRegistros = $rsMarcar->fields["RADI_NUME_RADI"];

if (!$verRegistros)
{	$estado = "Error";
	$mensaje = "Verifique si ya esta marcado como impreso xo enviado por E-mail...";
	if($textElements != null){
		foreach ($textElements as $item)
		{	$verrad_sal = trim ( $item );
			include "../envios/listaMarca.php";
		}
	}
	echo "<script>alert('No se puede Marcar el Documento $verrad_sal Como Enviado por E-mail. $mensaje  ')</script>";
}
else
{	while (!$rsMarcar->EOF) {
        $mensaje        = "";
		$verrad_sal     = $rsMarcar->fields["RADI_NUME_SALIDA"];
		$verradicado    = $rsMarcar->fields["RADI_NUME_RADI"];
		$verradicado2	= $verradicado;
		$ref_pdf        = $rsMarcar->fields["ANEX_NOMB_ARCHIVO"];
		$asunto         = $rsMarcar->fields["ANEX_DESC"];
		$sgd_dir_tipo   = $rsMarcar->fields["SGD_DIR_TIPO"];
		$rem_destino    = $rsMarcar->fields["SGD_DIR_TIPO"];
		$anex_radi_nume = $rsMarcar->fields["ANEX_RADI_NUME"];
		$dep_radicado   = substr($verrad_sal,4,3);
		$ano_radicado   = substr($verrad_sal,0,4);
		$carp_codi      = substr($dep_radicado,0,2);
		$radi_path_sal = "/$ano_radicado/$dep_radicado/docs/$ref_pdf";

		if(substr($rem_destino,0,1)=="7") $anex_radi_nume = $verrad_sal;
		$nurad = $anex_radi_nume;
		$verrad = $anex_radi_nume;
		$verradicado = $anex_radi_nume;

		$ruta_raiz= "..";
		include "../ver_datosrad.php";

		if ($radicadopadre) $radicado = $radicadopadre;
		if ($nurad) 	      $radicado = $nurad;


		$pCodDep = $dpto;
		$pCodMun = $muni;
		$nombre_us    = $otro . "-".substr($nombre . " " . $papel . " " . $sapel,0 ,29);

		if(!$rem_destino) $rem_destino =1;
		$sgd_dir_tipo = 1;
		echo "<input type=hidden name=$espcodi value='$espcodi'>";

		$ruta_raiz = "..";
		include "../jh_class/funciones_sgd.php";

		/*
		*Se incluyen ya que en ver_datosrad no esta contemplada esta opcion
		*que corresponde a copias
		*/
		$a = new LOCALIZACION($codep_us7,$muni_us7,$db);
		$dpto_nombre_us7 = $a->departamento;
		$muni_nombre_us7 = $a->municipio;
		/*
		* Fin modificacion
		*/
		$a = new LOCALIZACION($pCodDep,$pCodMun,$db);
		$dpto_nombre_us = $a->departamento;
		$muni_nombre_us = $a->municipio;
		$direccion_us = $dir;
		$destino      = $muni_nombre_us;
		$departamento = $dpto_nombre_us;
		$pais		  = $a->GET_NOMBRE_PAIS($pais,$db);
		$dir_codigo   = $documento;
		/*
		* Modificado 27072005
		* Se modifica para que asuma el destinatario
		*/

		if($rem_destino==1)
		{
	   		$email_us = $email_us1;
	   		$telefono_us = $telefono_us1;
	   		$nombre_us = trim($nombre_us1);
	   		if($otro_us1) $nombre_us = $otro_us1 . " - " . $nombre_us;
	   		if($tipo_emp_us1==0)  $nombre_us .= " " . trim($prim_apel_us1) . " " . trim($seg_apel_us1);
	   		$destino = $muni_nombre_us1;
	   		$departamento = $dpto_nombre_us1;
	   		$direccion_us = $direccion_us1;
	   		$dir_codigo = $dir_codigo_us1;
	   		$dir_tipo = 1;
		}
		if($rem_destino==2)
		{
	   		$email_us = $email_us2;
	   		$telefono_us = $telefono_us2;
	   		$nombre_us = trim($nombre_us2);
	   		if($otro_us2) $nombre_us = $otro_us2 . " - " . $nombre_us;
	   		if($tipo_emp_us2==0)  $nombre_us .= " " . trim($prim_apel_us2) . " ". trim($seg_apel_us2);
	   		$destino = $muni_nombre_us2;
 	   		$departamento = $dpto_nombre_us2;
	   		$direccion_us = $direccion_us2;
	   		$dir_codigo = $dir_codigo_us2;
	   		$dir_tipo = 2;
		}
		if($rem_destino==3)
		{
			$email_us = $email_us3;
			$telefono_us = $telefono_us3;
			$destino = $muni_nombre_us3;
			$departamento = $dpto_nombre_us3;
			$nombre_us = trim($nombre_us3);
			if($tipo_emp_us3==0)  $nombre_us .= " " . trim($prim_apel_us3) . " ".trim($seg_apel_us3);
			$dir_codigo = $dir_codigo_us3;
			$direccion_us = $direccion_us3;
			$dir_tipo = 3;
		}
		if(substr($rem_destino,0,1)==7)
		{
			$email_us = $email_us7;
			$telefono_us = $telefono_us7;
			$destino = $muni_nombre_us7;
			$departamento = $dpto_nombre_us7;
			$nombre_us = trim($nombre_us7);
        	if($otro_us7) $nombre_us = $otro_us7 . " - " . $nombre_us;
			if($tipo_emp_us7==0)  $nombre_us .= " " . trim($prim_apel_us7) . " ".trim($seg_apel_us7);
			$dir_codigo = $dir_codigo_us7;
			$direccion_us = $direccion_us7;
			$dir_tipo = $rem_destino;
		}
		$nombre_us = substr(trim($nombre_us),0 ,29);
		/*
		* Fin modificacion
		*/
		if (!$mensaje) {
			$mensaje = ""; $error = "no";
			if(!$nombre_us)    {$mensaje = "Nombre,";  $error = "si"; }
			if(!$direccion_us) {$mensaje .= "Direccion,"; $error = "si"; }
			if(!$destino)      {$mensaje .= "Municipio,";  $error = "si"; }
			if(!$departamento) {$mensaje .= "Departamento,";  $error = "si"; }
		}

		if ($error=="no"){

//SE COLOCA EL CODIGO PARA OBTENER EL EMAIL Y EL NOMBRE DEL USUARIO
$isql_usu = "
			select
                d.radi_nume_radi,
                --d.sgd_oem_codigo as destinaraio_codi,
                o.sgd_oem_oempresa+' : '+ sgd_oem_rep_legal as nom_usuario,
                o.email
            from
            	sgd_dir_drecciones d,
				sgd_oem_oempresas o
            where
				d.radi_nume_radi = $nurad and
                d.sgd_oem_codigo <> 0 and
                d.sgd_oem_codigo is not null and
                d.sgd_oem_codigo = o.sgd_oem_codigo

			union

 			select
				d.radi_nume_radi,
				--d.sgd_ciu_codigo,
				c.sgd_ciu_nombre+' '+
				c.sgd_ciu_apell1+' '+
				c.sgd_ciu_apell2 as destinatario,
				c.sgd_ciu_email
			from
				sgd_dir_drecciones d,
				sgd_ciu_ciudadano c
            where
				d.radi_nume_radi = $nurad and
				d.sgd_ciu_codigo <> 0 and
				d.sgd_ciu_codigo is not null and
				c.sgd_ciu_codigo = d.sgd_ciu_codigo

			union

 			select
                d.radi_nume_radi,
                --d.sgd_esp_codi,
                b.nombre_de_la_empresa +'--'+
				b.nombre_rep_legal,
                b.email
            from
				sgd_dir_drecciones d,
				bodega_empresas b
			where
				d.radi_nume_radi = $nurad and
				d.sgd_esp_codi <> 0 and
				d.sgd_esp_codi is not null and
			    d.sgd_esp_codi = b.identificador_empresa

			union

 			select
                d.radi_nume_radi,
                --d.sgd_doc_fun,
                u.usua_nomb,
                u.usua_email
            from
                sgd_dir_drecciones d,
                usuario u

            where

                d.radi_nume_radi = $nurad and
                (d.sgd_esp_codi = 0 or d.sgd_esp_codi is  null) and
                (d.sgd_ciu_codigo = 0 or d.sgd_ciu_codigo is  null) and
                (d.sgd_oem_codigo = 0 or d.sgd_oem_codigo is null) and
                 d.sgd_doc_fun = u.usua_doc
			";

$isql_path = "select
      			radi_path
      		from
      			RADICADO
      		where
      			RADI_NUME_RADI = $verradicado2";

//SE TOMA LA VERIABLE DEL RADICADO GENERADO POR ESTE MISMO DOCUMENTO$verradicado2;
//SE TOMA LA VARIABLE DEL FORMULARIO cuerpoMarcaEnviar QUE SE ENVIA EN EL POST A ESTE ARCHIVO $correo;
//SE TOMA LA VARIABLE DE LOS PARAMETROS GLOBALES DE LA SESSION
			$nombreEmisor = "Respuesta Web";
			//$nombreEmisor = $_SESSION["usua_nomb"];
//SE REALIZA UNA CONSULTA PARA TRAER EL DATO DEL NOMBRE Y EL CORREO DEL USUARIO DESTINATARIO
			$usuarioext = $db->conn->Execute($isql_usu);
			$Nomb_usua     	= $usuarioext->fields["nom_usuario"];
			$Email_usua     = $usuarioext->fields["email"];
//SE TRAE LA RUTA PATH DE ESE ARCHIVO BUSCADO EN LA CONSULTA
//Esta ruta no es por que muestra es un .doc
			$rutapath	=	$db->conn->Execute($isql_path);
			$finalpath	=	$rutapath->fields["radi_path"];
			$adjuntos = BODEGAPATH.$finalpath;
			$ext = substr($adjuntos,-4);
//$servidorSmtp; $correocopia; $nurad;
//LLAMAR A LA FUNCION ENVIAR CORRREO
			$respuesta = enviarCorreo(
			 			$verradicado2,
			 			$correo,
			 			$nombreEmisor,
			 			$Nomb_usua,
			 			$Email_usua,
						$servidorSmtp,
						$adjuntos,
						$ext,
						$correocopia,
						$nurad);

			if($Emailusua != null) $estado= "Ok"; else $estado= "Error !correo Destinatirio!";
			if($respuesta){
				$estado= "Email enviado";

//SE ELIMINA ANEX_ESTADO=3 Y SOLO SE CAMBIA LA VARIABLE DE ANEX_EXTADO_EMAIL=1
				$isql = "update ANEXOS
							set ANEX_ESTADO_EMAIL=1,SGD_FECH_IMPRES= " .$db->conn->OffsetDate(0,$db->conn->sysTimeStamp).",
							ANEX_FECH_ENVIO=" .$db->conn->OffsetDate(0,$db->conn->sysTimeStamp).",
							SGD_DEVE_FECH = NULL, SGD_DEVE_CODIGO=NULL
		             	where RADI_NUME_SALIDA =$verrad_sal
							and sgd_dir_tipo <>7";

				$rsUpdate = $db->conn->Execute($isql);
		    	if($rsUpdate) $estado= "Ok"; else $estado= "Mal";
//FIN DE LA CONSULTA

//GRABAR HISTORICO DEL EVENTO

		    	if ($estado == "Ok") {
				$rad = array($verrad_sal);
		        	$hist->insertarHistorico(
		        		$rad,
                    	$dep_radicado,
                        $_SESSION["codusuario"],
                        $dep_radicado,
                        $_SESSION["codusuario"],
                        "Enviado por E-mail ha: <br> E-mail ".$Email_usua." <br> C.C. ".$correocopia,
                        59);
		    	}

			}
			else{
				if($Emailusua == null) $estado= "Error !correo Destinatirio!";
				else $estado= "Error !Email no enviado al Destinatirio!";}


		}
		else
		{
			$estado = "<span class=titulosError> Error </span>" ;
			$mensaje= "Faltan Datos $mensaje";
			echo "<script>alert('No se puede Marcar el Documento $verrad_sal Como Enviado por E-mail. $mensaje  ')</script>";
		}

		include "../envios/listaMarca.php";
		$rsMarcar->MoveNext();
	} // FIN del WHILE (!$rsMarcar->EOF)
	$rsMarcar->Close();
} //FIN else if ($no_registros <=0)
?>
</table>
</form>
</body>
</html>