<?php
die("Por favor parametrizar");
ini_set('set_time_limit', 0);
ini_set('max_execution_time ', 0);
require './config.php';
require '/class_control/correoElectronico.php';

//$para = array('proyectogramalote@gmail.com');
//$cc = array('hortiz@dnp.gov.co', 'nlara@dnp.gov.co','mbohorquez@dnp.gov.co');
//$cco = array('jromerot@dnp.gov.co','hladino@dnp.gov.co','eestrada@dnp.gov.co');
$para = array('hollmanlp@gmail.com');
$cc = array('hladino@dnp.gov.co');

$asunto = "Respuesta del Departamento Nacional de Planeacional DNP a su solicitud No. 20186630333962";
$cuerpo ="<table width='80%'><tr><td><img src='https://orfeo.dnp.gov.co/img/escudo.jpg'></td><td><b>Comunicaci&oacute;n Oficial.</b></td></tr><tr><td colspan='2'>&nbsp;</td></tr></tr>".
            "<tr><td colspan='2'>El Sistema de Gesti&oacute;n Doumental ORFEO notifica que:</td></tr><tr><td>&nbsp;</td></tr>".
            "<tr><td colspan='2'>Debido a inconvenientes t&eacute;cnicos en el servicio de correo electr&oacute;nico usted no pudo recibir la respuesta a su petici&oacute;n <b>20186630333962</b>. 
                La misma fue generada con el n&uacute;mero <b>20184460400231</b></td></tr>".
            "<tr><td colspan='2'>&nbsp;</td></tr>".
            "<tr><td colspan='2'>Si no puede visualizar el correo, o los archivos adjuntos, puede consultarlos tambi&eacute;n en la siguiente direcci&oacute;n: <br>
             <a href='https://orfeo.dnp.gov.co/pqr/consulta.php?rad=20186630333962'>https://orfeo.dnp.gov.co/pqr/consulta.php</a></td></tr>".
             "<tr><td colspan='2'>Ofrecemos disculpas por el inconveniente presentado.<br /></td></tr>".
             "<tr><td colspan='2' align='center'><b>&nbsp;</b></td></tr><tr><td colspan='2' align='center'><b>DNP</b></td></tr></table>";
try {
	$objMail = new correoElectronico(".", false, false);
	$objMail->FromName = "Notificaciones DNP (Orfeo)";
	$objMail->agregarAdjunto('\\\\VORFEOBOD\\bodega\\2018\\663\\docs\\PSALAS1530213652_Respuesta.pdf','respuesta.pdf');
	$objMail->agregarAdjunto('\\\\VORFEOBOD\\bodega\\2018\\663\\20186630333962_93175.pdf','solicitud.pdf');

	//$objMail->agregarAdjunto('\\\\VORFEOBOD\\bodega\\anexos.zip','anexos.zip');
	//$objMail->agregarAdjunto('\\\\VORFEOBOD\\bodega\\2018\\446\\docs\\20184460400231_00002.pdf','Acto Formulacion de Cargos.pdf');
	//$objMail->agregarAdjunto('\\\\VORFEOBOD\\bodega\\2018\\446\\docs\\20184460400231_00003.pdf','Citacion notificacion Ex-Representante.pdf');
	//$objMail->agregarAdjunto('\\\\VORFEOBOD\\bodega\\2018\\446\\docs\\20184460400231_00004.pdf','Citacion notificacion personal.pdf');
	//$objMail->agregarAdjunto('\\\\VORFEOBOD\\bodega\\2018\\446\\docs\\20184460400231_00005.pdf','Constancia de Representante Legal.pdf');
	//$objMail->agregarAdjunto('\\\\VORFEOBOD\\bodega\\2018\\446\\docs\\20184460400231_00006.pdf','Memorando y Oficio Solicitud de Ajuste.pdf');
	//$objMail->agregarAdjunto('\\\\VORFEOBOD\\bodega\\2018\\446\\docs\\20184460400231_00007.pdf','Correo electronico Rad.20186630320332.pdf');
	//$objMail->agregarAdjunto('\\\\VORFEOBOD\\bodega\\2018\\446\\docs\\20184460400231_00008.pdf','Correo electronico.pdf');
	//$objMail->agregarAdjunto('\\\\VORFEOBOD\\bodega\\2018\\446\\docs\\20184460400231_00009.pdf','Correo Rad.20186630320332.pdf');
	//$objMail->agregarAdjunto('\\\\VORFEOBOD\\bodega\\2018\\446\\docs\\20184460400231_00010.pdf','Notificacion electronica Gobernador.pdf');
	//$objMail->agregarAdjunto('\\\\VORFEOBOD\\bodega\\2018\\446\\docs\\20184460400231_00011.pdf','Oficio COLCIENCIAS.pdf');
	//$objMail->agregarAdjunto('\\\\VORFEOBOD\\bodega\\2018\\446\\docs\\20184460400231_00012.pdf','Oficio de respuesta Jefe Oficina Asesora.pdf');
	//$objMail->agregarAdjunto('\\\\VORFEOBOD\\bodega\\2018\\446\\docs\\20184460400231_00013.pdf','oficio de Respuesta Departamento.pdf');
	//$objMail->agregarAdjunto('\\\\VORFEOBOD\\bodega\\2018\\446\\docs\\20184460400231_00014.pdf','PRESENTACION DE DESCARGOS ACTO ADMINISTRATIVO.pdf');


	if ($objMail->enviarCorreo($para, $cc, $cco, $asunto, $cuerpo))
		echo "Correo enviado";
	else 
		echo "No enviado";
} catch (Exception $e) {
    echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
}