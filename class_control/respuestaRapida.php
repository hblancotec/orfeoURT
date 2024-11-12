<?php
session_start();
$ruta_raiz = "..";

include_once("$ruta_raiz/config.php");
//include $ruta_raiz.'/tx/diasHabiles.php';
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//$db->conn->debug = true;
//$funcdias = new FechaHabil($db);
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
$ADODB_COUNTRECS = true;

require '../lib/Hash.php';
require '../lib/WsAuthHeader.php';
include_once "$ruta_raiz/include/tx/Historico.php";
$Historico = new Historico($db);

function objectToArray($d)
{
    if (is_object($d)) {
        $d = get_object_vars($d);
    }
    
    if (is_array($d)) {
        return array_map(__FUNCTION__, $d);
    } else {
        return $d;
    }
}

function return_bytes($val)
{	
    $val = trim($val);
    $ultimo = strtolower($val{strlen($val)-1});
    switch($ultimo)  {	// El modificador 'G' se encuentra disponible desde PHP 5.1.0
        case 'g':	$val *= 1024;
        case 'm':	$val *= 1024;
        case 'k':	$val *= 1024;
    }
    return $val;
}

class Color {
    //hex
    public $hex;
    
    // rgb
    public $red = 0;
    public $green = 0;
    public $blue = 0;
    
    // hsl
    public $hue = 0;
    public $saturation = 0;
    public $lightness = 0;
    
    // alpha
    public $alpha = 1;
    
    function __construct ($value) {
        $value = preg_replace('/\s/u', '', $value);
        
        // ex. : rgb(80, 0, 0), rgba(0, 0, 0, .5)
        if (strpos($value, 'rgb') !== false) {
            preg_match_all('/[0-9\.]+/u', $value, $matches);
            
            $components = $matches[0];
            
            $this->red = $components[0];
            $this->green = $components[1];
            $this->blue = $components[2];
            
            if (isset($components[3]) && $components[3] != 1) {
                $this->alpha = $components[3];
            }
            
            $this->setHslFromRgb();
            $this->setHex();
        }
        
        // ex. : hsl(0, 100%, 16%), hsla(0, 100%, 0%, .5)
        elseif (strpos($value, 'hsl') !== false) {
            preg_match_all('/[0-9\.]+/u', $value, $matches);
            
            $components = $matches[0];
            
            $this->hue = $components[0];
            $this->lightness = $components[1];
            $this->saturation = $components[2];
            
            $rgb = $this->hslToRgb($this->hue, $this->lightness, $this->saturation);
            
            $this->red   = $rgb['r'];
            $this->green = $rgb['g'];
            $this->blue  = $rgb['b'];
            
            if (isset($components[3]) && $components[3] != 1) {
                $this->alpha = $components[3];
            }
            
            $this->setHex();
        }
        
        // ex. : #00ff00", #fb0
        else {
            $value = str_replace('#', '', $value);
            $d = (strlen($value) / 3);
            
            preg_match_all('/[0-9a-fA-F]{' . $d . '}/u', $value, $matches);
            
            $bits = $matches[0];
            
            if ($d == 1) {
                array_walk($bits, function(&$item){ $item .= $item; });
            }
            
            $this->red = hexdec($bits[0]);
            $this->green = hexdec($bits[1]);
            $this->blue = hexdec($bits[2]);
            
            $this->setHslFromRgb();
            $this->setHex();
        }
    }
    
    public function setHex () {
        foreach (array($this->red, $this->green, $this->blue) as $color) {
            $color = dechex($color);
            $this->hex .= (strlen($color) < 2 ? '0' : '') . $color;
        }
    }
    
    public function setHslFromRgb () {
        $hsl = $this->rgbToHsl($this->red, $this->green, $this->blue);
        
        $this->hue = $hsl['h'];
        $this->saturation = $hsl['s'];
        $this->lightness = $hsl['l'];
    }
    
    // https://github.com/indyarmy/color2color
    public function rgbToHsl ($r, $g, $b) {
        $rgb = array();
        $rgb['r'] = ($r % 256) / 256;
        $rgb['g'] = ($g % 256) / 256;
        $rgb['b'] = ($b % 256) / 256;
        
        $max = max($rgb);
        $min = min($rgb);
        $hsl = array();
        $d;
        
        $hsl['l'] = ($max + $min) / 2;
        
        if ($max === $min) {
            $hsl['h'] = 0;
            $hsl['s'] = 0;
        }
        else {
            $d = ($max - $min);
            
            $hsl['s'] = $hsl['l'] > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
            
            switch ($max) {
                case $rgb['r']:
                    $hsl['h'] = ($rgb['g'] - $rgb['b']) / $d + ($rgb['g'] < $rgb['b'] ? 6 : 0);
                    break;
                case $rgb['g']:
                    $hsl['h'] = ($rgb['b'] - $rgb['r']) / $d + 2;
                    break;
                case $rgb['b']:
                    $hsl['h'] = ($rgb['r'] - $rgb['g']) / $d + 4;
                    break;
            }
            
            $hsl['h'] /= 6;
        }
        
        $hsl['h'] = round(($hsl['h'] * 360), 0);
        $hsl['s'] = round(($hsl['s'] * 100), 0);
        $hsl['l'] = round(($hsl['l'] * 100), 0);
        
        return $hsl;
    }
    
