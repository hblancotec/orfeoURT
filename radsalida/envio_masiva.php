<?php
//Programa que genera el formulario de seleccion de opciones para el envio de un grupo de documentos de radicacion masiva
    session_start();
    $ruta_raiz = "..";
    if (!isset($_SESSION['dependencia']))	include "../rec_session.php";		

    extract($_POST, EXTR_SKIP);
    extract($_GET, EXTR_SKIP);
    extract($_SESSION, EXTR_SKIP);
    
    require_once("$ruta_raiz/include/db/ConnectionHandler.php");
    include_once "$ruta_raiz/class_control/GrupoMasiva.php"; 
    require_once("$ruta_raiz/include/combos.php");
    require_once("$ruta_raiz/class_control/Dependencia.php");

    if (!$db)
        $db = new ConnectionHandler($ruta_raiz);
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $grupoMas = new GrupoMasiva($db);
    $objDepe =  new Dependencia($db);
    $objDepe->Dependencia_codigo($dep_sel);
    $codTerrEnvio = $objDepe->getDepe_codi_territorial();
    $terrEnvio = $objDepe->dependenciaArr($codTerrEnvio);
    $grupoMas->obtenerGrupo($dep_sel,$radGrupo,'');
    //var arreglo que  almacena los radicados inicial y final del grupo 
    $radsLimite = $grupoMas->getRadsLimite();
    //var arreglo que  almacena el numero de radicados nacionales y locales
    $numRadicados=$grupoMas->getNumNacionalesLocales($terrEnvio['dpto_codi'], $terrEnvio['muni_codi']);
