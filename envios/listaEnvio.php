<?php
if (!$k)  {
?>
	<tr class="listado2">
	<td height="21" align="center" class="listado2"> <?=$verrad_sal?></td>
	<td height="21" align="center" class="listado2"> <?=$verrad_padre?></td>
<?php
} else {
?>
	<tr>
	<td class="listado2"><?=$verrad_sal?></td>
	<td class="listado2"><?=$verrad_padre?></td>
<?php
}
?>
	<td height="21" align="center" valign="top" class=listado2>
	<input type=text name="nombre_us" id="nombre_us" value='<?=$nombre_us?>' size=20 maxlength="40" class="tex_area"></td>
	<td class="listado2">
        <input type="text" name="direccion_us" id="direccion_us" value='<?=$direccion_us?>' class="e_cajas" size="15" class="tex_area">
    </td>
    <td class="listado2">
        <input type="text" name="telefono_us" id="telefono_us" value='<?=$telefono_us?>' class="e_cajas" size="15" class="tex_area">
    </td>
	<?php
	if($cantidadDestinos>=1)
		$nombreDestinos = "destino$cantidadDestinos";
	else
		$nombreDestinos = "destino";
	?>
	<td class="listado2">
        	<input type="text" name="codpostal_us" id="codpostal_us" value='<?=$codpostal_us?>' class="e_cajas" size="7" class="tex_area">
	</td>
	<td class="listado2">
        <input type="text" name="<?=$nombreDestinos?>" value="<?=$destino?>" size="20" class="tex_area">
    </td>
	<td class="listado2">
        <input type="text" name="departamento_us" id="departamento_us" value="<?=strtoupper($departamento_us)?>" size="10" class="tex_area">
    </td>
    <td class="listado2">
		<input type="text" name="pais_us" id="pais_us" value="<?=$pais_us?>" size="10" class="tex_area">
		<input type="hidden" name="dir_codigo" id="dir_codigo" value="<?=$dir_codigo?>" size="5" class="tex_area">
	</td>
</tr>
<?php
    if (!$k)  {
?>
	<tr  class="titulos2">
	<td height="21" colspan="10" class="titulos2">Asunto
	<input type="text" disabled name="ra_asun" value='<?=$ra_asun?>' class="tex_area" size="120">
	</td>
</tr>
<tr class="titulos2">
	<td height="21" colspan="10">Observaciones o Desc Anexos :
        <input type="text" name="observaciones" value="<?=$observaciones?>" size="50" class="tex_area">
    </td>
</tr>
<tr class="titulos2">
	<td height="21" colspan="10">N&uacute;mero de Gu&iacute;a :
	    <input type="text" name="numGuia" value="<?=$numGuia?>" size="30" class="tex_area" maxlength="15">
    </td>
</tr>
<?php
    }
?>
