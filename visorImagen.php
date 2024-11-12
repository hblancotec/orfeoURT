<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "../sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}

if(isset($_REQUEST["ruta"])){
    // Get parameters
    $file = urldecode($_REQUEST["ruta"]); // Decode URL-encoded string
    
    /* Test whether the file name contains illegal characters
     such as "../" using the regular expression */
    //if(preg_match('/^[^.][-a-z0-9_.]+[a-z]$/i', $file)){
        $filepath = "bodega/guias/" . $file;
        
        // Process download
        if(file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            flush(); // Flush system output buffer
            readfile($filepath);
            die();
        } else {
            http_response_code(404);
            die();
        }
    //} else {
        //die("Invalid file name!");
    //}
}

?>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Imagen GUIA</title>
  <link rel="stylesheet" href="estilos/orfeo.css">
 </head>
 <body >
  <table style="width: 100%">
   <tr bgcolor="#006699">
	<td class="titulos4">
		<?php 
    		$dominio1 = $_SERVER['HTTP_HOST'];
    		$url = "http://" . "$dominio1" . $_GET['ruta']; 
		?>
		<img src="<?php echo $_GET['ruta'] ?>"  height="250" />
	</td>
   </tr>
  </table>
 </body>
</html>