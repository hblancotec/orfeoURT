<?php
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$sql = "select	m.id_cont as IDC, c.nombre_cont as NOMBREC, m.id_pais as IDP, p.nombre_pais as NOMBREP,
		m.dpto_codi as IDD, d.dpto_nomb as NOMBRED, m.muni_codi as IDM, m.muni_nomb as NOMBREM, x.dest472 as DEST472 
        from municipio m
        left join departamento d on m.dpto_codi=d.dpto_codi and m.id_pais=d.id_pais
        inner join sgd_def_paises p on d.id_pais=p.id_pais and d.id_cont=p.id_cont
        inner join sgd_def_continentes c on p.id_cont=c.id_cont
		left join sgd_municipio_472 x on m.id_cont=x.id_cont and m.id_pais=x.id_pais and m.dpto_codi=x.dpto_codi and m.muni_codi=x.muni_codi
        order by nombrec, nombrep, nombred, nombrem";
$rsx = $db->conn->Execute($sql);
$vcontiv = array();
$vpaisesv = array();
$vdptosv = array();
$vmcposv = array();
while ($row = $rsx->FetchRow()) {
    if (!array_key_exists($row["IDC"], $vcontiv)) $vcontiv[$row["IDC"]] = $row["NOMBREC"];
    if (searchForId($row["IDP"], $vpaisesv) === NULL) {
       $vpaisesv[] = array('NOMBRE'=>$row["NOMBREP"], 'ID0'=>$row["IDC"], 'ID1'=>$row["IDP"]);
    }
    if (searchForId($row["IDP"]."-".$row["IDD"], $vdptosv) === NULL) {
        $vdptosv[] = array('NOMBRE'=>$row["NOMBRED"], 'ID0'=>$row["IDP"], 'ID1'=> $row["IDP"]."-".$row["IDD"]);
    }
    if (searchForId($row["IDP"]."-".$row["IDD"]."-".$row["IDM"], $vmcposv) === NULL) {
        $vmcposv[] = array('NOMBRE'=>$row["NOMBREM"], 'ID0'=>$row["IDD"], 'ID'=> $row["IDP"], 'ID1'=>$row["IDP"]."-".$row["IDD"]."-".$row["IDM"]);
    }
} 

function searchForId($id, $array) {
    foreach ($array as $key => $val) {
        if ($val['ID1'] === $id) {
            return $key;
        }
    }
    return null;
}

//	Funcion que convierte un valor de PHP a un valor Javascript.
function valueToJsValue($value, $encoding = false) {
	if (!is_numeric($value)) {
		$value = str_replace('\\', '\\\\', $value);
		$value = str_replace('"', '\"', $value);
		$value = '"'.$value.'"';
	}
	
	if ($encoding) {
		switch ($encoding) {
			case 'utf8' :
				return iconv("ISO-8859-2", "UTF-8", $value);
				break;
		}
	} else {
		return $value;
	}
	return ;
}

/*
 *	Funcion que convierte un vector de PHP a un vector Javascript.
 *	Utiliza a su vez la funcion valueToJsValue.
 */
function arrayToJsArray( $array, $name, $nl = "\n", $encoding = false )
{	if (is_array($array))
	{	$jsArray = $name . ' = new Array();'.$nl;
	foreach($array as $key => $value)
	{	switch (gettype($value))
		{	case 'unknown type':
			case 'resource':
			case 'object':	break;
			case 'array':	$jsArray .= arrayToJsArray($value,$name.'['.valueToJsValue($key, $encoding).']', $nl);
			break;
			case 'NULL':	$jsArray .= $name.'['.valueToJsValue($key,$encoding).'] = null;'.$nl;
			break;
			case 'boolean':	$jsArray .= $name.'['.valueToJsValue($key,$encoding).'] = '.($value ? 'true' : 'false').';'.$nl;
			break;
			case 'string':	$jsArray .= $name.'['.valueToJsValue($key,$encoding).'] = '.valueToJsValue($value, $encoding).';'.$nl;
			break;
			case 'double':
			case 'integer':	$jsArray .= $name.'['.valueToJsValue($key,$encoding).'] = '.$value.';'.$nl;
			break;
			default:	trigger_error('Hoppa, egy j t�us a PHP-ben?'.__CLASS__.'::'.__FUNCTION__.'()!', E_USER_WARNING);
		}
	}
	return $jsArray;
}
else
{	return false;	}
}
?>