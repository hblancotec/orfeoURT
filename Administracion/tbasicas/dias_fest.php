<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "../../sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}
if ($_SESSION['usua_admin_sistema'] != 1){
	die(include "../../sinpermiso.php");
	exit;
}

$ruta_raiz = "../..";
include_once("$ruta_raiz/config.php"); // incluir configuracion.
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

function db_fetchAll($result) {
   $return = array();   
   while(!$result->EOF){
     $return[] = $result->fields;
     $result->MoveNext();
   };   
   return $return;
}


// trae los datos mssql server
function db_GetData($params, $db) {
    // params to variables
    extract($params);
        
    $sqlE = "select ";
    
    $limitE = " ";
    $sqlI   = " ";
    if(!is_null($start) && !is_null($limit)) {
        $limitI = " Top " . $start . " ";
        $limitE = " Top " . $limit . " ";
        $sqlI  = " where " . $sort . " ";
        $sqlI .= " not in ( select ";
        $sqlI .= $limitI;
        
        $sqlI .= $sort;
        $sqlI .= " from $table ";
        $sqlI .= isset($groupBy) && $groupBy ? " group by $groupBy" : "";
        if(!is_null($sort)) {
          $sqlI .= " order by $sort";
          $sqlI .= is_null($dir) ? "" : " $dir";
        }
        $sqlI .= " ) ";
    }
    
    $sqlE .= $limitE;
    $sqlE .= 'SGD_ID, SGD_FESTIVO';
    $sqlE .= " from SGD_DIAS_FESTIVOS order by SGD_FESTIVO" . $sqlI;    
    $sqlE .= isset($groupBy) && $groupBy ? " group by $groupBy" : "";
    
    if(!is_null($sort)) {
       $sqlE .= " order by $sort";
       $sqlE .= is_null($dir) ? "" : " $dir";
    }
    
    return db_fetchAll($db->conn->Execute($sqlE));
} 

// actualiza registro
function db_UpdateData($params, $db) {    
    $id          = isset($params["id"]) ? $params["id"] : null;
    $SGD_FESTIVO = isset($params["SGD_FESTIVO"]) ? $params["SGD_FESTIVO"] : null;        
    $sqlE = "update SGD_DIAS_FESTIVOS SET SGD_FESTIVO = '" .  $SGD_FESTIVO . "'" .
            " where SGD_ID = " . $id;    
    $db->conn->Execute($sqlE);
}

// crea registro
function db_InsertData($params, $db) {    
    $SGD_FESTIVO = isset($params["SGD_FESTIVO"]) ? $params["SGD_FESTIVO"] : null;
    $sqlE = "INSERT INTO SGD_DIAS_FESTIVOS (SGD_FESTIVO) VALUES ('" .  $SGD_FESTIVO . "')";    
    $db->conn->Execute($sqlE);
}

// elimina registro
function db_DeleteData($dellist, $db) {
   foreach ($dellist as $key => $id) {
      $sqlE = 'delete from SGD_DIAS_FESTIVOS where SGD_ID = ' . $id;    
      $db->conn->Execute($sqlE);
   }    
} 

if (!isset($_REQUEST["cmd"])) {
  return;
}
$_REQUEST["cmd"]($db);

// LEE TODOS LOS REGISTROS
function getData($db) {
        $params["start"]  = isset($_REQUEST["start"]) ? $_REQUEST["start"] : null;
	$params["limit"]  = isset($_REQUEST["limit"]) ? $_REQUEST["limit"] : null;
        $params["search"] = isset($_REQUEST["fields"]) ? json_decode($_REQUEST["fields"]) : array();
	$params["query"]  = isset($_REQUEST["query"]) ? $_REQUEST["query"] : null;
        $params["sort"]   = isset($_REQUEST["sort"]) ? $_REQUEST["sort"] : null;
	$params["dir"]    = isset($_REQUEST["dir"]) ? $_REQUEST["dir"] : null;
                
	$queryCount = "select count(*) as TOTAL from SGD_DIAS_FESTIVOS";
	$rsf = $db->conn->Execute($queryCount);
	$total = 0;
        if (!$rsf->EOF) {
	   $total = $rsf->fields["TOTAL"];          
        }		
        
	$response = array(
		 "success"=>true
		,"totalCount"=>$total
		,"rows"=>db_GetData($params, $db)
	);
	
	echo json_encode($response);	
} // eo function getData

// LEE UN REGISTRO
function editData($db) {
   $id       = isset($_REQUEST["id"]) ? $_REQUEST["id"] : null;	        
   $sqlE     = "select * from SGD_DIAS_FESTIVOS where SGD_ID=" . $id;	
   $response = array(
		      "success"    => true
	              ,"totalCount" => 1
		      ,"rows"       => db_fetchAll($db->conn->Execute($sqlE)
                    )
   );        
   echo json_encode($response);	
}

// crea o actualiza registro
function saveData($db) {
   $id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : null;
   if ($id != NULL) {
      db_UpdateData($_REQUEST, $db);
   }
   else {
      db_InsertData($_REQUEST, $db);
   }
   
   $response = array(
		      "success"    => true
	              ,"totalCount" => 1
		      ,"rows"       => 0
                    );
   echo json_encode($response);	
}

// elimina registros
function deleteData($db) {
   $delete = isset($_REQUEST["deleteIds"]) ? $_REQUEST["deleteIds"] : null;
   if ($delete != NULL) {
      $dellist = json_decode($delete);
      db_DeleteData($dellist, $db);
   }
   
   $response = array(
		      "success"    => true
	              ,"totalCount" => 1
		      ,"rows"       => 0
                    );
   
    echo json_encode($response);	
}
?>
