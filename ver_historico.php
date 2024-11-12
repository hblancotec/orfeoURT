<html>
 <head>
  <title>Untitled</title>
  <link rel="stylesheet" href="estilos/orfeo.css">
  <script type="text/javascript">
	function verHistoricoImagenC(radicado) {
		nombreventana= "ventHistCertimail";
		url="ver_hist_imagen.php?type=c&rad="+radicado;
		window.open(url,nombreventana,'height=400,width=630');
	}

	function abririmagen(ruta) {
		window.open("visorImagen.php?ruta="+ruta, "MyFile", "location=no,width=600,height=800,scrollbars=yes,Menubar=no,toolbar=no,Titlebar=no,resizable=no,top=100,left=100");			
	}
	</script>
 </head>
 <body >
  <table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#006699" >
   <tr bgcolor="#006699">
	<td class="titulos4" colspan="6" >HISTORICO </td>
   </tr>
  </table>

<?php
	include ("$ruta_raiz/_conf/constantes.php");
	$usuaDoc = (!empty($_SESSION['usua_doc'])) ? $_SESSION['usua_doc'] : null;
	require_once(ORFEOPATH . "class_control/Transaccion.php");
	require_once(ORFEOPATH . "class_control/Dependencia.php");
	require_once(ORFEOPATH . "class_control/usuario.php");

	$trans = new Transaccion($db);
	$objDep = new Dependencia($db);
	$objUs = new Usuario($db);

	$isql ="SELECT	USUA_NOMB
			FROM	USUARIO USUA,
					SGD_USD_USUADEPE USD
			WHERE	USUA.USUA_CODI = $radi_usua_actu 
					AND USUA.USUA_DOC = USD.USUA_DOC
					AND USUA.USUA_LOGIN = USD.USUA_LOGIN
					AND USD.DEPE_CODI = $radi_depe_actu";

	$rs = $db->conn->Execute($isql);
	if ($rs === false) {
		echo "Error de consulta";
		echo $db->conn->ErrorMsg();
		exit(1);
	}

	$usuario_actual = $rs->fields["USUA_NOMB"];
	$isql   = "	SELECT	DEPE_NOMB
				FROM	DEPENDENCIA
				WHERE	DEPE_CODI= $radi_depe_actu";
	$rs     = $db->conn->Execute($isql);

	if ($rs === false) {
		echo "Error de consulta";
		echo $db->conn->ErrorMsg();
		exit(1);
	}

	$dependencia_actual = $rs->fields["DEPE_NOMB"];
	// Para revision por que no debe traer con el codigo de usuario y de la dependencia si no en el documento del usuario
	//$isql   = "select USUA_NOMB FROM usuario WHERE usua_doc = $usuaDoc";
	$isql ="SELECT	USUA_NOMB
			FROM	USUARIO
			WHERE	DEPE_CODI = $radi_depe_radicacion
					AND USUA_CODI=$radi_usua_radi";
	$rs = $db->conn->Execute($isql);

	if ($rs === false) {
		echo "Error de consulta";
		echo $db->conn->ErrorMsg();
		exit(1);
	}

	$usuario_rad = $rs->fields["USUA_NOMB"];
	$isql   = "	SELECT	DEPE_NOMB
				FROM	DEPENDENCIA
				WHERE	DEPE_CODI = $radi_depe_radicacion";
	$rs     = $db->conn->Execute($isql);

	if ($rs === false) {
		echo "Error de consulta";
		echo $db->conn->ErrorMsg();
		exit(1);
	}
	$dependencia_rad = $rs->fields["DEPE_NOMB"];
?>
  <table width="100%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
   <tr align="left">
	<td width=10% class="titulos2" height="24">USUARIO ACTUAL</td>
	<td width=15% class="listado2" height="24" align="left"><?=$usuario_actual?></td>
	<td width=10% class="titulos2" height="24">DEPENDENCIA ACTUAL</td>
	<td width=15% class="listado2" height="24"><?=$dependencia_actual?></td>
   </tr>
  </table>
  
  <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
   <tr>
	<td height="25" class="titulos4">FLUJO HISTORICO DEL DOCUMENTO</td>
   </tr>
  </table>

  <table  width="100%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab" >
   <tr align="center">
	<td width=10% class="titulos2" height="24">DEPENDENCIA 	</td>
	<td width=5%  class="titulos2" height="24">FECHA		</td>
	<td width=15% class="titulos2" height="24">TRANSACCION 	</td>
	<td width=15% class="titulos2" height="24" >US. ORIGEN	</td>
	<td  width=55% height="30" class="titulos2">COMENTARIO</td>
   </tr>

