<?php 
session_start();
if (!$ruta_raiz) 
	$ruta_raiz= "..";
include_once ("$ruta_raiz/config.php");
include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("..");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//$db->conn->debug=true;
$nuRad = $_GET['numrad'];
$sqlSubstDesc =  $db->conn->substr."(anex_desc, 0, 90)";
$sql = "	SELECT	ANEX_CODIGO,
					ANEX_ORDEN,
					ANEX_CREADOR,
					$sqlSubstDesc AS ANEX_DESC,
					ANEX_FECH_ANEX
			FROM	ANEXOS
			WHERE	ANEX_RADI_NUME = $nuRad
					AND RADI_NUME_SALIDA IS NULL
					AND ANEX_SALIDA = 0
			ORDER BY ANEX_ORDEN";
$rs = $db->conn->Execute($sql);
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
		width:920px;
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
			$.post("order.php", ordenar, function(respuesta){
				$(".msg").html(respuesta).fadeIn("fast").fadeOut(2800);
			});
		}
		});
	});
   </script>
  </head>
  <body>
   <br>
   <Form action='../verradicado.php?&numrad=<?=$verrad?>' method=post name="regresar" id="regresar">
	<table align="center" width="97%" border="0" cellpadding="2" cellspacing="5" class="borde_tab">
		<tr>
			<td width='30%' class="titulos4">USUARIO: <br><br> <?=$_SESSION['usua_nomb']?> </td>
			<td width='30%' class="titulos4">DEPENDENCIA: <br><br> <?=$_SESSION['depe_nomb']?> <br></td>
			<td width='30%' class="titulos4">Anexos del Radicado:<br><br>  <?php echo $nuRad; ?> <br></td>
			<td width='5' class="grisCCCCCC">
				<input type="submit" value="REGRESAR" align="bottom" class="botones" id="REGRESAR">
			</td>
		</tr>
	</table>
	</Form> <br><br>
	<table align="center"> <tr><td>
	 <div class="content">
	  <ul id="encabezado">
		<li id="titulos">
			<table width="900px" align="center">
				<tr style="color:#3594C4; font:normal 14px Arial;">
					<th width="5%" align=left> No.</th>
					<th width="15%" align=center> Código Anexo </th>
					<th width="10%" align=center> Creador </th>
					<th width="55%" align=center> Descripción del Anexo </th>
					<th width="15%" align=center> Fecha de anexo </th>
				</tr>
			</table>
		</li>
	  </ul>
	</div>
	<div class="content">
	  <ul id="anexos">
		<?php
		while(!$rs->EOF){
			$row = $rs->fields["ANEX_CODIGO"];
		?>
			<li id="order-<?php echo $row."B".$_SESSION['usua_doc'];?>">
				<table width="900px">
					<tr style="color:#3594C8; font:normal 11px Arial;">
						<td width="5%"> <?php echo $rs->fields["ANEX_ORDEN"]; ?> &nbsp; | </td>
						<td width="15%" align=left> <?php echo $rs->fields["ANEX_CODIGO"]; ?> &nbsp; | </td>
						<td width="10%" align=center> <?php echo $rs->fields["ANEX_CREADOR"]; ?>  </td>
						<td width="55%"> | &nbsp;&nbsp; <?php echo $rs->fields["ANEX_DESC"]; ?> </td>
						<td width="15%" align=right> | &nbsp;&nbsp; <?php echo $rs->fields["ANEX_FECH_ANEX"]; ?></td>
					</tr>
				</table>
			</li>
		<?php
			$rs->MoveNext();
		}
		?>
	  </ul> <br>
	  <div class="msg" align=center></div>
	</div>
	</td></tr></table>
  </body>
</html>