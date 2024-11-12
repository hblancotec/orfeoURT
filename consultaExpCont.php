<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "./sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}

$ruta_raiz= "";
include_once ("./config.php");
require_once("./_conf/constantes.php");
include_once ("./include/db/ConnectionHandler.php");
session_start();
$db = new ConnectionHandler(ORFEOPATH);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//$db->conn->debug=true;
$krd = (!empty($_POST['krd'])) ? $_POST['krd'] : $_GET['krd'];

if(!$_POST['dependencia_busq']){
	$dependencia_busq = 640;
}

$serie_busq = 14;


if(!$_POST['subSerie_busq']){
	$subSerie_busq = 17;
}
else{
	$subSerie_busq = $_POST['subSerie_busq'];
}

if ($_REQUEST['tdoc'])
	$tdoc = $_REQUEST['tdoc'];
elseif ($subSerie_busq == 17){
	$tdoc = 3036;
}
else{
	$tdoc = 99999;
}

if($_POST['anExp'])
	$fechAno_busq = $_REQUEST['anExp'];
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;">
	<link href="./estilos/orfeo.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="./estilos/orfeo.css">
	<link type="text/css" href="./js/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
	<script type="text/javascript" src="./js/jquery.js"></script>
   	<script type="text/javascript" src="./js/jquery-ui-1.8.12.custom.min.js"></script>
	<script>
		var expSeleccionado;
		$(document).ready(function(){
			$("#exp1").autocomplete({
				minLength: 3,
				source: "consultaNomExpCont.php?dpn=<?php echo $dependencia_busq;?>&sre=<?php echo $serie_busq;?>&sbs=<?php echo $subSerie_busq;?>&an=<?php echo $fechAno_busq;?>&k=<?php echo $krd;?>",
				select: function(event, ui) {
					$("#exp1").val(ui.item.label);
					expSeleccionado = jQuery('#exp1').val()
					js_consultar();
				}
			});
		});

		function js_consultar()
		{
			var val;
			if(jQuery('#exp1').val()){
				val =jQuery('#exp1').val();
			}
			else if(expSeleccionado !=''){
				val = expSeleccionado;
			}
			
			if(val==''){
				alert ('Por favor seleccione expediente');
				return false;
			}

			jQuery('#d_detalle_vec2').html('Cargando...');
			jQuery.ajax({
				url:"expediente/DatosExpediente.php?<?=session_name()?>=<?=trim(session_id())?>",
				type:'POST',
				cache:false,
				dataType:'html',
				data:({
					val: val,
					tdoc: <?php echo $tdoc; ?>,
					frmActual:"<?php echo $_SERVER['PHP_SELF']; ?>"
					}),
				error:function (objeto,que_paso,objeto){
					alert ('Error de conexión');
				},
				success:function(data){
					jQuery('#d_respuesta').html(data);
					jQuery('#d_detalle_vec2').html('');
				}
			});
		}

		function BusquedaPost(){
			document.getElementById('exp1').value='<?php echo $val?>';
			document.getElementById('fechAno_busq').value='<?php echo $fechAno_busq?>';
			js_consultar();
		}
		
		function regresar(){ 
			js_consultar();
		}

	</script>
