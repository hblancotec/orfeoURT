<head>
<title>Adjuntar archivos a Expedientes</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="../estilos/orfeo.css" >
<link rel="stylesheet" type="text/css" href="../estilos/fonts-min.css" >
<link rel="stylesheet" type="text/css" href="../estilos/button.css" >
<link rel="stylesheet" type="text/css" href="../estilos/calendar.css" />

<script type="text/javascript" src="./js/yahoo-dom-event.js"></script>
<script type="text/javascript" src="./js/datasource-min.js"></script>
<script type="text/javascript" src="./js/element-min.js"></script>
<script type="text/javascript" src="./js/button-min.js"></script>
<script type="text/javascript" src="./js/yahoo-min.js"></script>
<script type="text/javascript" src="./js/event-min.js"></script>
<script type="text/javascript" src="./js/connection-min.js"></script>
<!-- archivo que maneja los eventos  -->
<script type="text/javascript" src="./js/adjuntarArchivo.js"></script>
<script type="text/javascript" src="./js/inacAdjArch.js"></script>
<script type="text/javascript" src="./js/calendar-min.js"></script>

<!--
Se llama la siguiente funcion ubicada
	en el archivo adjuntarArchivo.js para pasar
	el valor de la dependencia del usuario, obtenida
	desde la session  -->

<script type="text/javascript">	
	if(<!--{$salir}--> == 1){
		alert('Serie o Subserie del expediente no econtrada');
		window.close();
	};
</script>

</head>

<body  class=" yui-skin-sam">

<table width="100%" border="0" align="center" margin="4" CELLPADDING="10" cellspacing="0" >
<form name="adjuntarArchivo" id="adjuntarArchivo" method="POST">	

	<input type="hidden" value="<!--{$dependencia}-->" 	name="depeCodi">
	<input type="hidden" value="<!--{$usua_login}-->" 	name="usualogin">
	<input type="hidden" value="<!--{$num_exped}-->" 	name="numExpe">
	
	<tr bordercolor="#FFFFFF">
		<td colspan="2" height="40" align="center" class="titulos4"
			valign="middle">
			<b><span class=etexto>Adjuntar archivos a Expedientes</span></b>
		</td>
	</tr>

	<tr bordercolor="#FFFFFF">
		<td colspan="2"  class='titulos5' align="left">
		Los archivos que seleccione no pueden sobrepasar los 15mb.<br/>
		El nombre de este archivo preferiblemente no debe contener
		espacios, ejemplo (LogoDnp.jpg).Es incorrecto escribir (logo Dnp.jpg).<br/>
		No utilice nombre para los archivos superiores a 30 caracteres.<br />
		Seleccione una extensi&oacute;n de archivo para que pueda
		realizar la b&uacute;squeda.<br />
		</td>
	</tr>
	<tr>
		<td align="left" width="35%"><b>Descripci&oacute;n:
			<h6>Utilice solamente letras y numer&oacute para redactar el mensaje.</h6>
			<h6>Max. 200 caracteres.</h6></b></td>
		<td>
			<div id="postVars"><textarea id="descrip" rows="6" onKeyUp="return maximaLongitud(this)" class="campo4" name="descrip"></textarea></div>
		</td>
	</tr>
	<tr>
		<td align="left" ><b>Fecha del Proceso:<br />(d&iacute;a/mes/a&ntilde;o)</b></td>
		<td>
			<input type="text" name="date1" id="date1" readonly="READONLY"/>
			<button id="show1up" type="button">Calendario</button><br />
			<div id="cal1Container"></div>
		</td>
	</tr>
	<tr>
		<td align="left"><b>Tipo Documental: <!--{$tipoDocumental.1}--></b></td>
		<td>
			<div id="postVars">
				<select name="var2Value" id="var2Value" class="campo4">
					<option value="xx" selected="selected"></option>
					<!--{foreach key=key item=item from=$tipoDocumental}-->
						<option value=<!--{$key}-->><!--{$item}--></option>
					<!--{/foreach}-->
				</select>
			</div>
		</td>
	</tr>
	<!-- IBISCOM 2018-10-24-->
	<tr style="display:<!--{$oculta}-->">
		<td align="left"><b>Palabras clave:</b></td>
		<td>
			<div id="postVars">
				<textarea rows="2"  class="campo4" name="palabrasClave" id="palabrasClave"></textarea>
				<!-- <input type="text" name="palabrasClave" id="palabrasClave"/> -->
			</div>
		</td>
	</tr>
	<tr style="display:<!--{$oculta}-->">
		<td align="left"><b>* Folios:</b></td>
		<td>
			<div id="postVars">
				<input type="number" min="0" max="999" name="folios" id="folios" />
			</div>
		</td>
	</tr>
	<tr style="display:<!--{$oculta}-->">
		<td align="left"><b>Nombre Proyector:</b></td>
		<td>
			<div id="postVars">
				<input type="text" name="nombreProyector" id="nombreProyector" />
			</div>
		</td>
	</tr>
	<tr style="display:<!--{$oculta}-->">
		<td align="left"><b>Nombre Revisor:</b></td>
		<td>
			<div id="postVars">
				<input type="text" name="nombreRevisor" id="nombreRevisor" />
			</div>
		</td>
	</tr>
	
	<!-- IBISCOM 2018-10-24-->
	<tr>
		<td align="left" colspan="2">
	        <div id="inpfile">
	            <br/><b>Adjuntar: </b><input type="file" name="archs[]" id="div0" size="26"> <!--<br/>
				<b>Adjuntar: </b><input type="file" name="archs[]" id="div0" size="26"><br/>
				<b>Adjuntar: </b><input type="file" name="archs[]" id="div0" size="26"><br/>
				<b>Adjuntar: </b><input type="file" name="archs[]" id="div0" size="26"> -->
	        </div>		
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			<table width="100%" border='0px'>
				<tr>
					<td align="center">
						<div id="enviarForm">
						<img id="sonico" class="yui-hidden2" src="../../img/loading.gif"/>			
						</div>
					</td>
					<td align="center">
						<button class="botones" type="button" id="actualizar2"> . </button>
					</td>
					<td align="center">
						<div id="cancelForm"></div>					
					</td>
				</tr>
			</table>			
		</td>
	</tr>
	<tr>
		<td colspan="2" ></td>
	</tr>
