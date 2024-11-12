<?php

$page = $_GET['page']; // get the requested page 
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort 
$sord = $_GET['sord']; // get the direction
$depe = $_GET['dep'];
if (!$sidx)
    $sidx = 1; // connect to the database

$ADODB_COUNTRECS = true;
require_once "../config.php";
require "adodb/adodb.inc.php";

$wh = "";
$searchOn = Strip($_REQUEST['_search']);
if ($searchOn == 'true') {
    $searchstr = Strip($_REQUEST['filters']);
    $jsona = json_decode($searchstr, true);
    $wh = " AND " . getStringForGroup($jsona);
}

function constructWhere($s) {
    $qwery = "";
    //['eq','ne','lt','le','gt','ge','bw','bn','in','ni','ew','en','cn','nc']
    $qopers = array(
        'eq' => " = ",
        'ne' => " <> ",
        'lt' => " < ",
        'le' => " <= ",
        'gt' => " > ",
        'ge' => " >= ",
        'bw' => " LIKE ",
        'bn' => " NOT LIKE ",
        'in' => " IN ",
        'ni' => " NOT IN ",
        'ew' => " LIKE ",
        'en' => " NOT LIKE ",
        'cn' => " LIKE ",
        'nc' => " NOT LIKE ");
    if ($s) {
        $jsona = json_decode($s, true);
        if (is_array($jsona)) {
            $gopr = $jsona['groupOp'];
            $rules = $jsona['rules'];
            $i = 0;
            foreach ($rules as $key => $val) {
                $field = $val['field'];
                $op = $val['op'];
                $v = $val['data'];
                if ($v && $op) {
                    $i++;
                    // ToSql in this case is absolutley needed
                    $v = ToSql($field, $op, $v);
                    if ($i == 1)
                        $qwery = " AND ";
                    else
                        $qwery .= " " . $gopr . " ";
                    switch ($op) {
                        // in need other thing
                        case 'in' :
                        case 'ni' :
                            $qwery .= $field . $qopers[$op] . " (" . $v . ")";
                            break;
                        default:
                            $qwery .= $field . $qopers[$op] . $v;
                    }
                }
            }
        }
    }
    return $qwery;
}

