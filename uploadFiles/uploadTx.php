<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "../sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_FILES, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}
$ruta_raiz = "..";
if(!$_SESSION['dependencia']) include_once "$ruta_raiz/rec_session.php";
error_reporting(0);
require "../config.php";
/**
 * Retorna la cantidad de bytes de una expresion como 7M, 4G u 8K.
 * @param string $var
 * @return numeric 
 * */
function return_bytes($val)
{	$val = trim($val);
	$ultimo = strtolower($val{strlen($val)-1});
	switch($ultimo)  {	// El modificador 'G' se encuentra disponible desde PHP 5.1.0
		case 'g':	$val *= 1024;
		case 'm':	$val *= 1024;
		case 'k':	$val *= 1024;
	}
	return $val;
}

/*  REALIZAR TRANSACCIONES
 *  Este archivo realiza las transacciones de radicados en Orfeo.
 */
?>
<html>
<head>
	<title>Realizar Transaccion - Orfeo </title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link rel="stylesheet" href="../estilos/orfeo.css">
</head>
<?php
/**
* Inclusion de archivos para utilizar la libreria ADODB
*/
   include_once "$ruta_raiz/config.php";
   include_once "$ruta_raiz/include/db/ConnectionHandler.php";
   $db = new ConnectionHandler("$ruta_raiz");
   $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	/*
	* Genreamos el encabezado que envia las variable a la paginas siguientes.
	* Por problemas en las sesiones enviamos el usuario.
	* @$encabezado  Incluye las variables que deben enviarse a la singuiente pagina.
	* @$linkPagina  Link en caso de recarga de esta pagina.
	*/
	$encabezado = "".session_name()."=".session_id()."&krd=$krd&depeBuscada=$depeBuscada&filtroSelect=$filtroSelect&tpAnulacion=$tpAnulacion";

/*  FILTRO DE DATOS
 *  @$setFiltroSelect  Contiene los valores digitados por el usuario separados por coma.
 *  @$filtroSelect Si SetfiltoSelect contiene algunvalor la siguiente rutina realiza el arreglo de la condici? para la consulta a la base de datos y lo almacena en whereFiltro.
 *  @$whereFiltro  Si filtroSelect trae valor la rutina del where para este filtro es almacenado aqui.
 *
 */
if($checkValue)
{
	$num = count($checkValue);
	$i = 0;
	while ($i < $num)
	{
		$record_id = key($checkValue);
		$setFiltroSelect .= $record_id ;
		$radicadosSel[] = $record_id;
		if($i<=($num-2))
		{
			$setFiltroSelect .= ",";
		}
  	next($checkValue);
	$i++;
	}
	if ($radicadosSel)
	{
		$whereFiltro = " and b.radi_nume_radi in($setFiltroSelect)";
	}
}
 if($setFiltroSelect)
 {
		$filtroSelect = $setFiltroSelect;
 }
//session_start();
//if (!$dependencia or !$nivelus)  include "./rec_session.php";
$causaAccion = "Asociar Imagen a Radicado";
?>
<body>
<br>
<?php
/**
 * Aqui se intenta subir el archivo al sitio original
 */
