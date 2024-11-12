<?php 
	set_time_limit(0);
	$ruta_raiz = "./";
	include_once ("config.php");
	include_once ("include/db/ConnectionHandler.php");

	try {
	    $db = new ConnectionHandler($ruta_raiz);
	    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
		
		include_once(ORFEOCFG."include/tx/Historico.php");
		$hist = new Historico($db);
		
		### BUSCAMOS LOS RADICADOS QUE ESTAN CON SOLICITUD DE PRESTAMO
		### Y HAN PASADO MAS DE 5 DIAS DESDE LA FECHA DE SOLICTUD Y NO
		### HAN IDO A RECOGER  EL DOCUMENTO FISICO.
		$isql =	"SELECT P.RADI_NUME_RADI,
						P.PRES_ID,
						PRES_FECH_PEDI,
						CAST((GetDate()) - P.PRES_FECH_PEDI as numeric) AS DIAS_PEDIDO,
						dbo.diashabiles( GETDATE(), P.PRES_FECH_PEDI) as DIASHABILES
				FROM 	PRESTAMO P
				WHERE	(P.PRES_ESTADO = 1 OR P.PRES_ESTADO = 5)
						AND P.PRES_FECH_PEDI >= '2014-05-01' AND
						dbo.diashabiles( GETDATE(), P.PRES_FECH_PEDI) > 2";
		
		$rsPedidos = $db->conn->Execute($isql);
		
		while(!$rsPedidos->EOF) {
			$presId		= $rsPedidos->fields['PRES_ID'];
			$radicado[0]= $rsPedidos->fields['RADI_NUME_RADI'];
			$rad		= $rsPedidos->fields['RADI_NUME_RADI'];
			$fechaPedido= $rsPedidos->fields['PRES_FECH_PEDI'];
			$diasPedido	= $rsPedidos->fields['DIASHABILES'];
					
			$iSqlCancel = "	UPDATE 	PRESTAMO
							SET 	PRES_FECH_CANC = (GetDate()),
									PRES_ESTADO = 4,
									USUA_LOGIN_CANC = 'ARCHSALIDA1'
							WHERE  	PRES_ID = $presId AND
									RADI_NUME_RADI = $rad";
			
			if($db->conn->query($iSqlCancel)){
				$observa = "Cancelacion Automatica de Radicado.  Tiempo de espera ($diasPedido)";
				$hist->insertarHistorico($radicado,999,1,999,1,$observa,74);
				
				echo "<br> El radicado No. ".$rad." con Id_Pres No. ".$presId. " cuya
					fecha de solicitud fue el ".$fechaPedido." y el tiempo de espera es
					de ".$diasPedido." ha sido cancelado automaticamente"; 
			}
			$rsPedidos->MoveNext();
		}
	}
	catch(Exception $e) {
	    echo $e->getMessage();
	}
		
	
?>