    public function hslToRgb ($h, $s, $l) {
        $rgb = array();
        $q = 0;
        $p = 0;
        $hsl = array(
            'h' => ($h % 360) / 360,
            's' => ($s % 101) / 100,
            'l' => ($l % 101) / 100
        );
        
        if ($hsl['s'] === 0) {
            $v = round(255 * $hsl['l']);
            $rgb = array('r'=>$v,'g'=>$v,'b'=>$v);
        }
        else {
            $q = $hsl['l'] < 0.5 ? $hsl['l'] * (1 + $hsl['s']) : $hsl['l'] + $hsl['s'] - $hsl['l'] * $hsl['s'];
            $p = 2 * $hsl['l'] - $q;
            
            $rgb['r'] = round(($this->hueToRgb( $p, $q, ($hsl['h'] + 1 / 3) ) * 256), 0);
            $rgb['g'] = round(($this->hueToRgb( $p, $q, $hsl['h'] ) * 256), 0);
            $rgb['b'] = round(($this->hueToRgb( $p, $q, ($hsl['h'] - 1 / 3) ) * 256), 0);
        }
        
        return $rgb;
    }
    
    public function hueToRgb ($p, $q, $t) {
        if ($t < 0) {
            $t += 1;
        }
        if ($t > 1){
            $t -= 1;
        }
        
        if ($t < 1 / 6) {
            return $p + ($q - $p) * 6 * $t;
        }
        if ($t < 1 / 2) {
            return $q;
        }
        if ($t < 2 / 3) {
            return $p + ($q - $p) * (2 / 3 - $t) * 6;
        }
        
        return $p;
    }
}

function debug($filename, $data) {
    file_put_contents($filename, $data, FILE_APPEND);
}

function CSSCleaner($css){
    // suppression des commentaires
    $css = preg_replace('/\/\*[^*]*\*+([^\/][^*]*\*+)*\//', '', $css);
    // nettoyage des retours à la ligne
    $css = str_replace(array("\r\n","\r"),"\n",$css);
    // optimisation des espaces et monolignage des selecteurs
    $css = preg_replace('/\s*([{:;,])\s*/', "$1", $css);
    $css = preg_replace('/\s*([}])\s*/', "$1\n", $css);
    // suppression des lignes vides
    $css = implode("\n",array_filter(array_map('trim',explode("\n",$css))));
    
    return $css;
}

function generarCSV($arreglo, $ruta, $delimitador, $encapsulador){
    $file_handle = fopen($ruta, 'w');
    foreach ($arreglo as $linea) {
        fputcsv($file_handle, $linea, $delimitador, $encapsulador);
    }
    rewind($file_handle);
    fclose($file_handle);
}

$fyh = BODEGAPATH . "debug_" . date('Ymd_His') . ".html";
//debug($fyh, "inicio script" . $fyh . "<br>");

