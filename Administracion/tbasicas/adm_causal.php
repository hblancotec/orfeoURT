<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "../../sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}
if ($_SESSION['usua_admin_sistema'] != 1){
	die(include "../../sinpermiso.php");
	exit;
}

error_reporting(0);
	$verrad = $verradicado;
	$carpetaOld = $carpeta;
	$krdOld = $krd;
	$menu_ver_tmpOld = $menu_ver_tmp;
	$menu_ver_Old = $menu_ver;
	$accionTema = $_POST["accionTema"];
  $accionSector = $_POST["accionSector"];
	$ruta_raiz = "../..";
	//include "../../rec_session.php";
	if (!$ent) $ent = substr($verradicado, -1 );
	if(!$carpeta) $carpeta = $carpetaOld;
	if(!$menu_ver_tmp) $menu_ver_tmp = $menu_ver_tmpOld;
	if(!$menu_ver) $menu_ver = $menu_ver_Old;
	if(!$krd) $krd=$krdOld;
	if(!$menu_ver) {
		$menu_ver=3;
	}
	if($menu_ver_tmp) {
		$menu_ver=$menu_ver_tmp;
	}
    define('ADODB_ASSOC_CASE', 1);
	include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
	if(!$verrad) $verrad = $verradicado;
  $db = new ConnectionHandler($ruta_raiz);
	//$db->conn->debug = true;
  $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
  
  
  function strValido($string){
    $arr = array('/[^\w:()\sáéíóúÁÉÍÓÚ=#\-,.;ñÑ]+/', '/[\s]+/');
    $asu = preg_replace($arr[0], '',$string);
    return    strtoupper(preg_replace($arr[1], ' ',$asu));
  } 
?>
<html>
<head>
  <title>;-) Modificacion de Sector / Temas ;-) </title>
  <link rel="stylesheet" href="<?=$ruta_raiz?>/estilos/orfeo.css">
</head>
<body>
<center>

<table   cellpadding="0" cellspacing="5" class="borde_tab" >
<TR class='titulos2'>
  <TH COLSPAN=2>ADMINISTRACI&Oacute;N DE SECTOR / TEMA</TH>
</TR>
<form name=form_causales  method="post" action="<?=$ruta_raiz?>/Administracion/tbasicas/adm_causal.php?<?=session_name()?>=<?=trim(session_id())?>&krd=<?=$krd?>&verrad=<?=$verradicado?>&verradicado=<?=$verradicado?><?="&datoVer=$datoVer&mostrar_opc_envio=$mostrar_opc_envio&nomcarpeta=$nomcarpeta"?>">
<tr>
  <td class="titulos2" width="10%"> Sector