<?php
	$sqlFecha = $db->conn->SQLDate("d-m-Y H:i A","a.HIST_FECH");
	$isql ="SELECT	$sqlFecha HIST_FECH1,
		            a.DEPE_CODI,
		            a.USUA_CODI,
		            a.RADI_NUME_RADI,
		            CONVERT (TEXT, a.HIST_OBSE) AS HIST_OBSE,
		            a.USUA_CODI_DEST,
		            a.USUA_DOC,
		            a.SGD_TTR_CODIGO
			FROM	hist_eventos a
			WHERE	a.radi_nume_radi =$verrad
			order by hist_fech desc ";
	$i=1;
	$rs = $db->conn->Execute($isql);
	if ($rs !== false) {
		while(!$rs->EOF) {
			$usua_doc_dest = "";
			$usua_doc_hist = "";
			$usua_nomb_historico = "";
			$usua_destino = "";
			$numdata =  trim($rs->fields["CARP_CODI"]);
			if($data =="") 
				$rs1->fields["USUA_NOMB"];
		   	$data = "NULL";
			$numerot = $rs->fields["NUM"];
			$usua_doc_hist = $rs->fields["USUA_DOC"];
			$usua_codi_dest = $rs->fields["USUA_CODI_DEST"];
			$usua_dest=intval(substr($usua_codi_dest,3,3));
			$depe_dest=intval(substr($usua_codi_dest,0,3));
			$usua_codi = $rs->fields["USUA_CODI"];
			$depe_codi = $rs->fields["DEPE_CODI"];
			$codTransac = $rs->fields["SGD_TTR_CODIGO"];
			$descTransaccion = $rs->fields["SGD_TTR_DESCRIP"];
			if(!$codTransac)
				$codTransac = "0";
			$trans->Transaccion_codigo($codTransac);
			$objUs->usuarioDocto($usua_doc_hist);
			$objDep->Dependencia_codigo($depe_codi);
			if($carpeta==$numdata) {
				$imagen="usuarios.gif";
			}
			else {
				$imagen="usuarios.gif";
			}
			if($i==1) {
?>

   <tr class='tpar'>

<?php
				$i=1;
			}
?>

	<td class="listado2"> <?=$objDep->getDepe_nomb()?> 	</td>
    <td class="listado2"> <?=$rs->fields["HIST_FECH1"]?></td>
	<td class="listado2"> <?=$trans->getDescripcion()?> </td>
	<td class="listado2"> <?=$objUs->get_usua_nomb()?> 	</td>
	<td class="listado2"> <?=$rs->fields["HIST_OBSE"]?> </td>
   </tr>

<?php
			$rs->MoveNext();
		}
	}
    // Finaliza Historicos
?>

  </table>

<?php
	//empieza datos de envio
	include (ORFEOPATH . "include/query/queryver_historico.php");
	$isql = "SELECT	$numero_salida
			FROM	anexos a 
			WHERE	a.anex_radi_nume=$verrad";
	$rs = $db->conn->Execute($isql);
	$radicado_d= "";
	while(!$rs->EOF) {
		$valor = $rs->fields["RADI_NUME_SALIDA"];
		if(trim($valor)){
			$radicado_d .= "'".trim($valor) ."', ";
		}
		$rs->MoveNext();
	}
	$radicado_d .= "$verrad";
	include (ORFEOPATH . "include/query/queryver_historico.php");
	$sqlFechaEnvio = $db->conn->SQLDate("d-m-Y H:i A","a.SGD_RENV_FECH");
	$isql = "SELECT	$sqlFechaEnvio SGD_RENV_FECH, A.DEPE_CODI, A.USUA_DOC, A.RADI_NUME_SAL, A.SGD_RENV_NOMBRE,
			    A.SGD_RENV_DIR, A.SGD_RENV_MPIO, A.SGD_RENV_DEPTO, A.SGD_RENV_PLANILLA, B.DEPE_NOMB,
				C.SGD_FENV_DESCRIP, $numero_sal, A.SGD_RENV_OBSERVA, A.SGD_DEVE_CODIGO, A.SGD_DIR_TIPO,
				A.SGD_RENV_NUMGUIA, C.SGD_FENV_CODIGO, CG.NUMERO_GUIA, CG.RUTA
			FROM	SGD_RENV_REGENVIO A INNER JOIN DEPENDENCIA B ON B.DEPE_CODI = A.DEPE_CODI
					INNER JOIN SGD_FENV_FRMENVIO C ON C.SGD_FENV_CODIGO = A.SGD_FENV_CODIGO
                    LEFT JOIN CONTROL_GUIAS CG ON CG.RADICADO = A.RADI_NUME_SAL AND CG.NUMERO_GUIA = A.SGD_RENV_NUMGUIA
			WHERE	A.RADI_NUME_SAL IN ($radicado_d)
			ORDER BY A.SGD_RENV_FECH DESC";
	$rs = $db->conn->Execute($isql);
