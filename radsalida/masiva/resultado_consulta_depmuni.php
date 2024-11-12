<?php
    /**
     * Programa que despliega el resultado de la consulta, segun los parametros enviados desde consulta_depmuni.php
     * @author      Sixto Angel Pinzon
     * @version     1.0
     */
    session_start();
    $ruta_raiz = "../..";
    $localizacion = array();
    $localizacion["continente"]["america"] = 1;
    $localizacion["continente"]["pais"]["colombia"] = 170;

    require_once("$ruta_raiz/include/db/ConnectionHandler.php"); 
    if (!$db)	$db = new ConnectionHandler($ruta_raiz);
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);	
    include "$ruta_raiz/jh_class/funciones_sgd.php";

    //Si no llega la dependencia recupera la sesion 
    if(!$_SESSION['dependencia']) include "$ruta_raiz/rec_session.php"; 
    $consulta = $_POST['consulta'];
?>
<html>
<head>
<title>Consulta de DIVIPOLA</title>
<link rel="stylesheet" href="../../estilos/orfeo.css">
</head>
<body>
<?php
//variable que almacena los datos de la sesion 
$phpsession = session_name()."=".session_id();
//variable que almacena el dato a consultar
$consulta = strtoupper($consulta);
$cat = $db->conn->concat_operator;
$q = '';

switch ($db->driver) {
    case 'mssqlnative' :
        $q = "SELECT RTRIM(SGD_DEF_CONTINENTES.ID_CONT) AS IDP
                FROM SGD_DEF_CONTINENTES WHERE SGD_DEF_CONTINENTES.NOMBRE_CONT LIKE '%$consulta%'
            UNION
              SELECT RTRIM(SGD_DEF_PAISES.ID_CONT) 
                        $cat '-' $cat
                        RTRIM(SGD_DEF_PAISES.ID_PAIS) AS IDP
                FROM SGD_DEF_PAISES WHERE SGD_DEF_PAISES.NOMBRE_PAIS LIKE '%$consulta%'
            UNION
              SELECT RTRIM(DEPARTAMENTO.ID_CONT)
                        $cat '-' $cat
                        RTRIM(DEPARTAMENTO.ID_PAIS)
                        $cat '-' $cat
                        RTRIM(DEPARTAMENTO.DPTO_CODI) AS IDP 
                FROM DEPARTAMENTO WHERE DEPARTAMENTO.DPTO_NOMB LIKE '%$consulta%'
            UNION
              SELECT RTRIM(MUNICIPIO.ID_CONT)
                        $cat '-' $cat
                        RTRIM(MUNICIPIO.ID_PAIS)
                        $cat '-' $cat
                        RTRIM(MUNICIPIO.DPTO_CODI)
                        $cat '-' $cat
                        RTRIM(MUNICIPIO.MUNI_CODI) AS IDP 
                FROM MUNICIPIO WHERE MUNICIPIO.MUNI_NOMB LIKE '%$consulta%'";
        break;
    default : // Para Oracle
        $q = "SELECT  SGD_DEF_CONTINENTES.ID_CONT AS ID_CONT,
                        SGD_DEF_PAISES.ID_PAIS,
                        dep.ID_PAIS || '-' || dep.DPTO_CODI AS depto,
                        mun.ID_PAIS || '-' || mun.DPTO_CODI || '-' || mun.MUNI_CODI AS municip
                    FROM SGD_DEF_CONTINENTES, SGD_DEF_PAISES,  DEPARTAMENTO dep, MUNICIPIO mun
                    WHERE (dep.DPTO_NOMB LIKE '%$consulta%' OR mun.MUNI_NOMB LIKE '%$consulta%' ) and
                            mun.DPTO_CODI = dep.DPTO_CODI";
}

$rs = $db->conn->Execute($q);
unset($q);
?>
<form action="consulta_depmuni.php?<?=$phpsession?>&krd<?=$krd?>&dependencia=<?=$dependencia?>" method="post" enctype="multipart/form-data" name="formAdjuntarArchivos">
<table width='55%'  cellspacing="5"  align='center' class='borde_tab'>
	<tr align='center' class='titulos5'> 
		<td  class='titulos5' colspan='4'> 
        	RADICACION MASIVA <BR>CONSULTA DE LA DIVISION POLITICA ADMINISTRATIVA <BR>(DIVIPOLA)
        </td>
	</tr>
	<tr> 
		<td class="listado2" height="12" colspan="4"> 
        	<BR>Resultado de b&uacute;squeda: <?=$consulta ?><BR>
		</td>
	</tr>
	<tr> 
		<td width="10%" height="12" align="center" class="titulos3">Continente</td>
		<td width="30%" height="12" align="center" class="titulos3">Pa&iacute;s</td>
		<td width="30%" height="12" align="center" class="titulos3">Departamento</td>
		<td width="30%" height="12" align="center" class="titulos3">Municipio</td>
	</tr>
<?php
	if ($rs === false) {
		echo "<br><b>Error en consulta</b>";
        echo $db->conn->ErrorMsg();
        exit(1);
	}
	//Recorre la consulta para oracle
    
	while  (!$rs->EOF) {
        switch ($db->driver) {
            case 'mssqlnative' :
                $temp = explode('-', $rs->fields['IDP']);
                $a = new LOCALIZACION($temp[1].'-'.$temp[2],$temp[1].'-'.$temp[2].'-'.$temp[3],$db);
                //variables que guardan los datos resultado
                $codCont = $temp[0];
                $codPais = $temp[1];
                break;
            default :
                $codCont =  $rs->fields['ID_CONT'];
                $codPais = $rs->fields['ID_PAIS'];
                $a = new LOCALIZACION($rs->fields['DEPTO'], $rs->fields['MUNICIP'] ,$db );
		}
        $a->GET_NOMBRE_CONT($codCont,$db);
        $a->GET_NOMBRE_PAIS($codPais,$db);
?>
	<tr align="center"> 
		<td class="listado2" height="12" ><span class="etextomenu"><?=$a->continente ?></span></td>
		<td class="listado2" height="12" ><span class="etextomenu"><?=$a->pais ?></span></td>
		<td class="listado2" height="12" ><span class="etextomenu"><?=$a->departamento ?></span></td>
		<td class="listado2" height="12" ><span class="etextomenu"><?=$a->municipio ?></span></td>
	</tr>
<?php
		$rs->MoveNext();	
	 }
?>
	<tr align="center"> 
		<td height="30" class="listado2" colspan="4">
			<center>
			<input name="enviaPrueba" type="button"  class="botones" id="envia22"  onClick="document.formAdjuntarArchivos.action='menu_masiva.php?<?=$phpsession?>&krd=<?=$krd?>';document.formAdjuntarArchivos.submit();" value="Cerrar">
			<input name="consultar" type="SUBMIT"  class="botones" id="envia22"  value="Consultar"></center>
		</td>
	</tr>
</table>
</form>
</body>
</html>