</form>
	
	<!--{if $adj_exp_edit eq ''}-->
		&nbsp;
	<!--{else}-->
	<tr bordercolor="#FFFFFF">
		<td colspan="2" height="40" align="center" class="titulos4"
			valign="middle">
			<b><span class="etexto">Inactivar archivos adjuntos</span></b>
		</td>
	</tr>
	<form id="enviCamb" name="enviCamb">
	<tr>
		<td align="center" colspan="2">
					
			<table width="97%">
				<tr>		
					<td width="45%"><b> Nombre del adjunto </b></td>
					<td width="20%" align="center" ><b>Estado </b></td>
					<td width="35%" align="center"><b>Cambiar Estado</b></td>
				</tr>									
				<!--{foreach key=key item=item from = $adj_exp_edit}-->
				<tr>
					<td align="left"><!--{$item.nombre}--></td>
					<td align="center"><!--{$item.estado}--></td>
					<td align="center">
						<input type="checkbox" value="<!--{$key}-->" name="excluRad[]"> 
					</td>
				</tr>				
	            <!--{/foreach}-->
				<tr>
					<td align="left">						
						<button class="botones" type="button" id="actualizar"> Actualizar </button>
					</td>
					<td align="center">
									
					</td>
					<td align="center">
						<button class="botones" type="button" id="inactivar"> Inactivar </button>
						<button class="botones" type="button" id="activar"> Activar </button>									
					</td>
				</tr>
			</table>
			
		</td>
	</tr>	
	</form>					
	<!--{/if}-->
	
	<!--{if $adj_exp_bloc eq ''}-->
	  &nbsp;
	<!--{else}-->
	<tr bordercolor="#FFFFFF">
		<td colspan="2" height="40" align="center" class="titulos4"
			valign="middle">
			<b><span class="etexto">Archivos adjuntos de otros usuarios</span></b>
		</td>
	</tr>
	<tr>
		<td align="center" colspan="2">
			<form name="inacAdjExp">
			<table width="97%">
				<tr>		
					<td width="50%"><b> Nombre del adjunto </b></td>
					<td width="25%"><b>Estado <b/></td>
					<td width="25%" align="center"><b>Creador</b></td>
				</tr>									
				<!--{foreach key=key item=item from = $adj_exp_bloc}-->
				<tr>
					<td><!--{$item.nombre}--></td>
					<td><!--{$item.estado}--></td>
					<td align="center"><!--{$item.login}--></td>					
				</tr>
	            <!--{/foreach}-->
			</table>
			</form>
		</td>
	</tr>							
	<!--{/if}-->
	
	
	<tr>
		<td colspan="2">
			<div id="returnedDataDisplay"></div>
		</td>
	</tr>
</table>

<input type="hidden" id="ocultaDocElectronico" name="ocultaDocElectronico" value="<!--{$oculta}-->" />

</body>
</html>
