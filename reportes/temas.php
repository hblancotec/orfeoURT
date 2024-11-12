<?php
session_start();
$ruta_raiz = "..";



include ("$ruta_raiz/_conf/constantes.php");
$fechai = date("Y-m-d");
if (!$fecha_busq)
	$fecha_busq = date("Y-m-d");
if (!$fecha_busq2)
	$fecha_busq2 = date("Y-m-d",strtotime("+1 day", strtotime($fechai)));
if (!$tema_busq)
	$tema_busq = -2;
if (!$_SESSION['dependencia'])
	include "$ruta_raiz/rec_session.php";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");
define('ADODB_FETCH_ASSOC', 2);
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	require_once (ORFEOPATH . "class_control/Dependencia.php");
	require_once (ORFEOPATH . "class_control/usuario.php");
if(!empty($tema_busq))
	$rd= $db->conn->Execute("select SGD_TEMA_NOMBRE, id from SGD_TEM_NOMBRES where id = $tema_busq ");
?>
<head>
<link rel="stylesheet" href="../estilos/orfeo.css">
</head>
<BODY>
<div id="spiffycalendar" class="text"></div>
<link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
<script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>
<script language="javascript"><!--
  var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "formboton", "fecha_busq","btnDate1","<?=$fecha_busq?>",scBTNMODE_CUSTOMBLUE);
	var dateAvailable2 = new ctlSpiffyCalendarBox("dateAvailable2", "formboton", "fecha_busq2","btnDate1","<?=$fecha_busq2?>",scBTNMODE_CUSTOMBLUE);
//--></script><P>
	<form name=formboton  method=post  action='temas.php?<?=session_name()."=".session_id()."&krd=$krd&fecha_h=$fechah&fecha_busq=$fecha_busq&fecha_busq2=$fecha_busq2&tema_busq=$tema_busq"?>'>
<table class='titulos2' width='98%' align="center">
  <tr><TD class='titulos1' align="center">GENERACION LISTADO DE DOCUMENTOS POR TEMA</td></tr>
</table>
<TABLE class="borde_tab" width='98%' align="center">

	<TR>
    	<TD  class='titulos5'>Temas: <?= $rd->fields['SGD_TEMA_NOMBRE']?> </TD>
    	<TD> <?php $sql = "select SGD_TEMA_NOMBRE, id from SGD_TEM_NOMBRES";
				$rs = $db->conn->Execute($sql);
                print $rs->GetMenu2('tema_busq', '', false, false, 1, " id='tema_busq' class='select'");
			 ?>
    </TD>
    <TD  class='titulos5'> Fecha desde <?php echo "($fecha_busq)";?></TD>
    <TD>  <script language="javascript">
		        dateAvailable.date = "2003-08-05";
			    dateAvailable.writeControl();
			    dateAvailable.dateFormat="yyyy-MM-dd";
    		  </script>
	</TD>
    <TD class='titulos5'> Fecha Hasta <?php echo "($fecha_busq2)";?></TD>
    <TD class='listado5'>
        <script language="javascript">
		        dateAvailable2.date = "2003-08-05";
			    dateAvailable2.writeControl();
			    dateAvailable2.dateFormat="yyyy-MM-dd";
    	</script>
	</TD>
    <td><center>
		<INPUT TYPE=SUBMIT name=generar_informe Value='Generar Informe' class='botones_mediano'></center>
		</td>
	</tr>
  </TABLE>
<?php
if (!$fecha_busq)
	$fecha_busq = date("Y-m-d");