</head>
<body>
	<form name="formulario" id="form" method=post action='consultaExpCont.php?<?=session_name()."=".trim(session_id())."&krd=$krd"?>'>
		<table width="50%" border="0" align="center" class="t_bordeGris" cellspacing="4">
			<tr>
				<td class="titulos4" colspan=2 align="center"> 
					Consulta de Expedientes de Contratos
				</td>
			</tr>

			<tr>
				<td class="titulos2"> Dependencia: </td>
				<td class="listado2">
					<?php
					print "<select name='dependencia_busq' id='dependencia_busq' class='select' style='width:510px' onChange='formulario.submit();'>";
					
					$isqlus = "	SELECT	DEPE_CODI,
										DEPE_NOMB
								FROM	DEPENDENCIA
								WHERE	DEPE_CODI IN (640, 131)
								ORDER BY DEPE_CODI";
					$rs1 = $db->conn->Execute($isqlus);

					do{
						$codigo = $rs1->fields["DEPE_CODI"];
						$depnombre = $codigo;
						$depnombre = $depnombre." - ".$rs1->fields["DEPE_NOMB"];
						$datoss="";
						if($dependencia_busq==$codigo)
							$datoss= " selected ";
						echo "<option value='$codigo' $datoss>$depnombre</option>\n";
						$rs1->MoveNext();
					}
					while(!$rs1->EOF);
					?>

				</td>
			</tr>

			<tr>
				<td class="titulos2"> Serie:
					<?php
					$datoss = "";
					if($srdOn)
						$datoss = " checked ";
					?>

				</td>
				<td class="listado2">
					<select name='serie_busq' id='serie_busq' class="select" style='width:510px' onChange="formulario.submit();">

					<?php
					
					// Consulta de la serie con la Dependencia seleccionada
					$fecha_hoy 		= Date("Y-m-d");
					$sqlFechaHoy	= $db->conn->DBDate($fecha_hoy);
					$datoss	= " selected ";

					if($dependencia_busq){
						$depeConsulta =	'M.DEPE_CODI = '.$dependencia_busq. ' AND';
					}

					
					$getSerie =	"SELECT	DISTINCT (CONVERT(CHAR(4),S.SGD_SRD_CODIGO,0)+'- '+S.SGD_SRD_DESCRIP) AS DETALLE,
										S.SGD_SRD_CODIGO AS CODIGO
								FROM	SGD_MRD_MATRIRD M,
										SGD_SRD_SERIESRD S
								WHERE	$depeConsulta
										S.SGD_SRD_CODIGO = M.SGD_SRD_CODIGO
										AND S.SGD_SRD_CODIGO = 14
								";
					$rsSerie = $db->conn->Execute($getSerie);

					while(!$rsSerie->EOF)  {
						$detalle 	= $rsSerie->fields["DETALLE"];
						$codigoSer 	= $rsSerie->fields["CODIGO"];
						$datoss 	= ($serie_busq == $codigoSer)? " selected ":"";
						echo "<option value='$codigoSer' $datoss>$detalle</option>";
						$rsSerie->MoveNext();
					};
				  ?>

				  </select>
				</td>
			</tr>

			<tr>
				<td class="titulos2"> SubSerie: 

				  <?php
					$datossb = "";
					if($sbrdOn)
						$datossb = " checked ";
				  ?>

				  <label class="indicaciones"> Inactivas </label>
				  <input name="sbrdOn" type="checkbox" <?=$datossb?> onChange="formulario.submit();">
				</td>
				<td class="listado2">
				  <select name='subSerie_busq' id='subSerie_busq' class="select" style='width:510px' onChange="formulario.submit();">

					<?php
					// Consulta de la serie con la Dependencia seleccionada
					$fecha_hoy 		= Date("Y-m-d");
					$sqlFechaHoy	= $db->conn->DBDate($fecha_hoy);
					$datossb = 'selected';

					//Entra si NO se marca Series Inactivas
					if (!isset($_POST['srdOn'])) { 
						//Entra si NO se marca Sub-Series Inactivas
						if (!isset($_POST['sbrdOn']) ) {
							$whereSbrdOn = "AND m.SGD_MRD_ESTA=1";
							$whereSbrdOff = "";
						}
						else {
							$whereSbrdOn = "AND m.SGD_MRD_ESTA=0";
							$whereSbrdOff = "AND m.SGD_SBRD_CODIGO NOT IN 
												(SELECT DISTINCT m.SGD_SBRD_CODIGO 
												 FROM	SGD_MRD_MATRIRD m 
												 WHERE	$depeConsulta
														m.SGD_MRD_ESTA = 1)";
						}
					}

					else { //Entra SI se marco la opcion de Series Inactivas 
						$whereSbrdOn = 	"AND m.SGD_MRD_ESTA=0";
						$whereSbrdOff = "AND m.SGD_SBRD_CODIGO NOT IN
											(SELECT	DISTINCT m.SGD_SBRD_CODIGO 
											 FROM 	SGD_MRD_MATRIRD m 
											 WHERE	$depeConsulta
													m.SGD_MRD_ESTA = 1)";
					}

					$querySub =	"SELECT	DISTINCT (CONVERT(CHAR(4),SU.SGD_SBRD_CODIGO,0)+'- '+SU.SGD_SBRD_DESCRIP) AS DETALLE,
										SU.SGD_SBRD_CODIGO AS CODIGO
								 FROM	SGD_MRD_MATRIRD M 
										INNER JOIN SGD_SBRD_SUBSERIERD SU ON 
											M.SGD_SBRD_CODIGO = SU.SGD_SBRD_CODIGO
								 WHERE	$depeConsulta
										M.SGD_SRD_CODIGO   		= '$serie_busq'
										AND SU.SGD_SRD_CODIGO  	= '$serie_busq'
										$whereSbrdOn
										AND ".$sqlFechaHoy." BETWEEN SU.SGD_SBRD_FECHINI AND SU.SGD_SBRD_FECHFIN
										$whereSbrdOff
								 ORDER BY DETALLE";
					$rsSub=$db->conn->Execute($querySub);
					

					while(!$rsSub->EOF)  {
						$detalleSub	= $rsSub->fields["DETALLE"];
						$codigoSub 	= $rsSub->fields["CODIGO"];
						$datossb 	= ($subSerie_busq == $codigoSub)? $datossb = " selected ":"";
						echo "<option value='$codigoSub' $datossb>$detalleSub</option>";
						$rsSub->MoveNext();
					}

					?>

				  </select>
				</td>
			</tr>
			<tr>
				<td class="titulos2"> Año: </td>
				<td class="listado2">
				   <?php
					print "<select name='fechAno_busq' id='fechAno_busq' class='select' onChange='formulario.submit();'>";
					// Genera el rango de años para seleccionar
					$datossFec	= " selected ";
					for($i = Date("Y"); $i > 1999; $i-- ){
						$datossFec = ($fechAno_busq == $i)? $datossFec = " selected ":"";
						echo "<option value='$i' $datossFec>$i</option>";
					}
					?>

				  </select>
				</td>
			</tr>
			<tr>
				<td class="titulos2"> Expediente: </td>
				<td class="listado2">
					<input id="exp1" size="82" style="z-index: 60; position: relative"/>
				</td>
			</tr>
			<tr>
				<td colspan=2 align="center"> 
					<font size="2"  color="#FF0000">
						* Ingrese al menos 3 letras para obtener resultados.
						* Ingrese el texto sin acentos ortográficos  
					</font>
				</td>
			</tr>
		</table>
	</form> 
	<div id="d_detalle_vec2" style="height:20px;position: fixed; color:#F60; "></div>
	<div id='d_respuesta'> </div>
	<?php  if($val){?>
        <script>setTimeout(BusquedaPost(),1000)</script>
	<?php }?>
  </body>
</html>