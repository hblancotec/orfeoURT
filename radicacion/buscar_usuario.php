<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "../sinacceso.php");
    exit();
} else if (isset($_SESSION['krd'])) {
    $krd = $_SESSION["login"];
    extract($_POST, EXTR_SKIP);
    extract($_GET, EXTR_SKIP);
}

if (! $ruta_raiz)
    $ruta_raiz = "..";
require_once ($ruta_raiz . "/" . "_conf/constantes.php");
if (! $_SESSION['dependencia'])
    include "$ruta_raiz/rec_session.php";
// require_once("../radicacion/common.php");
require_once ("$ruta_raiz/include/db/ConnectionHandler.php");
if (! $db)
    $db = new ConnectionHandler("$ruta_raiz");
require "crea_combos_universales.php";
$db->conn->SetFetchMode(ADODB_FETCH_NUM);
?>
<html>
<head>
<title>Busqueda Remitente / Destino</title>
<link rel="stylesheet" href="../estilos/orfeo.css" type="text/css">
<SCRIPT Language="JavaScript" SRC="../js/crea_combos_2.js"></SCRIPT>
<script LANGUAGE="JavaScript">

<?php
// Convertimos los vectores de los paises,
// dptos y municipios creados en crea_combos_universales.php a vectores en JavaScript.
echo arrayToJsArray($vcontiv, 'vc');
echo arrayToJsArray($vpaisesv, 'vp');
echo arrayToJsArray($vdptosv, 'vd');
echo arrayToJsArray($vmcposv, 'vm');
?>

function verif_data() {
	if(rightTrim(document.formu1.nombre_nus1.value)=='') {
		alert("Debe colocar el nombre.");
		return false;
	}
	var tipo = document.formu1.tagregar.value;
	if (tipo == 0) {
    	if (rightTrim(document.formu1.prim_apell_nus1.value)=='') {
            alert("Debe colocar el 1er apellido / sigla.");
    		return false;
    	}
	}
    if ( 	document.formu1.idpais4.length == 0 ||
            document.formu1.codep_us4.length == 0 ||
            document.formu1.muni_us4.length == 0 ||
            document.formu1.muni_us4.value == 0
		) {
        alert("Seleccione la geograf\xeda completa del destinatario");
		return false;
	}
	if (rightTrim(document.formu1.direccion_nus1.value)=='') {
        alert("Debe colocar una Direcci\xf3n.");
		return false;
	}	
	return true;
}
function pasar_datos(fecha) {
	debugger;
	var band = true;
	var xid = -1;
    <?php
    ($busq_salida == true) ? $i_registros = 1 : $i_registros = 3;
    $i_registros = 3;
    for ($i = 1; $i <= $i_registros; $i ++) {
        echo "documento = document.formu1.documento_us$i.value;\n" . "nombre = document.formu1.nombre_us$i.value + '';";
        echo "if(documento) {
    	 			
    				apellido1 = document.formu1.prim_apell_us$i.value  + ' ' ;
    				apellido2 = document.formu1.seg_apell_us$i.value  + ' ' ;
    				opener.document.formulario.documento_us$i.value = documento;
    				opener.document.formulario.nombre_us$i.value = nombre ;
    				opener.document.formulario.prim_apel_us$i.value = apellido1;
    				opener.document.formulario.seg_apel_us$i.value  = apellido2;
    				opener.document.formulario.telefono_us$i.value  = document.formu1.telefono_us$i.value;
    				opener.document.formulario.mail_us$i.value      = document.formu1.mail_us$i.value;
    				opener.document.formulario.direccion_us$i.value = document.formu1.direccion_us$i.value;
    				opener.document.formulario.tipo_emp_us$i.value = document.formu1.tipo_emp_us$i.value;
    				opener.document.formulario.cc_documento_us$i.value = document.formu1.cc_documento_us$i.value;";
        
        if ($_GET['tipoval']) {
            echo "	opener.document.formulario.idcont$i.value = document.formu1.idcont$i.value;
    						opener.document.formulario.idpais$i.value = document.formu1.idpais$i.value;
    						opener.document.formulario.codep_us$i.value = document.formu1.codep_us$i.value;
    						opener.document.formulario.muni_us$i.value = document.formu1.muni_us$i.value;
    						\n }";
        } else {
            echo "	opener.cambiaUnParametro(opener.document.formulario,'idcont$i', document.formu1.idcont$i.value, vc[document.formu1.idcont$i.value]);
    				xid = getIdVjs(vp, document.formu1.idpais$i.value);
    				opener.cambiaUnParametro(opener.document.formulario,'idpais$i', vp[xid].ID1, vp[xid].NOMBRE);
    				xid = getIdVjs(vd, document.formu1.codep_us$i.value);
    				opener.cambiaUnParametro(opener.document.formulario,'codep_us$i', vd[xid].ID1, vd[xid].NOMBRE);	
    				xid = getIdVjs(vm, document.formu1.muni_us$i.value);
    				opener.cambiaUnParametro(opener.document.formulario,'muni_us$i', vm[xid].ID1, vm[xid].NOMBRE);	
    				\n }";
        }
    }
    echo "if (band) {	opener.focus(); window.close(); }\n";
    ?>
    }