if ($generar_informe) {
	# objeto que contienela historia por radicado
	class HistoriaRadicado {
		var $radicado;
		var $fecharadicado;
		var $entidad;
		var $asunto;
		var $tipodoc;
		var $dependencia;
		var $traslados = array ();
		var $asocia;
		var $fecha_asocia;
		var $asocia1;
		var $fecha_asocia1;
		function HistoriaRadicado($radicado) {
			$this->radicado = $radicado;
		}
	}

	class Traslados {
		var $dependencia;
		var $fecha;
		var $usuario;
		function Traslados($dependencia, $fecha, $usuario) {
			$this->dependencia = $dependencia;
			$this->usuario = $usuario;
			$this->fecha = $fecha;
		}
	}

	$objUs = new Usuario($db);
	$objDep = new Dependencia($db);

	$sqlFecha = $db->conn->SQLDate("d-m-Y", "a.HIST_FECH");
	$radFecha = $db->conn->SQLDate("d-m-Y", "r.RADI_FECH_RADI");

	$fecha_ini = $fecha_busq;
	$fecha_fin = $fecha_busq2;

	$fechaIni = "'$fecha_busq'";
	$fechaFin = "'$fecha_busq2'";
	$serie = 8;
	$subSerie = 7;

	$tema = $tema_busq;

	//fecha se captura
$isql =
	"select
		c.RADI_NUME_RADI
		,c.RADI_PATH
		,e.DEPE_NOMB
		,c.radi_fech_radi
		,s.SGD_TPR_DESCRIP
		,c.MREC_CODI
		,f.RADI_NUME_SALIDA

	from
		SGD_TPR_TPDCUMENTO s,
		DEPENDENCIA e,
		SGD_TEM_NOMBRES h,
		radicado c
		LEFT OUTER JOIN anexos f ON (
				c.RADI_NUME_RADI = f.ANEX_RADI_NUME
            	AND f.RADI_NUME_SALIDA in (
            		select
                		a.RADI_NUME_SALIDA
            		from
                		anexos a
                		,radicado c
            		where
                		a.anex_radi_nume <> a.radi_nume_salida
                		and (a.anex_estado >= 3 or a.anex_estado_email = 1)
                		and a.radi_nume_salida = c.radi_nume_radi
                		and convert(varchar(15), a.RADI_NUME_SALIDA) like '%1'
                		and a.anex_borrado= 'N'
                		and a.sgd_dir_tipo <> 7
                		and ((c.SGD_EANU_CODIGO <> 2
                		and c.SGD_EANU_CODIGO <> 1)
                		or c.SGD_EANU_CODIGO IS NULL)
                		and a.ANEX_SALIDA = 1
           		)
		)
	where
		(c.RADI_FECH_RADI BETWEEN $fechaIni and $fechaFin )
		and c.RADI_TEMA_ID = h.id
		and c.RADI_TEMA_ID = $tema
		and	s.sgd_tpr_codigo = c.tdoc_codi
		and e.DEPE_CODI = c.RADI_DEPE_ACTU
	order by c.RADI_NUME_RADI desc, c.radi_fech_radi asc";

	$result=$db->conn->Execute($isql);

	$contenido = "";
	$contenido .= '<?xml version="1.0" encoding="iso-8859-1"?>';
	$contenido .= "\n<Asignaciones>\n ";
	while($result && !$result->EOF)
	{
		$medio_recp = $result->fields['MREC_CODI'];

		if (!empty($result->fields['RADI_NUME_SALIDA']))
			$ESTADO = "Contestado";
		else
			$ESTADO = "En tramite";
		if(!empty($medio_recp))
			$medio_recp = $db->conn->Execute("select mrec_desc from MEDIO_RECEPCION where  mrec_codi = $medio_recp ");


		$contenido .= "	<Radicado>\n ";
		$contenido .= "		<Nro_Radicado>R:"   . $result->fields['RADI_NUME_RADI'] . "</Nro_Radicado>\n ";
		$contenido .= "		<Nro_Salida>"   . $result->fields['RADI_NUME_SALIDA'] . "</Nro_Salida>\n ";
		$contenido .= "		<Fecha_Radicado>" . $result->fields['radi_fech_radi'] . "</Fecha_Radicado>\n ";
		$contenido .= "		<Dependencia_Actual>"    . $result->fields['DEPE_NOMB'] . "</Dependencia_Actual>\n ";
		$contenido .= "		<Tipo>"           . $result->fields['SGD_TPR_DESCRIP'] . "</Tipo>\n ";
		$contenido .= "		<Medio_Llegada>"  .  $medio_recp->fields['MREC_DESC']. "</Medio_Llegada>\n ";
		$contenido .= "		<Estado>"  . $ESTADO . "</Estado>\n ";
		$contenido .= "	</Radicado>\n ";		$result->MoveNext();
	}
	$contenido .= "</Asignaciones> \n ";
	$hora = date("H") . "_" . date("i") . "_" . date("s");
	// var que almacena el dia de la fecha
	$ddate = date('d');
	// var que almacena el mes de la fecha
	$mdate = date('m');
	// var que almacena el año de la fecha
	$adate = date('Y');
	// var que almacena  la fecha formateada
	$fecha = $adate . "_" . $mdate . "_" . $ddate;
	//guarda el path del archivo generado
	$ruta_raiz = "..";
	$archivo = $ruta_raiz . "/bodega/tmp/tmp_0" . "_$fecha" . "_$hora.$salida" . "xls";
	$fp = fopen($archivo, "wb");
	fputs($fp, $contenido);
	fclose($fp);
?>
<TABLE class="tablas" width='98%' align="center">
    <tr>
    <td>Se ha generado el archivo
    	<?=strtoupper($salida) ?> con el resultado de la consulta realizada.
    	Para obtener el archivo guarde del destino del siguiente v&iacute;nculo
    	al archivo: <a href="<?=$archivo?>" target="_blank"><?=strtoupper($salida)?> GENERADO</a>.</td>
	</tr>
</table>
<?php

}
?>

