<form name=form_temas  method="post" action="verradicado.php?<?=session_name()?>=<?=trim(session_id())."&mostrar_opc_envio=$mostrar_opc_envio&nomcarpeta=$nomcarpeta&carpeta=$carpeta&leido=$leido"?>" >
<table border=0 width 100% cellpadding="0" cellspacing="5" class="borde_tab">
<input type=hidden name=ver_tema value="Si ver Causales">
<input type=hidden name=carpeta value='<?=$carpeta?>'>
<tr> 
<?php
$isql = "select * FROM SGD_TMA_TEMAS where depe_codi=$dependencia";
$rs=$db->conn->Execute($isql);
$regs = $rs->RecordCount();
if($regs>0)
{
?>
	<td bgcolor='#cccccc'> Tema</td>
	<td width="323" >
		<select name="tema" class="select">
	<?php
	do
	{	$codigo_tma = $rs->fields["SGD_TMA_CODIGO"];
		$nombre_tma = $rs->fields["SGD_TMA_DESCRIP"];
		if($codigo_tma==$tema)
		{	$datoss = " selected ";	} 
		else
		{	$datoss = "  ";	}
		echo "<option value=$codigo_tma $datoss>$nombre_tma</option>";
		$rs->MoveNext();
	}while(!$rs->EOF);
	?> 
		</select>
		<input type=submit name=grabar_tema value='Grabar Cambio' class='ebuttons2'>
	<?php
}
if($grabar_tema)
{
/**  INTENTA ACTUALIZAR EL TEMA 
 *  
 * */
	if(!$tema) $tema=0;
	$recordSet["SGD_TMA_CODIGO"] = $tema;		
	$recordWhere["RADI_NUME_RADI"] = $verrad;			
	$db->update("RADICADO", $recordSet,$recordWhere);									  
	$actualizados = $db->RecordCount();
	if($actualizados > 0)	echo "<span class=info>Tema Actualizado</span>";
	if($actualizados==0)	echo "<span class=alarmas>No se ha podido Actualizar el tema</span>";
/* FIN ACUTALIZACION DE TEMAS */ 
}
echo "</td>";
	?>
</tr>
</table>
</form>