</script>
</head>
<body bgcolor="#FFFFFF">
	<script LANGUAGE="JavaScript">
    tipo_emp=new Array();
    nombre=new Array();
    documento=new Array();
    cc_documento=new Array();
    direccion=new Array();
    apell1=new Array();
    apell2=new Array();
    telefono=new Array();
    mail=new Array();
    codigo = new Array();
    codigo_muni = new Array();
    codigo_dpto = new Array();
    codigo_pais = new Array();
    codigo_cont = new Array();
    function pasar(indice,tipo_us) {
    debugger;
    <?php
    $nombre_essp = strtoupper($nombre_essp);
    
    (! $envio_salida and ! $busq_salida) ? $i_registros = 3 : $i_registros = 1;
    $i_registros = 3;
    for ($i = 1; $i <= $i_registros; $i ++) {
        echo "if(tipo_us==$i) {
    				document.formu1.documento_us$i.value = documento[indice];
    				document.formu1.no_documento1.value = cc_documento[indice];
    				document.formu1.codigo.value = codigo[indice];
    				document.formu1.cc_documento_us$i.value = cc_documento[indice];
    				document.formu1.nombre_nus1.value = nombre[indice];
    				document.formu1.nombre_us$i.value = nombre[indice];
    				document.formu1.prim_apell_us$i.value = apell1[indice];
    				document.formu1.prim_apell_nus1.value = apell1[indice];
    				document.formu1.seg_apell_us$i.value = apell2[indice];
    				document.formu1.seg_apell_nus1.value = apell2[indice];
    				document.formu1.direccion_us$i.value = direccion[indice];
    				document.formu1.direccion_nus1.value = direccion[indice];
    				document.formu1.telefono_us$i.value = telefono[indice];
    				document.formu1.telefono_nus1.value = telefono[indice];
    				document.formu1.mail_us$i.value = mail[indice];
    				document.formu1.mail_nus1.value = mail[indice];
    				document.formu1.tipo_emp_us$i.value = tipo_emp[indice];
    				document.formu1.tagregar.value = tipo_emp[indice];
    				document.formu1.muni_us$i.value = codigo_muni[indice];
    				document.formu1.codep_us$i.value = codigo_dpto[indice];
    				document.formu1.idpais$i.value = codigo_pais[indice];
    				document.formu1.idcont$i.value = codigo_cont[indice];
    				document.formu1.idcont4.value = codigo_cont[indice];
    				cambia(formu1,'idpais4','idcont4');
    				document.formu1.idpais4.value = codigo_pais[indice];
    				cambia(formu1,'codep_us4','idpais4');
    				document.formu1.codep_us4.value = codigo_dpto[indice];
    				cambia(formu1,'muni_us4','codep_us4');
    				document.formu1.muni_us4.value = codigo_muni[indice];
    		}";
    }
    ?>
}
function soloNumeros(e){
    var key = window.Event ? e.which : e.keyCode
    return (key >= 48 && key <= 57) || key == 45 
}

function activa_chk(forma) {
	//var obj = document.getElementById(chk_desact);
	if (forma.tbusqueda.value == 1)
		forma.chk_desact.disabled = false;
	else
		forma.chk_desact.disabled = true;
}
</script>
<?php
if (! $envio_salida and ! $busq_salida) {
    $label_us = $nombreTp1;
    $label_pred = $nombreTp2;
    $label_emp = $nombreTp3;
} else {
    $label_us = "DESTINATARIO";
    $label_pred = "$nombreTp2";
    $label_emp = "$nombreTp3";
}

/*$tbusqueda = $_POST['tbusqueda'];
if ($tagregar and $agregar) {
    $tbusqueda = $tagregar;
}*/

