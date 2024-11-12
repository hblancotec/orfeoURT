<?php
	define('ADODB_ASSOC_CASE', 0);
	$ruta_raiz 	= "../..";
    require_once('../../_conf/constantes.php');
	require_once(ORFEOPATH . "include/db/ConnectionHandler.php");
	require_once("HTML/Template/IT.php");

    // Constantes de configuracion
    define('RADI_WEB_PATH', ORFEOPATH . 'radicacionWeb/');
    define('QRS_PATH', RADI_WEB_PATH . 'radicacionQrs/');
    define('QRS_TPL', QRS_PATH . 'tpl/');
	$pathEstilos 	= "../css/";
	$estilo 	    = "estilosQrs.css";
	$tituloPagina 	= "Formulario Qrs";
	$archivoExec	= "verificacion.php";
	$estilosRadicacion = $pathEstilos . $estilo;
	$paginaDeInicio = "http://www.superservicios.gov.co/";
	$ipServidor 	= "orfeo.superservicios.gov.co:81";
	$archivoExec	= "http://$ipServidor/~cmauricio/orfeo3.6/radicacionWeb/radicacionQrs/verificacion.php";
	$db             = new ConnectionHandler(ORFEOPATH);
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    //$db->conn->debug = true;
	$tpl            = new HTML_Template_IT(QRS_TPL);
	$comenzarRad    = (!empty($HTTP_POST_VARS["radiQrs"])) ? $HTTP_POST_VARS["radiQrs"] : null;
	$comenzarRad    = 1;


	if(!empty($comenzarRad)) {
		// Capturando municipios del departamento para construir select
		// si tiene un departamento asignado
		if (!empty($departamento["depto_codi"])) {
			$municipio = (empty($municipio["codigo"])) ?
					null : $municipio["codigo"];
			$sqlMuni = "SELECT muni_nomb, muni_codi
                        FROM municipio
                        WHERE dpto_codi = '". $departamento["depto_codi"] .
                        "' ORDER BY 1";
			$res = $db->conn->Execute($sqlMuni);
			$selectMuni = $res->GetMenu2('municipio[codigo]',
						$municipio,
						false,
						false,
						0,
						'id="municipio" class="select"');
		} else {
			$selectMuni = '<select id="municipio" name="municipio[codigo]"  class="select">'. "\n".
                            '<option value="1" > Seleccione Municipio </option>' . "\n" .
                            '</select>';
		}

		$sql = "SELECT dpto_nomb,
                        dpto_codi
                    FROM departamento
                    WHERE id_pais = 170
                    ORDER BY dpto_nomb";
		$res = $db->conn->Execute($sql);

		// Si no tiene asignado ningun departamento entonces muestra
		// en el select la palabra todos
		$departamento = (empty($departamento["depto_codi"])) ?
					1 : $departamento["depto_codi"];

		$selectDepto = $res->GetMenu2('departamento[depto_codi]',
						$departamento,
						false,
						false,
						0,
						'onChange="javascript:cambiar_seccion(this);" class="select"');

		$sql = "SELECT DPTO_CODI,
				MUNI_CODI,
				MUNI_NOMB
			FROM municipio ORDER BY muni_nomb";

		$res = $db->conn->Execute($sql);

		while (!$res->EOF) {
			$municipios[$cont]["codigoDepto"] = $res->fields["DPTO_CODI"];
			$municipios[$cont]["codigoMun"] = $res->fields["MUNI_CODI"];
			$municipios[$cont]["nombre"] = $res->fields["MUNI_NOMB"];
			$cont++;
			$res->MoveNext();
		}// Fin de la obtencion de los municipios

		$mostrarComa = "";
		$cont = 0;
		$coma = ",";

		foreach ($municipios as $municipio) {
			if ($cont != 0) $mostrarComa = $coma . "\n";
			if ($cont > 0) $mostrarComa .= "\t\t\t\t\t";
			$arregloJs .= $mostrarComa . "new seccionE (\"" . $municipio["codigoMun"] .
					"\",\"" . $municipio["nombre"] .
					"\",\"" . $municipio["codigoDepto"] . "\")";
			$cont++;
		}

		$tpl->loadTemplatefile("inicioRadicacion.tpl");
		$tpl->setVariable("TITULO_PAGINA",$tituloPagina);
		$tpl->setVariable("ARCHIVO_EXEC",$archivoExec);
		$tpl->setVariable("ESTILOS_RADICADO",$estilosRadicacion);
		$tpl->setVariable("ARCHIVO_EXEC",$archivoExec);
		$tpl->setVariable("MUNICIPIO_SELECT",$selectMuni);
		$tpl->setVariable("DEPARTAMENTO_SELECT",$selectDepto);
		$tpl->setVariable("ARREGLOJS",$arregloJs);
		$tpl->show();
		exit();
	} else {
		header("Location : $paginaDeInicio");
	}
?>
