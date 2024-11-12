<?php
    $krdOld = $krd;
	$carpetaOld = $_GET['carpeta'];
	$tipoCarpOld = $tipo_carp;
	session_start();
	error_reporting(7);	
	if(!$krd) $krd=$krdOsld;
	if(!$_SESSION['dependencia'] or !$_SESSION['tpDepeRad']) include_once("$ruta_raiz/rec_session.php");
	include_once "$ruta_raiz/include/db/ConnectionHandler.php";
	$db = new ConnectionHandler($ruta_raiz);	 
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	$nomcarpetaOLD = $nomcarpeta;
	if (!isset($_GET['carpeta']))
	{
	  $carpeta = "0";
	  $nomcarpeta = "Entrada";
	}
?>
<table BORDER=0  cellpad=2 cellspacing='0' WIDTH=100% class='borde_tab' valign='top' align='center' >
  <tr>
    <td width='35%' >
      <table width='100%' border='0' cellspacing='1' cellpadding='0'>
        <tr> 
          <td height="20" bgcolor="377584"><div align="left" class="titulo1">LISTADO DE: </div></td>
        </tr>
		<tr class="info">
          <td height="20"><?=$_GET['nomcarpeta']?></td>
        </tr>
      </table>
    </td>
     <td width='35%' >
      <table width='100%' border='0' cellspacing='1' cellpadding='0'>
        <tr> 
          <td height="20" bgcolor="377584"><div align="left" class="titulo1">USUARIO </div></td>
        </tr>
		<tr class="info">
          <td height="20" ><?=$_SESSION['usua_nomb']?></td>
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
          <td height="20" ><?=$_SESSION['depe_nomb']?></td>
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
		<form name=formboton action='<?=$pagina_actual?>?<?=session_name()."=".session_id()."&krd=$krd" ?>&estado_sal=<?=$estado_sal?>&estado_sal_max=<?=$estado_sal_max?>&pagina_sig=<?=$pagina_sig?>&dep_sel=<?=$dep_sel?>&nomcarpeta=<?=$nomcarpeta?>' method=post>		  		
		<td height="1">
		<?php
		include_once "$ruta_raiz/include/query/envios/queryPaencabeza.php";			
		$sqlConcat = $db->conn->Concat($db->conn->substr."($conversion,1,5) ", "'-'",$db->conn->substr."(depe_nomb,1,30) ");
		$sql = "select $sqlConcat ,depe_codi from dependencia 
						order by depe_codi";
		$rsDep = $db->conn->Execute($sql);
                if(!$depeBuscada) $depeBuscada=$dependencia;
		print $rsDep->GetMenu2("dep_sel","$dep_sel",false, false, 0," onChange='submit();' class='select'");
		
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