if ($no_documento1 and ($agregar or $modificar)) {
    $no_documento = $no_documento1;
}
if (! $no_documento1 and $nombre_nus1 and ($agregar or $modificar)) {
    $nombre_essp = $nombre_nus1;
}
if (! $formulario) {
    ?>
<form method="post" name="formu1" id="formu1"
		action="buscar_usuario.php?busq_salida=<?=$busq_salida?>&krd=<?=$krd?>&verrad=<?=$verrad?>&nombreTp1=<?=$nombreTp1?>&nombreTp2=<?=$nombreTp2?>&nombreTp3=<?=$nombreTp3?>&tipoval=<?=$_GET['tipoval'] ?>">
<?php
}
?>
<input type="hidden" name="ent" id="ent" value="<?= $ent?>">
<input type="hidden" name="radicados" value="<?= $radicados_old?>">
		<table border=0 width="78%" class="borde_tab" cellpadding="0"
			cellspacing="5">
			<!-- <tr>
				<td width="30%" class="titulos5"><font class="tituloListado">BUSCAR
						POR </font></td>
				<td width="50%" class="titulos5"><select name='tbusqueda'
					class='select' onchange="activa_chk(this.form)">
                <?php
                if ($tipo_emp == 0) {
                    $datos = "selected";
                    $tipo_emp = 0;
                } else {
                    $datos = "";
                }
                ?>

            <option value=0 <?=$datos ?>>USUARIO</option>
				<?php
				if ($tipo_emp == 1) {
        $datos = "selected";
    } else {
        $datos = "selected";
    }
    echo "<option value=1 $datos>ENTIDAD</option>";
    if ($tipo_emp == 2) {
        $datos = "selected";
    } else {
        $datos = "";
    }
    ?>
			<option value=2 <?=$datos ?>>OTRAS EMPRESAS</option>
			<?php if($tipo_emp==6){$datos = " selected ";$tipo_emp=6;}else{$datos= "";}?>
			<option value=6 <?=$datos ?>>FUNCIONARIO</option>


				</select></td>
				<td width="20%" rowspan="2" align="center" class="titulos5"><input
					type=submit name=buscar value='BUSCAR' class="botones"></td>
			</tr>-->
			<tr>
				<td class="listado5" valign="middle"><span class="titulos5">Documento</span>
					<input type=text name=no_documento value='' class="tex_area"> </font>
				</td>
				<td class="listado5" valign="middle"><span class="titulos5">Nombre</span>
					<input type=text name=nombre_essp value='' class="tex_area"> <!-- <input
					type="checkbox" name="chk_desact" id="chk_desact"
					<?php ($_POST['tbusqueda'] != 1)? print "disabled" : print "";?>>Incluir
					no vigentes-->
					<input type=submit name=buscar value='BUSCAR' class="botones">
				</td>
			</tr>
		</table>
		<br>
		<TABLE class="borde_tab" width="100%">
			<tr class=listado2>
				<td colspan=10>
					<center>RESULTADO DE BUSQUEDA</center>
				</td>
			</tr>
		</TABLE>
		<table class=borde_tab width="100%" cellpadding="0" cellspacing="5">
			<!--DWLayoutTable-->
			<tr class="grisCCCCCC" align="center">
				<td width="11%" CLASS="titulos5">DOCUMENTO</td>
				<td width="11%" CLASS="titulos5">NOMBRE</td>
				<td width="14%" CLASS="titulos5">PRIM.<BR>APELLIDO o SIGLA
				</td>
				<td width="15%" CLASS="titulos5">SEG.<BR>APELLIDO o R Legal
				</td>
				<td width="14%" CLASS="titulos5">DIRECCI&Oacute;N</td>
				<td width="9%" CLASS="titulos5">TEL&Eacute;FONO</td>
				<td width="7%" CLASS="titulos5">EMAIL</td>
				<td colspan="3" CLASS="titulos5">COLOCAR COMO</td>
			</tr>
  <?php
$grilla = "timpar";
$i = 0;

// ********************************
function db_fill_array($sql_query, $k, $v)
{
    global $ruta_raiz;
    $db = new ConnectionHandler($ruta_raiz);
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    // $db->conn->debug=true;
    $rs = $db->conn->Execute($sql_query);
    
    if ($rs && ! $rs->EOF) {
        do {
            $ar_lookup[$rs->fields[$k]] = $rs->fields[$v];
            $rs->MoveNext();
        } while ($rs && ! $rs->EOF);
        return $ar_lookup;
    } else
        return false;
}

// ********************************
if ($modificar == "MODIFICAR" or $agregar == "AGREGAR") {
    $muni_tmp = explode("-", $muni_us4);
    $muni_tmp = $muni_tmp[2];
    $dpto_tmp = explode("-", $codep_us4);
    $dpto_tmp = $dpto_tmp[1];
}
if ($modificar == "MODIFICAR" and $tagregar == 0) {
    $nombre_nus1 = str_replace("&", "Y", $nombre_nus1);
    $direccion_nus1 = str_replace("&", "Y", $direccion_nus1);
    $prim_apell_nus1 = str_replace("&", "Y", $prim_apell_nus1);
    $seg_apell_nus1 = str_replace("&", "Y", $seg_apell_nus1);
    $isql = "update SGD_CIU_CIUDADANO set SGD_CIU_CEDULA='$no_documento1',
                                                SGD_CIU_NOMBRE='$nombre_nus1',
      			                                SGD_CIU_DIRECCION='$direccion_nus1',
                                                SGD_CIU_APELL1='$prim_apell_nus1',
                                                SGD_CIU_APELL2='$seg_apell_nus1',
                                                SGD_CIU_TELEFONO='$telefono_nus1',
                                                SGD_CIU_EMAIL='$mail_nus1',
                                                ID_CONT=$idcont4,
                                                ID_PAIS=$idpais4,
                                                DPTO_CODI=$dpto_tmp,
                                                MUNI_CODI=$muni_tmp
                                        where SGD_CIU_CODIGO=$codigo ";
    $rs = $db->conn->Execute($isql);
    if (! $rs) {
        die("<span class='etextomenu'>No se pudo actualizar SGD_CIU_CIUDADANO ($isql) ");
    }
    $isql = "select * from SGD_CIU_CIUDADANO where SGD_CIU_CEDULA='$no_documento1'";
    $rs = $db->conn->Execute($isql);
}

$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

