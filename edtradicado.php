<?php
session_start();
?>
<html>
<head>
<title>Buscar Radicado</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="estilos_totales.css" type="text/css">
</head>

<body>
<center></P>
  <form action="radicacion/NEW.php?sid_j=<?=$sid_j?>&<?=session_name()."=".session_id()?>" method="post" name="FrmBuscar">
    <table width="70%" border="0" >
  <tr class="tencabezado"> 
        <td width="25%" height="49">Numero de Radicado</td>
    <td width="55%"><center></center><input type=text name=nurad class=ecajas>
	 <input type=submit name=Buscar Value="Buscar Radicado" class=ebuttons2> 
	 <?php
	 if($drd){$drde=md5($drd);}
	 echo "<INPUT TYPE=hidden name=drde value=$drde>
	 <INPUT TYPE=hidden name=krd value=$krd>
	 <INPUT TYPE=hidden name=depende value=$depende>
	 <INPUT TYPE=hidden name=contra value=$contra>";
	 
	 ?>
	 </td>
  </tr>
</table>
</form>
</center>
</body>
</html>
