<?php
/************************************************************************************/
/* ORFEO GPL:Sistema de Gestion Documental		http://www.orfeogpl.org	     		*/
/*	Idea Original de la SUPERINTENDENCIA DE SERVICIOS PUBLICOS DOMICILIARIOS     	*/
/*				COLOMBIA TEL. (57) (1) 6913005  orfeogpl@gmail.com   				*/
/* ===========================                                                      */
/*                                                                                  */
/* Este programa es software libre. usted puede redistribuirlo y/o modificarlo      */
/* bajo los terminos de la licencia GNU General Public publicada por                */
/* la "Free Software Foundation"; Licencia version 2. 			             		*/
/*                                                                                  */
/* Copyright (c) 2005 por :	  	  	                                     			*/
/* SSPS "Superintendencia de Servicios Publicos Domiciliarios"                      */
/*   Jairo Hernan Losada  jlosada@gmail.com                Desarrollador            */
/*   Sixto Angel Pinzón López --- angel.pinzon@gmail.com   Desarrollador             */
/* C.R.A.  "COMISION DE REGULACION DE AGUAS Y SANEAMIENTO AMBIENTAL"                 */ 
/*   Liliana Gomez        lgomezv@gmail.com                Desarrolladora            */
/*   Lucia Ojeda          lojedaster@gmail.com             Desarrolladora            */
/* D.N.P. "Departamento Nacional de Planeación"                                      */
/*   Hollman Ladino       hladino@gmail.com                Desarrollador             */
/*                                                                                   */
/* Colocar desde esta lInea las Modificaciones Realizadas Luego de la Version 3.5    */
/*  Nombre Desarrollador   Correo     Fecha   Modificacion                           */
/*************************************************************************************/
	$verradicado = $verrad;
	$carpetaOld = $carpeta;
	$krdOld = $krd;
	$menu_ver_tmpOld = $menu_ver_tmp;
	$menu_ver_Old = $menu_ver;
	$ruta_raiz = "..";
	if(empty($verrad)) {
		$verrad = $verradicado;
	}
	if (isset($grabar_causal)) {
		$grabar_sector = $grabar_causal;
	}
	if (!$ent) $ent = substr($verradicado, -1 );
	if(!$carpeta) $carpeta = $carpetaOld;
	if(!$menu_ver_tmp) $menu_ver_tmp = $menu_ver_tmpOld;
	if(!$menu_ver) $menu_ver = $menu_ver_Old;
	if(!$krd) $krd=$krdOld;
	if (!$menu_ver) {
		$menu_ver=3;
	}
	if ($menu_ver_tmp) {
		$menu_ver=$menu_ver_tmp;
	}
    define('ADODB_ASSOC_CASE', 1);
	include_once "$ruta_raiz/include/db/ConnectionHandler.php";
?>
	<input type=hidden name=ver_sectores value="Si ver Sector">
	<input type=hidden name="nomcarpeta" value="<?=$nomcarpeta?>">
	<input type=hidden name="sectorNombreAnt" value="<?=$sectorNombreAnt?>">
	<input type=hidden name="sectorCodigoAnt" value="<?=$sectorCodigoAnt?>">
	<input type=hidden name="verrad" value="<?=$verrad?>">
<?php
    if (!$ruta_raiz) {
    	$ruta_raiz="..";
    }
  	include_once($ruta_raiz."/include/tx/Historico.php");
  	$objHistorico= new Historico($db);
  	$arrayRad = array();
	$arrayRad[]=$verrad;
	$fecha_hoy = Date("Y-m-d");
	$sqlFechaHoy = $db->conn->DBDate($fecha_hoy);
	$isql = "SELECT * FROM PAR_SERV_SERVICIOS";

	if (is_array($recordSet) && (count($recordSet)>0) ) {
		array_splice($recordSet, 0);
	}
	if (is_array($recordWhere) && (count($recordWhere)>0) ) {
		array_splice($recordWhere, 0);
	}
	$rs=$db->conn->Execute($isql);
	if (!$rs->EOF) {
		$mostrarSector = '<select name="sector" class="select">' ."\n";
		do {
			$codigo_sect = $rs->fields["PAR_SERV_SECUE"];
			$nombre_sect = $rs->fields["PAR_SERV_NOMBRE"];
			if($codigo_sect==$sector) {
				$datoss = "selected";
			}else{
				$datoss = " ";
			}
			$mostrarSector .= "<option value='$codigo_sect'  $datoss>$codigo_sect $nombre_sect</option>\n";
			$rs->MoveNext();
		}while(!$rs->EOF);
		// Varible que contiene dropdown de sector esta se muestra en mod_causal
		$mostrarSector .= "</select>\n";
	}
		
	if($grabar_sector) {
		// Intenta actualizar la causal, si esta no esta entonces simplemente la inserta
		if(!$ddca_causal) $ddca_causal=0;
		if(!$deta_causal) $data_causa =0;
		$recordSet["PAR_SERV_SECUE"] = $sector;
		$recordWhere["RADI_NUME_RADI"] = $verrad;
		if($sector != $sectorCodigoAnt) {
		$ok = $db->update("RADICADO", $recordSet,$recordWhere);
		array_splice($recordSet, 0);
		array_splice($recordWhere, 0);
		$sector_nombre = (isset($sectorCodigoAnt) && $sectorNombreAnt != '') ? 
									$sectorNombreAnt : 'Sin tipificar';
		if ($ok) {
			$mostrarAct = "<span class=info>Sector Actualizado</span>";
			$observa = "*Cambio Sector* Anterior($sector_nombre)";
			$codusdp = str_pad($dependencia, 3, "0", STR_PAD_LEFT).str_pad($codusuario, 3, "0", STR_PAD_LEFT);	
			$objHistorico->insertarHistorico($arrayRad,$dependencia ,$codusuario, $dependencia,$codusuario, $observa, 18);
			
			$isql = "SELECT serv.PAR_SERV_SECUE, serv.PAR_SERV_NOMBRE
						FROM RADICADO rad, 
						      PAR_SERV_SERVICIOS serv 
						WHERE rad.RADI_NUME_RADI =" . $verrad . "  AND
								serv.PAR_SERV_SECUE = rad.PAR_SERV_SECUE";
			$rs = $db->conn->Execute($isql);
			
			if(!$rs->EOF){
				$sectorNombreAnt = $rs->fields["PAR_SERV_NOMBRE"];
				$sectorCodigoAnt = $rs->fields["PAR_SERV_SECUE"];
			}
		}
	} // Fin acutalizacion o insercion de causales
}
?>
