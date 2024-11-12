<?php

include realpath(dirname(__FILE__))."/../config.php";
define('RUTARAIZ','../');
// Variables de configuración general de la aplicacion
define('URL', SERVIDOR.'/orfeo.api/');
define('LIBS', 'libs/');
define('DB_TYPE', $driver);
define('DB_HOST', $servidor);
define('DB_NAME', $servicio);
define('DB_USER', $usuario);
define('DB_PASS', $contrasena);
/**
 * Nombre de la carpeta que alberga la bodega en  Orfeo.
 * @var string
 */
define('BODEGA', $carpetaBodega);
//WSDL OrfeoServiceWCF
//TEST
//define('WSDLORFEOSERVICEWCF','https://orfeoservicetest.dnp.gov.co/OrfeoServiceWCF/OrfeoServiceWCF.svc?wsdl');
//PROD
//define('WSDLORFEOSERVICEWCF','https://orfeows.dnp.gov.co/OrfeoServiceWCF.svc?wsdl');

//WSDL 
//TEST
//define('WSDLEMAILSERVICE', 'http://trservicetest.dnp.gov.co/EmailServiceWCF/EmailServiceWCF.svc?wsdl');
//PROD
//define('WSDLEMAILSERVICE', 'http://trservice.dnp.gov.co/EmailServiceWCF/EmailServiceWCF.svc?wsdl');


//Credenciales de conexion al Servicio de Orfeo
//define('USUARIOWSORFEO',$usrComodin);
//define('PASSWORDWSORFEO',$pwdComodin);

//define('USUARIOWSEMAIL','ORFEOUser');
//define('PASSWORDWSEMAIL',',.1234,.');


define('URLLOGIN', SERVIDOR);

define ("WEBEMAILS", json_encode(array (array("correo"=>"orfeo@dnp.gov.co",
                                                  "password"=>""),
                                            array("correo"=>"PruebaBuzon@dnp.gov.co",
                                                  "password"=>"DNP2013+")
                                            )));


define('SERVER_MAIL',$server_mail);
define('USUARIO_MAIL',$usuario_mail);
define('CORREO_MAIL',$correo_mail);
define('PASSWORD_MAIL',$passwd_mail);
define('TLS_MAIL',$tls_mail);
define('REQUIRE_AUTH',$auth_mail);

define('USUARIO_MAILFE',$usuario_facelec);
define('CORREO_MAILFE', $correo_facelec);
define('PASSWORD_MAILFE',$passwd_facelec);

define('CORREOCERTIFICADO_ASUNTO', $asunto_certimail);
define('CORREOCERTIFICADO_CUERPO', $cuerpo_certimail);

define('WSDL', $wsdl472);

// CONSTANTE que almacena el nombre del ESTILO de ExtJS
// La idea es que a futuro el usuario seleccione su ESTILO y Orfeo siempre lo utilice al instanciarse.
// Los valores posibles son: aria, classic, crisp, gray, neptune.
define('ESTILO', 'classic');
?>