if ($agregar == "AGREGAR" and $tagregar == 0) {
    $cedula = 999999;
    if ($no_documento) {
        $isql = "select SGD_CIU_CEDULA  from SGD_CIU_CIUDADANO WHERE  SGD_CIU_CEDULA='$no_documento'";
        $rs = $db->conn->Execute($isql);
        
        if (! $rs->EOF)
            $cedula = $rs->fields["SGD_CIU_CEDULA"];
        $flag == 0;
    }
    
    if ($cedula == $no_documento and $no_documento != "" and $no_documento != 0) {
        echo "<center><b><font color=red><center><< No se ha podido agregar el usuario, El usuario ya se encuentra >> </center></font>";
    } else {
        
        $nextval = $db->nextId("SEC_CIU_CIUDADANO");
        if ($nextval == - 1) {
            die("<span class='etextomenu'>No se encontr&oacute; la secuencia sec_ciu_ciudadano ");
        }
        $nombre_nus1 = str_replace("&", "Y", $nombre_nus1);
        $direccion_nus1 = str_replace("&", "Y", $direccion_nus1);
        $prim_apell_nus1 = str_replace("&", "Y", $prim_apell_nus1);
        $seg_apell_nus1 = str_replace("&", "Y", $seg_apell_nus1);
        $isql = "INSERT INTO SGD_CIU_CIUDADANO(SGD_CIU_CEDULA, TDID_CODI, SGD_CIU_CODIGO, SGD_CIU_NOMBRE,
					SGD_CIU_DIRECCION, SGD_CIU_APELL1, SGD_CIU_APELL2, SGD_CIU_TELEFONO, SGD_CIU_EMAIL, 
					ID_CONT, ID_PAIS, DPTO_CODI, MUNI_CODI) values ('$no_documento', 0, $nextval, '$nombre_nus1', '$direccion_nus1',
					'$prim_apell_nus1', '$seg_apell_nus1','$telefono_nus1', '$mail_nus1',
					$idcont4, $idpais4, $dpto_tmp, $muni_tmp)";
        if (! trim($no_documento))
            $nombre_essp = "$nombre_nus1 $prim_apell_nus1 $seg_apell_nus1";
        $rs = $db->conn->Execute($isql);
        if (! $rs) {
            $db->conn->RollbackTrans();
            die("<span class='etextomenu'>No se pudo actualizar SGD_CIU_CIUDADANO ($isql) ");
        }
    }
    if ($flag == 1) {
        echo "<center><b><font color=red><center>No se ha podido agregar el usuario, verifique que los datos sean correctos</center></font>";
    }
    $isql = "select * from SGD_CIU_CIUDADANO where SGD_CIU_CEDULA='$no_documento1'";
    $rs = $db->conn->Execute($isql);
}

if ($agregar == "AGREGAR" and $tagregar == 2) {
    $nextval = $db->nextId("SEC_OEM_OEMPRESAS");
    
    if ($nextval == - 1) {
        die("<span class='etextomenu'>No se encontr&oacute; la secuencia sec_oem_oempresas ");
    }
    
    $nombre_nus1 = str_replace("&", "Y", $nombre_nus1);
    $direccion_nus1 = str_replace("&", "Y", $direccion_nus1);
    $prim_apell_nus1 = str_replace("&", "Y", $prim_apell_nus1);
    $seg_apell_nus1 = str_replace("&", "Y", $seg_apell_nus1);
    $isql = "INSERT INTO SGD_OEM_OEMPRESAS( tdid_codi, SGD_OEM_CODIGO, SGD_OEM_NIT, SGD_OEM_SIGLA, SGD_OEM_OEMPRESA, SGD_OEM_DIRECCION,
				SGD_OEM_REP_LEGAL, SGD_OEM_TELEFONO, ID_CONT, ID_PAIS, DPTO_CODI, MUNI_CODI, EMAIL)
				values (4, $nextval, '$no_documento', '$prim_apell_nus1','$nombre_nus1', '$direccion_nus1', '$seg_apell_nus1', 
						'$telefono_nus1', $idcont4, $idpais4, $dpto_tmp, $muni_tmp, '$mail_nus1')";
    $rs = $db->conn->Execute($isql);
    
    if (! $rs)
        die("<span class='titulosError'>No se pudo insertar en SGD_OEM_OEMPRESAS ($isql) ");
}
if ($modificar == "MODIFICAR" and $tagregar == 2) {
    $nombre_nus1 = str_replace("&", "Y", $nombre_nus1);
    $direccion_nus1 = str_replace("&", "Y", $direccion_nus1);
    $prim_apell_nus1 = str_replace("&", "Y", $prim_apell_nus1);
    $seg_apell_nus1 = str_replace("&", "Y", $seg_apell_nus1);
    $isql = "UPDATE SGD_OEM_OEMPRESAS SET SGD_OEM_NIT='$no_documento1', SGD_OEM_OEMPRESA='$nombre_nus1',
				SGD_OEM_DIRECCION='$direccion_nus1', SGD_OEM_REP_LEGAL='$seg_apell_nus1',
				SGD_OEM_SIGLA='$prim_apell_nus1', SGD_OEM_TELEFONO='$telefono_nus1', ID_CONT=$idcont4, 
				ID_PAIS= $idpais4, DPTO_CODI=$dpto_tmp, MUNI_CODI=$muni_tmp, EMAIL = '$mail_nus1' where SGD_OEM_CODIGO='$codigo'";
    $rs = $db->conn->Execute($isql);
    
    if (! $rs) {
        $db->conn->RollbackTrans();
    }
}

