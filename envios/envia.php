<?php
    $krdOld = $krd;
    session_start();
    $ruta_raiz = "..";
    $carpetaOld = $carpeta;
    $tipoCarpOld = $tipo_carp;
    if(!$tipoCarpOld) $tipoCarpOld= $tipo_carpt;
    if(!$krd) $krd=$krdOld;

    include_once "$ruta_raiz/include/db/ConnectionHandler.php";
    include_once("$ruta_raiz/include/class/DatoContacto.php");
    if (!$db)	$db = new ConnectionHandler($ruta_raiz);
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    if (!$_SESSION['dependencia'])	include_once "../rec_session.php";
    
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION);
?>
<html>
<head>
<title>Untitled Document</title>
<link href="../estilos/orfeo.css" rel="stylesheet" type="text/css">
<?php
if (! $radicados) {
    $radicados = implode('*,*', array_keys($checkValue));
    $whereFiltro = ' and a.anex_codigo in(*' . $radicados . '*)';
}
$procradi = $radicados;
?>
<script>
    function back1() {
        history.go(-1);
    }

    function generar_envio() {
        if (document.forma.elements['valor_unit'].value == '') {
            alert('Seleccione Empresa de Envio Y digite el peso del mismo');
            return false;
        }
	
	if (document.forma.elements['codpostal_us'].value == '') {
            alert('Digite c\xf3digo postal.');
            return false;
        }
    }
</script>
</head>
<body>
<span class="etexto">
<center>
<a class="vinculos" href='../envios/cuerpoEnvioNormal.php?<?=session_name()."=".session_id()."&krd=$krd&fecha_h=$fechah&dep_sel=$dep_sel&estado_sal=$estado_sal&estado_sal_max=$estado_sal_max&nomcarpeta=$nomcarpeta"?>'>Devolver a Listado</a>
</center></span>
<center>
<table width="100%" class="borde_tab">
    <tr class="titulos2">
        <td align="center">ENVIO DE DOCUMENTOS</td>
    </tr>
</table>
</center>
<form name='forma' action='envia.php?<?=session_name()."=".session_id()."&krd=$krd&fecha_h=$fechah&dep_sel=$dep_sel&whereFiltro=$whereFiltro&estado_sal=$estado_sal&estado_sal_max=$estado_sal_max&no_planilla=$no_planilla&codigo_envio=$codigo_envio&verrad_sal=$verrad_sal&nomcarpeta=$nomcarpeta"?>' method="post">
<input type='hidden' name='radicados' value='<?= $radicados ?>'>
<?php
    $whereFiltro = str_replace("*","'", $whereFiltro);
    include_once("$ruta_raiz/include/query/envios/queryEnvia.php");
    if(!isset($_POST["reg_envio"])) {
?>
<table border="0" width="100%" class="borde_tab">
	<!--DWLayoutTable-->
	<tr class="titulos2">
		<td >Empresa De envio</td>
		<td >Peso(Gr)</td>
		<td >U.Medida</td>
		<td colspan="2" >Valor Total C/U</td>
	</tr>
	<tr class=timparr>
	<td height="26" align="center"><font size="2"><B>
<?php
    $rsEnv = $db->conn->Execute($sql);
    echo $rsEnv->GetMenu2("empresa_envio",
                            0,
                            "0:&lt;&lt; Seleccione  &gt;&gt;",
                            false,
                            0,
                            ' id="empresa_envio" class="select" onChange="calcular_precio();"');
?>
   </td>
   <td><input type='text' name='envio_peso' id='envio_peso' value='<?=$envio_peso?>' size="6" onChange="calcular_precio();" class="tex_area"></td>
		<TD><input type="text" name="valor_gr" id="valor_gr"  value='<?=$valor_gr?>' size="30" disabled class="tex_area"> </td>
		<td align="center"> <input type="text" name="valor_unit" id="valor_unit"  readonly   value="<?=$valor_unit?>" class="tex_area"> </td>
		<td> <input type="button" name="Calcular_button" id="Calcular_button" Value="Calcular" onClick="calcular_precio();" class="tex_area"> </td>
    </tr>
  </table>
<?php
    }
?>
<table border="4" width="100%" class="borde_tab">
	<!--DWLayoutTable-->
	<tr class='titulos2' >
		<td valign="top" >Radicado</td>
		<td valign="top" >Radicado Padre</td>
		<td valign="top" >Destinatario</td>
		<td valign="top" >Direcci&oacute;n</td>
		<td valign="top" >Tel&eacute;fono</td>
		<td valign="top" >C&oacute;d. Postal</td>
		<td valign="top" >Municipio</td>
		<td valign="top" >Depto</td>
		<td valign="top" >Pa&iacute;s</td>
	</tr>