<?php
	if (!$ruta_raiz) $ruta_raiz="..";
	include_once($ruta_raiz."/include/tx/Historico.php");
	$objHistorico= new Historico($db);
	if (is_array($recordSet) && (count($recordSet)>0) )
	array_splice($recordSet, 0);  		
	if (is_array($recordWhere) && (count($recordWhere)>0) )
	array_splice($recordWhere, 0);  
	$fecha_hoy = Date("Y-m-d");
	$sqlFechaHoy=$db->conn->DBDate($fecha_hoy);     
	$arrayRad = array();
	$arrayRad[]=$verradicado;
	$actualizo = 0;
	$actualizoFlag = false;
	$insertoFlag = false;
	
		if($causal==0) {
			$ddca_causal="0"; 
			$data_causa ="0";
		}
		//$db->conn->debug = true;

		array_splice($recordSet, 0);
		array_splice($recordWhere, 0);
			$flag = 0;
			if($accionCausal=="Agregar Sector" && trim($newSector))
			{
				
				$sqlSelect = "SELECT SGD_CAU_DESCRIP
				FROM SGD_CAU_CAUSAL
				WHERE SGD_CAU_DESCRIP = '".strtoupper(trim($newSector))."'
				ORDER BY SGD_CAU_DESCRIP";
				//$db->conn->debug = true;
				$rs = $db->conn->Execute($sqlSelect);
			if(!$rs->fields["SGD_CAU_DESCRIP"]){
			$sqlSelect = "SELECT (SGD_CAU_CODIGO+1) NEWCODIGOCAUSAL
						FROM SGD_CAU_CAUSAL
						ORDER BY SGD_CAU_CODIGO DESC";
			//$db->conn->debug = true;
			$rs = $db->conn->Execute($sqlSelect);
			$codCausalNew = $rs->fields["NEWCODIGOCAUSAL"];
			$recordSet["SGD_CAU_CODIGO"] = "'".$codCausalNew."'";
      $recordSet["SGD_CAU_ESTADO"] = 1;
			$recordSet["SGD_CAU_DESCRIP"] = "'".strtoupper(strValido(trim($newSector)))."'";										
			$rs = $db->insert("SGD_CAU_CAUSAL", $recordSet);							
				array_splice($recordSet, 0);  	
				if ($rs) {
					//echo "<span class=info>Causal Agregada</span>";
					$mensajeSector = "Se inserto Sector <$newSector>";
				} // Fin de actualizacion o insercion de casales
			}else{
				$mensajeSector = "<FONT COLOR=RED>No se inserto Sector <$newSector>. Ya existe el sector ingresado >>>". $rs->fields["SGD_CAU_DESCRIP"] . "<<<</FONT>";
			}
			}
    if($accionCausal=="Modificar Sector" && trim($newSector))
		{
				$sqlSelect = "SELECT SGD_CAU_DESCRIP
				FROM SGD_CAU_CAUSAL
				WHERE SGD_CAU_DESCRIP = '".strtoupper(trim($newSector))."'
				ORDER BY SGD_CAU_DESCRIP";
				//$db->conn->debug = true;
				$rs = $db->conn->Execute($sqlSelect);
			if(!$rs->fields["SGD_CAU_DESCRIP"]){
				 $recordWhere["SGD_CAU_CODIGO"] = "'".$causal_new."'";
			  $recordSet["SGD_CAU_DESCRIP"] = "'".strtoupper(strValido(trim($newSector)))."'";										
		    if($db->update("SGD_CAU_CAUSAL", $recordSet,$recordWhere)){
			  $mensajeSector = "Modificacion Sector Realizada Correctamente <$newSector>";
			  $actualizo = "Ok";
		   }else{
			  $mensajeSector = "No se ha podido Actualizar el Sector/tema indicado";
		  }
			}else{
			  $mensajeSector = "<FONT COLOR=RED>No se modifico Sector <$newSector>. Ya existe el sector ingresado >>>". $rs->fields["SGD_CAU_DESCRIP"] . "<<<</FONT>";	
			}
		}
    if(trim($accionCausal)=="Inactivar Sector" && $causal_new>=1)
		{
      $recordWhere["SGD_CAU_CODIGO"] = "'".$causal_new."'";
      $recordSet["SGD_CAU_ESTADO"] = 0;
      if($db->update("SGD_CAU_CAUSAL", $recordSet,$recordWhere)){
       $mensajeSector = "Se inactivo el Sector";
       $actualizo = "Ok";
      }else{
       $mensajeSector = "Se inactivo el Sector indicado";
		  }
		}
    $accionTema = substr($accionTema,0, 12);
			if($accionTema=="Agregar Tema" && trim($newTema) && $causal_new!=0)
			{
				
			$sqlSelect = "SELECT cau.SGD_CAU_DESCRIP, dcau.SGD_DCAU_DESCRIP
				FROM SGD_CAU_CAUSAL cau, SGD_DCAU_CAUSAL dcau
				WHERE  cau.SGD_CAU_CODIGO=dcau.SGD_CAU_CODIGO
				AND dcau.SGD_DCAU_ESTADO=1
				AND dcau.SGD_DCAU_DESCRIP = '".strtoupper(strValido(trim($newTema)))."'
				";
				//$db->conn->debug = true;
				$rs = $db->conn->Execute($sqlSelect);
			if(!$rs->fields["SGD_DCAU_DESCRIP"]){	
			$sqlSelect = "SELECT (SGD_DCAU_CODIGO+1) NEWCODIGOTEMA
						FROM SGD_DCAU_CAUSAL
						ORDER BY SGD_DCAU_CODIGO DESC";
			//$db->conn->debug = true;
			$rs = $db->conn->Execute($sqlSelect);
			$codTemaNew = $rs->fields["NEWCODIGOTEMA"];
      $recordSet["SGD_CAU_CODIGO"] = $causal_new;
			$recordSet["SGD_DCAU_CODIGO"] = $codTemaNew;
      $recordSet["SGD_DCAU_ESTADO"] = 1;
			$recordSet["SGD_DCAU_DESCRIP"] = "'".strtoupper(strValido(trim($newTema)))."'";										
			$rs = $db->insert("SGD_DCAU_CAUSAL", $recordSet);							
				array_splice($recordSet, 0);  	
				if ($rs) {
					//echo "<span class=info>Causal Agregada</span>";
					$mensajeTema = "Se inserto Sector <$newSector>";
				} // Fin de actualizacion o insercion de casales
			}else{
				$mensajeTema = "<FONT COLOR=RED>No se inserto Tema <$newTema>. Ya existe el tema ingresado >>>". $rs->fields["SGD_DCAU_DESCRIP"] . "<<< en el Sector >>>> ".$rs->fields["SGD_CAU_DESCRIP"]."<<<<</FONT>";
			}
			}
      if($accionTema=="Modificar Te" && trim($newTema))
			{
						$sqlSelect = "SELECT cau.SGD_CAU_DESCRIP, dcau.SGD_DCAU_DESCRIP
				FROM SGD_CAU_CAUSAL cau, SGD_DCAU_CAUSAL dcau
				WHERE  cau.SGD_CAU_CODIGO=dcau.SGD_CAU_CODIGO
				AND dcau.SGD_DCAU_ESTADO=1
				AND dcau.SGD_DCAU_DESCRIP = '".strtoupper(trim($newTema))."'
				";
				//$db->conn->debug = true;
				$rs = $db->conn->Execute($sqlSelect);
			if(!$rs->fields["SGD_DCAU_DESCRIP"]){	
			$recordWhere["SGD_DCAU_CODIGO"] = $deta_causal;
			$recordSet["SGD_DCAU_DESCRIP"] = "'".strtoupper(strValido(trim($newTema)))."'";										
		  if($db->update("SGD_DCAU_CAUSAL", $recordSet,$recordWhere)){
			 $mensajeTema = "Modificacion Tema Correctamente <$newTema>";
			 $actualizo = "Ok";
		  }else{
			 $mensajeTema = "No se ha podido Actualizar el Sector/tema indicado";
		  }
			}else {
				  $mensajeTema = "<FONT COLOR=RED>No se modifico el Tema. Ya existe el tema ingresado >>>". $rs->fields["SGD_DCAU_DESCRIP"] . "<<< en el Sector >>>> ".$rs->fields["SGD_CAU_DESCRIP"]."<<<<</FONT>";
			  }
      }
      //$db->conn->debug = true;
      if($accionTema=="Inactivar Te" && $deta_causal>=1)
			{
			$recordWhere["SGD_DCAU_CODIGO"] = $deta_causal;
			$recordSet["SGD_DCAU_ESTADO"] = 0;										
		  if($db->update("SGD_DCAU_CAUSAL", $recordSet,$recordWhere)){
			$mensajeTema = "Se Inactivo el  Tema Correctamente";
			$actualizo = "Ok";
		  }else{
			 $mensajeTema = "Se inactivo el tema Correctamente";
		  }
		}
      
      ?>
  </td>
  <TD >
	<?php
	error_reporting(7);
	// capturando causal cuando envie el radicado 
	$isql = "SELECT caux.SGD_DCAU_CODIGO, 
					dcau.SGD_CAU_CODIGO 
				FROM SGD_CAUX_CAUSALES caux, 
					SGD_DCAU_CAUSAL dcau  
				WHERE caux.SGD_DCAU_CODIGO = dcau.SGD_DCAU_CODIGO
				ORDER BY dcau.SGD_DCAU_DESCRIP
           ";
	$rsDetalleCau = $db->conn->Execute($isql);
	
	if(!$rsDetalleCau->EOF) {
		if (empty($causal_new)) {
			$deta_causal = $rsDetalleCau->fields["SGD_DCAU_CODIGO"];
			$causal_new = $rsDetalleCau->fields["SGD_CAU_CODIGO"];
		}
	}
	
	$isql = "SELECT * FROM SGD_CAU_CAUSAL WHERE SGD_CAU_ESTADO=1 ORDER BY SGD_CAU_DESCRIP";
	$rs = $db->conn->Execute($isql);
	if(!$rs->EOF) {
	?>
	<SELECT name=causal_new id=causal_new onChange="submit();"  class="select" style="WIDTH:650;">
	<?php
	do {
		$codigo_cau = $rs->fields["SGD_CAU_CODIGO"];
		$nombre_cau = $rs->fields["SGD_CAU_DESCRIP"];
		if($codigo_cau==$causal_new) {
		$datoss = "selected";
		$sectorSeleccionado = $nombre_cau;
	 }else{
		$datoss = " ";
		}
		echo "<option value=$codigo_cau $datoss>$nombre_cau</option>\n";
		$rs->MoveNext();
	}while(!$rs->EOF);
	?>
	</SELECT>
  <?php
	}
	?>
  </BR><INPUT TYPE="text"  NAME="newSector" class="select" id="newSector" style="WIDTH:650;">
  </TD>
 </TR>
