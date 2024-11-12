<?php 
session_start();
if (!$ruta_raiz) 
	$ruta_raiz= "..";
include_once ("$ruta_raiz/config.php");
include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("..");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//$db->conn->debug=true;

$nuExp = $_REQUEST['numeroExpediente'];

?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html">
		<link rel="stylesheet" href="<?=$ruta_raiz;?>/estilos/orfeo.css">
		<script type="text/javascript" src="../js/jquery.js"> </script>
		<script type="text/javascript" src="../js/jquery-ui-1.8.12.custom.min.js"> </script>

		<style type="text/css">
			 body{
				 margin:0; 
				 padding:0;
			 }
			 .content{
				 padding-top:10px;
				 width:820px;
				 margin:0 auto;
			 }
			 ul{
				 list-style:none;
				 margin:0;
				 padding:0;
			 }
			 ul li{
				 display:block;
				 background:#FFFFFF;
				 border:1px solid #3594C4;
				 color:#3594C4;
				 margin-top:3px;
				 height:20px;
				 padding:4px;
			 }
			 .ui-state-highlight {
				 background:#FFF0A5;
				 border:2px solid #FED22F;
			 }
			 .msg{
				 color:#00C;
				 font:normal 12px Arial;
			 }
		</style>
		<script type="text/javascript">
			$(document).ready(function(){
				$("ul#anexos").sortable({ placeholder: "ui-state-highlight",opacity: 0.6, cursor: 'move', update: function() {
					var ordenar = $(this).sortable("serialize");
					$.post("ordenarAnexosExpTx.php", ordenar, function(respuesta){
						$(".msg").html(respuesta).fadeIn("fast").fadeOut(2800);
					});
				}});
			});
		</script>
	</head>
	<body>
	<br>
	<form action='./ordenarAnexosExp.php' method=post name="ordenar" id="ordenar">
		<input type='hidden' name='numeroExpediente' value='<?php echo $nuExp; ?>'>
		<table align="center" width="95%" border="0" cellpadding="2" cellspacing="5" class="borde_tab">
			<tr>
				<td width='23%' class="titulos4">USUARIO: <br><br> <?=$_SESSION['usua_nomb']?> </td>
				<td width='23%' class="titulos4">DEPENDENCIA: <br><br> <?=$_SESSION['depe_nomb']?> <br></td>
				<td width='23%' class="titulos4">Anexos del Expediente:<br><br>  <?php echo $nuExp; ?> <br></td>
				<td width='23%' class="titulos4">Fecha de anexos:<br><br>  
					<select name='fecha_busq' id='fecha_busq' class='select'  onChange='this.form.submit();'>
						<?php
							$datos= " selected ";
							echo "<option value='9999' $datos>-- Seleccione una fecha --</option>\n";
							
							$getFecha ="SELECT	DISTINCT ANEXOS_EXP_FECH_CREA
										FROM	SGD_ANEXOS_EXP
										WHERE	SGD_EXP_NUMERO = '".$nuExp."' AND
												ANEXOS_EXP_ESTADO = 0";
							$rsFecha = $db->conn->Execute($getFecha);

							while(!$rsFecha->EOF)  {
								$detalle = $rsFecha->fields["ANEXOS_EXP_FECH_CREA"];
								$codigo  = $rsFecha->fields["ANEXOS_EXP_FECH_CREA"];
								$datos 	 = ($fecha_busq == $codigo)? $datos= " selected ":"";
								echo "<option value='$codigo' $datos>$detalle</option>";
								$rsFecha->MoveNext();
							};

						?>	
					</select>

				</td>
			</tr>
		</table>
	</form>	
		<br>
		
	<?php
	
	if ($_POST['fecha_busq']){
		$fechaSelec = "AND ANEXOS_EXP_FECH_CREA = '".$_POST['fecha_busq']."'";
		$sqlSubstDesc =  $db->conn->substr."(ANEXOS_EXP_DESC, 0, 90)";

		$sql = "	SELECT	ANEXOS_EXP_ID,
							ANEXOS_EXP_ORDEN,
							USUA_LOGIN_CREA,
							$sqlSubstDesc AS ANEX_DESC,
							ANEXOS_EXP_FECH_CREA AS FECHA
					FROM	SGD_ANEXOS_EXP
					WHERE	SGD_EXP_NUMERO = '".$nuExp."' AND
							ANEXOS_EXP_ESTADO = 0
							$fechaSelec
					ORDER BY ANEXOS_EXP_ORDEN";
		$rs = $db->conn->Execute($sql);
	
	?>
		
		<table width="95%" align="center"> 
			<tr>
				<td>
					<div class="content">
						<ul id="encabezado">
							<li id="titulos">
								<table width="100%" align="center">
									<tr style="color:#3594C4; font:normal 15px Arial;">
										<th width="5%" align=left> No. </th>
										<th width="15%" align=center> Código Anexo </th>
										<th width="20%" align=center> Creador </th>
										<th width="30%" align=center> Descripción del Anexo </th>
										<th width="30%" align=center> Fecha de anexo </th>
									</tr>
								</table>
							</li>
						</ul>
					</div>
					<div class="content">
						<ul id="anexos">
							<?php
							while(!$rs->EOF){
								$row = $rs->fields["ANEXOS_EXP_ID"];
							?>
							<li id="order-<?php echo $row."B".$_SESSION['usua_doc'];?>">
								<table width="95%">
									<tr style="color:#3594C8; font:normal 11px Arial;">
										<td width='2%' align='left'> <?php echo $rs->fields["ANEXOS_EXP_ORDEN"]; ?> </td>
										<td width='18%' align='center'> | &nbsp; <?php echo $rs->fields["ANEXOS_EXP_ID"]; ?> &nbsp; </td>
										<td width='20%' align='center'> &nbsp; | &nbsp; <?php echo $rs->fields["USUA_LOGIN_CREA"]; ?> &nbsp; </td>
										<td width='30%' align='left'> &nbsp; | &nbsp; <?php echo $rs->fields["ANEX_DESC"]; ?> &nbsp; </td>
										<td width='30%' align='center'> &nbsp; | &nbsp; <?php echo $rs->fields["FECHA"]; ?></td>
									</tr>
								</table>
							</li>
							<?php
								$rs->MoveNext();
							}
							?>
						</ul>
						<br>
						<div class="msg" align=center> </div>
					</div>
				</td>
			</tr>
		</table>
		
	<?php
	}
	?>
		
	</body>
</html>