<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "../sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	extract($_SESSION);
}
else {
	$krd = $_REQUEST['krd'];
}
if ($_SESSION['usua_admin_sistema'] != 1){
	die(include "../sinpermiso.php");
	exit;
}

    $krdOld = $krd;
	$carpetaOld = $carpeta;
	$tipoCarpOld = $tipo_carp;

	error_reporting(0);	
	if(!$krd) $krd=$krdOsld;
	if(!$_SESSION['dependencia'] or !$_SESSION['tpDepeRad']) include "$ruta_raiz/rec_session.php";
	include_once "$ruta_raiz/include/db/ConnectionHandler.php";
	$db = new ConnectionHandler($ruta_raiz);	 
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	$nomcarpetaOLD = $nomcarpeta;

		if (!$carpeta) 
		{
		  $carpeta = "0";
		  $nomcarpeta = "Entrada";
		}
?>
<table BORDER=0  cellpad=2 cellspacing='0' WIDTH=98% class='t_bordeGris' valign='top' align='center' >
  <tr>
    <td width='35%' >
      <table width='100%' border='0' cellspacing='1' cellpadding='0'>
        <tr> 
          <td height="20" bgcolor="377584"><div align="left" class="titulo1">LISTADO DE: </div></td>
        </tr>
		<tr class="info">
          <td height="20"><?=$nomcarpeta?></td>
        </tr>
      </table>
    </td>
     <td width='35%' >
      <table width='100%' border='0' cellspacing='1' cellpadding='0'>
        <tr> 
          <td height="20" bgcolor="377584"><div align="left" class="titulo1">USUARIO </div></td>
        </tr>
		<tr class="info">
          <td height="20" ><?=$usua_nomb?></td>
        </tr>
      </table>
    </td>
	<?php
    if (!$swBusqDep)  {
    ?>
 	<td width="33%">
	    <table width='100%' border='0' cellspacing='1' cellpadding='0'>
        <tr> 
          <td height="20" bgcolor="377584"><div align="left" class="titulo1">DEPENDENCIA </div></td>
        </tr>
		<tr class="info">
          <td height="20" ><?=$depe_nomb?></td>
        </tr>
      </table>
     </td>
	<?php
    } else {
    ?>
	<td width="35%">
      <table width="100%" border="0" cellspacing="5" cellpadding="0">
     <tr class="info" height="20">
    	<td bgcolor="377584"  ><div align="left" class="titulo1">DEPENDENCIA</div></td>
        </tr>
		<tr>
		  <form name='formboton' action='<?=$pagina_actual?>?<?=session_name()."=".session_id()."&krd=$krd" ?>&estado_sal=<?=$_GET['estado_sal']?>&estado_sal_max=<?=$estado_sal_max?>&pagina_sig=<?=$pagina_sig?>&dep_sel=<?=$dep_sel?>&nomcarpeta=<?=$_GET['nomcarpeta']?>' method='post'>	
			<td height="1">
<?php
			include_once "$ruta_raiz/include/query/envios/queryPaencabeza.php";			
 			$sqlConcat = $db->conn->Concat($db->conn->substr."($conversion,1,5) ", "'-'",$db->conn->substr."(depe_nomb,1,30) ");
			$sql = "select $sqlConcat ,depe_codi from dependencia order by depe_codi";
			$rsDep = $db->conn->Execute($sql);
			if(!$depeBuscada) $depeBuscada=$_SESSION['dependencia'];
			print $rsDep->GetMenu2("dep_sel",$dep_sel,false, false, 0," onChange='submit();' class='select'");
?>			
		</td>
 		  </form>
		</tr>
      </table>
    </td>

	<?php
    } 
    ?>

  </tr>
</table>