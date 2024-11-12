<?php
$mantenimiento = false;
if ($mantenimiento == true)
    die("	<table align='center'><tr><td><br/><br/><img src='img/escudo.jpg' width='200px' alt='escudo' /><br/><br/><br/></td><tr><tr><td><span style='color:Red; font-weight:Bold'>Estimado Usuario:</span>
			<p>Nos encontramos en ventana de mantenimiento entre el 16-08-2024 desde las 06:00 pm hasta el 17-08-2024 a 10:00 pm, por favor intentar despu&eacute;s de la ventana.</p>
			<p>Muchas gracias por su comprensi&oacute;n.</p>
			<p>Atte. Oficina de Tecnolog&iacute;a y Sistemas de Informaci&oacute;n.</p></td></tr>");
if (! defined('ORFEOCFG'))
    define('ORFEOCFG', 'E:/OI_OrfeoPHP7_64/orfeo/');
require_once (ORFEOCFG . "_conf/constantes.php");

$servicio = "GdOrfeo";
$servidor = "192.168.70.105";
$usuario = "SaOrfeo";
$contrasena = "URT2023+-+";

$db = $servicio;
$driver = "mssqlnative";
// Variable que indica el ambiente de trabajo, sus valores pueden ser desarrollo,prueba,orfeo
$ambiente = "orfeo";

// Tamano en KB maximo de la sumatoria de anexos a enviar por correo electronico.
$tamAnexosCorreo = 20000; // 10MB
                          
// $servProcDocs = "Linmerge.dnp.ad:8080";
$entidad = "URT";
$entidad_largo = "Unidad de Restitución de Tierras";
$entidad_tel = 5960300;
$entidad_dir = "Calle 26 # 13 - 19";
// Guarda el codigo de la dependencia de salida por defecto al radicar dcto de salida.
$entidad_depsal = 0;
// 0 = Carpeta salida del radicador >0 = Redirecciona a la dependencia especificada

/**
 * Se crea la variable $ADODB_PATH.
 * El Objetivo es que al independizar ADODB de ORFEO, este (ADODB) se pueda actualizar sin causar
 * traumatismos en el resto del codigo de ORFEO. En adelante se utilizar esta variable para hacer
 * referencia donde se encuentre ADODB
 */

$ADODB_PATH = ORFEOCFG . "webServices/adodb";
$ADODB_CACHE_DIR = "E:\\sessionesorfeo\\";	//  <<<== Hay que crear esta carpeta.
$archivado_requiere_exp = true;
$anoInicialCreaDir = 2005;

/**
 * Variable utilizada con el fin de poder parametrizar a futuro el nombre de la carpeta que contiene
 * la bodega de Orfeo.
 * Acualmente (09-feb-2011) es utilizada en el modulo de Administracion de Dependencias.
 */
$carpetaBodega = "bodega/";
$serverCompartida = "\\\\192.168.101.132\\Bodega\\";

/**
 * SERVIDOR DE CORREO ELECTRONICO SALIDA - OFFICE365
 */
$server_mail_0365 = "smtp.office365.com";
$usuario_mail_0365 = "Notificaciones SGD Orfeo"; 
$correo_mail_0365 = "notificaciones.orfeo@urt.gov.co"; 
$passwd_mail_0365 = "Buw29467";
$port_mail_0365 = 587;
$protocolo_mail_0365 = "smtp";
$tls_mail_0365 = "tls";
$auth_mail_0365 = true;

/*$server_mail_0365 = "smtp.office365.com:587";
$usuario_mail_0365 = "Notificaciones SGD Orfeo"; 
$correo_mail_0365 = "notificaciones_orfeodnp@URT.gov.co"; 
$passwd_mail_0365 = "RC34025*";
$protocolo_mail_0365 = "smtp";
$tls_mail_0365 = "tls";
$auth_mail_0365 = true;*/

$server_mail_2 = "smtp.office365.com";
$usuario_mail_2 = "Notificaciones SGD Orfeo";
$correo_mail_2 = "notificaciones.orfeo@urt.gov.co";
$passwd_mail_2 = "Buw29467";
$port_mail_2 = 587;
$protocolo_mail_2 = "smtp";
$tls_mail_2 = "tls";
$auth_mail_2 = true;

/**
 * SERVIDOR DE CORREO ELECTRONICO SALIDA - RELAY OFFICE365
 */
$server_mail = $server_mail_0365; // "vrelay.dnp.ad:25";
$usuario_mail = $usuario_mail_0365; // "Correo Orfeo DNP";
$correo_mail = $correo_mail_0365; // "notificaciones@dnp.gov.co";
$passwd_mail = $passwd_mail_0365; // "DNP2015+";
$protocolo_mail = $protocolo_mail_0365; // "smtp";
$tls_mail = $tls_mail_0365; // false;
$auth_mail = $auth_mail_0365; // false;

/**
 * SERVIDOR DE CORREO ELECTRONICO ENTRADA OFFICE365
 */
$server_mail_incoming = "smtp.office365.com";
$port_mail_incoming = "993"; // 995=pop3_con_ssl 993=imap_con_ssl
$correo_mail_incoming = "radicacionorfeo@dnp.gov.co";
$passwd_mail_incoming = "Qwertyasdfgh2022*/";
$prot_mail_incoming = "imap";

/**
 * INFORMACION CORREO ELECTRONICO CERTIFICADO - CERTIMAIL
 */
// Usuario creado en Dep de Correspondencia para usarlo como puente al crear historicos automaticos.
// Tambien es usado como USUARIO de conexion al Servicio WCF de Orfeo.
$usrComodin = "GESPROY";
$pwdComodin = "d0492312862c952cdc5c08b31d426e87";

/**
 * Cuentas para envio de correo Factura Electronica
 */
$correo_facelec = "facturaelectronica@dnp.gov.co";
$usuario_facelec = "Factura Electronica";
$passwd_facelec = "L'>yooy>-8_CAP";

//$correo_certimail = $correo_mail_0365;
$correo_certimail = $correo_mail_2;
//$passwd_certimal = $passwd_mail_0365;
$passwd_certimal = $passwd_mail_2;
$asunto_certimail = "Envío de notificación radicado ";
$cuerpo_certimail = "<table width='80%'><th><tr><td><img src='https://orfeo.dnp.gov.co/img/logoNuevo.jpg'></td><td><b>Comunicaci&oacute;n Oficial.</b></td>
                    <tr><td colspan='2' style='font-family: verdana; font-size: 75%'>
                    El Departamento Nacional de Planeaci&oacute;n le env&iacute;a este oficio mediante notificaci&oacute;n certificada.
					\"De acuerdo a la Directiva Presidencial No. 04 de abril 3 del a&ntilde;o 2012 y de conformidad con lo previsto en la Ley 1437 de 2011,
					por el cual se expide el C&oacute;digo de Procedimiento Administrativo y de lo Contencioso.
                    </td><tr>
                    <tr><td colspan='2'><b>***Importante: Por favor no responda a este correo electr&oacute;nico. Esta cuenta no permite recibir correo.</b></td></tr>
                    </table>";
$carpetaLecturaCertimail = "Inbox"; // Carpeta donde BUSCA el script los correos a atualizar en Orfeo.
$carpetaGestionCertimail = "AcusesGestionadosCorreoCertificado"; // Carpeta donde MUEVE los correos gestionados en Orfeo.

/**
 * INFORMACION ACTIVE DIRECTORY
 */
// Nombre o IP del servidor de autenticacion LDAP
$ldapServer = 'ldap://uaegrtd.local';
// Cadena de busqueda en el servidor.
// usuario en da es "OrfeoAD"
$cadenaBusqLDAP = 'OU=Administracion Usuarios URT,DC=uaegrtd,DC=local';
// Campo seleccionado (variable LDAP) para realizar la autenticacion.
$campoBusqLDAP = 'mail';
// Usuario AD para realizar bind con el servidor AD. SOLO para W2K3 o superior.
$usrLDAP = "DC=uaegrtd, DC=local";
// Contrasena del usuario anterior.
$pwdLDAP = 'og77(5WN4W"7U|C';

/**
 * INFORMACION SERVICIO WEB DE OFFICE
 */
$wsdlOffice = "http://firmas.urt.gov.co/officeWcfService.svc?wsdl";
//$wsdlOffice = "http://localhost:800/officeWcfService.svc?wsdl";
/**
 * Cascaron del cuerpo para notificaciones.
 * Cree su contenido y reemplazelo por la cadena 'XYX'.
 * **** PENDIENTE ****** Las comillas dobles tienen que ir escapadas con '\'. => \" <=
 */
$cuerpoMail = "<html><head></head><body><br />" . "<table width=\"80%\" style=\"border: 2px solid ROYALBLUE\" cellpadding=\"10\"><tr><td>" . "<table width=\"100%\"><tr><td><img src=\"https://orfeo.urt.gov.co/img/escudo.jpg\" width=\"200\"></td><td><b>Comunicaci&oacute;n Oficial.</td></tr>" . "<tr><td colspan=\"2\" align=\"center\">El Sistema de Gesti&oacute;n Documental ORFEO informa que:</td></tr>" . "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\" align=\"justify\">XYX</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr></table></td></tr></table></body></html>";

$ocultaDocElectronico = 1;

/**
 * INFORMACION SERVICIO WEB 472
 */
$wsdl472 = "https://urt.correocertificado4-72.com.co/webService.php?wsdl";

?>