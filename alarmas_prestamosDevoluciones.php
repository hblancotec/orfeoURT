<?php 
	###########################################################################
	##	Archivos requeridos para ejecutar este script
	set_time_limit(0);
	$ruta_raiz = "./";
	include_once ("include/db/ConnectionHandler.php");
	###########################################################################

	
	try {
		$db = new ConnectionHandler($ruta_raiz);
		$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
		$db->conn->debug = false;
	}
	catch(Exception $e) {
		echo $e->getMessage();
	}
	
	echo "Inicia Alarmas: ".date('Y/m/d_h:i:s')."\n\r";

	###########################################################################
	###	CUENTA DE CORREO PARA ENVIAR LAS COPIAS
	$sqlCC = "SELECT USUA_EMAIL FROM USUARIO WHERE USUA_VENC_PRESTAMO = 1";
	$rs = $db->conn->Execute($sqlCC);
	while(!$rs->EOF) {
	    $umail = $rs->fields['USUA_EMAIL'];
	    $usuaCC[] = $umail;
	    echo "Correo copia: ".$umail."\n\r";
	    $rs->MoveNext();
	}
	###########################################################################

	###########################################################################
	## SE BUSCAN LOS RADICADOS QUE ESTAN PRESTADOS POR TIEMPO DEFINIDO
	## Y LOS USUARIOS A LOS CUALES LES DEBEN LLEGAR ALERTAS
	
	$isql =	"SELECT	DISTINCT P.RADI_NUME_RADI,
					U.USUA_LOGIN,
					U.USUA_CODI,
					U.USUA_NOMB,
					U.USUA_EMAIL,
					D.DEPE_NOMB,
					U.DEPE_CODI,
                    P.PRES_FECH_PEDI,
					P.PRES_FECH_VENC
			FROM 	PRESTAMO P
					JOIN USUARIO U ON 
						U.USUA_LOGIN = P.USUA_LOGIN_ACTU AND 
						U.USUA_ESTA = 1
					JOIN DEPENDENCIA D ON 
						D.DEPE_CODI = U.DEPE_CODI
					JOIN RADICADO R ON
						R.RADI_NUME_RADI = P.RADI_NUME_RADI AND
						R.RADI_DEPE_ACTU NOT IN (900,999)
			WHERE	P.PRES_ESTADO = 2 AND
					(GetDate()+0) >= P.PRES_FECH_VENC AND
					P.PRES_FECH_PRES >= '2014-09-01'
			ORDER BY USUA_NOMB";
	
	$result = $db->conn->Execute($isql);
	###########################################################################

	###########################################################################
	###	ALMACENA DATOS PROCESADOS PARA EL ENVIO DE LAS ALERTAS

	while(!$result->EOF) {
		
							
		$radicado = $result->fields['RADI_NUME_RADI'];
		$depeCodi = $result->fields['DEPE_CODI'];
		$usuaCodi = $result->fields['USUA_CODI'];
		$nombre	  = $result->fields['USUA_NOMB'];
		$usuaMail = $result->fields['USUA_EMAIL'];
		$fechPres = $result->fields['PRES_FECH_PEDI'];
		$fechVenc = $result->fields['PRES_FECH_VENC'];
		
		enviarCorreo($radicado, $usuaMail, $nombre, $fechPres, $fechVenc, $usuaCC);
		
		$result->MoveNext();
	}
		
	###########################################################################
	###	FUNCION QUE GENERA EL ENVIO DE LAS ALERTAS A TRAVES DE CORREO
	function enviarCorreo($noRad, $correo, $nombre, $fechPres, $fechVenc, $conCopia) {
	/* Description: funcion que genera el envio de alertas al correo electronico
     * @param , $noRad: variable donde vienen los numeros de radicados
     * @param , $mail:	variable donde vienen el correo del usuario
     * @param , $mailJ: variable donde viene el correo del jefe del usuario
     * @return, n/a
     * @Creado Feb de 2013
	 * @Actualizado Ago de 2014
     */
		
		$ruta_raiz = "";
		include_once ("config.php");
		require_once ORFEOCFG."class_control/correoElectronico.php";
		include_once ORFEOCFG."include/db/ConnectionHandler.php";
		
		try {
			$db = new ConnectionHandler($ruta_raiz);
			$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
		}
		catch(Exception $e) {
			echo $e->getMessage();
		}
		
		echo "Usuario: $nombre con los siguientes radicados prestados en estado vencido : ". $noRad . "\n\r";
		
		$result = "-1";
		$asunto = "Orfeo-DNP Alerta Préstamo de Radicados";
		/*$cuerpo = "Sr(a). " . $nombre . " actualmente el radicado Nro. $noRad (Documento Físico) se encuentra prestado y está a su nombre. <br> 
                    El documento fue prestado el día: 13 – Julio – 2010.
                    El préstamo vence (io) el día: 23 – Julio – 2010
                    Por favor cerciórese de devolver el documento al Grupo de Biblioteca y Archivo o renueve el préstamo. <br><br> ";*/
		$cuerpo = "<table width='80%'>
                                                	<tr>
                                                		<td><img src='https://orfeo.dnp.gov.co/img/escudo.jpg'></td>
                                                		<td><b>Comunicaci&oacute;n Oficial.</b></td>
                                                	</tr>
                                                	<tr>
                                                		<td colspan='2'>&nbsp;</td>
                                                	</tr>
                                                	<tr>
                                                		<td colspan='2'>El Sistema de Gesti&oacute;n Doumental ORFEO notifica que:</td>
                                                	</tr>
                                                	<tr>
                                                		<td colspan='2'>&nbsp;</td>
                                                	</tr>
                                                	<tr>
                                                		<td colspan='2'>Señor $nombre : actualmente el radicado Nro. $noRad (Documento Físico) se encuentra prestado y esta a su nombre.</td>
                                                	</tr>
                                                    <tr>
                                                		<td colspan='2'>El documento fue prestado el día: $fechPres.</td>
                                                	</tr>
                                                    <tr>
                                                		<td colspan='2'>El préstamo vence (io) el día: $fechVenc.</td>
                                                	</tr>
                                                    <tr>
                                                		<td colspan='2' align='center'><b>Por favor cerciórese de devolver el documento al grupo de Archivo o renueve el préstamo del documento</b></td>
                                                	</tr>
                                                	<tr>
                                                		<td colspan='2' align='center'><b>&nbsp;</b></td>
                                                	</tr>
                                                	<tr>
                                                		<td colspan='2' align='center'><b>DNP</b></td>
                                                	</tr>
                                                </table>";
		
		$objMail = new correoElectronico(ORFEOCFG);
		$objMail->FromName = "Notificaciones Orfeo";
		
		if (is_array($conCopia)) {
            foreach ($conCopia as $key => $dest) {
				echo "Con Copia: ".$dest."\n\r";
			}
		}
					
		$result = $objMail->enviarCorreo(array($correo), $conCopia, null, $asunto, $cuerpo);
		echo "Respuesta envío: ".$result."\n\r";
		unset($correo);
		unset($conCopia);
		//unset($cco);
		return $result;
	}
	###########################################################################
?>