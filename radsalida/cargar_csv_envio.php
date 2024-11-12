<?php
	session_start();
	if( empty($_FILES['file']['name']) ) {
		$msg2 = "Por favor asegurese de seleccionar un archivo en formato CVS";
	}
	
	### SE ABRE EL ARCHIVO CSV, CARGADO POR EL USUARIO
	$fp = fopen ($_FILES['file']['tmp_name'], "r" );
	if ($fp){
		$j = 0;
		
		### SE RECORRE EL ARCHIVO
		while (( $data = fgetcsv ($fp ,1000,",")) !== FALSE ) {
			
			### EN LA FILA 3 DEL ARCHIVO CSV QUE ARROJA EL SIPOST, ES DONDE VIENEN
			### LOS NOMBRES DE LAS COLUMNAS, EN ESTA SECCIÓN SE IDENTIFICA EN QUE
			### POSICION DE COLUMUNA VIENEN LOS CAMPOS REQUERIDOS PARA ACTUALIZAR EN ORFEO
			### OJO: SI CAMBIAN LOS NOMBRES DE LAS COLUMNAS EN SIPOST, SE DEBEN CAMBIAR LOS CASE
			if ($j == 3){
				$i = 0;
				foreach($data as $row) {
					switch ($row) {
						case 'ORDEN_SERVICIO':
							$orden = $i;
							break;
						case 'ENVIO':
							$envio = $i;
							break;
						case 'REFERENCE':
							$ref = $i;
							break;
					}
					$i++;
				}
			}
			
			### DE LA FILA 4 EN ADELANTE VIENEN LOS DATOS DE ENVIO DE CADA RADICADO
			if ($j >= 4){
				$i = 0;

				foreach($data as $row) {
					### echo " Campo $i : $row "; // Muestra todos los campos de la fila actual

					### SE EVALUAN LAS COLUMNAS COMPARANDOLAS CON LA POSICIÓN
					### DEL TITULO Y SE ASIGNA SU VALOR A UNA VARIABLE
					switch ($i) {
						case $orden:
							$ordServ = $row;
							break;
						case $envio:
							$guia = $row;
							break;
						case $ref:
							$rad = $row;
							break;
					}
					$i++;
				} // Cierra el foreach ($data as $row)
				$resp = registrar($ordServ, $guia, $rad, $j);
				$res .= "<br/>".$resp;
			} // Cierra el if ($j >= 4)	
			$j++;
		} // Cierra el While
		$j = $j - 4;
		$msg2 = "Se procesaron ".$j." registros";
	} // Cierra el if ($fp)
	else{
		$msg2 = "Por favor intente cargar el archivo de nuevo, no fue posible abrir el archivo";
	}
	fclose ( $fp );
	$res .= "<br/>".$msg2;
	
	
	### FUNCION QUE ACTUALIZA LOS ENVIO CON NUMERO DE GUIA Y ORDEN DE SERVICIO
	function registrar($ordServ, $guia, $rad, $j)
	{
		$ruta_raiz = "..";
		include_once "$ruta_raiz/include/db/ConnectionHandler.php";
		$db = new ConnectionHandler("$ruta_raiz");
		$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	
		### VIENE EL No. DE RADICADO
		if ($rad){
				
			### SE EXTRAE No. RADICADO Y No COPIA
			$radi = substr($rad,0,14);
			$copia = substr($rad,15);
			
			### VIENE EL No. DE GUIA
			if ($guia){
					
				### VIENE EL No. DE ORDEN DE SERVICIO
				if ($ordServ){
						
					### VIENEN TODOS LOS DATOS, SE HACE EL UPDATE
					$sqlUp = "	UPDATE	SGD_RENV_REGENVIO
								SET		SGD_RENV_PLANILLA = '$ordServ',
										SGD_RENV_NUMGUIA = '$guia'
								WHERE	RADI_NUME_SAL = $radi AND
										SGD_DIR_TIPO = $copia";
					$rsUp = $db->conn->Execute($sqlUp);
					$cntRows = $db->conn->Affected_Rows();
					if ($cntRows > 0){
						$msg = "Radicado No. ".$radi." cargado correctamente,
								con No. gu&iacute;a ".$guia." y planilla No.".$ordServ;
					}
					else {
						$msg = "No se actualiz&oacute; el radicado No. ".$radi." porque
								no se encontr&oacute; el radicado en Orfeo";
					}	
				}
				### NO VIENE EL No. DE ORDEN DE SERVICIO
				else{
					$msg = "El radicado n&uacute;mero ".$radi." no pudo ser cargado porque no se encontr&oacute; el No. de Orden de Servicio";;
				}
			}
			### NO VIENE EL No. DE GUIA
			else{
				$msg = "El radicado n&uacute;mero ".$radi." no pudo ser cargado porque no se encontr&oacute; el No. la gu&iacute;a";
			}
		}
		### VIENE EL No. DE RADICADO
		else{
			$n = $j - 5;
			$msg = "El registro número ".$n." no pudo ser cargado porque no contiene radicado";
		}
		return $msg;
	}
?>

<html>
	<head>
		<title> Resultado del cargue de archivos csv</title>
		<link rel="stylesheet" href="../estilos/orfeo.css">
	</head>
	<body>
		<table align="center">
			<tr>
				<td class="titulos2" align="center">
					Resultado del cargue de archivo
				</td>
			</tr>
			<tr>
				<td>
					
		<?php
			echo $res;
		?>
				</td>
			</tr>
			<tr>
				<td align="center">
					<br/>
					<a href='cargar_envio.php'> Cargar otro archivo </a>
				</td>
			</tr>
		</table>
	</body>
</html>