function splitToLevenshtein($data, $size)
{
    $searchName = array();
    foreach ($data as $id => $value) {
        $fields_1 = explode(" ", $value);
        foreach ($fields_1 as $fk_1 => $fv_1) {
            $fields = explode("-", $fv_1);
            foreach ($fields as $fk => $fv) {
                if (strlen($fv) > $size) {
                    $fv = trim(strtoupper($fv));
                    if (! array_key_exists($fv, $searchName)) {
                        $searchName[$fv] = array();
                    }
                    $searchName[$fv][] = $id;
                }
            }
        }
    }
    ksort($searchName, SORT_STRING);
    reset($searchName);
    return $searchName;
}

$maxLevel = 2;

function makeLevenshtein($keyGlobal, $keySearch, $keySave, &$arrSave)
{
    $fields = explode(" ", $keySearch);
    $arrField = array();
    foreach ($fields as $fk => $fv) {
        if (strlen($fv) > 3) {
            $arrField[strtoupper($fv)] = array();
        }
    }
    
    foreach ($keyGlobal as $key => $value) {
        foreach ($fields as $fk => $fv) {
            if (strlen($fv) > 3) {
                $lev = levenshtein(strtoupper($fv), $key);
                if (($lev >= 0) && ($lev <= 1)) {
                    foreach ($value as $k => $v) {
                        $arrField[strtoupper($fv)][] = $v;
                    }
                    $arrSave[$lev][$keySave][$key] = $value;
                }
                ;
            }
            ;
        }
    }
    
    $arrIntersect = array();
    // cada palabra digitada por el usuario a buscar
    $intersect = false;
    $listIntersect = array();
    foreach ($fields as $fk => $fv) {
        if (strlen($fv) > 3) {
            $listIntersect[] = $arrField[strtoupper($fv)];
            if (count($arrIntersect) == 0) {
                $intersect = false;
                $arrIntersect = $arrField[strtoupper($fv)];
            } else {
                $intersect = true;
                $arrIntersect = array_intersect($arrIntersect, $arrField[strtoupper($fv)]);
            }
        }
    }
    
    $arrIntersect = array_unique($arrIntersect);
    if ((! $intersect) and (count($fields) > 1)) {
        $arrIntersect = array();
    }
    
    return $arrIntersect;
}

