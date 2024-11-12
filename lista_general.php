    <?php
/************************************************************************************/
/* ORFEO GPL:Sistema de Gestion Documental		http://www.orfeogpl.org				*/
/*	Idea Original de la SUPERINTENDENCIA DE SERVICIOS PUBLICOS DOMICILIARIOS		*/
/*				COLOMBIA TEL. (57) (1) 6913005  orfeogpl@gmail.com					*/
/* ===========================														*/
/*                                                                                   */
/* Este programa es software libre. usted puede redistribuirlo y/o modificarlo       */
/* bajo los terminos de la licencia GNU General Public publicada por                 */
/* la "Free Software Foundation"; Licencia version 2. 			             */
/*                                                                                   */
/* Copyright (c) 2005 por :	  	  	                                     */
/* SSPS "Superintendencia de Servicios Publicos Domiciliarios"                       */
/*   Jairo Hernan Losada  jlosada@gmail.com                Desarrollador             */
/*   Sixto Angel Pinzón López --- angel.pinzon@gmail.com   Desarrollador             */
/* C.R.A.  "COMISION DE REGULACION DE AGUAS Y SANEAMIENTO AMBIENTAL"                 */
/*   Liliana Gomez        lgomezv@gmail.com                Desarrolladora            */
/*   Lucia Ojeda          lojedaster@gmail.com             Desarrolladora            */
/* D.N.P. "Departamento Nacional de Planeación"                                      */
/*   Hollman Ladino       hollmanlp@gmail.com                Desarrollador          */
/*                                                                                   */
/* Colocar desde esta lInea las Modificaciones Realizadas Luego de la Version 3.5    */
/*  Nombre Desarrollador   Correo     Fecha   Modificacion                           */
/*************************************************************************************/
include_once "class_control/AplIntegrada.php";
$objApl = new AplIntegrada($db);
if(!$verradicado){
   $verradicado = $verrad;
}
$permDespla = $_SESSION['usua_perm_despla'];

$lkGenerico = "&usuario=$krd&nsesion=".trim(session_id())."&nro=$verradicado"."$datos_envio";
?>
<script src="js/popcalendar.js"></script>
<script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery.blockUI.js"></script>

<script>
function regresar()
{	//window.history.go(0);
	window.location.reload();
}

function verHistoricoImagenR(radicado)
{
	nombreventana= "ventHistRadDoc";
	url="ver_hist_imagen.php?type=r&rad="+radicado;
	window.open(url,nombreventana,'height=400,width=630');
}

function verHistoricoTrd(radicado)
{
	nombreventana= "ventHistRadDoc";
	url="ver_hist_trd.php?type=r&rad="+radicado;
	window.open(url,nombreventana,'height=400,width=850,scrollbars=yes');
}

function aprobarTRD(rad, usua, depe, notifica)
{
	$.blockUI({
	      message: 'Espere Un Momento ...',
	      css: {
	        border: 'none',
	        padding: '15px',
	        backgroundColor: '#000',
	        '-webkit-border-radius': '10px',
	        '-moz-border-radius': '10px',
	        opacity: '.5',
	        color: '#fff',
	        fontSize: '18px',
	        fontFamily: 'Verdana,Arial',
	        fontWeight: 200 } });
    
	var aprueba = 0;
	var opcion = "";
	if (notifica == 1) {
		opcion = confirm(" Desea aprobar el cambio de la TRD ? ");
	} else {
		opcion = confirm(" Desea aprobar el cambio del Tipo Documental ? ");
	}
    if (opcion == true) {
    	aprueba = 1;		
    }

    var parametros = {
    	"rad" : rad,
    	"tipo" : 2,
    	"usua" : usua,
    	"depe" : depe,
    	"aprueba" : aprueba,
    	"notifica" : notifica
    };
    			
    $.ajax({
    	url: './class_control/ModificaTRD.php',
    	type: 'POST',
    	cache: false,
    	async: false,
    	data:  parametros,
    	success: function(text) {
    		$.unblockUI();
    		if(text == 2) {
				alert("SÍ se aprob\u00f3 el cambio de TRD !!");
    		} else if(text == 0) {
    			alert("NO se aprob\u00f3 el cambio de TRD !!");
    		} else if(text == 4) {
    			alert("SÍ se aprob\u00f3 el cambio de Tipo Documental !!");
    		} else {
    			alert("Error en el proceso, consulte el administrador del sistema." + text);
    		} 
    		document.form2.submit();
    	},
    	error: function(text) { 
    		$.unblockUI();
        	alert('Se ha producido un error ' + text); 
        }
    });
}
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#006699" >
<tr bgcolor="#006699">
	<td class="titulos4" colspan="6" >INFORMACI&Oacute;N GENERAL </td>
