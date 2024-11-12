<?php
session_start();
if($_POST['EnviarFax']){
    if(!isset($ruta_raiz))
        $ruta_raiz='../../..';
    define('RUTA_RAIZ',$ruta_raiz);
    $dbTmp=$db;
    require(RUTA_RAIZ."/config.php");
    define('ADODB_ASSOC_CASE', 1);
    require "adodb/adodb.inc.php";
    $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
    $dsn = $driver."://".$usuario.":".$contrasena."@".$servidor."/".$db;
    $conn = NewADOConnection($dsn); 
    $db=$dbTmp;
    if(isset($_POST['hidRadsFax']))
    {   include(RUTA_RAIZ."/webServices/appClient/modulosCliente/FaxServerCliente.php"); 
        include_once(RUTA_RAIZ . "/include/tx/Historico.php");
        include_once(RUTA_RAIZ ."/include/class/DatoContacto.php");
        $clienteFax= new FaxServerCliente($conn,RUTA_RAIZ);
        $tableResult="
        <table class='borde_tab' width='100%' cellspacing='5'>
<tr><td class='titulos5' align='center' valign='middle'><B>Env&iacute;o de Documentos por FAX</B></td></tr>
</table>    
<table border=0 width=100% class=borde_tab cellspacing='5'>
        <tr class='titulos2'>
	<td >Estado</td>
	<td >Radicado</td>
        <td >Copia</td>
	<td >Destinatario</td>
        <td >Observac&ioacute;n</td>
</tr>";
        $rads = explode(",",$_POST['hidRadsFax'] );
        $objHist= new Historico($db);
        $objDatosDest=new DatoContacto($conn);
        $cont=0;
        foreach ($rads as $i =>$val){
            
            $resultado['band']=true;
            $vecRad=explode("-",$val);
            $sql="select * from radicado where radi_nume_radi=".$vecRad[0];
            $rs=$conn->Execute($sql);
            if($vecRad[1]>1){
                $copia=substr($vecRad[1],2,2);
            }
            else{
                $copia="";
            }
            if($cont%2==0){
                $estilo="class=listado2";
            }else{
                $estilo="class=listado1";
            }
            if($rs && !$rs->EOF)
            { 
                    $vecDatosDestino=$objDatosDest->obtieneDatosReales(false, $vecRad[0],$vecRad[1]);
                    if(is_file($ruta_raiz."/bodega/".$rs->fields['RADI_PATH'])){
                        $ext=substr($rs->fields['RADI_PATH'], stripos($rs->fields['RADI_PATH'], ".")+1);
                        if(strtolower($ext)=="pdf" or strtolower($ext)=="tif" or strtolower($ext)=="tiff"){
                            $strFile = file_get_contents($ruta_raiz."/bodega/".$rs->fields['RADI_PATH']);
                            $clienteFax->parametrosSendFax['nombreArchivo']=$vecRad[0].$vecRad[1].substr($rs->fields['RADI_PATH'], strripos($rs->fields['RADI_PATH'], "/")+1);
                            $clienteFax->parametrosSendFax['ArchivoSerializado'] = base64_encode($strFile);
                            $clienteFax->parametrosSendFax['NFaxDestino']=$_POST['txtNFax'];
                            $clienteFax->parametrosSendFax['NombreDestinatario']=$vecDatosDestino[0]['NOMBRE']." ".$vecDatosDestino[0]['APELLIDO'];
                            $clienteFax->parametrosSendFax['NFaxRemitente']= $_SESSION["usua_ext"]? $_SESSION["usua_ext"]: "3815001";
                            $clienteFax->parametrosSendFax['NombreRemitente']=$_SESSION["usua_nomb"];
                            $clienteFax->parametrosSendFax['Asunto']=$vecRad[0];
                            $resultado=$clienteFax->SendFax(); 
                        }
                        else{
                        
                            $resultado['band']=false;
                            $resultado['msg'][]="Debe asociar imagen del documento en un archivo PDF o TIF";
                       
                        }
                    }
                    else{
                        
                        $resultado['band']=false;
                        $resultado['msg'][]="No existe en orfeo el archivo a enviar o no se ha asociado la imagen del radicado!";
                       
                    }
                    
                    if($resultado['band']){
                        $val=strpos($resultado["msg"]['SendFaxResult'],"@");
                        $idFAX=substr($resultado["msg"]["SendFaxResult"], strpos($resultado["msg"]['SendFaxResult'],"@")+1);
                        if($idFAX && $val>0){
                            $tableResult.="<tr $estilo>
                            <td>OK</td>
                            <td>$vecRad[0]</td>
                            <td>$copia</td>
                            <td>".$vecDatosDestino[0]['NOMBRE']." ".$vecDatosDestino[0]['APELLIDO']."</td>
                            <td>Recibido por Fax Server pendiente envio definitivo</td>
                            </tr>";
                            $rad[0]=$vecRad[0];
                             $objDatosDest->actualizaDatosDirEnvioFax(false, $vecRad[0], $vecRad[1],2, 0, $_POST['txtNFax'], $idFAX, 0);
                             $objHist->insertarHistorico($rad, $_SESSION["dependencia"], $_SESSION["codusuario"], $_SESSION["dependencia"],$_SESSION["codusuario"], "El servidor de FAX ha recibido la petición de envio de Fax al número marcado:".$_POST['txtNFax']." y destinatario:".$vecDatosDestino[0]['NOMBRE']." ".$vecDatosDestino[0]['APELLIDO'].", esta solicitud se encuentra en espera de envío definitivo" , 87);
                        }
                         else{
                              $tableResult.="<tr $estilo>
                                <td><span class='titulosError'>Falla</td>
                                <td>$vecRad[0]</td>
                                <td>$copia</td>
                                <td>".$vecDatosDestino[0]['NOMBRE']." ".$vecDatosDestino[0]['APELLIDO']."</td>
                                 <td>".implode("<br>",$resultado['msg'])."</td>
                                </tr>";
                         }

                    }
                    else{
                        $tableResult.="<tr $estilo>
                        <td><span class='titulosError'>Falla</td>
                        <td>$vecRad[0]</td>
                        <td>$copia</td>
                        <td>".$vecDatosDestino[0]['NOMBRE']." ".$vecDatosDestino[0]['APELLIDO']."</td>
                         <td>".implode("<br>",$resultado['msg'])."</td>
                        </tr>";

                    }    
            }
            unset($resultado);
            $cont++;
        }
        $tableResult.="</table>";
    }
    else{

        $msg="Aplicativo desconocido!";
    }
    
    $prueba= $clienteFax->obtenerDatosView("vFaxMsgs",101);
?>
<html>
<head>
<title>Orfeo.  Envio de Documentos por FAX</title>
<link href="<?php echo RUTA_RAIZ?>/estilos/orfeo.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
    die($tableResult);
?>
</body>
</html>
<?php
}
else{
?>
<div>
    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post"/>
    <table width="100%" >
        <tr  >
            <td class="titulos2" align="right"><span class="porExcluir">N&uacute;mero de Fax</span><input type="text" id="txtNFax" name="txtNFax" >
           <input value="Enviar Documentos Fax" name="EnviarFax" id="EnviarFax" class="botones_largo" type="submit" onclick=" return enviaFax();"></td>
        </tr>
    </table>

</div>
<?php }?>