?>
<html>
<head>
<title>Untitled Document</title>
<link href="../estilos/orfeo.css" rel="stylesheet" type="text/css">
<script src="../js/formchek.js"></script>
<script>
function back1() {
    history.go(-1);
}
<?php
$grupoMas->javascriptCalcularPrecio();
?>
function enviar() {
    sw=0;
    if (document.form1.empresa_envio.value!='null' && isInteger(document.form1.envio_peso.value)) {
        sw=1;
    } else {
        alert ('Debe suministrar los datos de la empresa de envio y el peso de los documentos');
        return;
    }

    if (document.form1.observaciones.value.length>1 && document.form1.planilla.value.length>1) {
        sw=1;
    } else {
        alert ('Debe suministrar las observaciones y el numero de planilla');
        return;
    }
    document.form1.submit();	
}
</script>
<style type="text/css">
<!--
.style1 {color: #CC0000}
-->
</style>
</head>
<body>
<span class=etexto>
<center>
</center>
</span> 
<form name="form1" method="post" action="envio_masiva_registro.php?<?=session_name()."=".session_id()."&krd=$krd" ?>" class="borde_tab" >
<table width="100%" class="borde_tab">
<tr>
    <td class="titulos5" align="center"><b>ENVIO DE DOCUMENTOS - RADICACION MASIVA</b></td>
</tr>
</table>
<table border="0" width="50%" class="borde_tab" cellspacing="5" align="center">
<tr class="titulos2" align="center" > 
    <td></td>
</tr>
</table>
  <div align="center">
    <table border=0 width=50% class=borde_tab cellspacing="5" >
      <!--DWLayoutTable-->
      <tr class=titulos2 align="center" > 
        <td width="26%" >
          GRUPO</td>
        <td width="35%" >
          EMPRESA DE ENVIO</td>
        <td width="13%" >
          PESO(Gr) C/U </td>
        <td width="26%" >
          U.MEDIDA</td>
  </tr>
<?php
    //echo "-->".$valor_unit;
?>
  <tr class="listado2"> 
        <td height="26" align="center" width="26%"> 
          <?php echo ($radsLimite[0]."<br>".$radsLimite[1]);?>
          <input type="hidden" name="rangoini" value="<?=$radsLimite[0]?>" >
          <input type="hidden" name="rangofin" value="<?=$radsLimite[1]?>" >
        </td>
        <td height="26" align="center" width="35%"> 
          <select name="empresa_envio" id="empresa_envio" class="select" onClick=" if (this.value!='null'&& envio_peso.value.length>1) calcular_precio('empresa_envio','envio_peso','valor_gr','local','nacional');" >
         <option selected value="null">--- empresas de env&iacute;o ---</option>
		<?php
 			$a = new combo($db);
			$s = "select SGD_FENV_CODIGO as COD,SGD_FENV_DESCRIP as DES FROM SGD_FENV_FRMENVIO ";
			$r = "COD"; 
			$t = "DES";
			$v = $estado;
			$sim = 0; 
      $a->conectar($s,$r,$t,$v,$sim,0);	
   ?>
      </select>
    </td>
        <td width="13%"> 
          <input type="text" class="tex_area" name="envio_peso" id="envio_peso"  size="6" onChange="calcular_precio('empresa_envio','envio_peso','valor_gr','local','nacional');">
    </td>
        <td width="26%"> 
          <input type=text name=valor_gr id=valor_gr class=tex_area   size=30 disabled>
    </td>
  </tr>
</table>
<br>
<tr bgcolor class="#cccccc" >
  <td class="#cccccc">&nbsp;</td>
  <td class="#cccccc">&nbsp; </td>
</tr>
<table border=0 width=33% class=borde_tab  >
  <!--DWLayoutTable-->
  <tr class="titulos2" align="center"  > 
    <td valign="top" width="12%" >DESTINO</td>
    <td valign="top" width="17%" >DOCUMENTOS</td>
    <td valign="top" width="34%" >VALOR 
      C/U</td>
    <td valign="top" width="28%" >VALOR 
      TOTAL</td>
    <td valign="top" rowspan="4" width="9%" > 
      <input type=button class="botones" name=Calcular_button id=Calcular_button value=Calcular onClick='calcular_precio('empresa_envio','envio_peso','valor_gr','local','nacional');'>
    </td>
  </tr>
  <tr > 
    <td height="21" align="center" valign="top" width="12%" class="titulos5">Local </td>
    <td align="center" valign="top" width="17%" class='listado2'> 
      <center><?=$numRadicados["local"]?></center>
      <input type="hidden" name="local" value="<?=$numRadicados["local"]?>" id=local>
    </td>
    <td valign="midle" width="34%" class='listado2'> 
      <input type=text class='tex_area' name=valor_unit_local id=valor_unit_local  readonly     >
    </Td>
    <td width="28%" class='listado2'> 
      <input type=text class='tex_area' name=valor_total_local id=valor_total_local  readonly     >
    </Td>
  </tr>
  <tr > 
    <td height="21" align="center" valign="top" width="12%" class="titulos5">Nacional </td>
    <td align="center" valign="top" width="17%" class='listado2'> 
      <center><?=$numRadicados["nacional"]?></center>
      <input type="hidden" name="nacional" value="<?=$numRadicados["nacional"]?>" id=nacional >
    </td>
    <td valign="midle" width="34%" class='listado2'> 
      <input type=text class='tex_area'  name=valor_unit_nacional id=valor_unit_nacional  readonly     >
    </Td>
    <td width="28%" class='listado2'> 
      <input type=text class='tex_area' name=valor_total_nacional id=valor_total_nacional  readonly    >
    </Td>
  </tr>
  <tr class=listado2>
    <td width="12%"></td>
    <td width="17%">
          <input type="hidden" name="primRadNac" value="<?=$grupoMas->getPrimerRadicadoNacional() ?>" id=nacional >
          <input type="hidden" name="primRadLoc" value="<?=$grupoMas->getPrimerRadicadoLocal() ?>" id=nacional >
          <input type="hidden" name="grupo" value="<?=$radGrupo ?>" id=nacional >
          <input type="hidden" name="renv_codigo" value="<?=$grupoMas->getSgd_renv_codigo()?>" id=nacional >
        </td>
    <td width="34%">&nbsp; </Td>
    <td width="28%"> 
      <input type="text" class='tex_area' name=valor_total id=valor_total  readonly    >
  </td>
</table>
<br>
<table class="borde_tab" width="33%" border="0" align="center">
 <tr>
  <td>
  </td>
 </tr>
</table>
  <table class="borde_tab" width="33%" border="0">
    <tbody> 
    <tr class=titulos5 bgcolor=""> 
      <td class="#cccccc" width="29%">Observaciones o desc. anexos</td>
      <td class="#cccccc" width="71%"> 
        <input id="observaciones" name="observaciones" type="text" size="56" class='tex_area' >
      </td>
    </tr>
    <tr class=titulos5 bgcolor=""> 
      <td class="#cccccc" width="29%">No. De Planilla</td>
      <td class="#cccccc" width="71%"> 
        <input value="" id="planilla" name="planilla" type="text" class='tex_area' >
      </td>
    </tr>
    </tbody> 
  </table>
</div>
<br>
<p align="center"> 
    <input name="reg_envio" type="button" class="botones_largo" value='GENERAR REGISTRO DE ENVIO' onClick='enviar()' >
<span class="etexto">
</span>
</p>
</form>
<span class="vinculos">
    <center>
        <a href=javascript:back1()>Regresar a Listado</a>
    </center>
</span>
</body>
</html> 