</tr>
</table>
<table border=0 cellspace=2 cellpad=2 WIDTH="100%" align="left" class="borde_tab" id=tb_general>
<tr>
    <td align="right" bgcolor="#CCCCCC" height="25" class="titulos2" width="14%">FECHA DE RADICADO</td>
    <td height="25" class="listado2" width="20%"><?=$radi_fech_radi ?></td>
    <td bgcolor="#CCCCCC" align="right" height="25" class="titulos2" width="13%">ASUNTO</td>
    <td class='listado2' colspan="3"><?=$ra_asun ?></td>
</tr>
<tr>
<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2"><?=$tip3Nombre[1][$ent]?></td>
<td align="right" bgcolor="#CCCCCC" height="25" class="listado2"><?="$nombre_us1 <br> $otro_us1 "?></td>
     <td bgcolor="#CCCCCC" align="right" height="25" class="titulos2" >DIRECCI&Oacute;N CORRESPONDENCIA</td>
     <td class='listado2' width="20%"><?php if($direccion_us1 != null) echo $direccion_us1; elseif ( $direccion_us7 != null) echo $direccion_us7; else echo "N/A"; ?></td>
     <td bgcolor="#CCCCCC" align="right" height="25" class="titulos2" width="13%">MUN/DPTO</td>
     <td class='listado2' width="20%"><?php if($dpto_nombre_us1 != null) echo $dpto_nombre_us1."/".$muni_nombre_us1; elseif ($dpto_nombre_us7 != null) echo $dpto_nombre_us7."/".$muni_nombre_us7;  else echo "N/A"; ?></td>
</tr>

<tr>
<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2"><?=$tip3Nombre[2][$ent]?></td>
<td class='listado2' height="25"><?="$nombre_us2 <br> $otro_us2 "?></td>
    <td bgcolor="#CCCCCC" align="right" height="25" class="titulos2">DIRECCI&Oacute;N CORRESPONDENCIA </td>
    <td class='listado2'> <?=$direccion_us2 ?></td>
    <td bgcolor="#CCCCCC" align="right" height="25" class="titulos2">MUN/DPTO</td>
    <td class='listado2'> <?=$dpto_nombre_us2."/".$muni_nombre_us2 ?></td>
</tr>
<tr>
	<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2"><?=$tip3Nombre[3][$ent]?></td>
	<td class='listado2' height="25"> <?=$nombret_us3 ?></td>
    <td bgcolor="#CCCCCC" align="right" height="25" class="titulos2">DIRECCI&Oacute;N CORRESPONDENCIA </td>
    <td class='listado2'> <?=$direccion_us3 ?></td>
    <td bgcolor="#CCCCCC" align="right" height="25" class="titulos2">MUN/DPTO</td>
    <td class='listado2'> <?=$dpto_nombre_us3."/".$muni_nombre_us3 ?></td>
</tr>
<tr>
	<td height="25" bgcolor="#CCCCCC" align="right" class="titulos2"> <p>N&ordm; DE PAGINAS</p></td>
    <td class='listado2' height="25"> <?=$radi_nume_hoja ?></td>
    <td bgcolor="#CCCCCC" height="25" align="right" class="titulos2"> DESCRIPCION ANEXOS </td>
    <td class='listado2' height="11"> <?=$radi_desc_anex ?></td>
    <td bgcolor="#CCCCCC" align="right" height="25" class="titulos2">MEDIO DE RECEPCI&Oacute;N</td>
    <td class='listado2'><?=$desc_mediorecp?></td>