<?php
    include "$ruta_raiz/config.php";
    require_once("$ruta_raiz/class_control/ControlAplIntegrada.php");
    include_once "$ruta_raiz/include/db/ConnectionHandler.php";
    $isql = "SELECT a.SGD_DIR_TIPO, $RADI_NUME_SALIDA as RADI_NUME_SALIDA,
                    $radi_nume_deri AS RADI_NUME_DERI,
                    b.RA_ASUN,
		    dir.SGD_DIR_NOMREMDES, dir.SGD_DIR_CODPOSTAL 
                FROM ANEXOS a,RADICADO b, SGD_DIR_DRECCIONES dir
                WHERE a.radi_nume_salida = b.radi_nume_radi $whereFiltro AND
		      a.radi_nume_salida = dir.radi_nume_radi and
		      a.sgd_dir_tipo = dir.sgd_dir_tipo and		      
                      anex_estado=3 AND
                       a.sgd_dir_tipo <> 7 $comb_salida
                ORDER BY a.SGD_DIR_TIPO ";
    $db = new ConnectionHandler("$ruta_raiz");
    $db->conn->BeginTrans();
    if (isset($_POST["reg_envio"]))  {
        $objCtrlAplInt = new ControlAplIntegrada($db);
    }
    if (!defined('ADODB_FETCH_ASSOC'))	{	define('ADODB_FETCH_ASSOC',2);	}
    $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
    $ADODB_COUNTRECS = true;
    $rsEnviar = $db->conn->Execute($isql);
    $ADODB_COUNTRECS = false;
    $igual_destino = "si";
    $tmp  = explode('-',$_SESSION['cod_local']);
    $tmp_idcl = $tmp[0];
    $tmp_idpl = $tmp[1];
    $tmp_iddl = $tmp_idpl.'-'.$tmp[2]*1;
    $tmp_idml = $tmp_iddl.'-'.$tmp[3]*1;
    unset($tmp);
    if ($rsEnviar && !$rsEnviar->EOF  ) {
    	$pCodDepAnt = "";
        $pCodMunAnt = "";
        if (!isset($_POST["reg_envio"])) {
        	$cnt_idcl = 0;
            $cnt_idcc = 0;
            $cnt_idpl = 0;
            $cnt_idpc = 0;
            $cnt_idml = 0;
            $cnt_idmc = 0;
            while (!$rsEnviar->EOF) {
	    	$codpostal_us	= $rsEnviar->fields["SGD_DIR_CODPOSTAL"];
                $verrad_sal     = $rsEnviar->fields["RADI_NUME_SALIDA"];   //OK
                $verrad         = $rsEnviar->fields["RADI_NUME_SALIDA"];   //OK
                $verrad_padre   = $rsEnviar->fields["RADI_NUME_DERI"];
                $sgd_dir_tipo   = $rsEnviar->fields["SGD_DIR_TIPO"];
                $rem_destino    = $rsEnviar->fields["SGD_DIR_TIPO"];
                $anex_radi_nume = $rsEnviar->fields["RADI_NUME_SALIDA"];
		$nombreDestinatario = $rsEnviar->fields["SGD_DIR_NOMREMDES"];
                $dep_radicado   = substr($verrad_sal,4,3);
                $ano_radicado   = substr($verrad_sal,0,4);
                $carp_codi      = substr($dep_radicado,0,2);
                $radi_path_sal = "/$ano_radicado/$dep_radicado/docs/$ref_pdf";

                if (substr($rem_destino,0,1)=="7") $anex_radi_nume = $verrad_sal;
                $nurad = $anex_radi_nume;
                $verrad = $anex_radi_nume;

                $ruta_raiz= "..";
                include "../ver_datosrad.php";
		$telefono_us = (empty($telefono_us)) ? $telUsX : $telefono_us;
                if ($radicadopadre)	$radicado = $radicadopadre;
                if ($nurad)	$radicado = $nurad;

                include "../clasesComunes/datosDest.php";

                $dat = new DATOSDEST($db,$radicado,$espcodi,$sgd_dir_tipo,$rem_destino);
                $pCodDep = $dat->codep_us;
                $pCodMun = $dat->muni_us;
                $pNombre = $dat->nombre_us;
                $pPriApe = $dat->prim_apel_us;
                $pSegApe = $dat->seg_apel_us;
                $nombre_us    = substr($pNombre . " " . $pPriApe . " " . $pSegApe, 0 ,29);
		if(trim($rsEnviar->fields["SGD_DIR_NOMREMDES"])) $nombre_us = $rsEnviar->fields["SGD_DIR_NOMREMDES"];
                $direccion_us = $dat->direccion_us;
                if ($pCodDepAnt == "")   $pCodDepAnt = $pCodDep;
                if ($pCodMunAnt == "")   $pCodMunAnt = $pCodMun;
                //	Validacion de local(local/nacional)/intenacional(grupo1/grupo2)
                if ($dat->idcont == $tmp_idcl)	//Comparativo desde el 1er continente con el continente local
                {	$cnt_idcl += 1;
                    if ($dat->idpais == $tmp_idpl)	//Comparativo desde el 1er pais con el continente local
                    {	$cnt_idpl += 1;
                        if ($dat->muni_us == $tmp_idml)	//Comparativo desde el 1er mcpio con el continente local
                        {	$cnt_idml += 1;
                        }
                        else	$cnt_idmc += 1;
                    }
                    else	$cnt_idpc += 1;
                }
                else	$cnt_idcc += 1;

                if(!$rem_destino) $rem_destino =1;
                $sgd_dir_tipo = 1;
                echo "<input type='hidden' name='$espcodi' value='$espcodi'>";

                $ruta_raiz = "..";
                include "../jh_class/funciones_sgd.php";
                $a = new LOCALIZACION($pCodDep,$pCodMun,$db);
                $departamento_us = $a->departamento;
                $destino         = $a->municipio;
		
                $pais_us         = $a->GET_NOMBRE_PAIS($dat->idpais,$db);
                $dir_codigo      = $dat->documento_us;
                include "../envios/listaEnvio.php";
                $cantidadDestinos++;
                $rsEnviar->MoveNext();
            }
            if ($cnt_idcl > 0 && $cnt_idcc >0)
                $igual_destino = "no";
            else
            {	($cnt_idcl > 0) ? $masiva = 3 : $masiva = 4;
                //Si contador continente local > 0  ==> masiva = 3 (Grupo 1)  sino masiva = 4 (Grupo 2)
                if ($cnt_idpl > 0 && $cnt_idpc >0)
                    $igual_destino = "no";
                else
                {	if ($cnt_idpl > 0)	$masiva = 2;
                    //Si contador paises local > 0  ==> masiva = 2 (Envios nacionales)
                    if ($cnt_idml > 0 && $cnt_idmc >0)
                        $igual_destino = "no";
                    else
                    {	if ($cnt_idml > 0)	$masiva = 1;
                        //Si contador municipio local > 0  ==> masiva = 1 (Envios locales)
            }	}	}
        }	// no reg_envio
        if ($igual_destino == "si") {
        	if (!isset($_POST["reg_envio"])) {
?>
	<tr>
	<td colspan="9">
	<table class="borde_tab" width="100%" border="3">
	<tr>
		<td height="21" valign="top" align="center">
			<font size=2>
			<input name="reg_envio" type="submit" value="GENERAR REGISTRO DE ENVIO DE DOCUMENTO" id="GENERAR REGISTRO DE ENVIO DE DOCUMENTO" onClick="return generar_envio();" class="botones_largo">
			<input name="masiva" value="<?=$masiva?>" type="hidden">
			</font>
		</td>
	</tr>
	</table>
	</td>
	</tr>
<?php
		} else {
            if (!$k) {
                $rsEnviar->MoveFirst();
				while (!$rsEnviar->EOF) {
                    $verrad_sal     = $rsEnviar->fields["RADI_NUME_SALIDA"];
					$verrad_padre   = $rsEnviar->fields["RADI_NUME_DERI"];
					$rem_destino    = $rsEnviar->fields["SGD_DIR_TIPO"];
					$campos["P_RAD_E"]=$verrad_sal;
					$estQueryAdd=$objCtrlAplInt->queryAdds($verrad_sal,$campos,$MODULO_ENVIOS);

					if ($estQueryAdd==0)
					{	$db->conn->RollbackTrans();
						die;
					}

					if(!$rem_destino) $rem_destino =1;
					if (!trim($rem_destino))
						$isql_w = " sgd_dir_tipo is null ";
					else	$isql_w = " sgd_dir_tipo='$rem_destino' ";
					$isql = "update ANEXOS set ANEX_ESTADO=4,
                                    ANEX_FECH_ENVIO= " . $db->conn->OffsetDate(0,$db->conn->sysTimeStamp) . "
							where RADI_NUME_SALIDA =$verrad_sal and
                                    sgd_dir_tipo <>7 and  $isql_w";
					$rsUpdate = $db->conn->Execute($isql);

					if ($rsUpdate)  $k++;
					if (!$codigo_envio)
					{	//include_once("$ruta_raiz/include/query/envios/queryEnvia.php");
						$objDir = new DatoContacto($db->conn);
						$sql_sgd_renv_codigo = "select SGD_RENV_CODIGO FROM SGD_RENV_REGENVIO ORDER BY SGD_RENV_CODIGO DESC ";
						$rsRegenvio = $db->conn->SelectLimit($sql_sgd_renv_codigo,10);
						$nextval = $rsRegenvio->fields["SGD_RENV_CODIGO"];
						$nextval++;
						$codigo_envio = $nextval;
						$radi_nume_grupo =  $verrad_sal ;
						$isql = "update RADICADO set SGD_EANU_CODIGO=9 where RADI_NUME_RADI =$verrad_sal";
						$rsUpdate = $db->conn->Execute($isql);
						$objDir->actualizaDatosDirEnvioFax(false, $verrad_sal, 1, false, false , false, false, 1);
					}
					else
					{	$nextval = $codigo_envio;
						$valor_unit=0;
					}
					$dir_tipo = $rem_destino;
                    $numGuia  = (!empty($_POST['numGuia'])) ? $_POST['numGuia'] : "NULL";
					$isql = "INSERT INTO SGD_RENV_REGENVIO(USUA_DOC,
                                                            SGD_RENV_CODIGO,
                                                            SGD_FENV_CODIGO,
                                                            SGD_RENV_FECH,
                                                            RADI_NUME_SAL,
                                                            SGD_RENV_DESTINO,
                                                            SGD_RENV_TELEFONO,
                                                            SGD_RENV_MAIL,
                                                            SGD_RENV_PESO,
                                                            SGD_RENV_VALOR,
                                                            SGD_RENV_CERTIFICADO,
                                                            SGD_RENV_ESTADO,
                                                            SGD_RENV_NOMBRE,
                                                            SGD_DIR_CODIGO,
                                                            DEPE_CODI,
                                                            SGD_DIR_TIPO,
                                                            RADI_NUME_GRUPO,
                                                            SGD_RENV_PLANILLA,
                                                            SGD_RENV_DIR,
                                                            SGD_RENV_DEPTO,
                                                            SGD_RENV_MPIO,
                                                            SGD_RENV_PAIS,
                                                            SGD_RENV_OBSERVA,
                                                            SGD_RENV_CANTIDAD,
                                                            SGD_RENV_NUMGUIA,
							    SGD_RENV_CODPOSTAL)
                                                    VALUES('$usua_doc',
                                                            '$nextval',
                                                            '$empresa_envio',
                                                            " .$db->conn->OffsetDate(0,$db->conn->sysTimeStamp).",
                                                            '$verrad_sal',
                                                            '$destino',
                                                            '$telefono_us',
                                                            '$mail',
                                                            '$envio_peso',
                                                            '$valor_unit',
                                                            0,
                                                            1,
                                                            '".substr($nombre_us,0,29)."',
                                                            '$dir_codigo',
                                                            '$dependencia',
                                                            '$dir_tipo',
                                                            '$radi_nume_grupo',
                                                            '$no_planilla',
                                                            '$direccion_us',
                                                            '$departamento_us',
                                                            '$destino',
                                                            '$pais_us',
                                                            '$observaciones',
                                                            1,
                                                            $numGuia,
							    '$codpostal_us')";
                                                            echo $isql;
					$rsInsert = $db->conn->Execute($isql);
					$rsEnviar->MoveNext();
				}
				$db->conn->CommitTrans();
			}	//$k
			include "../envios/listaEnvio.php";
			echo "<b><span class=listado2>Registro de Envio Generado</span> </b><br><br>";
		}	 //FIN else no reg_envio
	} else {
		echo "<hr><table class=borde_tab><tr class=titulosError><td>NO PUEDE SELECCIONAR VARIOS DOCUMENTOS PARA UN MISMO DESTINO CON CIUDAD Y/O DEPARTAMENTO DIFERENTE</td></tr></table><hr>";
	}
}
?>
</table>
</form>
<?php
$encabezado = "krd=$krd&fecha_h=$fechah&dep_sel=$dep_sel&estado_sal=$estado_sal
&estado_sal_max=$estado_sal_max&nomcarpeta=$nomcarpeta";
?>
<center>
<a class=vinculos href='cuerpoEnvioNormal.php?<?=session_name()."=".session_id()."&$encabezado"?>'>Devolver a Listado</a>
</center>
<script>
<?php
if($igual_destino=='si') {
	echo "function calcular_precio() {";
$ruta_raiz = "..";
$no_tipo="true";
include_once "../config.php";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
if (!isset($db))	$db = new ConnectionHandler("$ruta_raiz");

if (!defined('ADODB_FETCH_ASSOC')) {
    define('ADODB_FETCH_ASSOC',2);
}
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

//	HLP. Creamos el query que trae los valores para envios nacionales o internacionales.
switch ($masiva) {
	case 1:
	case 2:
		$var_grupo = 1;
		$campos_valores = " b.SGD_TAR_VALENV1 as VALOR1, b.SGD_TAR_VALENV2 as VALOR2 ";
		break;
	case 3:
		$var_grupo = 2;
		$campos_valores = " b.SGD_TAR_VALENV1G1 as VALOR1 ";
		break;
	case 4:
		$var_grupo = 2;
		$campos_valores = " b.SGD_TAR_VALENV2G2 as VALOR1 ";
}
$isql = "SELECT a.SGD_FENV_CODIGO,
                a.SGD_CLTA_DESCRIP,
                a.SGD_CLTA_PESDES,
                a.SGD_CLTA_PESHAST,
                $campos_valores
            FROM SGD_CLTA_CLSTARIF a,
                    SGD_TAR_TARIFAS b
			WHERE a.SGD_FENV_CODIGO = b.SGD_FENV_CODIGO AND
                    a.SGD_TAR_CODIGO = b.SGD_TAR_CODIGO AND
                    a.SGD_CLTA_CODSER = b.SGD_CLTA_CODSER AND
                    a.SGD_CLTA_CODSER = $var_grupo";
$rsEnvio = $db->conn->Execute($isql);
$tmp = 0 ;
echo "\n
var obj_peso = document.getElementById('envio_peso');
if (obj_peso.value != '')
{	if (isNaN(parseInt(obj_peso.value)) || obj_peso.value < 0)
	{	alert('Digite Correctamente Peso del Envio');
		obj_peso.value = '';
		return false;
	}
	var hallar_rango = false;\n";
while ($rsEnvio && !$rsEnvio->EOF)
{	$tmp+=1;
	if ($masiva==1 or $masiva==2)
	{	$valor_local = $rsEnvio->fields["VALOR1"];
		$valor_fuera = $rsEnvio->fields["VALOR2"];
	}
	else
	{	$valor_local = $rsEnvio->fields["VALOR1"];
		$valor_fuera = $rsEnvio->fields["VALOR1"];
	}

	$rango = $rsEnvio->fields["SGD_CLTA_DESCRIP"];
	$fenvio =$rsEnvio->fields["SGD_FENV_CODIGO"];
	echo "\nif (document.forma.elements['empresa_envio'].value==$fenvio && document.getElementById('envio_peso').value>=".$rsEnvio->fields["SGD_CLTA_PESDES"]." &&  document.getElementById('envio_peso').value<=".$rsEnvio->fields["SGD_CLTA_PESHAST"].") \n
			{	hallar_rango = true;
				document.getElementById('valor_gr').value = '$rango';
				dp_especial='$dependencia';
				if (document.forma.elements['destino'].value=='$depe_municipio' || (dp_especial=='840' && (document.forma.elements['destino'].value=='FLORIDABLANCA' || document.forma.elements['destino'].value=='GIRON (SAN JUAN DE)' || document.forma.elements['destino'].value=='PIEDECUESTA')))
				{	valor = $valor_local + 0; }
				else
				{
					valor = $valor_fuera +0 ;

				}
			}";
	$rsEnvio->MoveNext();
}
?>
if (hallar_rango)
{	document.getElementById('valor_unit').value = valor ;
}
else
{
	alert('Rango y peso especificado no est\xe1 configurado,\nComun\xedquese con el administrador del sistema.');
}}}
<?php
}
else echo "function calcular_precio() {}";
?>
</script>
</body>
</html>