$css = "<style> .image.image_resized { max-width: 100%; display: block; box-sizing: border-box; } " .
    " .image.image_resized img { width: 100%; } .image.image_resized > figcaption { display: block; } " .
    " .image > figcaption { display: table-caption; caption-side: bottom; word-break: break-word; color: var(--ck-color-image-caption-text); " .
    " background-color: var(--ck-color-image-caption-background); padding: .6em; font-size: .75em; outline-offset: -1px; } " .
    " .image { display: table; clear: both; text-align: center; margin: 0.9em auto; min-width: 50px; } " .
    " .image img { display: block; margin: 0 auto; max-width: 100%; min-width: 100%; } .image-inline { " .
    " display: inline-flex; max-width: 100%; align-items: flex-start; } .image-inline picture { display: flex; } " .
    " .image-inline picture, .image-inline img { flex-grow: 1; flex-shrink: 1; max-width: 100%; } " .
    " .image-style-block-align-left, .image-style-block-align-right { max-width: calc(100% - var(--ck-image-style-spacing)); } " .
    " .image-style-align-left, .image-style-align-right { clear: none; } .image-style-side { " .
    " float: right; margin-left: var(--ck-image-style-spacing); max-width: 50%; } .image-style-align-left { float: left; " .
    " margin-right: var(--ck-image-style-spacing); } .image-style-align-center { margin-left: auto; margin-right: auto; } " .
    " .image-style-align-right { float: right; margin-left: var(--ck-image-style-spacing); } .image-style-block-align-right { " .
    " margin-right: 0; margin-left: auto; } .image-style-block-align-left { margin-left: 0; margin-right: auto; } " .
    " p . .image-style-align-left, p . .image-style-align-right, p . .image-style-side { margin-top: 0; } " .
    " .image-inline.image-style-align-left, .image-inline.image-style-align-right { margin-top: var(--ck-inline-image-style-spacing); " .
    " margin-bottom: var(--ck-inline-image-style-spacing); } .image-inline.image-style-align-left { margin-right: var(--ck-inline-image-style-spacing); } " .
    " .image-inline.image-style-align-right { margin-left: var(--ck-inline-image-style-spacing); } .marker-yellow { background-color: var(--ck-highlight-marker-yellow); } " .
    " .marker-green { background-color: var(--ck-highlight-marker-green); } .marker-pink { background-color: var(--ck-highlight-marker-pink); } " .
    " .marker-blue { background-color: var(--ck-highlight-marker-blue); } .pen-red { color: var(--ck-highlight-pen-red); background-color: transparent; } " .
    " .pen-green { color: var(--ck-highlight-pen-green); background-color: transparent; } .text-tiny { font-size: .7em; } " .
    " .text-small { font-size: .85em; } .text-big { font-size: 1.4em; } .text-huge { font-size: 1.8em; } " .
    " hr { margin: 15px 0; height: 4px; background: hsl(0, 0%, 87%); border: 0; }pre { padding: 1em; color: hsl(0, 0%, 20.8%); background: hsla(0, 0%, 78%, 0.3); border: 1px solid hsl(0, 0%, 77%); " .
    " border-radius: 2px; text-align: left; direction: ltr; tab-size: 4; white-space: pre-wrap; font-style: normal; min-width: 200px; } pre code { background: unset; padding: 0; border-radius: 0; } " .
    " blockquote { overflow: hidden; padding-right: 1.5em; padding-left: 1.5em; margin-left: 0; margin-right: 0; font-style: italic; border-left: solid 5px hsl(0, 0%, 80%); blockquote { border-left: 0; " .
    " border-right: solid 5px hsl(0, 0%, 80%); } code { background-color: hsla(0, 0%, 78%, 0.3); padding: .15em; border-radius: 2px; } " .
    " .table > figcaption {display: table-caption; caption-side: top; word-break: break-word; text-align: center; color: var(--ck-color-table-caption-text); background-color: var(--ck-color-table-caption-background); " .
    " padding: .6em; font-size: .75em; outline-offset: -1px; } .table { margin: 0.9em auto; display: table; } .table table { border-collapse: collapse; border-spacing: 0; width: 100%; height: 100%; border: 1px double hsl(0, 0%, 70%); } " .
    " .table table td, .table table th { min-width: 2em; padding: .4em; border: 1px solid hsl(0, 0%, 75%); } .table table th { font-weight: bold; background: hsla(0, 0%, 0%, 5%); } .table th { text-align: right; } .table th { text-align: left; } " .
    " .page-break { position: relative; clear: both; padding: 5px 0; display: flex; align-items: center; justify-content: center; } .page-break::after { content: ''; position: absolute; border-bottom: 2px dashed hsl(0, 0%, 77%); width: 100%; } " .
    " .page-break__label { position: relative; z-index: 1; padding: .3em .6em; display: block; text-transform: uppercase; border: 1px solid hsl(0, 0%, 77%); border-radius: 2px; font-family: Helvetica, Arial, Tahoma, Verdana, Sans-Serif; font-size: 0.75em; font-weight: bold; color: hsl(0, 0%, 20%); background: hsl(0, 0%, 100%); box-shadow: 2px 2px 1px hsla(0, 0%, 0%, 0.15); -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; } .media { clear: both; margin: 0.9em 0; display: block; min-width: 15em; } .todo-list { list-style: none; } .todo-list li { margin-bottom: 5px; } .todo-list li .todo-list { margin-top: 5px; } .todo-list .todo-list__label > input { -webkit-appearance: none; display: inline-block; position: relative; width: var(--ck-todo-list-checkmark-size); height: var(--ck-todo-list-checkmark-size); vertical-align: middle; border: 0; left: -25px; margin-right: -15px; right: 0; margin-left: 0; } .todo-list .todo-list__label > input::before { display: block; position: absolute; box-sizing: border-box; content: ''; width: 100%; height: 100%; border: 1px solid hsl(0, 0%, 20%); border-radius: 2px; transition: 250ms ease-in-out box-shadow, 250ms ease-in-out background, 250ms ease-in-out border; } .todo-list .todo-list__label > input::after { display: block; position: absolute; box-sizing: content-box; pointer-events: none; content: ''; left: calc( var(--ck-todo-list-checkmark-size) / 3 ); top: calc( var(--ck-todo-list-checkmark-size) / 5.3 ); width: calc( var(--ck-todo-list-checkmark-size) / 5.3 ); height: calc( var(--ck-todo-list-checkmark-size) / 2.6 ); border-style: solid; border-color: transparent; border-width: 0 calc( var(--ck-todo-list-checkmark-size) / 8 ) calc( var(--ck-todo-list-checkmark-size) / 8 ) 0; transform: rotate(45deg); } .todo-list .todo-list__label > input[checked]::before { background: hsl(126, 64%, 41%); border-color: hsl(126, 64%, 41%); } .todo-list .todo-list__label > input[checked]::after { border-color: hsl(0, 0%, 100%); } .todo-list .todo-list__label .todo-list__label__description { vertical-align: middle; } span[lang] { font-style: italic; } .mention { background: var(--ck-color-mention-background); color: var(--ck-color-mention-text); } @media print { .page-break { padding: 0; } .page-break::after { display: none; } </style>";

$mensaje = $_POST['content'];
//debug($fyh, "Mensaje: " . $mensaje);