</tr>
<tr>
	<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">DOCUMENTO<br>Anexo/Asociado</td>
	<td class='listado2' height="25">
	<?	if($radi_tipo_deri!=1 and $radi_nume_deri)
		{	echo $radi_nume_deri;
			echo "<br>(<a class='vinculos' href='$ruta_raiz/verradicado.php?verrad=$radi_nume_deri &session_name()=session_id()&krd=$krd' target='VERRAD$radi_nume_deri_".date("Ymdhi")."'>Ver Datos</a>)";
		}
		if($verradPermisos == "Full" or $datoVer=="985")
		{
	?>
		<input type=button name=mostrar_anexo value='...' class=botones_2 onClick="verVinculoDocto();">
	<?php
		}
	?>
	</td>
    <td bgcolor="#CCCCCC" align="right" height="25" class="titulos2">REF/OFICIO/CUENTA INTERNA </td>
    <td class='listado2'> <?=$cuentai ?>&#160;&#160;&#160;&#160;&#160;
    <?php
		$muniCodiFac = "";
		$dptoCodiFac = "";
		if($sector_grb==6 and $cuentai and $espcodi)
		{	if($muni_us2 and $codep_us2)
			{	$muniCodiFac = $muni_us2;
				$dptoCodiFac = $codep_us2;
			}
			else
			{	if($muni_us1 and $codep_us1)
				{	$muniCodiFac = $muni_us1;
					$dptoCodiFac = $codep_us1;
				}
			}
	?>
		<a href="./consultaSUI/facturacionSUI.php?cuentai=<?=$cuentai?>&muniCodi=<?=$muniCodiFac?>&deptoCodi=<?=$dptoCodiFac?>&espCodi=<?=$espcodi?>" target="FacSUI<?=$cuentai?>"><span class="vinculos">Ver Facturacion</span></a>
	<?php
		}
	?>
		
    </td>
	
	<?php
	if ( substr($verradicado, -1, 1) == 2 || substr($verradicado, -1, 1) == 3 ){
	?>
	
    <td bgcolor="#CCCCCC" align="right" height="25" class="titulos2"> RESPUESTA </td>
    <td class='listado2' height="25" >
		<?	
		if($radi_respuesta)	{	
			echo $radi_respuesta;
			echo "<br>(<a class='vinculos' href='$ruta_raiz/verradicado.php?verrad=$radi_respuesta &session_name()=session_id()&krd=$krd' target='VERRAD$radi_respuesta_".date("Ymdhi")."'>Ver Datos</a>)";
		}
		?>
		
		<input type=button name=mostrar_resp value='...' class=botones_2 onClick="verVinculoResp();">
	</td> 
	
	<?php
		}
		else {
	?>
	
	<td bgcolor="#CCCCCC" align="right" height="25" class="titulos2">  </td>
    <td class='listado2' height="25" > </td> 
	
	<?php
		}
	?>
	
  </tr>
  <tr>
	<td align="right" height="25" class="titulos2">IMAGEN</td>
	<td class='listado2' colspan="1">
		<span class='vinculos'><?=$imagenv ?></span>
		&nbsp;
		<a href="javascript:verHistoricoImagenR('<?=$verrad?>')" class="vinculos"><img border="0" src="imagenes/log.png" alt="Log del documento" title="Log del documento" height="12" width="12" /></a>
	</td>
	<td align="right" height="25"  class="titulos2">ESTADO ACTUAL</td>
	<td class='listado2'>
		<?=$flujo_nombre?>
		<?php
			if($verradPermisos == "Full" or $datoVer=="985")
	  		{
	  	?>
			<input type=button name=mostrar_causal value='...' class=botones_2 onClick="ver_flujo();">
		<?php
			}
		?>
	</td>
	<td align="right" height="25"  class="titulos2">Nivel de Seguridad</td>
	<td class='listado2'>
	<?php
		if( $nivelRad == 1 ) {
			echo "Privado (Dependencia)";
		}
		else if ( $nivelRad == 2) {
			echo "Privado (Usuario)";
		}
		else {	
			echo "P&uacute;blico";
		}
		if($verradPermisos == "Full" or $datoVer=="985")
	  	{	$varEnvio = "krd=$krd&numRad=$verrad&nivelRad=$nivelRad";
			
			$ver = 1;
			
			### SI EL RADICADO ESTA MARCADO COMO PRIVADO, SE CONSULTA EL USUARIO Y DEPENDENCIA QUE PRIVATIZO EL RADICADO
			$sqlPriv = "SELECT	U.USUA_DOC, U.DEPE_CODI
						FROM	RADICADO R
								JOIN USUARIO U ON U.USUA_DOC = R.RADI_USUA_PRIVADO
						WHERE	RADI_NUME_RADI = $verradicado";
			$rsPriv = $db->conn->Execute($sqlPriv);
		
			$usuaDoc = $rsPriv->fields['USUA_DOC'];
			$depeCod = $rsPriv->fields['DEPE_CODI'];
			
				
			switch ($nivelRad) {
				case 1:
					if($_SESSION['dependencia'] != $depeCod ){
						$ver = 0;
					}
					break;
				case 2:
					if($_SESSION['usua_doc'] != $usuaDoc ){
						$ver = 0;
					}
					break;
			}
			
			## SI LA CONSULTA $sqlPriv NO TRAE VALORES, ES PORQUE EL CAMPO RADI_USUA_PRIVADO ESTA VACIO
			## LO QUE INDICA QUE EL RADICADO FUE PRIVATIZADO ANTES DE IMPLEMTAR LOS NIVELES DE PRIVACIDAD
			## POR LO TANTO ESE RADICADO LO PUEDE CAMBIAR DE ESTADO EL USUARIO ACTUAL
			if (!$usuaDoc){
				$ver = 1;
			}
			
			if ($ver == 1){
				
			
	?>
				<input type=button name=mostrar_causal value='...' class=botones_2 onClick="window.open('<?=$ruta_raiz?>/seguridad/radicado.php?<?=$varEnvio."&".session_name()."=".session_id() ?>','Cambio_Nivel_de_Seguridad_Radicado', 'height=300, width=500,left=350,top=300')">
	<?php
			}
		}
	?>
	</td>