<form name='formEnviar' method='post'>
<TABLE width="98%" align="center" cellspacing="0" cellpadding="0">
	<tr>
		<td class="grisCCCCCC">
		<table width="100%" border="0" cellpadding="0" cellspacing="5"
			class="borde_tab"'>
			<tr class='titulos3'>
				<td width='8%' align="center">Radicado</td>
				<td width='8%' align="center">Respuesta</td>
				<td width='14%' align="center">Fecha Radicado</td>
				<td width='25%' align="center">Descripcion</td>
				<td width='12%' align="center">Tipo de Recepcion</td>
				<td width='12%' align="center">Dependencia</td>
				<td width='15%' align="center">Estado</td>
			</tr>
			<?php


$i = 1;
$ki = 0;
$result=$db->conn->Execute($isql);
$registro = $pagina * 50;
while ($result && !$result->EOF) {
	if ($ki >= $registro and $ki < ($registro +50)) {
		$medio_recp = $result->fields['MREC_CODI'];
		if(!empty($medio_recp))
			$medio_recp = $db->conn->Execute("select mrec_desc from MEDIO_RECEPCION where  mrec_codi = $medio_recp ");

		$RADI_NUME_RADI = $result->fields['RADI_NUME_RADI'];
		$HID_RADI_PATH = $result->fields['RADI_PATH'];
		$radi_fech_radi = $result->fields['radi_fech_radi'];
		$SGD_TPR_DESCRIP = $result->fields['SGD_TPR_DESCRIP'];
		$MREC_DESC = $medio_recp->fields['MREC_DESC'];
		$DEPE_NOMB = $result->fields['DEPE_NOMB'];
		$RADI_NUME_SALIDA = $result->fields['RADI_NUME_SALIDA'];
		$contestado = $res_usua->fields["RADICADO"];
		if (!empty($result->fields['RADI_NUME_SALIDA']))
			$ESTADO = "Contestado";
		else
			$ESTADO = "En tramite";


		//***********************************************
		$edoDev = 0;
		if ($data == "")
			$data = "NULL";
		if ($i == 1) {
			$formato = "listado2";
			$i = 2;
		} else {
			$formato = "listado1";
			$i = 1;
		}
		if (!$HID_RADI_PATH)$enlace = $RADI_NUME_RADI;
		else $enlace = '<a href=../bodega/tmp/'.$HID_RADI_PATH.'>'.$RADI_NUME_RADI.'</a>';
?>
			<tr class='<?=$formato?>'>
				<td class='<?=$leido ?>' align="center" width="8%"><?echo $enlace;?></td>
				<td class='<?=$leido ?>' align="center" width="7%"><?echo $RADI_NUME_SALIDA;?>
				</a></td>
				<td class='<?=$leido ?>' align="center" width="13%"><?=$radi_fech_radi ?><a
					href='../verradicado.php?verrad=<?=$RADI_NUME_RADI ?>&<?=session_name()."=".session_id()."&krd=$krd"?> '>
					[--Ver--]</a></td>
				<td class='<?=$leido ?>' width="22%"><?=$SGD_TPR_DESCRIP ?></td>
				<td class='<?=$leido ?>' width="12%">&nbsp; <?=$MREC_DESC?></td>
				<td class='<?=$leido ?>' width="38%">&nbsp; <?=$DEPE_NOMB?></td>
				<td class='<?=$leido ?>' width="38%">&nbsp; <?=$ESTADO?></td>

			</tr>
			<?php

	}
	$ki = $ki +1;
	$result->MoveNext();
}
?>
		</table>
		</TD>
	</tr>
</TABLE>
</form>
