<?php
/**
 * Clase abstracta que debe implementar TODAS las formas de Envio. 
 * Iniciaremos con Certim@il. 28-04-2014
 */
abstract class Envios {
    
    var $cedula = null;
    var $formaEnvio = null;
    /**
     * Tipo de envio. 1=Original, 701=Copia 01, etc
     * @var int 
     */
    var $dirTipo = null;
    /**
     * Número del radicado a enviar.
     * @var integer
     */
    var $radicado = null;
    /**
     * 
     * Nombre del destinatario.
     * @var String(30);
     */
    var $nombre = null;
    /**
     * 
     * Nombre de la ciudad destino.
     * @var String(150)
     */
    var $destino = null;
    /**
     * Dirección fisica del envío.
     * @var string(100)
     */
    var $direccion = null;
    /**
     * 
     * Telefono del destinatario.
     * @var String(50).
     */
    var $telefono = null;
    /**
     * 
     * Correo Electrónico del destinataro.
     * @var string(150).
     */
    var $correoe = null;
    /**
     * 
     * Peso del envio. Puede ser MB, Kilos, etc.
     * @var float
     */
    var $pesoEnvio = null;
    /**
     * 
     * Valor del envio.
     * @var integer
     */
    var $valorEnvio = null;
    /**
     * 
     * Conexion ADODB STANDAR a la BD.
     * @var object ADODB
     */
    var $conn = null;
    /**
     * Valor de SGD_RENV_REGENVIO.SGD_DIR_CODIGO.
     * @var integer 
     */
    var $codEnvio = null;
    /**
     * Código de dependencia del usuario que genera la transacción.
     * @var integer
     */
    var $dependencia = null;
    /**
     * Codigo de la planilla donde se enviará el radicado según forma de envío.
     * @var string(8) 
     */
    var $planilla = null;
    /**
     * Nombre del pais destino del envío.
     * @var string(30)
     */
    var $pais = null;
    /**
     * Nombre del departamento destino del envío.
     * @var string(30)
     */
    var $departamento = null;
    /**
     * Nombre del municipio destino del envío.
     * @var string(30)
     */
    var $municipio = null;
    /**
     * Observación realizada al hacer el envío.
     * @var string(200)
     */
    var $observacion = null;
    /**
     * Número de guia del envío.
     * @var string(15)
     */
    var $numguia = null;
    /**
     * Código postal del destinatario.
     * @var string(8)
     */
    var $codpostal = null;
    abstract function validarDatos();
    abstract function enviarRadicado();
    abstract function devolverValorUnitario();
}
?>