$ruta_raiz = "..";
include ("$ruta_raiz/include/upload/upload_class.php"); //classes is the map where the class file is stored (one above the root)
$max_size = return_bytes(ini_get('upload_max_filesize')); // the max. size for uploading
$my_upload = new file_upload;
$my_upload->language="es";
$my_upload->upload_dir = "$ruta_raiz/bodega/tmp/"; // "files" is the folder for the uploaded files (you have to create this folder)
$my_upload->extensions = array(".tif", ".pdf"); // specify the allowed extensions here
//$my_upload->extensions = "de"; // use this to switch the messages into an other language (translate first!!!)
$my_upload->max_length_filename = 50; // change this value to fit your field length in your database (standard 100)
$my_upload->rename_file = true;
if(isset($_POST['Realizar'])) {
	$tmpFile = trim($_FILES['upload']['name']);
	
	$date = date_create(date('Y-m-d h:m:s'));
	$date = date_format($date,"YmdHis");
	
	$newFile = $valRadio."_".$date."_".mt_rand(1,99999);
	$uploadDir = BODEGAPATH . substr($valRadio,0,4)."/".substr($valRadio,4,4)."/";
	$fileGrb = substr($valRadio,0,4)."/".substr($valRadio,4,4)."/$newFile".".".substr($tmpFile,-3);
	$my_upload->upload_dir = $uploadDir;

	$my_upload->the_temp_file = $_FILES['upload']['tmp_name'];
	$my_upload->the_file = $_FILES['upload']['name'];
	$my_upload->http_error = $_FILES['upload']['error'];
	$my_upload->replace = (isset($_POST['replace'])) ? $_POST['replace'] : "n"; // because only a checked checkboxes is true
	$my_upload->do_filename_check = (isset($_POST['check'])) ? $_POST['check'] : "n"; // use this boolean to check for a valid filename
	//$new_name = (isset($_POST['name'])) ? $_POST['name'] : "";
	if ($my_upload->upload($newFile)) {
		// new name is an additional filename information, use this to rename the uploaded file
		$full_path = $my_upload->upload_dir.$my_upload->file_copy;
		$reallyFullPath = BODEGAPATH . $fileGrb;
		$info = $my_upload->get_uploaded_file_info($full_path);
		if( (strtolower(substr($tmpFile,-3))=="pdf") || (strtolower(substr($tmpFile,-3))=="tif") || (strtolower(substr($tmpFile,-4))=="tiff") ) {
		    try {
		        if (strtolower(substr($tmpFile,-3))=="pdf") {
					$pdftext = file_get_contents($full_path);
		            $pagecount = preg_match_all("/\/Page\W/", $pdftext,$dummy);
		        }else
		        {
		            $im = new Imagick($reallyFullPath);
		            //$datos = $im->identifyimage();
		            $pagecount = $im->getnumberimages();
		            $im->destroy();
		        }
		    } catch (Exception $e) {
		        if (strtolower(substr($tmpFile,-3))=="pdf") {
		            // Hacemos la misma lógica que si fuera TIFF. No 
		            // la hago de una arriba porque es más eficaz el conteo de página de la forma inicial. 
		            $im = new Imagick($full_path);
		            //$datos = $im->identifyimage();
		            $pagecount = $im->getnumberimages();
		            $im->destroy();
		        } else $pagecount=0;
		    }
		} else $pagecount=0;
	}
	else{
		die("<table class=borde_tab><tr><td class=titulosError>Ocurrio un Error la Fila no fue cargada Correctamente <p>".$my_upload->show_error_string()."<br><blockquote>".nl2br($info)."</blockquote></td></tr></table>");
	}
} else{
	die("<table class=borde_tab><tr><td class=titulosError>Error inexperado. <b>Archivo no cargado en sistema</b></td></tr></table>");
}

include "$ruta_raiz/include/tx/Historico.php";
include "$ruta_raiz/include/tx/Radicacion.php";
$hist = new Historico($db);
$rad = new Radicacion($db);
$noRadicadoImagen = $valRadio;
$radicadosSel[] = $valRadio;
$band=false;

if(substr($valRadio,-1,1)=="6" && $rad->getRadicadoSuifp($valRadio)){
      include "../webServices/clienteRadicadoCerrar.php";
      if($a){
          $band=($a[key($a)]['exitoso']=="true")?true:false;
          $msg=$a[key($a)]['mensaje'];
          if($band){

                $hist->insertarHistorico($radicadosSel,  $dependencia , $codusuario, $dependencia, $codusuario, "Respuesta Suifp ".$msg, 77);
                $sql = "INSERT INTO SGD_HIST_IMG_RAD ( RADI_NUME_RADI, RUTA, USUA_DOC, USUA_LOGIN, FECHA, ID_TTR_HIAN) VALUES
                        		( $valRadio, '".$fileGrb."', '".$_SESSION['usua_doc']."', '".$krd."',".$db->conn->sysTimeStamp.",42)";
                $db->conn->Execute($sql);
          }
          else{ 
            $info="<br><span class='titulosError'>No es posible Asociar imagen en Orfeo</span>";
            $hist->insertarHistorico($radicadosSel,  $dependencia , $codusuario, $dependencia, $codusuario, "Respuesta Suifp ".$msg, 77);
            $hist->insertarHistorico($radicadosSel,  $dependencia , $codusuario, $dependencia, $codusuario, "No es posible Asociar imagen en Orfeo.", 42);
          }
          $msg="<br><table cellspace=2 WIDTH=60% id=tb_general align='left' class='borde_tab'>
                <tr><td colspan='2' class='titulosError'>
                $msg
                </td></tr>
                </table>"; 
       }
       else{
            $info= "<br><span class='titulosError'>Servidor de SUIFP no responde</span>";
            $hist->insertarHistorico($radicadosSel,  $dependencia , $codusuario, $dependencia, $codusuario, "No se pudo cerrar autom?ticamente el tr?mite en SUIFP - Servidor no responde", 77);
            $hist->insertarHistorico($radicadosSel,  $dependencia , $codusuario, $dependencia, $codusuario, "No es posible Asociar imagen en Orfeo. - Servidor de SUIFP no responde", 42);
            $msg="<br><table cellspace=2 WIDTH=60% id=tb_general align='left' class='borde_tab'>
                <tr><td colspan='2' class='titulosError'>
                Debe intentar de nuevo asociar Imagen a Radicado.
                </td></tr>
                </table>";
       }
 }
 else{
      $band=true;
 }

 //IBISCOM 2018-12-13
 //Función que inserta un registro en la tabla de metadatos para un anexo a un expediente
 function cambioMetadatos(	    $db	    ,$codigo	    ,$hash	    ,$folios	    ,$nombre_proyector	    ,$nombre_revisor
 ,$nombre_firma	    ,$palabras_clave,$fechaProduccion)	{
     $funcionHash = 'sha1';
     $id_tipo=0;//aplica si es para un anexo al radicado(0) o un anexo al expediente(1)
     
     $codigoSQL =  "SELECT ANEX_CODIGO as codigoPa
                     FROM ANEXOS
                     WHERE ANEX_RADI_NUME = '$codigo' AND RADI_NUME_SALIDA IS NOT NULL";
     $codigo = $db->conn->Execute($codigoSQL)->fields["codigoPa"];
     
     $validaSQL = "SELECT COUNT('$codigo') as NumMetad FROM METADATOS_DOCUMENTO WHERE id_anexo =  '$codigo'";
     $resultadoContador = $db->conn->Execute( $validaSQL)->fields["NumMetad"];
     
     if($resultadoContador == 0 ){
         $insertSQL = "INSERT INTO METADATOS_DOCUMENTO ".
             "VALUES ('$codigo',$id_tipo,'$hash','$funcionHash','$folios','$nombre_proyector','$nombre_revisor','$nombre_firma','$palabras_clave',NULL,'$fechaProduccion')";
         
         $insertMetadatos	= $db->conn->Execute( $insertSQL);
     }else{
         //HACER UPDATE
         $updateSQL = "UPDATE METADATOS_DOCUMENTO ".
             " SET hash = '$hash', folios = '$folios' , fecha_produccion = '$fechaProduccion', palabras_clave = '$palabras_clave', nombre_proyector = '$nombre_proyector' , nombre_revisor= '$nombre_revisor'
               WHERE id_anexo = $codigo ";
         $UpMetadatos	= $db->conn->Execute($updateSQL);
     }
 };
 //IBISCOM 2018-12-13
 
