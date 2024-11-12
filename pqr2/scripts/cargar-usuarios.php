<?php
include("clases/class.sqlsrv.php");
include("clases/class.combos.php");

if ($_GET["tipo"] == '1') {
   
    $usuarios = new selects();
    if (is_array($_GET["code"])) {
        $usuarios->code = $_GET["code"][0];
        $idDep = $_GET["code"][0];
    } else {
        $usuarios->code = $_GET["code"];
        $idDep = $_GET["code"];
    }
    $datos = $usuarios->cargarUsuarios();
    
    foreach($datos as $key=>$value)
    {
        $idus = $idDep."-".$key;
        echo "<li id=\"$idus\">$value</option>";
    }
}
elseif ($_GET["tipo"] == '2') {
    
    $objMssql = new SQLSRV("../..");
    $borrar = $objMssql->consulta("DELETE FROM SGD_PQR_TEMAUSU WHERE SGD_DCAU_CODIGO = ".$_GET['tema']) ;
    if ($borrar) {
        
        $codes = explode(",", $_GET['code']);
        $j = 1;
        foreach ($codes as &$valor) {
            $dats = explode("-", $valor);
            $ok_Tdoc = $objMssql->consulta("INSERT INTO SGD_PQR_TEMAUSU (SGD_DCAU_CODIGO, DEPE_CODI, USUA_CODI, SGD_PQR_PRIORIDAD) 
                                VALUES(".$_GET['tema'].", ".$dats[0].", ".$dats[1].", $j )");
            $j += 1;
        }
        
        $ok_Tdoc = $objMssql->consulta("UPDATE SGD_DCAU_CAUSAL SET SGD_DCAU_DISTRIBUCION = '" . $_GET["cantidad"] . "' WHERE SGD_DCAU_CODIGO = " . $_GET['tema'] . "");
        
        echo $ok_Tdoc;
    }
    
} 
else {
    echo "<option value=\"\">Seleccione Usuario</option>";
    foreach($datos as $key=>$value)
    {
        echo "<option value=\"$key\" $selec>$value</option>";
    }
}
    
?>