</tr>
<tr>
	<td align="right" height="25" class="titulos2">TIPO DE SOLICITUD</td>
	<td class='listado2'>
		<?=$soli_tipo?>
	</td>
	<td align="right" height="25" class="titulos2">N&Uacute;MERO DE SOLICITUD</td>
	<td class='listado2'>
		<?=$radi_nume_soli?>
	</td>
	<td align="right" height="25" class="titulos2">TR&Aacute;MITE</td>
	<td class='listado2'>
		<?php echo $tramite_nombre;?>
		<input type=button name=mostrar_causal value='...' class=botones_2 onClick="window.open('<?=$ruta_raiz?>/radicacion/asignar_tramite.php?<?=$varEnvio."&".session_name()."=".session_id() ?>','Actualizacion_Tramite', 'height=220, width=350,left=250,top=300')">
	</td>
</tr>
<tr>
	<td align="right" height="25" class="titulos2">TRD</td>
	<td class='listado2' colspan="6">
	<?php
	   $serieactu = 0;
	   $subseactu = 0;
	   $docuactu = 0;
    	$sqlDt = "SELECT R.SGD_MRD_CODIGO, R.RADI_NUME_RADI, M.SGD_SRD_CODIGO, M.SGD_SBRD_CODIGO, M.SGD_TPR_CODIGO
                FROM SGD_RDF_RETDOCF R INNER JOIN SGD_MRD_MATRIRD M ON R.SGD_MRD_CODIGO = M.SGD_MRD_CODIGO
                WHERE RADI_NUME_RADI = $verradicado ";
    	$rsDt = $db->conn->Execute($sqlDt);
    	if ($rsDt && ! $rsDt->EOF) {
    	    $serieactu = $rsDt->fields["SGD_SRD_CODIGO"];
    	    $subseactu = $rsDt->fields["SGD_SBRD_CODIGO"];
    	    $docuactu = $rsDt->fields["SGD_TPR_CODIGO"];
    	}
	
    	$retipifica = ($_SESSION['retipificatrd'] == null ? 0 : $_SESSION['retipificatrd']);
		if(!$codserie) $codserie = "0";
		if(!$tsub) $tsub = "0";
		if(trim($val_tpdoc_grbTRD)=="///") $val_tpdoc_grbTRD = "";
	?>
		<?=$serie_nombre ?><font color=black>/</font><?=$subserie_nombre ?><font color=black>/</font><?=$tpdoc_nombreTRD;
		if(!$tpdoc_nombreTRD) echo $TDCactu; 

		$sqlCambio = "SELECT R.RADI_USUA_ACTU, R.RADI_DEPE_ACTU, R.SGD_CAMBIO_TRD FROM RADICADO R WHERE	R.RADI_NUME_RADI = $verradicado";
		$rsCam = $db->conn->Execute($sqlCambio);
		if ($rsCam && !$rsCam->EOF)
		{
		    if ($rsCam->fields['SGD_CAMBIO_TRD'] == 1 && $_SESSION["modificatrd"] == 1) {
		    ?>
		    	<input type="button" name="btAceptar" id="btAceptar" value="Aprobación" class="botones" onclick="aprobarTRD(<?=$verradicado?>, <?=$rsCam->fields['RADI_USUA_ACTU']?>, <?=$rsCam->fields['RADI_DEPE_ACTU']?>, 1);" >
		    <?php
		    }
		    if ($rsCam->fields['SGD_CAMBIO_TRD'] == 3 && $_SESSION["modificaTipodoc"] == 1) {
		        ?>
		    	<input type="button" name="btAceptar" id="btAceptar" value="Aprobación" class="botones" onclick="aprobarTRD(<?=$verradicado?>, <?=$rsCam->fields['RADI_USUA_ACTU']?>, <?=$rsCam->fields['RADI_DEPE_ACTU']?>, 2);" >
		    <?php
		    }
		    
		    if(($verradPermisos == "Full" or $datoVer=="985") && ($rsCam->fields['SGD_CAMBIO_TRD'] != 1) && ($rsCam->fields['SGD_CAMBIO_TRD'] != 3)) {
    		?>
    			<input type=button name=mosrtar_tipo_doc2 value='...' class=botones_2 onClick="ver_tipodocuTRD(<?=$serieactu?>,<?=$subseactu?>,<?=$docuactu?>, <?=$retipifica?>);">
    		<?php
    		}
		}
	?>
		&nbsp;
		<a href="javascript:verHistoricoTrd('<?=$verrad?>')" class="vinculos">
			<img border="0" src="imagenes/log.png" alt="Log de TRD" title="Log de TRD" height="12" width="12" />
		</a>
	</td>