<TR>
<!-- <td colspan="1" align="left"></td> -->
<TD colspan="2" align="center">
  <input type="submit" name=accionCausal value='Modificar Sector' class=botones style="HEIGHT:20; WIDTH:150;">
	<input type="submit" name=accionCausal value='Agregar Sector' class=botones style="HEIGHT:20; WIDTH:350;">
	<input type="submit" name=accionCausal value='Inactivar Sector' class=botones style="HEIGHT:20; WIDTH:350;">
</TD>
<TR>
	<TD align="center" colspan=4 class='listado1'>
		<?=$mensajeSector?>
	</TD>
	</TR>
<TR>
<td class="titulos2" > Tema</td>
    <TD width="323" COLSPAN=4>
      <?php
  $isql = "SELECT SGD_DCAU_CODIGO, SGD_DCAU_DESCRIP 
        FROM SGD_DCAU_CAUSAL 
        WHERE SGD_DCAU_ESTADO=1 AND SGD_CAU_CODIGO = $causal_new";
  $rs = $db->conn->Execute($isql);
  if($rs && !$rs->EOF) {
?>
      
    <SELECT NAME="deta_causal" id="deta_causal" class="select"  size="5" style="HEIGHT:80; WIDTH:650;" />
    <?php
    do {
			$codigo_dcau = $rs->fields["SGD_DCAU_CODIGO"];
			$nombre_dcau = $rs->fields["SGD_DCAU_DESCRIP"];
		  	if($codigo_dcau==$deta_causal) {
				$datoss = "selected";
			} else {
				$datoss = " ";
			}
			echo "<option value=$codigo_dcau $datoss>$nombre_dcau</option>\n";
			$rs->MoveNext();
		} while(!$rs->EOF);
    ?>
    </SELECT>
       <?php
  }