$levenshtein = array();
$lString = "";
if ($no_documento or $nombre_essp) {
    
    $where = "";
    $where1 = "";
    $where2 = "";
    if (strlen($nombre_essp) > 0) {
        $vars = explode(" ", $nombre_essp);
        if (count($vars) > 0) {
            for ($i = 0; $i < count($vars); $i++) {
                if ($vars[$i] != 'Y' && $vars[$i] != 'O') {
                    if (strlen($where) > 1) {
                        $where .= " AND ";
                        $where1 .= " AND ";
                    }
                    $where .= " contains (*, ''$vars[$i]'') ";
                    $where1 .= " contains (USUA.*, ''$vars[$i]'') ";
                }
            }
            $wheredef = " N'WHERE " . $where;
            $where2 = " N'WHERE " . $where1;
        }
    }
    if (strlen($no_documento) > 0) {
        if (strlen($wheredef) > 7) {
            $wheredef .= " AND contains (*, ''$no_documento'') '";
            $where2 .= " AND contains (USUA.*, ''$no_documento'') AND USUA.USUA_ESTA = ''1'' '";
        } else {
            $wheredef .= " N'WHERE contains (*, ''$no_documento'') '";
            $where2 .= " N'WHERE contains (USUA.*, ''$no_documento'') AND USUA.USUA_ESTA = ''1'' '";
        }
    } else {
        $wheredef .= "'";
        $where2 .= " AND USUA.USUA_ESTA = ''1'' '";
    }
    
    $tipo = 0;
    if ($ent == 3) {
        $tipo = 1;
    }
    
    $st = " DECLARE @return_value int
                                                           
            EXEC @return_value = [dbo].[BUSQUEDA_Usuarios]
                                 @where = $wheredef,
                                 @whereusua = $where2,
                                 @tipo = $tipo ";
    //echo $st;
    $rs = $db->conn->Execute($st);
    if ($rs && ! $rs->EOF) {
        while (! $rs->EOF) {
            ($grilla == "timparr") ? $grilla = "timparr" : $grilla = "tparr";
            $tipo_emp = $rs->fields["TIPO"];
            ?>
				<tr class='<?=$grilla ?>'>
				<TD class="listado5"><font size="-3"><?=$rs->fields["SGD_CIU_CEDULA"] ?></font></TD>
				<TD class="listado5"><font size="-3"> <?=substr($rs->fields["SGD_CIU_NOMBRE"],0,120) ?></font></TD>
				<TD class="listado5"><font size="-3"> <?=substr($rs->fields["SGD_CIU_APELL1"],0,70) ?></font></TD>
				<TD class="listado5"><font size="-3"> <?=$rs->fields["SGD_CIU_APELL2"] ?> </font></TD>
				<TD class="listado5"><font size="-3"> <?=$rs->fields["SGD_CIU_DIRECCION"] ?></font></TD>
				<TD class="listado5"><font size="-3"> <?=$rs->fields["SGD_CIU_TELEFONO"] ?> </font></TD>
				<TD class="listado5"><font size="-3"> <?=$rs->fields["SGD_CIU_EMAIL"] ?></font></TD>
				<TD width="6%" align="center" valign="top" class="listado5"><font
					size="-3"><a href="#" onClick="pasar('<?=$i ?>',1);"
						class="titulos5"><?=$label_us?></a></font></TD>
    <?php
            if (! $envio_salida or $ent == 5) {
                ?>
					<td width="6%" align="center" valign="top" class="listado5"><font
					size="-3"><a href="#" onClick="pasar('<?=$i ?>',2);"
						class="titulos5"><?=$label_pred?></a></font></td>
    <?php
                if ($tipo_emp == 1) {
                    ?>
						<td width="7%" align="center" valign="top" class="listado5"><font
					size="-3"> <a href="#" onClick="pasar('<?=$i ?>',3);"
						class="titulos5"><?=$label_emp?></a></font></td>
    <?php
                }
            }
            ?>
  </tr>
			<script>
		<?php
            $cc_documento = trim($rs->fields["SGD_CIU_CODIGO"]) . " ";
            $email = str_replace('"', ' ', $rs->fields["SGD_CIU_EMAIL"]) . " ";
            $telefono = str_replace('"', ' ', $rs->fields["SGD_CIU_TELEFONO"]) . " ";
            $direccion = str_replace('"', ' ', $rs->fields["SGD_CIU_DIRECCION"]) . " ";
            $apell2 = str_replace('"', ' ', $rs->fields["SGD_CIU_APELL2"]) . " ";
            $apell1 = str_replace('"', ' ', $rs->fields["SGD_CIU_APELL1"]) . " ";
            $nombre = str_replace('"', ' ', $rs->fields["SGD_CIU_NOMBRE"]) . " ";
            $codigo = trim($rs->fields["SGD_CIU_CODIGO"]);
            $codigo_cont = $rs->fields["ID_CONT"];
            $codigo_pais = $rs->fields["ID_PAIS"];
            $codigo_dpto = $codigo_pais . "-" . $rs->fields["DPTO_CODI"];
            $codigo_muni = $codigo_dpto . "-" . $rs->fields["MUNI_CODI"];
            $cc_documento = trim($rs->fields["SGD_CIU_CEDULA"]);
            ?>
			tipo_emp[<?=$i?>]= "<?=$tipo_emp?>";
			documento[<?=$i?>]= "<?=$codigo?>";
			cc_documento[<?=$i?>]= "<?=$cc_documento?>";
			nombre[<?=$i?>]= "<?=$nombre?>";
			apell1[<?=$i?>]= "<?=$apell1?>";
			apell2[<?=$i?>]= "<?=$apell2?>";
			direccion[<?=$i?>]= "<?=$direccion?>";
			telefono[<?=$i?>]= "<?=$telefono?>";
			mail[<?=$i?>]= "<?=$email?>";
			codigo[<?=$i?>]= "<?=$codigo?>";
			codigo_muni[<?=$i?>]= "<?=$codigo_muni?>";
			codigo_dpto[<?=$i?>]= "<?=$codigo_dpto?>";
			codigo_pais[<?=$i?>]= "<?=$codigo_pais?>";
			codigo_cont[<?=$i?>]= "<?=$codigo_cont?>";
		</script>
  <?php
            $i ++;
            $rs->MoveNext();
        }
        echo "<span class='listado2'>Registros Encontrados</span>";
    } else {
        echo "<span class='titulosError'>No se encontraron Registros -- $no_documento</span>";
    }
}
?>
</table>
		<BR>
		<?php 
		///if ($ent != 3) {
		?>
    		<table class=borde_tab width="100%" cellpadding="0" cellspacing="4">
    			<tr class=listado2>
    				<TD colspan="10">
    					<center>DATOS A COLOCAR EN LA RADICACI&Oacute;N</center>
    				</TD>
    			</tr>
    			<tr align="center">
    				<td CLASS=titulos5>USUARIO</td>
    				<td CLASS=titulos5>DOCUMENTO</td>
    				<td CLASS=titulos5>NOMBRE</td>
    				<td CLASS=titulos5>PRIM.<BR>APELLIDO o SIGLA
    				</td>
    				<td CLASS=titulos5>SEG.<BR>APELLIDO o REP LEGAL
    				</td>
    				<td CLASS=titulos5>DIRECCION</td>
    				<td CLASS=titulos5>TELEFONO</td>
    				<td CLASS=titulos5>EMAIL</td>
    			</tr>
    			<tr class='<?=$grilla ?>'>
    				<td align="center" class="listado5"><font
    					face="Arial, Helvetica, sans-serif"><?=$nombreTp1?></font></td>
    				<TD align="center" class="listado5"><input type="hidden"
    					name="tipo_emp_us1" value="<?=$tipo_emp_us1?>"> <input
    					type="hidden" name="documento_us1" size="3"
    					value="<?=$documento_us1?>"> <input type="hidden" name="muni_us1"
    					value="<?=$muni_us1 ?>"> <input type="hidden" name="codep_us1"
    					value="<?=$codep_us1 ?>"> <input type="hidden" name="idpais1"
    					value="<?=$idpais1 ?>"> <input type="hidden" name="idcont1"
    					value="<?=$idcont1 ?>"> <input type="text"
    					name="cc_documento_us1" value="<?=$cc_documento_us1 ?>"
    					class="ecajasfecha"></TD>
    				<TD align="center" class="listado5"><input type="text"
    					name="nombre_us1" value="<?=$nombre_us1?>" class="ecajasfecha"
    					size="15"></TD>
    				<TD align="center" class="listado5"><input type="text"
    					name="prim_apell_us1" value="<?=$prim_apell_us1 ?>"
    					class="ecajasfecha" size="14"></TD>
    				<TD align="center" class="listado5"><input type="text"
    					name="seg_apell_us1" value="<?=$seg_apell_us1 ?>"
    					class="ecajasfecha" size="14"></TD>
    				<TD align="center" class="listado5"><input type="text"
    					name="direccion_us1" value="<?=$direccion_us1 ?>"
    					class="ecajasfecha" size="15"></TD>
    				<TD align="center" class="listado5"><input type="text"
    					name="telefono_us1" value="<?=$telefono_us1 ?>" class="ecajasfecha"
    					size="10"></TD>
    				<TD align="center" class="listado5"><input type="text"
    					name="mail_us1" value="<?=$mail_us1 ?>" class="ecajasfecha"
    					size="16" readonly></TD>
    			</tr>
    			<tr class='<?=$grilla ?>'>
    				<td align="center" class="listado5"><font
    					face="Arial, Helvetica, sans-serif"><?=$nombreTp2?><BR> o Seg. Not</font></TD>
    				<TD align="center" class="listado5"><input type="hidden"
    					name="tipo_emp_us2" value="<?=$tipo_emp_us2?>"> <input
    					type="hidden" name="documento_us2" value="<?=$documento_us2?>"> <input
    					type="hidden" name="codep_us2" value="<?=$codep_us2 ?>"> <input
    					type="hidden" name="muni_us2" value="<?=$muni_us2 ?>"> <input
    					type="hidden" name="idpais2" value="<?=$idpais2 ?>"> <input
    					type="hidden" name="idcont2" value="<?=$idcont2 ?>"> <input
    					type="text" name="cc_documento_us2" value="<?=$cc_documento_us2?>"
    					class="ecajasfecha" size="13"></TD>
    				<TD align="center" class="listado5"><input type="text"
    					name="nombre_us2" value="<?=$nombre_us2 ?>" class="ecajasfecha"
    					size="15"></TD>
    				<TD align="center" class="listado5"><input type="text"
    					name="prim_apell_us2" value="<?=$prim_apell_us2 ?>"
    					class="ecajasfecha" size="14"></TD>
    				<TD align="center" class="listado5"><input type="text"
    					name="seg_apell_us2" value="<?=$seg_apell_us2 ?>"
    					class="ecajasfecha" size="14"></TD>
    				<TD align="center" class="listado5"><input type="text"
    					name="direccion_us2" value="<?=$direccion_us2 ?>"
    					class="ecajasfecha" size="15"></TD>
    				<TD align="center" class="listado5"><input type="text"
    					name="telefono_us2" value="<?=$telefono_us2 ?>" class="ecajasfecha"
    					size="10"></TD>
    				<TD align="center" class="listado5"><input type="text"
    					name="mail_us2" value="<?=$mail_us2 ?>" class="ecajasfecha"
    					size="16" readonly></TD>
    			</tr>
    			<tr class='<?=$grilla ?>'>
    				<td align="center" class="listado5"><font
    					face="Arial, Helvetica, sans-serif"><?=$nombreTp3?></font></td>
    				<TD align="center" class="listado5"><input type=hidden
    					name='tipo_emp_us3' value='<?=$tipo_emp_us3?>' class="ecajasfecha">
    					<font face="Arial, Helvetica, sans-serif"> <input type="hidden"
    						name="tipo_emp_us3" value="<?=$tipo_emp_us3?>"> <input type=hidden
    						name=documento_us3 class=e_cajas size=3
    						value='<?=$documento_us3?>'> <input type=hidden name=codep_us3
    						value='<?=$codep_us3 ?>' size=4 class="ecajasfecha"> <input
    						type=hidden name=muni_us3 value='<?=$muni_us3 ?>' size=4
    						class="ecajasfecha"> <input type="hidden" name="idpais3"
    						value="<?=$idpais3 ?>"> <input type="hidden" name="idcont3"
    						value="<?=$idcont3 ?>"> <input type=text
    						name=cc_documento_us3 value='<?=$cc_documento_us3?>' size=13
    						class="ecajasfecha"></TD>
    				<TD align="center" class="listado5"><input type="text"
    					name="nombre_us3" value="<?=$nombre_us3 ?>" class="ecajasfecha"
    					size="15"></TD>
    				<TD align="center" class="listado5"><input type="text"
    					name="prim_apell_us3" value="<?=$prim_apell_us3 ?>"
    					class="ecajasfecha" size="14"></TD>
    				<TD align="center" class="listado5"><input type="text"
    					name="seg_apell_us3" value="<?=$seg_apell_us3 ?>"
    					class="ecajasfecha" size="14"></TD>
    				<TD align="center" class="listado5"><input type="text"
    					name="direccion_us3" value="<?=$direccion_us3 ?>"
    					class="ecajasfecha" size="15"></TD>
    				<TD align="center" class="listado5"><input type="text"
    					name="telefono_us3" value="<?=$telefono_us3 ?>" class="ecajasfecha"
    					size="10"></TD>
    				<TD align="center" class="listado5"><input type="text"
    					name="mail_us3" value="<?=$mail_us3 ?>" class="ecajasfecha"
    					size="16" readonly></TD>
    			</tr>
    	<?php
    $nombre_tt = str_replace('"', ' ', $rs->fields["SGD_CIU_NOMBRE"]);
    ?><script>
    		 nombre[<?=$i ?>]= "<?=$nombre_tt?>";
    	</script>
    		</table>
    	<?php 
		//}
    	?>

		<center>
  <?php