?>

  <table width="100%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
   <tr>
	<td height="25" class="titulos4">DATOS DE ENVIO</td>
   </tr>
  </table>
  <table width="100%"  align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab" >
   <tr align="center">
	<td class="titulos2" height="24">RADICADO 	</td>
	<td class="titulos2" >COPIA			</td>
	<td class="titulos2" >DEPENDENCIA	</td>
	<td class="titulos2" >FECHA 		</td>
	<td class="titulos2" >DESTINATARIO	</td>
	<td class="titulos2" >DIRECCION 	</td>
	<td class="titulos2">DEPARTAMENTO	</td>
	<td class="titulos2" >MUNICIPIO		</td>
	<td class="titulos2" >TIPO DE ENVIO	</td>
	<td class="titulos2"> No. PLANILLA	</td>
	<td class="titulos2"> No. GUIA		</td>
	<td class="titulos2">OBSERVACIONES O DESC DE ANEXOS </td>
   </tr>

<?php
	$i=1;
	if ($rs && !$rs->EOF) {
	while(!$rs->EOF) {
		$radDev = $rs->fields["SGD_DEVE_CODIGO"];
		$radEnviado = $rs->fields["RADI_NUME_SAL"];
                if($rs->fields["SGD_DIR_TIPO"]>1){
                    $copia=substr($rs->fields["SGD_DIR_TIPO"],2,2);
                }
                else{
                    $copia="";
                }
		if($radDev) {
			$imgRadDev = "<img src='$ruta_raiz/imagenes/devueltos.gif' alt='Documento Devuelto por empresa de Mensajeria' title='Documento Devuelto por empresa de Mensajeria'>";
		}
        else {
			$imgRadDev = "";
		}
		$numdata =  trim($rs->fields["CARP_CODI"]);
		if($data =="")
			$data = "NULL";
		//$numerot = $rs->RecordCount();
		if($carpeta==$numdata) {
			$imagen="usuarios.gif";
		}
		else {
			$imagen="usuarios.gif";
		}
		if($i==1) {
			echo "<tr>\n";
			$i=1;
		}
?>
   <tr>
	<td class="listado2" > <?=$imgRadDev?><?=$radEnviado?> 	</td>
	<td class="listado2"><center> <?=$copia?></center></td>
	<td class="listado2" > <?=$rs->fields["DEPE_NOMB"]?> 	</td>
	<td class="listado2">

<?php
		echo "<a class=vinculos href='./verradicado.php?verrad=$radEnviado&krd=$krd' target='verrad$radEnviado'> 
			<span class='timpar'>".$rs->fields["SGD_RENV_FECH"]." </span> </a>";
?>
	</td>
    <td class="listado2"> <?=$rs->fields["SGD_RENV_NOMBRE"]?>	</td>
    <td class="listado2"> <?=$rs->fields["SGD_RENV_DIR"]?>		</td>
    <td class="listado2"> <?=$rs->fields["SGD_RENV_DEPTO"]?> 	</td>
    <td class="listado2"> <?=$rs->fields["SGD_RENV_MPIO"]?> 	</td>
    <td class="listado2"> <?=$rs->fields["SGD_FENV_DESCRIP"]?> 	</td>
    <td class="listado2"> <?=$rs->fields["SGD_RENV_PLANILLA"]?> </td>
	<td class="listado2"> 
		<?php 
                $vecIdFenvCorreo = array(106, 112, 114);
		if ( in_array($rs->fields["SGD_FENV_CODIGO"], $vecIdFenvCorreo) ) {
                ?>
                    <a href="javascript:verHistoricoImagenC('<?=$radEnviado; ?>')" class="vinculos"><img src="imagenes/log.png" alt="Log del documento" title="Log del documento" border="0" height="12" width="12"></a>
                <?php
		} else {
		    if (strlen($rs->fields["NUMERO_GUIA"]) > 1) {
		        $rutaWeb = $rs->fields["RUTA"] . "/" . $rs->fields["NUMERO_GUIA"] . ".jpg";
		        $rutaFisica = BODEGAPATH ."guias/" . $rs->fields["RUTA"] . "/" . $rs->fields["NUMERO_GUIA"] . ".jpg";
		        if (file_exists($rutaFisica)) {
		              echo '<p><a href="visorImagen.php?ruta=' . urlencode($rutaWeb) . '">'. $rs->fields["NUMERO_GUIA"] . '</a></p>';
		        } else {
		            ?>
		            	<a href="http://svc1.sipost.co/trazawebsip2/default.aspx?Buscar=<?php echo $rs->fields["SGD_RENV_NUMGUIA"]; ?>" ><?php echo $rs->fields["SGD_RENV_NUMGUIA"];?></a>
		            <?php 
		        }
		    } else {
		?>
                    <a href="http://svc1.sipost.co/trazawebsip2/default.aspx?Buscar=<?php echo $rs->fields["SGD_RENV_NUMGUIA"]; ?>" ><?php echo $rs->fields["SGD_RENV_NUMGUIA"];?></a>
                <?php
		    }
		}
                ?>
	</td>
    <td class="listado2"> <?=$rs->fields["SGD_RENV_OBSERVA"]?> 	</td>
   </tr>

<?php
		$rs->MoveNext();
	}
	}
	// Finaliza Historicos
?>

  </table>
 </body>
</html>