?>
    <br><input type=text  name="newTema" class="select" id="newCausal" style="WIDTH:650;">
 
    </td>
</tr>
			 
<tr>
<td colspan="2" align="center">
  <input type="submit" name="accionTema" value="Modificar Tema" class="botones" style="HEIGHT:20; WIDTH:150;">
  <input type="submit" name="accionTema" value="Agregar Tema del Sector <?=$sectorSeleccionado?>" class="botones" style="HEIGHT:20; WIDTH:350;"  >		
	<input type="submit" name="accionTema" value="Inactivar Tema del Sector <?=$sectorSeleccionado?>" class="botones" style="HEIGHT:20; WIDTH:350;">
</TD>
</tr>
<TR>
	<TD colspan=5><?=$mensajeTema?></TD>
	</TR>
</table>
<input type=hidden name=ver_causal value="Si ver Causales">
<input type=hidden name="grabar_causal" value="1">
<input type=hidden name="$verrad" value="<?=$verradicado?>">
<input type=hidden name="sectorNombreAnt" value="<?=$sectorNombreAnt?>">
<input type=hidden name="sectorCodigoAnt" value="<?=$sectorCodigoAnt?>">
<input type=hidden name="causal_grb" value="<?=$causal_grb?>">
<input type=hidden name="causal_nombre" value="<?=$causal_nombre?>">
<input type=hidden name="deta_causal_grb" value="<?=$deta_causal_grb?>">
<input type=hidden name="dcausal_nombre" value="<?=$dcausal_nombre?>">
<table class='borde_tab'>
  <tr ><td class='listado1'>
    <?=$mensajeAccionTema?>
  </td></tr>
  </table>

</form>


		</center>
	<?php
	$ruta_raiz = ".";
?>