$mensaje = str_replace('class="image_resized"', "", $mensaje);
preg_match_all('/<img src="([^"]+)">/', $mensaje, $matches);
$imgs = $matches[0];
for ($i=0; $i < count($imgs); $i++ )
{
    $datoA = $imgs[$i];
    $dato = substr($imgs[$i], 0, strlen($imgs[$i]) - 1);
    $imgs[$i] = $dato."/>";
    $mensaje = str_replace($datoA, $imgs[$i], $mensaje);
}

preg_match_all('/<img  src="([^"]+)">/', $mensaje, $matches);
$imgs = $matches[0];
for ($i=0; $i < count($imgs); $i++ )
{
    $datoA = $imgs[$i];
    $dato = substr($imgs[$i], 0, strlen($imgs[$i]) - 1);
    $imgs[$i] = $dato."/>";
    $mensaje = str_replace($datoA, $imgs[$i], $mensaje);
}

$formats = array(
    '/(rgb|hsl)a?\((\s*[a-z0-9\.]{1,3}%?\s*,?){3,4}\)/i',
    '/#[0-9A-F]{3,6}/i'
);

$colors = array();
$clscss = array();
foreach ($formats as $format) {
    if( preg_match_all($format, $mensaje, $matches) ){
        $colors = array_merge($colors, $matches[0]);
    }
    if( preg_match_all($format, $css, $encuentra) ){
        $clscss = array_merge($clscss, $encuentra[0]);
    }
}

$_colors = array();
foreach ($colors as $V) {
    $V = preg_replace('/\s*/i', '', $V);
    $C = new Color($V);
    
    $_colors[] = "#".$C->hex;
}

$_clscss = array();
foreach ($clscss as $V) {
    $V = preg_replace('/\s*/i', '', $V);
    $C = new Color($V);
    
    $_clscss[] = "#".$C->hex;
}

$css = str_replace($clscss, $_clscss, $css);
$mensaje = str_replace($colors, $_colors, $mensaje);
$mensaje = str_replace(SERVIDOR . "bodega/", BODEGAPATH, $mensaje);
$mensaje = str_replace("<br>", "<br />", $mensaje);
$mensaje = str_replace("<figure ", "<div ", $mensaje);
$mensaje = str_replace("</figure>", "</div>", $mensaje);
$mensaje = str_replace("<p> </p>", "<br />", $mensaje);
$mensaje = str_replace("<p>", "<br /><p>", $mensaje);
$mensaje = str_replace("<div ", "<br /><div ", $mensaje);

preg_match_all('/<p style="([^"]+)">[\s]+</', $mensaje, $repetido);
$parrafo = $repetido[0];
for ($i=0; $i < count($parrafo); $i++ )
{
    $mensaje = str_replace($parrafo[$i]."/p>", "<br />", $mensaje);
}

if (strpos($mensaje, 'class="image image_resized image-style-side"'))
{
    //$mensaje = str_replace('class="image image_resized image-style-side"', "", $mensaje);
    //preg_match_all('<style="([^"]+)">', $mensaje, $encuentra);
    //$style = $encuentra[0][0];
    $mensaje = str_replace("class=\"image image_resized image-style-side\"", "style=\"width:90%; text-align: right;\"", $mensaje);
    //$mensaje = str_replace("<img ", "<img style=\"width: 70%; height: 70%;\"", $mensaje);
}
if (strpos($mensaje, 'class="image image_resized"'))
{
    //$mensaje = str_replace('class="image image_resized"', "", $mensaje);
    //preg_match_all('<style="([^"]+)">', $mensaje, $encuentra);
    //$style = $encuentra[0][0];
    $mensaje = str_replace("class=\"image image_resized\"", "style=\"width: 90%; text-align: center;\"", $mensaje);
    //$mensaje = str_replace("<img ", "<img style=\"width: 70%; height: 70%\"", $mensaje);
}
if (strpos($mensaje, 'class="image_resized"'))
{
    $mensaje = str_replace('class="image_resized"', "", $mensaje);
    preg_match_all('<style="([^"]+)">', $mensaje, $encuentra);
    $style = $encuentra[0][0];
    //$mensaje = str_replace("class=\"image_resized\"", "style=\"width: 300px; height: 300px;\"", $mensaje);
    $mensaje = str_replace($style, "style=\"width: 70% height: 70%\"", $mensaje);
}
if (strpos($mensaje, 'class="image"'))
{
    $mensaje = str_replace("class=\"image\"", "style=\"display: block; margin: 0 auto; max-width: 100%; min-width: 100%;\"", $mensaje);
}

if (strpos($mensaje, 'class="table"'))
{
    $mensaje = str_replace("class=\"table\"", "style=\"width: 90%; margin: 0 auto; text-align:left; display: table; position: absolute;\"", $mensaje);
    $mensaje = str_replace("<table>", "<table style=\"margin: auto; border-collapse: collapse; border-spacing: 0; border: 1px double hsl(0, 0%, 70%); width: 90%;\">", $mensaje);
    $mensaje = str_replace("<td>", "<td style=\"min-width: 2em; padding: .4em; border: 1px solid hsl(0, 0%, 75%);\">", $mensaje);
    $mensaje = str_replace("<th>", "<th style=\"min-width: 2em; padding: .4em; border: 1px solid hsl(0, 0%, 75%); font-weight: bold; background: hsla(0, 0%, 0%, 5%);\">", $mensaje);
}

