<?php
 	session_start(); 
    if (empty($_SESSION["usua_perm_despla"])){
        die;
    } 
	$ruta_raiz = ".."; 
	include_once("$ruta_raiz/include/db/ConnectionHandler.php");
	$db = new ConnectionHandler("$ruta_raiz");
	include_once "$ruta_raiz/include/tx/Historico.php";
	$objHistorico= new Historico($db);
	$arrayRad = array();
	$arrayRad[]=$_GET['verrad'].$_POST['verrad'];
    $inputdespla = $_POST['inputdespla'];

	$fecha_hoy = Date("Y-m-d");
	$sqlFechaHoy=$db->conn->DBDate($fecha_hoy);

	$encabezadol = $_SERVER['PHP_SELF']."?".session_name()."=".session_id()."&krd=$krd&verrad=$verrad&dependencia=$dependencia&codusuario=$codusuario"; 

     if ($insertar_registro && $verrad !='' ){

        $isqlM = "select RADI_DESPLA 
                  FROM RADICADO
                  where RADI_NUME_RADI = $verrad";

        $rsM=$db->conn->Execute($isqlM);
        
        //Actualiza el radi_despla Tabla Radicados
        $recordSet["RADI_DESPLA"]       = $inputdespla;
        $recordWhere["RADI_NUME_RADI"]  = $verrad;	  
        $radi_desplaActu = empty($inputdespla)? 'No' : 'Si';
        $radi_desplaAnte = empty($rsM->fields[0])? 'No' : 'Si';

        $ok = $db->update("RADICADO", $recordSet,$recordWhere);

        if($ok){
            $mensaje = "<hr><center><b><span class=info>Se cambio el estado</span></center></b></hr>";
            $codusdp = str_pad($dependencia, 3, "0", STR_PAD_LEFT).str_pad($codusuario, 3, "0", STR_PAD_LEFT);	
            $observa = "Se cambio el estado desplazado en el radicado de $radi_desplaAnte a $radi_desplaActu";
            if($radi_desplaActu != $radi_desplaAnte)
            $objHistorico->insertarHistorico($arrayRad,$dependencia ,$codusuario, $dependencia,$codusuario, $observa, 80);
        }else{
            $mensaje = "<hr><center><b><span class='alarmas'>    
                No se encontro el radicado, por favor 
                verifique e intente de nuevo</span></center></b></hr>";  
        }
    }
?>
<html>
<head>
    <title>Vinculacion Documento</title>
    <link href="../estilos/orfeo.css" rel="stylesheet" type="text/css">
    <script>
        function regresar(){   	
            document.VincDocu.submit();
        }
    </script>
</head>
<body bgcolor="#FFFFFF">
    <form method="post" action="<?=$encabezadol?>" name="VincDocu"> 
        <table border=0 width=70% align="center" class="borde_tab" cellspacing="0">
            <tr align="center" class="titulos2">
                <td height="15" colspan="2" class="titulos2">Condici&oacute;n de desplazado<br/>Radicado No.<?=$verrad?></td>
            </tr>
            <tr>
                <td class="titulos5" >Cambiar estado </td>
                <td class=listado5 >
                    <select  name='inputdespla'  class='select'>
                        <?php 
                        if($inputdespla==0){$datosel=" selected ";}else {$datosel=" ";}
                            echo "<option value='0' $datosel><font>- No -</font></option>";
                        if($inputdespla==1){$datosel=" selected ";}else {$datosel=" ";}
                            echo "<option value='1' $datosel><font>- Si -</font></option>";
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="titulos5" >
                    <center><input name="insertar_registro" type=submit class="botones_funcion" value="Grabar Cambio "></center></TD>
                </td>
                <td>
                    <center><input type=button onclick="opener.regresar();window.close();" class="botones_funcion" value=" Cerrar "></center></TD>
                </td>
            </tr>
        </table>
    </form>
    <?=$mensaje?>
</body>
</html>
