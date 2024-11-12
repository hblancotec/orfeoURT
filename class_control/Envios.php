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
     * N�mero del radicado a enviar.
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
     * Direcci�n fisica del env�o.
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
     * Correo Electr�nico del destinataro.
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
     * C�digo de dependencia del usuario que genera la transacci�n.
     * @var integer
     */
    var $dependencia = null;
    /**
     * Codigo de la planilla donde se enviar� el radicado seg�n forma de env�o.
     * @var string(8) 
     */
    var $planilla = null;
    /**
     * Nombre del pais destino del env�o.
     * @var string(30)
     */
    var $pais = null;
    /**
     * Nombre del departamento destino del env�o.
     * @var string(30)
     */
    var $departamento = null;
    /**
     * Nombre del municipio destino del env�o.
     * @var string(30)
     */
    var $municipio = null;
    /**
     * Observaci�n realizada al hacer el env�o.
     * @var string(200)
     */
    var $observacion = null;
    /**
     * N�mero de guia del env�o.
     * @var string(15)
     */
    var $numguia = null;
    /**
     * C�digo postal del destinatario.
     * @var string(8)
     */
    var $codpostal = null;
    abstract function validarDatos();
    abstract function enviarRadicado();
    abstract function devolverValorUnitario();
}
?>