</tr>
<tr>
<!--	<td align="right" height="25" class="titulos2">RELACION PROCEDIMENTAL</td>
	<td class='listado2' colspan="6">
	<?
		if(Trim($val_tpdoc_grb)=="///") $val_tpdoc_grb = "";
	?>
      <?=$tpdoc_nombre ?>
      <font color=black>/ </font><?=$funcion_nombre ?><font color=black>/ </font>
      <?=$proceso_nombre ?><font color=black>/ </font> <?=$procedimiento_nombre ?>
      <?
	  if($verradPermisos == "Full" or $datoVer=="985")
	  {
	  ?>
		<input type=button name=mosrtar_tipo_doc2 value='...' class=botones_2 onClick="ver_tipodocumento();">
      <?
	  }
	  ?>
	</td>
</tr>
-->
<tr>
    <td align="right" height="25" class="titulos2">SECTOR / TEMA</td>
    <?php
	$causal_nombre_grb = $causal_nombre;
	$dcausal_nombre_grb = $dcausal_nombre;
	?>
    <td class='listado2' colspan="3">
      <?php
// Se comenta la posibilidad de editar o cambiar el sector/tema. NADIE debería modificar ese dato. Solo
// se utiliza para el tema de PQR (combos Tema y Tipo de Solicitud). La responsable de las PQR (Juliana Toro)
// exige que nadie -SOLO CON UN PERMISO- pueda cambiar ese dato dado por el ciudadano via PQR.
	  if ($_SESSION['USUAPERMTEMAS'] == 1) {
			$datosEnviar = "./causales/mod_causal.php?".session_name()."=".session_id()."&verradicado=$verradicado&krd=$krd"
	  ?>

   <input type=button name="mostrar_causal" value="..." class='botones_2' onClick="window.open('<?=$datosEnviar?>','Tipificacion_Documento','height=450,width=750,scrollbars=no')">

		<?php
	  }
	 $sqlSelect = "SELECT caux.SGD_CAUX_CODIGO,cau.SGD_CAU_DESCRIP as SECTOR
	 , dcau.SGD_DCAU_DESCRIP as CAUSAL,caux.SGD_CAUX_CODIGO
	 ,caux.RADI_NUME_RADI COUNT_RADI, caux.SGD_CAUX_FECHA
	 FROM SGD_CAUX_CAUSALES caux, SGD_CAU_CAUSAL cau, SGD_DCAU_CAUSAL dcau
	 WHERE RADI_NUME_RADI = '$verradicado'
	 and caux.SGD_DCAU_CODIGO=dcau.sgd_dcau_codigo
	 and dcau.SGD_CAU_CODIGO=cau.SGD_CAU_CODIGO
	 ";
	$rs = $db->conn->Execute($sqlSelect);
	while (!$rs->EOF)  {
		?>
		<br>
		<?=$rs->fields["SECTOR"]?> / <?=$rs->fields["CAUSAL"]?>
		<?php
		$rs->MoveNext();
	  }
	  ?>
    </td>
  </tr>
   <!--<tr>
    <td align="right" height="25" class="titulos2">TEMA SEGUIMIENTO</td>
    <td class='listado2' colspan="6">
      <?
        $sql =  "select * from SGD_TEM_NOMBRES where SGD_TEM_NOMBRES.id = " . $sgd_tema;
        //echo $sql;
        /**$rs=$db->query($sql);
        if  ($rs && !$rs->EOF) {
           echo $rs->fields["SGD_TEMA_NOMBRE"];
        }**/
      ?>
    </td>
  </tr>-->
</table>
</form>

<table align="center" border=0 id=ver_datos witdth=80%>
<tr><td>
<?
 $ruta_raiz = ".";
 if($verradPermisos=="Full" or $datoVer=="985") {
 	include ("tipo_documento.php");
 }
?>
</td></tr>
<tr><td align='center'>
<?
 // <input type=button name=mod_tipo_doc3 value='Ver datos' class=botones_2 onClick="ver_datos();">
?>
</td></tr>
</table>