function getStringForGroup($group) {
    $i_ = '';
    $sopt = array('eq' => "=", 'ne' => "<>", 'lt' => "<", 'le' => "<=", 'gt' => ">", 'ge' => ">=", 'bw' => " {$i_}LIKE ", 'bn' => " NOT {$i_}LIKE ", 'in' => ' IN ', 'ni' => ' NOT IN', 'ew' => " {$i_}LIKE ", 'en' => " NOT {$i_}LIKE ", 'cn' => " {$i_}LIKE ", 'nc' => " NOT {$i_}LIKE ", 'nu' => 'IS NULL', 'nn' => 'IS NOT NULL');
    $s = "(";
    if (isset($group['groups']) && is_array($group['groups']) && count($group['groups']) > 0) {
        for ($j = 0; $j < count($group['groups']); $j++) {
            if (strlen($s) > 1) {
                $s .= " " . $group['groupOp'] . " ";
            }
            try {
                $dat = getStringForGroup($group['groups'][$j]);
                $s .= $dat;
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }
    if (isset($group['rules']) && count($group['rules']) > 0) {
        try {
            foreach ($group['rules'] as $key => $val) {
                if (strlen($s) > 1) {
                    $s .= " " . $group['groupOp'] . " ";
                }
                $field = $val['field'];
                $op = $val['op'];
                $v = $val['data'];
                if ($op) {
                    switch ($op) {
                        case 'bw':
                        case 'bn':
                            $s .= $field . ' ' . $sopt[$op] . "'$v%'";
                            break;
                        case 'ew':
                        case 'en':
                            $s .= $field . ' ' . $sopt[$op] . "'%$v'";
                            break;
                        case 'cn':
                        case 'nc':
                            $s .= $field . ' ' . $sopt[$op] . "'%$v%'";
                            break;
                        case 'in':
                        case 'ni':
                            $s .= $field . ' ' . $sopt[$op] . "( '$v' )";
                            break;
                        case 'nu':
                        case 'nn':
                            $s .= $field . ' ' . $sopt[$op] . " ";
                            break;
                        default :
                            $s .= $field . ' ' . $sopt[$op] . " '$v' ";
                            break;
                    }
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    $s .= ")";
    if ($s == "()") {
        //return array("",$prm); // ignore groups that don't have rules
        return " 1=1 ";
    } else {
        return $s;
        ;
    }
}

function ToSql($field, $oper, $val) {
    // we need here more advanced checking using the type of the field - i.e. integer, string, float
    switch ($field) {
        case 'id':
            return intval($val);
            break;
        case 'amount':
        case 'tax':
        case 'total':
            return floatval($val);
            break;
        default :
            //mysql_real_escape_string is better
            if ($oper == 'bw' || $oper == 'bn')
                return "'" . addslashes($val) . "%'";
            else if ($oper == 'ew' || $oper == 'en')
                return "'%" . addcslashes($val) . "'";
            else if ($oper == 'cn' || $oper == 'nc')
                return "'%" . addslashes($val) . "%'";
            else
                return "'" . addslashes($val) . "'";
    }
}

$error = 0;
$dsn = $driver . "://$usuario:$contrasena@$servidor/$servicio";
$conn = NewADOConnection($dsn);

if ($conn->connect()) {
    $conn->SetFetchMode(ADODB_FETCH_ASSOC);
} else
    die("Error al conectar BD");

function mssql_real_escape_string($s) {
    if (get_magic_quotes_gpc()) {
        $s = stripslashes($s);
    }
    $s = str_replace("'", "''", $s);
    return $s;
}

$sql = "";

//$radiPath = $conn->Concat($conn->substr . "(a.anex_codigo,1,4) ", "'/'", $conn->substr . "(a.anex_codigo,5,3) ", "'/docs/'", "a.anex_nomb_archivo");
$copia = $conn->substr . '(convert(char(3),a.sgd_dir_tipo),2,3)';
$idr = $conn->Concat('a.radi_nume_salida', "'_'", $copia);
$sql = "select	$idr as idr, a.radi_nume_salida as radicado, $copia as copia, d.sgd_dir_nomremdes as destinatario, d.sgd_dir_direccion as direccion, d.sgd_dir_codpostal as codpostal, 
	d.sgd_dir_mail as email, m.muni_nomb as municipio, t.dpto_nomb as departamento, p.nombre_pais as pais, c.RADI_PATH as ruta,
	a.anex_desc as descripcion, d.sgd_dir_codigo as dircodigo  
    from ANEXOS a 
        inner join radicado c on a.radi_nume_salida = c.radi_nume_radi and c.radi_depe_radi=$depe 
	inner join sgd_dir_drecciones d on a.radi_nume_salida=d.radi_nume_radi and a.sgd_dir_tipo = d.sgd_dir_tipo
	inner join sgd_def_paises p on d.id_cont=p.id_cont and d.id_pais=p.id_pais
	inner join departamento t on d.id_cont=t.id_cont and d.id_pais=t.id_pais and d.dpto_codi=t.dpto_codi
	inner join municipio m on d.id_cont=m.id_cont and d.id_pais=m.id_pais and d.dpto_codi=m.dpto_codi and d.muni_codi=m.muni_codi
where a.anex_estado=3 and a.anex_borrado='N' and 
	(	( a.sgd_deve_codigo <=0 and a.sgd_deve_codigo <=99 ) OR a.sgd_deve_codigo IS NULL )
			and (	(c.sgd_eanu_codigo <> 2 and c.sgd_eanu_codigo <> 1) or c.sgd_eanu_codigo IS NULL) $wh ";
$rs = $conn->Execute($sql);

$ADODB_COUNTRECS = false;

$count = $rs->RecordCount();

if ($count > 0) {
    $total_pages = ceil($count / $limit);
} else {
    $total_pages = 0;
}

if ($page > $total_pages)
    $page = $total_pages;
$start = $limit * $page - $limit;
//$start = ($page==1) ? 1 : (($page-1)+$limit)+1;

$sql .= " order by $sidx $sord OFFSET $start Rows Fetch NEXT $limit ROWS ONLY";
$rs = $conn->Execute($sql);

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i = 0;
while ($row = $rs->FetchRow()) {
    $responce->rows[$i]['id'] = (substr(trim($row['idr']), -1) == '_') ? trim($row['idr']) . "00" : trim($row['idr']);
    $responce->rows[$i]['cell'] = array($row['radicado'], trim($row['copia']), $row['destinatario'], $row['direccion'], $row['codpostal'], $row['email'], $row['municipio'], $row['departamento'], $row['pais'], $row[ruta], $row[dircodigo]);
    $i++;
}

echo json_encode($responce);

function Strip($value) {
    if (get_magic_quotes_gpc() != 0) {
        if (is_array($value))
            if (array_is_associative($value)) {
                foreach ($value as $k => $v)
                    $tmp_val[$k] = stripslashes($v);
                $value = $tmp_val;
            }
            else
                for ($j = 0; $j < sizeof($value); $j++)
                    $value[$j] = stripslashes($value[$j]);
        else
            $value = stripslashes($value);
    }
    return $value;
}

function array_is_associative($array) {
    if (is_array($array) && !empty($array)) {
        for ($iterator = count($array) - 1; $iterator; $iterator--) {
            if (!array_key_exists($iterator, $array)) {
                return true;
            }
        }
        return !array_key_exists(0, $array);
    }
    return false;
}

?>