if($band){
    //IBISCOM 2018-12-13
    if ($ocultaDocElectronico == 1) {
        $palabras_clave		= $_POST['palabrasClave'];
        $folios         	= $_POST['folios'];
        $nombre_proyector    = $_POST['nombreProyector'];
        $nombre_revisor      = $_POST['nombreRevisor'];
        $fechaProduccion = date("Y")."-".date("m")."-".date("d");
        $hash = hash_file("sha1",BODEGAPATH.$fileGrb);
        $codigoDOC = $valRadio;
        cambioMetadatos($db,$codigoDOC , $hash, $folios , $nombre_proyector, $nombre_revisor, NULL, $palabras_clave,$fechaProduccion);
    }
    //IBISCOM 2018-12-13
    
    $query = "update radicado set radi_path='$fileGrb', radi_nume_hoja=$pagecount  where radi_nume_radi=$valRadio";
    //$db->conn->debug = true;
    if($db->conn->Execute($query)) {
        $codTx = 42;	//Codigo de la transacci?n
        $hist->insertarHistorico($radicadosSel,  $dependencia , $codusuario, $dependencia, $codusuario, $observa, $codTx);
        $sql = "INSERT INTO SGD_HIST_IMG_RAD ( RADI_NUME_RADI, RUTA, USUA_DOC, USUA_LOGIN, FECHA, ID_TTR_HIAN) VALUES
        		( $valRadio, '".$fileGrb."', '".$_SESSION['usua_doc']."', '".$krd."',".$db->conn->sysTimeStamp.",$codTx)";
        $db->conn->Execute($sql);
        $cerrarRadicado = "Sip";
    }
    else {
            $msg= "<hr>No actualizo la BD <hr>";
            $cerrarRadicado = "Nop";
    }
} else{


} 
?>
<table cellspace=2 WIDTH=60% id=tb_general align="left" class="borde_tab">
	<tr>
		<td colspan="2" class="titulos4">ACCI&Oacute;N REQUERIDA --> <?=$causaAccion ?> </td>
	</tr>
	<tr>
		<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">ACCI&Oacute;N REQUERIDA :
	</td>
		<td  width="65%" height="25" class="listado2_no_identa">
		ASOCIACI&Oacute;N DE IMAGEN A RADICADO
		</td>
	</tr>
	<tr>
		<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">RADICADOS INVOLUCRADOS :</td>
		<td  width="65%" height="25" class="listado2_no_identa"><?=$valRadio?> </td>
	</tr>
	<tr>
		<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">Datos Fila Asociada :</td>
		<td  width="65%" height="25" class="listado2_no_identa">  <?=$info?>  </td>
	</tr>
	<tr>
		<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">FECHA Y HORA : </td>
		<td  width="65%" height="25" class="listado2_no_identa">  <?=date("m-d-Y  H:i:s")?> </td>
	</tr>
	<tr>
		<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">USUARIO ORIGEN: </td>
		<td  width="65%" height="25" class="listado2_no_identa"> <?=$_SESSION['usua_nomb']?> </td>
	</tr>
	<tr>
		<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">DEPENDENCIA ORIGEN: </td>
		<td  width="65%" height="25" class="listado2_no_identa"> <?=$_SESSION['depe_nomb']?> </td>
	</tr>
</table>
<?=$msg?>
</form>
</body>
</html>