if ($envio_salida) {
    ?>
      <input type=submit name=grb_destino
				value='Grabar el Destino de Este Radicado' class="botones:largo"> <input
				type=hidden name=verrad_sal value='<?=$verrad_sal?>'>
    <?php
} else {
    ?>
       <B><a href="javascript:pasar_datos('<?=$fechah?>');"><span
					class="botones_largo">PASAR DATOS AL FORMULARIO DE RADICACI&Oacute;N</span></a></B>
			<input type='hidden' name='verrad_sal' value='<?=$verrad_sal?>'>
    <?php
}
?>
    <BR>
			<BR>
			<table class=borde_tab width="100%" cellpadding="0" cellspacing="4">
				<tr align="center">
					<td CLASS=titulos5>DOCUMENTO</td>
					<td CLASS=titulos5>NOMBRE</td>
					<td CLASS=titulos5>PRIMER<BR>APELLIDO o Sigla
					</td>
					<td CLASS=titulos5>SEG.<BR>APELLIDO o REP LEGAL
					</td>
					<td CLASS=titulos5>EMAIL</td>
				</tr>
				<tr class='listado5' align="center">
					<TD>
						<input type="text" name="codigo" class="e_cajas" size="3" value='<?=$codigo?>'>
						<input type="text" name="no_documento1" value="<?=$no_documento ?>" onKeyPress="return soloNumeros(event)" size="13" class="ecajasfecha">
					</TD>
					<TD><input type="text" name="nombre_nus1" value="<?=$nombre_nus1?>"
						class="ecajasfecha" size=20></TD>
					<TD><input type="text" name="prim_apell_nus1"
						value="<?=$prim_apell_nus1?>" class="ecajasfecha" size="14"></TD>
					<TD><input type="text" name="seg_apell_nus1"
						value="<?=$seg_apell_nus1?>" class="ecajasfecha" size="14"></TD>
					<TD><input type="text" name="mail_nus1" value="<?=$mail_nus1?>"
						class="ecajasfecha" size=32></TD>
				</tr>
				<tr align="center">
					<td CLASS=titulos5 colspan="2">DIRECCI&Oacute;N</td>
					<td CLASS=titulos5>TEL&Eacute;FONO</td>
					<td rowspan="2" CLASS="grisCCCCCC" valign="middle">
						<select id="tagregar" name="tagregar" class="select">
							<option value='0'>USUARIO(Ciudadano)</option>
							<option value='2'>OTRAS EMPRESAS</option>
						</select> 
						<input type='SUBMIT' name='modificar' value='MODIFICAR' class="botones" onclick="return verif_data();">
						<input type='SUBMIT' name='agregar' value='AGREGAR' class="botones" onclick="return verif_data();">
					</td>
				</tr>
				<tr>
					<TD colspan="2"><input type="text" name="direccion_nus1"
						value="<?=$direccion_nus1?>" class="ecajasfecha" size="45"></TD>
					<TD><input type="text" name="telefono_nus1"
						value="<?=$telefono_nus1?>" class="ecajasfecha" size="10"></TD>
				</tr>
				<tr align="center">
					<td CLASS=titulos5>Continente</font></td>
					<td CLASS=titulos5>Pa&iacute;s</font></td>
					<td CLASS=titulos5>Dpto / Estado</font></td>
					<td CLASS=titulos5>Municipio</font></td>
				</tr>
				<tr class='celdaGris' align="center">
					<TD>
	<?php
