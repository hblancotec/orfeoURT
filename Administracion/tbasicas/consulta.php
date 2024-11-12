<?php

$ruta_raiz = "../..";
include "$ruta_raiz/config.php";   // incluir configuracion.
define('ADODB_ASSOC_CASE', 1);
include 'adodb/adodb.inc.php'; // $ADODB_PATH configurada en config.php
$dsn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
$conn = NewADOConnection($dsn);

if ($conn) {
    $conn->SetFetchMode(ADODB_FETCH_ASSOC);

    $respuesta = "";
    
    if($_POST['datos']) {
        $codigo = $_POST['codigo'];
  
        if ($_POST['idtabla'] == 1) {
            $sql = "SELECT SGD_CAU_DESCRIP AS NOMBRE, SGD_CAU_CODIGO AS ID, SGD_CAU_ESTADO AS ESTADO FROM SGD_CAU_CAUSAL WHERE SGD_CAU_CODIGO = " . $codigo . " ORDER BY 1";
            $rs = $conn->Execute($sql);
            if ($rs) {
                $it = 0;
                while (!$rs->EOF)
                {
                    $vector[$it]['ID'] = $rs->fields['ID'];
                    $vector[$it]['NOMBRE'] = $rs->fields['NOMBRE'];
                    $vector[$it]['ESTADO'] = $rs->fields['ESTADO'];
                    $it += 1;
                    $rs->MoveNext();
                }
                $rs->Close();
                unset($rs); unset($sql);
                
                $respuesta = json_encode($vector);
            }
        }
        elseif ($_POST['idtabla'] == 2) {
            $sql = "SELECT SGD_DEVE_DESC AS NOMBRE, SGD_DEVE_CODIGO AS ID, SGD_DEVE_ESTADO AS ESTADO FROM SGD_DEVE_DEV_ENVIO WHERE SGD_DEVE_CODIGO = " . $codigo . " ORDER BY 1";
            $rs = $conn->Execute($sql);
            if ($rs) {
                $it = 0;
                while (!$rs->EOF)
                {
                    $vector[$it]['ID'] = $rs->fields['ID'];
                    $vector[$it]['NOMBRE'] = $rs->fields['NOMBRE'];
                    $vector[$it]['ESTADO'] = $rs->fields['ESTADO'];
                    $it += 1;
                    $rs->MoveNext();
                }
                $rs->Close();
                unset($rs); unset($sql);
                
                $respuesta = json_encode($vector);
            }
        }
        elseif ($_POST['idtabla'] == 3) {
            $sql = "SELECT SGD_FENV_DESCRIP AS NOMBRE, SGD_FENV_CODIGO AS ID, SGD_FENV_ESTADO AS ESTADO, SGD_FENV_DESCRIPCION AS DESCRIPCION FROM SGD_FENV_FRMENVIO WHERE SGD_FENV_CODIGO = " . $codigo . " ORDER BY 1";
            $rs = $conn->Execute($sql);
            if ($rs) {
                $it = 0;
                while (!$rs->EOF)
                {
                    $vector[$it]['ID'] = $rs->fields['ID'];
                    $vector[$it]['NOMBRE'] = iconv("iso-8859-1", "utf-8", $rs->fields['NOMBRE']);
                    $vector[$it]['ESTADO'] = $rs->fields['ESTADO'];
                    $vector[$it]['DESCRIPCION'] = iconv("iso-8859-1", "utf-8", $rs->fields['DESCRIPCION']);
                    $it += 1;
                    $rs->MoveNext();
                }
                $rs->Close();
                unset($rs); unset($sql);
                
                $respuesta = json_encode($vector);
            }
        }
        elseif ($_POST['idtabla'] == 4) {
            $sql = "SELECT MREC_DESC AS NOMBRE, MREC_CODI AS ID, MREC_ESTADO AS ESTADO, MREC_DESCRIPCION AS DESCRIPCION FROM MEDIO_RECEPCION WHERE MREC_CODI = " . $codigo . " ORDER BY 1";
            $rs = $conn->Execute($sql);
            if ($rs) {
                $it = 0;
                while (!$rs->EOF)
                {	
                    $vector[$it]['ID'] = $rs->fields['ID'];
                    $vector[$it]['NOMBRE'] = $rs->fields['NOMBRE'];
                    $vector[$it]['ESTADO'] = $rs->fields['ESTADO'];
                    $vector[$it]['DESCRIPCION'] = $rs->fields['DESCRIPCION'];
                    $it += 1;
                    $rs->MoveNext();
                }
                $rs->Close();
                unset($rs); unset($sql);
                
                $respuesta = json_encode($vector);
            }
        }
        elseif ($_POST['idtabla'] == 5) {
            $sql = "SELECT SGD_INFPOB_DESC AS NOMBRE, ID_INFPOB AS ID, SGD_INFPOB_ACTIVO AS ESTADO FROM SGD_INF_INFPOB WHERE ID_INFPOB = " . $codigo . " ORDER BY 1";
            $rs = $conn->Execute($sql);
            if ($rs) {
                $it = 0;
                while (!$rs->EOF)
                {
                    $vector[$it]['ID'] = $rs->fields['ID'];
                    $vector[$it]['NOMBRE'] = $rs->fields['NOMBRE'];
                    $vector[$it]['ESTADO'] = $rs->fields['ESTADO'];
                    $it += 1;
                    $rs->MoveNext();
                }
                $rs->Close();
                unset($rs); unset($sql);
                
                $respuesta = json_encode($vector);
            }
        }
    }
    elseif($_POST['consulta']) {
    	//$fenv_codi = escapeshellarg($_POST['fenv_codi']);
        
        $sqlTips =	"SELECT SGD_TRAD_CODIGO as ID FROM SGD_FRMENVIO_TIPOSRAD WHERE SGD_FENV_CODIGO = ". $_POST['fenv_codi'];
        $vecTips = $conn->query($sqlTips);
        if ($vecTips) {
            while(!$vecTips->EOF) {
                $datos[] = $vecTips->fields["ID"];
                $vecTips->MoveNext();
            }
                
            $respuesta = json_encode($datos);        
        }
    }
    elseif($_POST['filtrar']) {
        $origen = $_POST['origen'];
        $idtabla = escapeshellarg($_POST['idtabla']);
        
        if ($_POST['idtabla'] == 3) {
            $sql = "SELECT SGD_FENV_DESCRIP AS NOMBRE, SGD_FENV_CODIGO AS ID FROM SGD_FENV_FRMENVIO WHERE SGD_FENV_ORIGEN = ". $origen . " ORDER BY 1";

        } 
        elseif ($_POST['idtabla'] == 4) 
        {
            $sql = "SELECT MREC_DESC AS DESCRIP, MREC_CODI AS ID FROM MEDIO_RECEPCION WHERE MREC_ORIGEN = " . $origen . " ORDER BY 1";
        }
        
        $rs = $conn->query($sql);
        if ($rs)
        {
            $tmp1 = ":&lt;&lt;SELECCIONE&gt;&gt;";
            $tmp2 = "Onchange='Actual(" . $_POST['idtabla'] . ")'";
            $respuesta = $rs->GetMenu('slc_cmb2',false,$tmp1,false,false,"id='slc_cmb2' class='select' $tmp2 ");
            unset($rs); unset($tmp1); unset($tmp2);
        }
    }
    elseif($_POST['municipios']) {
        $codigo = escapeshellarg($_POST['codigo']);
        $idcont = escapeshellarg($_POST['idcont']);
        $idpais = escapeshellarg($_POST['idpais']);
        
        $sql = "SELECT MUNI_NOMB AS NOMBRE, MUNI_CODI AS ID FROM MUNICIPIO WHERE DPTO_CODI = " . $codigo . " AND ID_PAIS = " . $idpais . " AND ID_CONT = " .$idcont . " ORDER BY 1";
        $rs = $conn->Execute($sql);
        if ($rs) {
            $tmp1 = ":&lt;&lt;SELECCIONE&gt;&gt;";
            $tmp2 = "Onchange='borra_datos();Actual(this.value)'";
            $respuesta = $rs->GetMenu('municipio',false,$tmp1,false,false,"id='municipio' class='select' $tmp2 ");
            unset($rs); unset($tmp1); unset($tmp2);
        }
    }
    elseif($_POST['paises']) {
        $codigo = escapeshellarg($_POST['codigo']);
        
        $sql = "SELECT NOMBRE_PAIS AS NOMBRE, ID_PAIS AS ID FROM SGD_DEF_PAISES WHERE ID_CONT = " . $codigo . " ORDER BY 1";
        $rs = $conn->Execute($sql);
        if ($rs) {
            $tmp1 = ":&lt;&lt; SELECCIONE &gt;&gt;";
            $tmp2 = "Onchange='borra_datos();deptos(this.value)'";
            $respuesta = $rs->GetMenu('idpais',false,$tmp1,false,false,"id='idpais' class='select' $tmp2 ");
            unset($rs); unset($tmp1); unset($tmp2);
        }
    }
    elseif($_POST['deptos']) {
        $codigo = escapeshellarg($_POST['codigo']);
        $idcont = escapeshellarg($_POST['idcont']);
        
        $sql = "SELECT DPTO_NOMB AS NOMBRE, DPTO_CODI AS ID FROM DEPARTAMENTO WHERE ID_PAIS = " . $codigo . " AND ID_CONT = " . $idcont . " ORDER BY 1";
        $rs = $conn->Execute($sql);
        if ($rs) {
            $tmp1 = ":&lt;&lt; SELECCIONE &gt;&gt;";
            $tmp2 = "Onchange='borra_datos();municipios(this.value)'";
            $respuesta = $rs->GetMenu('codep',false,$tmp1,false,false,"id='codep' class='select' $tmp2 ");
            unset($rs); unset($tmp1); unset($tmp2);
        }
    }
    elseif($_POST['municipio']) {
        $codigo = escapeshellarg($_POST['codigo']);
        $idcont = escapeshellarg($_POST['idcont']);
        $idpais = escapeshellarg($_POST['idpais']);
        $iddept = escapeshellarg($_POST['codep']);
        
        $sql = "SELECT m.MUNI_NOMB AS NOMBRE, m.MUNI_CODI AS ID, x.DEST472
                FROM MUNICIPIO m left join sgd_municipio_472 x on m.id_cont=x.id_cont and m.id_pais=x.id_pais and m.dpto_codi=x.dpto_codi and m.muni_codi=x.muni_codi
                WHERE m.MUNI_CODI = " .$codigo . " AND m.DPTO_CODI = " . $iddept . " AND m.ID_PAIS = " . $idpais . " AND m.ID_CONT = " .$idcont . " 
                ORDER BY 1";
        $rs = $conn->Execute($sql);
        if ($rs) {
            $it = 0;
            while (!$rs->EOF)
            {
                $vector[$it]['ID'] = $rs->fields['ID'];
                $vector[$it]['NOMBRE'] = $rs->fields['NOMBRE'];
                $vector[$it]['DEST472'] = $rs->fields['DEST472'];
                $it += 1;
                $rs->MoveNext();
            }
            $rs->Close();
            unset($rs); unset($sql);
            
            $respuesta = json_encode($vector);
        }
    }
    else {
        $respuesta = "Faltan datos básicos";
    }
} else {
    $respuesta = "No hay conexion con la base de datos";
}

echo $respuesta;

?>