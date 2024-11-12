<?php
	set_time_limit(0);
	echo "Inicia Alarmas: ".date('Y/m/d_h:i:s')."\n";
	#############################################################################
	##	ARCHIVOS REQUERIDOS PARA EJECUTAR ESTE SCRIPT
//	$ruta_raiz = ".";
//	require dirname(__FILE__)."/class_control/correoElectronico.php";
//	include_once dirname(__FILE__)."/class_control/class_gen.php";
//	define('ADODB_ASSOC_CASE', 1);
    #############################################################################
	
	
	require dirname(__FILE__) . "/config.php";
	require dirname(__FILE__) . '\\lib\\adodb\\adodb.inc.php';
	$dsnn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
	$conn = NewADOConnection($dsnn);
	
	if ($conn) {
	    $conn->SetFetchMode(ADODB_FETCH_ASSOC);
	
    	###########################################################################
    	###	SE CALCULA LA FECHA DE 2 MESES ATRAS, A PARTIR DE LA FECHA ACTUAL
    	$ano_ini = date("Y");
    	$mes_ini = substr("00".(date("m")-3),-2);
    	$dia_ini = date("d");
    	
    	switch ($mes_ini){
    		case '0':
    			$mes_ini="12";
    			$ano_ini = date("Y") - 1;
    			break;
    		case '-1':
    			$mes_ini="11";
    			$ano_ini = date("Y") - 1;
    			break;
    		case '-2':
    			$mes_ini="10";
    			$ano_ini = date("Y") - 1;
    			break;
    	}
    	
    	$fecha_ini = "$ano_ini/$mes_ini/$dia_ini";
    	###########################################################################
    	### SE CONSULTAS LAS ALERTAS EN ESTADO ACTIVO
    	$sql = "SELECT	* 
    			FROM	SGD_ALERTAS
    			WHERE	SGD_ESTADO_ALER = 1";
    	$rs_aler = $conn->Execute($sql);
    	while ($arr_al = $rs_aler->FetchRow()){
    		$g = ''; 
    		$d = '';
    		for ($i = 1; $i < 10; $i++) {
    			if ($arr_al["SGD_TRAD".$i."G_ALER"] == 1){
    				$g .= $i;
    			}
    			if ($arr_al["SGD_TRAD".$i."D_ALER"] == 1){
    				$d .= $i;
    			}	
    		}
    
    	
    		### CONSULTA DE LOS RADICADOS QUE ESTAN TIPIFICADOS CON TIPO DOCUMENTAL QUE PUEDE GENERAR ALERTA
    		### Y QUE FUE RADICADO ENTRE LA FECHA ACTUAL Y HASTA 3 MESES ATRAS.
    		$query = "	SELECT	R.RADI_NUME_RADI AS RADICADO,
    							R.RADI_FECH_RADI AS FECHA_RAD,
    							D.SGD_TPR_DESCRIP AS TIPO_DOC,
    							U1.USUA_NOMB	AS USUA_ON, 
    							U1.USUA_EMAIL	AS USUA_OM,
    							U2.USUA_NOMB	AS USUA_CN,
    							U2.USUA_EMAIL	AS USUA_CM
    					FROM	RADICADO AS R
    							JOIN SGD_TPR_TPDCUMENTO D ON
    								R.TDOC_CODI=D.SGD_TPR_CODIGO
    							INNER JOIN USUARIO U1 ON
    								R.RADI_USUA_ACTU = U1.USUA_CODI AND
    								R.RADI_DEPE_ACTU = U1.DEPE_CODI
    							INNER JOIN USUARIO U2 ON
    								U2.USUA_DOC = '".$arr_al['SGD_USUADOC_ALER']."'
    					WHERE	RADI_NUME_RADI LIKE '%[$g]' AND
    							R.TDOC_CODI=".$arr_al['SGD_TDOC_ALER']." AND 
    							R.RADI_FECH_RADI >= '$fecha_ini' AND
    							R.RADI_DEPE_ACTU <> 999";
    		$rsQ = $conn->Execute($query);
    		foreach ($rsQ as $qRs) {
    				
    			$enviaMail = 1; // Bandera que determina si se envia o no correo electronico (1 indica que SI; 0 indica que NO)
    			$flag = 0;
    			$fechaRad	= $qRs['FECHA_RAD'];
    			$radicado	= $qRs['RADICADO'];
    			$tipoDoc	= $qRs['TIPO_DOC'];
    			$termino	= $arr_al['SGD_DIASTER_ALER'];
    			$iniAlerta	= $termino - $arr_al['SGD_DIASANT_ALER'];
    			$finAlerta	= $termino + $arr_al['SGD_DIASDES_ALER'];
    					
    			echo "<br/><br/>Radicado No.: ".$radicado. " con Fecha: ".$fechaRad;
    				
    			## SE CONSULTA SI EL RADICADO YA TIENE RESPUESTA
    			$sqlResp = "SELECT	A.RADI_NUME_SALIDA,
    								R.RADI_PATH
    						FROM	ANEXOS A
    								LEFT JOIN RADICADO R ON
    									R.RADI_NUME_RADI = A.RADI_NUME_SALIDA
    						WHERE	A.ANEX_RADI_NUME = ".$qRs['RADICADO']." AND
    								A.RADI_NUME_SALIDA LIKE '%[$d]' AND
    								A.ANEX_SALIDA = 1";
    			$rsResp = $conn->Execute($sqlResp);
    				
    			$pathResp  = $rsResp->fields['RADI_PATH'];
    			$respuesta = $rsResp->fields['RADI_NUME_SALIDA'];
    				
    			## SE VERIFICA SI EXISTE UNA RESPUESTA Y SI TIENE IMAGEN ASOCIADA
    			if($respuesta && substr($pathResp, -3, 3) == pdf) {
    				$enviaMail = 0;
    				$flag = 1;
    			}
    			
    			## SE INVOCA LA FUNCION SUMADIASHABILES, PARA DETERMINAR LA FECHA DE VENCIMIENTO
    			$sqlFec		= "SELECT dbo.sumadiashabiles('$fechaRad', $termino)";
    			$fecVence	= $conn->getone($sqlFec);
	
    					
    			## SE INVOCA LA FUNCION  diashabilestramite, PARA DETERMINAR LA CANTIDAD DE DIAS QUE HAN
    			## TRANSCURRIDO DESDE QUE SE GENERO EL RADICADO HASTA EL DIA ACTUAL
    			$sqlDiasV	= "SELECT dbo.diashabilestramite('$fechaRad', GetDate())";
    			$diasTram	= $conn->getone($sqlDiasV);
    				
    			$diasRest = $termino - $diasTram; //dias restantes para dar respuesta
    			
    			if ($flag == 1){
    				$diasRest = 0;
    			}
    					
    			$fecVence	= substr($fecVence,0,10);
    				
    			###	ACTUALIZACION DE FECHAS
    			//echo "	Se actualiza la fecha de vencimiento: " .$fecVence." el termino legal
    			//		es: " .$termino. " y han transcurrido " .$diasTram." dias desde la radicacion";
    
    			/*$update = "	UPDATE	RADICADO
    						SET		RADI_FECHA_VENCE = '$fecVence',
    								RADI_DIAS_VENCE = $diasRest
    						WHERE	RADI_NUME_RADI = $radicado";
    			$rsUp = $db->conn->Execute($update);*/
    				
    			## SI $diasTram SE ENCUENTRA ENTRE LOS DIAS DE $iniAlerta y $finAlerta, Y 
    			## LA VARIABLE $enviaMail = 1, ENTRA PARA GENERAR ALERTA AL CORREO				
    			if ( $diasTram >= $iniAlerta && $diasTram <= $finAlerta){
    				if ($enviaMail == 1){
    									
    					$correos = array();
    					$usuaCC	= array();
    					$result = "-1";
    						
    					if($dias < 0) {
    						$diasN = $diasRest*(-1);
    						$diasMsg = "Hace ".$diasN." d&iacute;a(s) venci&oacute; el Radicado No: ";
    					}
    					else{
    						$diasMsg = "En ".$diasRest." d&iacute;a(s) h&aacute;bil(es) se vence el Radicado No: ";
    					}
    						
    					###	ASUNTO DEL CORREO
    					$asunto = "OrfeoDNP Alerta Radicado Nro: ".$radicado;
    						
    					###	CONTENIDO DEL CORREO
    					$cuerpo = $diasMsg . $radicado." de tipo ".$tipoDoc.", que fue
    								radicado en la fecha ".$fechaRad." y vence(i&oacute;)
    								el d&iacute;a ".$fecVence.", por favor dar tramite a este documento.";
    						
    					###	CUENTA DE CORREO PARA ENVIAR LAS ALERTAS
    					$correos[] = $qRs['USUA_OM'];
    						
    					###	CUENTA DE CORREO PARA ENVIAR COPIA DE LAS ALERTAS
    					$usuaCC[] = $qRs['USUA_CM'];
    						
    					###	CUENTA DE CORREO PARA ENVIAR LAS COPIAS OCULTAS
    					$cco = 'ajmartinez@dnp.gov.co';
    						
    					### SE ENVIA CORREO ELECTRONICO CON LA ALERTA DEL RADICADO
    					//$objMail = new correoElectronico(".");
    					//$objMail->FromName = "Notificaciones Orfeo";
    					//$result = $objMail->enviarCorreo($correos, $usuaCC, array($cco), $asunto, $cuerpo);
    					
    					include_once dirname(__FILE__) . "/envioEmail.php";
    					$objMail = new correo();
    					$result = $objMail->enviarCorreo($correos, $usuaCC, array($cco), $cuerpo, $asunto);
    					echo $result;

    					echo "<br/>Correo de alerta a: ".$arr_rad['USUA_OM']." con copia a: ".$arr_rad['USUA_CM']." <br/>Con cuerpo: $cuerpo<br/>";
    				}
    			}
    		}
    	}
	}
?>