$i = 4;
echo "<SELECT NAME=\"idcont$i\" ID=\"idcont$i\" CLASS=\"select\" onchange=\"cambia(this.form, 'idpais$i', 'idcont$i')\">";
echo "<option value='0'>&lt;&lt; seleccione &gt;&gt;</option>";
foreach ($vcontiv as $key => $value) {
	echo "<option value='".$key."'>".$value."</option>";
}
echo "</SELECT>";
?>
	</TD>
	<TD>
	<?php
	//Visualizamos el combo de paises.
	echo "<SELECT NAME=\"idpais$i\" ID=\"idpais$i\" CLASS=\"select\" onchange=\"cambia(this.form, 'codep_us$i', 'idpais$i')\"></SELECT>";
	?>
	</TD>
	<TD>
	<?php
	echo "<SELECT NAME=\"codep_us$i\" ID=\"codep_us$i\" CLASS=\"select\" onchange=\"cambia(this.form, 'muni_us$i', 'codep_us$i')\"></SELECT>";
	?>
	</TD>
	<TD>
	<?php
	echo "<SELECT NAME=\"muni_us$i\" ID=\"muni_us$i\" CLASS=\"select\" ></SELECT>";
	?>
		</td>
				</tr>
			</table>
<?php
if(!$formulario)
{
?>

	
	</form>
<?php
}
?>
<center>
		<input type='button' value='Cerrar' class="botones_largo"
			onclick='window.close()'>
	</center>
</body>
</html>