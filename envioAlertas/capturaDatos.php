<?php
	include(ORFEOPATH . "config.php");
	include_once(ORFEOPATH . "include/db/ConnectionHandler.php");
    $ruta_raiz = '..';
	//$db = new ConnectionHandler("$ruta_raiz");
	$db = new ConnectionHandler(ORFEOPATH);
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	//$db->conn->debug = true;
	$flujos 	= array();	    // que va a contener los flujos puestos en el sistema
	$usuaEnviar 	= array();	// Usuario a enviar
	$idFlujoAnt 	= 0;
	$getUsuaExp 	= "";		// Contiene la sentencia SQL que consulta los usuario con los Expedientes
	$numeDiasMax 	= 2;		// Contiene el numero de dias empezara el sistema a enviarle 
					            // las alertas antes de que se cumpla el plazo
	$numeDiasDesMax = 1;		/* Contiene el numero de dias que le va avisar el sistema 
					             * despues a la fecha de cumplimiento 
					             */ 
	$codigoFlujo 	= 0;
	$contUsuario 	= 0;		// Variable que se encarga de controlar el numero
                                //  de usuarios que se le va a enviar correo
	$unDiaUnix	= 86400;	    // Cuantos segundos tiene un Unix timeStamp
	$fechaHoyUnix 	= time();
	//$fechaInicial 	= $fechaHoy - mktime(0,0,0,0,$numeDiasMax,0);	// fecha inicial formato unixa
	$fechaInicial 	= $fechaHoy - $numeDiasMax * 86400;	// fecha inicial formato unix
	$enviaAlerta 	= array();		// Arreglo de usuarios a los que le van a llegar correo

	// Capturando los flujos que se encuentran en el sistema
	$getFlujos = "SELECT	PEXP.SGD_PEXP_CODIGO AS CODIGO_FLUJO,
                        FEXP.SGD_FEXP_CODIGO AS CODIGO_ETAPA,
                        FEXP.SGD_FEXP_ORDEN AS ORDEN_ETAPA,
                        FEXP.SGD_FEXP_TERMINOS AS NUM_DIAS_PROCESO,
                        FEXP.SGD_FEXP_DESCRIP AS NOMBRE_ETAPA
                    FROM 	SGD_PEXP_PROCEXPEDIENTES PEXP INNER JOIN
                        SGD_FEXP_FLUJOEXPEDIENTES FEXP ON 
                        PEXP.SGD_PEXP_CODIGO = FEXP.SGD_PEXP_CODIGO
                        ORDER BY PEXP.SGD_PEXP_CODIGO, FEXP.SGD_FEXP_ORDEN";
	
	$rsFlujos = $db->conn->Execute($getFlujos);
	while (!$rsFlujos->EOF){
		$flujos[$rsFlujos->fields["CODIGO_FLUJO"]]["CODIGO_FLUJO"]	= $rsFlujos->fields["CODIGO_FLUJO"];
		$flujos[$rsFlujos->fields["CODIGO_FLUJO"]]["CODIGO_ETAPA"] 	= $rsFlujos->fields["CODIGO_ETAPA"] = 1;
		$flujos[$rsFlujos->fields["CODIGO_FLUJO"]]["ORDEN_ETAPA"] 	= $rsFlujos->fields["ORDEN_ETAPA"] = 1;
		$flujos[$rsFlujos->fields["CODIGO_FLUJO"]]["NUM_DIAS_PROCESO"] 	= $rsFlujos->fields["NUM_DIAS_PROCESO"] = 1;
		$flujos[$rsFlujos->fields["CODIGO_FLUJO"]]["NOMBRE_ETAPA"] 	= $rsFlujos->fields["NOMBRE_ETAPA"] = 1;
		$rsFlujos->MoveNext();
	}
	// MAX(" . $db->conn->SQLDate('Y/m/d h:m:sA', 'HFLD.SGD_HFLD_FECH') . ") AS ULTIMA_FECH_MODIFICACION
	
	foreach ($flujos as $flujo) {
		$codigoFlujo = $flujo["CODIGO_FLUJO"];
		$getUsuaExp = "SELECT	SEXP.SGD_EXP_NUMERO,
					SEXP.USUA_DOC_RESPONSABLE,
					SEXP.SGD_SEXP_SECUENCIA,
					USUA.USUA_NOMB AS NOMBRE_USUARIO,
					USUA.USUA_EMAIL AS EMAIL,
					FEXP.SGD_FEXP_CODIGO AS CODIGO_ETAPA,
					FEXP.SGD_FEXP_ORDEN AS ORDEN_ETAPA,
					FEXP.SGD_FEXP_TERMINOS AS DIAS_PROCESO,
					FEXP.SGD_FEXP_DESCRIP AS NOMBRE_ETAPA,
					MAX(" . $db->conn->SQLDate('Y/m/d H:m:s', 'HFLD.SGD_HFLD_FECH') . ") AS ULTIMA_FECH_MODIFICACION
				FROM	SGD_SEXP_SECEXPEDIENTES SEXP,
					SGD_HFLD_HISTFLUJODOC HFLD,
					SGD_FEXP_FLUJOEXPEDIENTES FEXP,
					SGD_PEXP_PROCEXPEDIENTES PEXP,
					USUARIO USUA,
                    SGD_USD_USUADEPE USD
				WHERE   USUA.USUA_DOC = SEXP.USUA_DOC_RESPONSABLE AND
                    USUA.USUA_LOGIN = USD.USUA_LOGIN AND
                    USUA.USUA_DOC = USD.USUA_DOC AND
                    USD.SGD_USD_DEFAULT = 1 AND
					USD.DEPE_CODI = SEXP.DEPE_CODI AND
					(SEXP.SGD_PEXP_CODIGO = $codigoFlujo) AND 
					(HFLD.SGD_EXP_NUMERO = SEXP.SGD_EXP_NUMERO) AND
					(HFLD.SGD_TTR_CODIGO NOT IN (57, 58, 59)) AND
					SEXP.SGD_PEXP_CODIGO = PEXP.SGD_PEXP_CODIGO AND
					SEXP.SGD_FEXP_CODIGO = FEXP.SGD_FEXP_CODIGO
				GROUP BY SEXP.SGD_EXP_NUMERO,
					SEXP.USUA_DOC_RESPONSABLE,
					SEXP.SGD_SEXP_SECUENCIA,
					USUA.USUA_NOMB,
					USUA.USUA_EMAIL,
					FEXP.SGD_FEXP_CODIGO,
					FEXP.SGD_FEXP_ORDEN,
					FEXP.SGD_FEXP_TERMINOS,
					FEXP.SGD_FEXP_DESCRIP
				ORDER BY SEXP.SGD_EXP_NUMERO";
		$rsUsuaExp = $db->conn->Execute($getUsuaExp);
		
		while(!$rsUsuaExp->EOF) {
			$numDiasProceso 	= ($rsUsuaExp->fields["DIAS_PROCESO"] < 0) ? 0 : $rsUsuaExp->fields["DIAS_PROCESO"];
			$numDiasProcesoUnix = $numDiasProceso * $unDiaUnix;
			$ultimaFechaMod 	= $rsUsuaExp->fields["ULTIMA_FECH_MODIFICACION"];
			$ultimaFechaModArray= explode("/",$ultimaFechaMod);	// Pos 0 Ano, Pos 1 Mes, Pos 2 Dia
			$horaFechaModArray	= explode(" ",$ultimaFechaMod);
			$hmsFechaModArray	= explode(":",$horaFechaModArray[1]);
			$ultimaFechaModUnix = gmmktime(intval($hmsFechaModArray[0]),
                                                intval($hmsFechaModArray[1]),
                                                intval($hmsFechaModArray[2]),
                                                intval($ultimaFechaModArray[1]),
                                                intval($ultimaFechaModArray[2]),
                                                intval($ultimaFechaModArray[0]));
			$fechaFinProcesoUnix= $ultimaFechaModUnix + $numDiasProcesoUnix;
			$fechaFinProceso 	= date("j/m/Y G:i:s",$fechaFinProcesoUnix);
			$fechaInicialUnix 	= $fechaFinProcesoUnix - mktime(0,0,0,0,$numeDiasMax,0);
			$fechaInicialUnix	= $fechaHoyUnix;
			$numeroDiasUnix 	= $fechaHoyUnix - $ultimaFechaModUnix;
			$numeroDias 		= date('z', $numeroDiasUnix);
			//if ($fechaHoyUnix <= $fechaFinProcesoUnix && $fechaHoyUnix >= $fechaInicialUnix ) {
			if ($fechaHoyUnix >= $fechaInicialUnix) {
				$enviarAlerta[$contUsuario]["NOMBRE_USUARIO"] = $rsUsuaExp->fields["NOMBRE_USUARIO"];
				$enviarAlerta[$contUsuario]["EMAIL"] 	= $rsUsuaExp->fields["EMAIL"];
				$enviarAlerta[$contUsuario]["NUM_EXPEDIENTE"] = $rsUsuaExp->fields["SGD_EXP_NUMERO"];
				$enviarAlerta[$contUsuario]["NOMBRE_FLUJO"] = $rsUsuaExp->fields[""];
				$enviarAlerta[$contUsuario]["NOMBRE_ETAPA"] = $rsUsuaExp->fields["NOMBRE_ETAPA"];
				$enviarAlerta[$contUsuario]["NUM_DIAS"] 	= $numeroDias;
				$enviarAlerta[$contUsuario]["FECHA_FINALIZACION"] = $fechaFinProceso;
				$contUsuario++;
			}
			$rsUsuaExp->MoveNext();
		}
	}
	exit();
?>