$medidas = array();
if( preg_match_all('/([0-9]{1,3}|[0-9]{1,3}\.[0-9]{1,3})pt/i', $mensaje, $coincide) ){
    $medidas = array_merge($medidas, $coincide[0]);
}

$_medidas = array();
$i = 0;
foreach ($medidas as $V) {
    $V = substr($V, 0, strlen($V) - 2);
    if ($i == 0) {
        $px = number_format(($V / 0.75) + 1, 0, '.', '');
    } else {
        $px = number_format($V / 0.75, 0, '.', '');
    }
    $_medidas[] = $px."px";
    $i += 1;
}
$mensaje = str_replace($medidas, $_medidas, $mensaje);
//debug($fyh, "Mensaje: " . $mensaje);
//$mensaje = $css . $mensaje;


if ($_POST["pdf"]) {
    
    // Llamamos el Servicio de Radicacion
    $sql = "SELECT * FROM SGD_APLICACIONES APP WHERE APP.SGD_APLI_CODIGO = 1 ";
    $rs = $db->conn->Execute($sql);
    if($rs && !$rs->EOF){
        $wsServicios = trim($rs->fields['CLIENTE_WS_URLWSDL']);
        $usuario = trim($rs->fields['CLIENTE_WS_USUARIO']);
        $password = trim($rs->fields['CLIENTE_WS_PASSWORD']);
        //$wsServicios = "http://localhost:81/OrfeoService/OrfeoServiceWCF.svc?wsdl";
    }
    
    $radicadoPad = "";
    if (isset ( $_POST ['data'] )) {
        $data = json_decode ( $_POST ['data'] );
        if (is_array ( $data ) && count ( $data ) > 0) {
            $datosRegistroSeleccionado = ( array ) $data [0];
        }
        if (is_array ( $datosRegistroSeleccionado ) && count ( $datosRegistroSeleccionado ) > 0) {
            $radicadoPad = $datosRegistroSeleccionado ['RADI_NUME_RADI'];
        }
    }
    
    $parametros = array();
    $parametros['radicado'] = $radicadoPad;
    $parametros['mensaje'] = $mensaje;
    $parametros['dependencia'] = "900";
    $parametros['correos'] = $_POST['correos'];
    $parametros['servidor'] = SERVIDOR;
    
    $options = array(
        'style' => SOAP_RPC,
        'use' => SOAP_ENCODED,
        'soap_version' => SOAP_1_1,
        'cache_wsdl' => WSDL_CACHE_NONE,
        'connection_timeout' => 30,
        'trace' => true,
        'encoding' => 'UTF-8',
        'exceptions' => true,
        'features' => SOAP_SINGLE_ELEMENT_ARRAYS + SOAP_USE_XSI_ARRAY_TYPE
    );
    
    $client = new SoapClient($wsServicios, $options);
    
    $password = Hash::create('MD5', $password);
    $wsse_header_auth = new WsAuthHeader($usuario, $password);
    $client->__setSoapHeaders(array($wsse_header_auth));
    
    try {
        $respuestaRadicacion = $client->generarPDFRespuestaJSON($parametros);
    } catch (SoapFault $sf) {
        $respuesta = json_encode( Array ( "success" => false,
            "msg" => utf8_encode($sf->getMessage())
            ));
    } catch (Exception $e) {
        $respuesta = json_encode( Array ( "success" => false,
            "msg" => utf8_encode($e->getMessage())
            ));
    }
    
    $array = objectToArray($respuestaRadicacion);
    $responseArray = json_decode($array["generarPDFRespuestaJSONResult"], true);
    
    if (is_array($responseArray) && count($responseArray) > 0) {
        if ($responseArray["RespuestaRadicadoDT"][0]["estado"] == true) {
            $respuesta = json_encode(Array(
                "success" => true,
                "msg" => $responseArray["RespuestaRadicadoDT"][0]["mensaje"]
            ));
        } else {
            $respuesta = json_encode(Array(
                "success" => false,
                "msg" => utf8_encode("El servicio de radicación responde con el siguiente mensaje: ") . $responseArray["RespuestaRadicadoDT"][0]["mensaje"]
            ));
        }
    }
    
    echo $respuesta;
}
else 
{
    $band = true;
    if ($_POST['respuesta'] == 1) { 
        
        if (isset ( $_FILES )) {
            if (is_array ( $_FILES ) && count ( $_FILES ) > 0) {
                
                foreach ( $_FILES as $key => $value ) {
                    
                    if ($value ['size'] > return_bytes( ini_get ( 'upload_max_filesize' ) ) || (strlen ( $value ['name'] ) > 0 && ! $value ['size'])) {
                        $band = false;
                        $respuesta = json_encode( Array ( "success" => false,
                            "msg" => "Verifique el tamaño del Archivo: " . $value ['name'] . " Excede el tamaño permitido" . ini_get ( 'upload_max_filesize' ) . "!\n"
                            ));
                    }
                }
            }
        }
        
        if ($band == true) {

            
            $respuesta = null;
            $usuarioRadica = null;
            $usuarioDestino = null;
            $datosContacto = null;
            $datosRadicado = null;
            $datosDocumentoElectronico = null;
            $usuarioRadica ['UsuarioTXDT'] [] = Array (
                "login" => $_SESSION ['krd']
                );
            $usuarioDestino ['UsuarioTXDT'] [] = Array (
                "login" => $_SESSION ['krd']
                );
            if (isset ( $_POST ['data'] )) { // se capturan los datos del registro enviado desde la vista de carpetas
                
                $data = json_decode ( $_POST ['data'] );
                if (is_array ( $data ) && count ( $data ) > 0) {
                    $datosRegistroSeleccionado = ( array ) $data [0];
                }
                if (is_array ( $datosRegistroSeleccionado ) && count ( $datosRegistroSeleccionado ) > 0) {
                    // Se Obtienen los datos del contacto asociado.
                    if ($datosRegistroSeleccionado ['idCiudadano']) {
                        $datosContacto ['CiudadanoDT'] [] = Array (
                            "idCiudadano" => $datosRegistroSeleccionado ['idCiudadano']
                            );
                    }
                    if ($datosRegistroSeleccionado ['idEmpresa']) {
                        $datosContacto ['EmpresaDT'] [] = Array (
                            "idEmpresa" => $datosRegistroSeleccionado ['idEmpresa']
                            );
                    }
                    if ($datosRegistroSeleccionado ['idEntidad']) {
                        $datosContacto ['EntidadDT'] [] = Array (
                            "idEntidad" => $datosRegistroSeleccionado ['idEntidad']
                            );
                    }
                    if ($datosRegistroSeleccionado ['loginFuncionario']) {
                        $datosContacto ['FuncionarioDT'] [] = Array (
                            "login" => $datosRegistroSeleccionado ['loginFuncionario']
                            );
                    }
                    // Se configuran los datos propios de la radicacion.
                    $datosRadicado ['DatosRadicacionDT'] [] = array_map ( "htmlentities", Array (
                        "asunto" => "Respuesta al radicado No." . $datosRegistroSeleccionado ['RADI_NUME_RADI'],
                        "tipoRadicado" => 1,
                        "medioRecepcionEnvio" => 112,
                        "fechaOficio" => date ( 'Y/m/d' ),
                        "noRadicadoPadre" => $datosRegistroSeleccionado ['RADI_NUME_RADI']
                        ) );
                } else {
                    $respuesta = json_encode( Array ( "success" => false,
                        "msg" => "No se envio la informacion con la variable data en formato JSON correctamente por el metodo POST, verifiquela"
                        ));
                    $band = false;
                }
                
                if ($band == true) // Importante validar que no hayan ocurrido errores al configurar las variables para el paso 1.
                {
                    if(isset($_FILES)) {
                        $path = "";
                        if(is_array($_FILES) && count($_FILES) > 0) {
                            foreach ($_FILES as $key => $value) {
                                
                                if ($value ["error"] == UPLOAD_ERR_OK && strlen ( $value ["tmp_name"] ) > 0) {
                                    $tmp_name = $value ["tmp_name"];
                                    $name = $value ["name"];
                                    $ext = pathinfo($name, PATHINFO_EXTENSION);
                                    
                                    $img = file_get_contents($tmp_name);
                                    $data = base64_encode($img);
                                    
                                    $datosDocumentoElectronico ['datosDocumentoElectronico'] [] = array_map ( "htmlentities", Array (
                                        "fileBase64Binary" => $data,
                                        "extension" => $ext,
                                        "nombre" => $name
                                        ) );
                                    
                                    $zip = new ZipArchive();
                                    $filename = BODEGAPATH."/tmp/Adjuntos_".date(YmdHmi).".zip";
                                    
                                    if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
                                        $respuesta = json_encode( Array ( "success" => false,
                                            "msg" => utf8_encode("cannot open: $filename")
                                        ));
                                        echo $respuesta;
                                        exit();
                                    } 
                                    
                                    $zip->addFile($tmp_name, $name);
                                    
                                    /*$local_file = BODEGAPATH ."tmp/". $archivo;
                                    $server_file = $archivo;
                                    
                                    $conn_id = ftp_ssl_connect("10.10.21.35");
                                    $login_result = ftp_login($conn_id, "srvapporfeo", "P896c8P6o5Rh8VDts");
                                    
                                    if (ftp_put($conn_id, "tmp/$name", $tmp_name, FTP_ASCII)) {
                                    //if (ftp_get($conn_id, $tmp_name, "tmp/$name", FTP_BINARY)) {
                                        echo "Successfully written to $local_file </br>";
                                    }
                                    else {
                                        echo "There was a problem </br>";
                                        die("Error descargando el archivo del ftp ");
                                    }
                                    ftp_close($conn_id);*/
                                }
                            }
                            
                            $resultZip = $zip->close();
                            
                            $nomFile = basename($filename);
                            if ($resultZip) {
                                $strFile = file_get_contents($filename);
                                $datazip = base64_encode($strFile);
                            }
                            
                            $datosDocumentoElectronico ['datosRadicado'] [] = array_map ( "htmlentities", Array (
                                "esPrincipal" => 'false',
                                "NoRadicado" => 0,
                                "observacion" => 'Documento Anexo '
                                ) );
                        } else {
                            
                            $datosDocumentoElectronico ['datosDocumentoElectronico'] [] = array_map ( "htmlentities", Array (
                                "fileBase64Binary" => "",
                                "extension" => "",
                                "nombre" => ""
                                ) );
                            
                            $datosDocumentoElectronico ['datosRadicado'] [] = array_map ( "htmlentities", Array (
                                "esPrincipal" => 'false',
                                "NoRadicado" => 0,
                                "observacion" => 'Documento Anexo '
                                ) );
                        }
                    }
                    
                    
                    if ($band == true) 
                    {
                        // Llamamos el Servicio de Radicacion
                        $sql = "SELECT * FROM SGD_APLICACIONES APP WHERE APP.SGD_APLI_CODIGO = 1 ";
                        $ADODB_COUNTRECS = true;
                        $rs = $db->conn->Execute($sql);
                        $ADODB_COUNTRECS = false;
                        if($rs && !$rs->EOF){
                            $wsServicios = trim($rs->fields['CLIENTE_WS_URLWSDL']);
                            $usuario = trim($rs->fields['CLIENTE_WS_USUARIO']);
                            $password = trim($rs->fields['CLIENTE_WS_PASSWORD']);
                            //$wsServicios = "http://localhost:81/OrfeoService/OrfeoServiceWCF.svc?wsdl";
                        }
                        
                        /*$respuesta = $mensaje;
                        $radicado_salida = "20219000012341";
                        $directorio     = $ruta_raiz . '/bodega/2021/900/docs/';
                        $archivo_final  = "20219000012341_0012.pdf";
                        $archivo_grabar = $directorio . $archivo_final;
                        include "../respuestaRapida/crear_pdf.php";*/
                        
                        if ($usuarioRadica == null || $usuarioDestino == null || $datosContacto == null || $datosRadicado == null || $datosDocumentoElectronico == null) 
                        {
                            $respuesta = json_encode(Array ( "success" => false,
                                "msg" => utf8_encode("Faltan datos para la radicación ")
                                ));
                        }
                        else {
                            
                            $servicioWeb = $wsServicios; // url del servicio
                            $parametros = array();
                            $parametros['usuarioRadica'] = json_encode($usuarioRadica);
                            $parametros['usuarioDestino'] = json_encode($usuarioDestino);
                            $parametros['datosContacto'] = json_encode($datosContacto, JSON_NUMERIC_CHECK);
                            $parametros['datosRadicacion'] = json_encode($datosRadicado);
                            $parametros['datosDocumentoElectronico'] = json_encode($datosDocumentoElectronico);
                            $parametros['correos'] = $_POST['correos'];
                            $parametros['mensaje'] = $mensaje;
                            
                           /* foreach ($parametros as $value) {
                                $valores .= $value."\n";
                            }
                            $respuesta = json_encode( Array ( "success" => false,
                                "msg" => $valores
                            ));
                            echo $respuesta;
                            exit();*/
                            
                            $options = array(
                                'style' => SOAP_RPC,
                                'use' => SOAP_ENCODED,
                                'soap_version' => SOAP_1_1,
                                'cache_wsdl' => WSDL_CACHE_NONE,
                                'connection_timeout' => 30,
                                'trace' => true,
                                'encoding' => 'UTF-8',
                                'exceptions' => true,
                                'features' => SOAP_SINGLE_ELEMENT_ARRAYS + SOAP_USE_XSI_ARRAY_TYPE
                            );
                            
                            $client = new SoapClient($servicioWeb, $options);
                            
                            $password = Hash::create('MD5', $password);
                            $wsse_header_auth = new WsAuthHeader($usuario, $password);
                            $client->__setSoapHeaders(array($wsse_header_auth));
                            
                            try {
                                $respuestaRadicacion = $client->respuestaRapidaJSON($parametros);
                            } catch (SoapFault $sf) {
                                $respuesta = json_encode( Array ( "success" => false,
                                    "msg" => utf8_encode($sf->getMessage())
                                    ));
                            } catch (Exception $e) {
                                $respuesta = json_encode( Array ( "success" => false,
                                    "msg" => utf8_encode($e->getMessage())
                                    ));
                            }
                            
                            $array = objectToArray($respuestaRadicacion);
                            $responseArray = json_decode($array["respuestaRapidaJSONResult"], true);
                            
                            if (is_array($responseArray) && count($responseArray) > 0) {
                                if ($responseArray["RespuestaRadicadoDT"][0] ["estado"] == false ) {
                                    $respuesta = json_encode( Array ( "success" => false,
                                        "msg" => utf8_encode("La respuesta del servicio de radicación no fué la adecuada. " . $responseArray["RespuestaRadicadoDT"][0]["mensaje"])
                                        ));
                                } else {
                                    if ($responseArray["RespuestaRadicadoDT"][0]["estado"] == true && $responseArray["RespuestaRadicadoDT"][0]["NoRadicado"])
                                    {
                                        $radicado = $responseArray["RespuestaRadicadoDT"][0]["NoRadicado"];
                                        
                                        require_once '../orfeo.api/libs/conexionServicioExterno.php';
                                        $servicio = new servicioExterno($db);
                                        
                                        /*$arreglo = array();
                                        $correos = explode(",", $_POST['correos']);
                                        foreach ($correos as $value) {
                                            if ($value != "") {
                                                $arreglo[] = array($datosRegistroSeleccionado['SGD_DIR_NOMREMDES'], $value);
                                            }
                                        }
                                        $ruta = BODEGAPATH."/tmp/Destinatarios_".date(YmdHmi).".csv";
                                        generarCSV($arreglo, $ruta, $delimitador = ';', $encapsulador = '"');
                                        $strFile = file_get_contents($ruta);
                                        $datacsv = base64_encode($strFile);*/
                                        
                                        /*$cuerpoMail = "<html><head></head><body><br /><table width=\"80%\" style=\"border: 2px solid ROYALBLUE\" cellpadding=\"10\"><tr><td><table width=\"100%\"><tr><td><img src=\"https://orfeo.dnp.gov.co/img/escudo.jpg\" width=\"200\"></td><td><b>Comunicaci&oacute;n Oficial.</td></tr><tr><td colspan=\"2\" align=\"center\">El Sistema de Gesti&oacute;n Documental ORFEO informa que:</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\" align=\"justify\">XYX</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr></table></td></tr></table></body></html>";
                                        $asunto = "(" . $radicado . "_1) Respuesta del Departamento Nacional de Planeacional DNP a su solicitud No. " . $datosRegistroSeleccionado ['RADI_NUME_RADI'];
                                        $cuerpo = "<table width=\"100%\"><tr><td>El Departamento Nacional de Planeaci&oacute;n ha dado respuesta a su solicitud No. " . $datosRegistroSeleccionado ['RADI_NUME_RADI'] . " mediante el oficio de salida No. " . $radicado . ", el cual tambi&eacute;n puede ser consultado en el portal Web del DNP.</p><br /><b><center>Si no puede visualizar el correo, " . "o los archivos adjuntos, puede consultarlos tambi&eacute;n en la siguiente direcci&oacute;n: <br />" . "<a href=\"https://pqrsd.dnp.gov.co/consulta.php?rad=" . $datosRegistroSeleccionado ['RADI_NUME_RADI'] . "\">" . "https://pqrsd.dnp.gov.co/consulta.php </a><br> DNP </b></center></td></tr></table>";
                                        $cuerpm = str_replace('XYX', $cuerpo, $cuerpoMail);*/
                                        
                                        if (trim($datosRegistroSeleccionado['SGD_DIR_NOMREMDES']) == "" || $datosRegistroSeleccionado['SGD_DIR_NOMREMDES'] == null) {
                                            $datosRegistroSeleccionado['SGD_DIR_NOMREMDES'] = "N/A";
                                        }
                                        
                                        $cuerpoMail = "Comunicación Oficial. \r\n El Sistema de Gestión Documental ORFEO informa que: \r\n ";
                                        $asunto = "(" . $radicado . "_1) Respuesta del Departamento Nacional de Planeación (DNP) a su solicitud No. " . $datosRegistroSeleccionado ['RADI_NUME_RADI'];
                                        $cuerpoMail .= "El Departamento Nacional de Planeación ha dado respuesta a su solicitud No. " . $datosRegistroSeleccionado ['RADI_NUME_RADI'] . " mediante el oficio de salida No. " . $radicado . ", el cual también puede ser consultado en el portal Web del DNP. \r\n Si no puede visualizar el correo, o los archivos adjuntos, puede consultarlos en el siguiente enlace: https://pqrsd.dnp.gov.co/consulta.php ";
                                        
                                        $asunto = iconv('iso_8859-1', 'utf-8', $asunto);
                                        $cuerpoMail = iconv('iso_8859-1', 'utf-8', $cuerpoMail);
                                        $correos = explode(",", $_POST['correos']);
                                        foreach ($correos as $value) {
                                            if ($value != "" && $value != "web_ggi@dnp.gov.co") {
                                                $correoval = explode(";", $value);
                                                foreach ($correoval as $valor) {
                                                    $response = $servicio->RegistrarMensaje($radicado, $asunto, $cuerpoMail, $datosRegistroSeleccionado['SGD_DIR_NOMREMDES'], trim($valor), $datazip, $nomFile, $wsdl472);
                                                }
                                            }
                                        }
                                        //$response = $servicio->RegistrarMensaje($radicado, $asunto, $cuerpm, "listaDestinatarios.csv", $datacsv, $datazip, $nomFile, $wsdl472);
                                        
                                        $respuesta = json_encode( Array ( "success" => true,
                                            "msg" => $radicado
                                            ));
                                    } else {
                                        $respuesta = json_encode( Array ( "success" => false,
                                            "msg" => utf8_encode("El servicio de radicación responde con el siguiente mensaje: " . $responseArray["RespuestaRadicadoDT"][0]["mensaje"])
                                            ));
                                    }
                                }
                            }
                        }
                    } 
                }
                
                
            } else {
                $respuesta = json_encode( Array ( "success" => false,
                    "msg" => utf8_encode("Uno de sus archivos adjuntos supera el tamaño permitido(No data POST), verifíquela")
                    ));
            }
            
            echo $respuesta;
        }
    }
}