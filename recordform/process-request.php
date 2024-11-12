<?php

require("classes/csql.php");


if(!isset($_REQUEST["cmd"])) {
	return;
}

$objects = array(
	// LABELS
	"SGD_PQR_MASTER"=>array(
		 "table"=>"SGD_PQR_MASTER"
		,"idName"=>"ID"
		,"fields"=>array(
			 "ID"
			,"SGD_PQR_TPD"
			,"SGD_PQR_LABEL"
			,"SGD_PQR_DEPE"
			,"SGD_PQR_USUA"
			,"SGD_PQR_DESCRIP"			
		)
	)
	,"DEPENDENCIA"=>array(
		"table"=>"DEPENDENCIA"
		,"idName"=>"DEPE_CODI"
		,"groupBy"=>"DEPENDENCIA.DEPE_CODI"
		,"fields"=>array(
			  "DEPE_CODI"
			 ,"DEPE_NOMB"			 
		)
	)	
);

// create PDO object and execute command
$osql = new csql("mssql");
$_REQUEST["cmd"]($osql);

// command processors
// {{{
/**
  * getData: Outputs data to client
  *
  * @author    Ing. Jozef Sakáloš <jsakalos@aariadne.com>
  * @date      31. March 2008
  * @return    void
  * @param     PDO $osql
  */
function getData($osql) {
	global $objects;	
	$params = $objects[$_REQUEST["objName"]];
	$params["start"] = isset($_REQUEST["start"]) ? $_REQUEST["start"] : null;
	$params["limit"] = isset($_REQUEST["limit"]) ? $_REQUEST["limit"] : null;
	$params["search"] = isset($_REQUEST["fields"]) ? json_decode($_REQUEST["fields"]) : null;
	$params["query"] = isset($_REQUEST["query"]) ? $_REQUEST["query"] : null;
	$params["sort"] = isset($_REQUEST["sort"]) ? $_REQUEST["sort"] : null;
	$params["dir"] = isset($_REQUEST["dir"]) ? $_REQUEST["dir"] : null;	
	$response = array(
		 "success"=>true
		,"totalCount"=>$osql->getCount($params)
		,"rows"=>$osql->getData($params)
	);	
	$osql->output($response);

} // eo function getData

/**
  * saveData: saves data to table
  *
  * @author    Ing. Jozef Sakáloš <jsakalos@aariadne.com>
  * @date      02. April 2008
  * @return    void
  * @param     PDO $osql
  */
function saveData($osql) {
        global $objects;
        $params = $objects[$_REQUEST["objName"]];
	unset($params["fields"]);
	$data = str_replace('\\', '', $_REQUEST["data"]);
	$params["data"] = json_decode(stripslashes($_REQUEST["data"]));	
	$osql->output($osql->saveData($params));
}

function deleteData($osql) {
    global $objects;
    $params = $objects[$_REQUEST["objName"]];
    unset($params["fields"]);
    $params["data"] = json_decode(stripslashes($_REQUEST["data"]));
    $osql->output($osql->deleteData($params));
}


function data_dependencia($osql) {
  $paramscount = array("table" => "DEPENDENCIA");
  $osqlcount   = new csql("mssql");
  
  $sql         = "select DEPE_CODI,DEPE_NOMB from DEPENDENCIA order by DEPE_NOMB";
  $ostmt       = $osql->odb->query($sql);  
  $response = array(
		 "success"    => true
		,"totalCount" => $osqlcount->getCount($paramscount)
		,"rows"       => $ostmt->fetchAll(PDO::FETCH_OBJ)
   );
  $osql->output($response);
}

function data_usuario($osql) {
  $paramscount = array("table" => "USUARIO");
  $osqlcount   = new csql("mssql");
  $dep         = "";
  if ($_REQUEST["dep"]) {
     $dep = $_REQUEST["dep"];   
  }
  $sql = "select USUA_CODI, USUA_NOMB from USUARIO";
  if ($dep != "") {
     $sql .= " where DEPE_CODI = $dep"; 
  }
  $sql .= " order by USUA_NOMB";
  $ostmt       = $osql->odb->query($sql);  
  $response = array(
		 "success"    => true
		,"totalCount" => $osqlcount->getCount($paramscount)
		,"rows"       => $ostmt->fetchAll(PDO::FETCH_OBJ)
   );
  $osql->output($response);
}

function data_tipo($osql) {
  $paramscount = array("table" => "SGD_TPR_TPDCUMENTO");
  $osqlcount   = new csql("mssql");
  
  $sql         = "select SGD_TPR_CODIGO, SGD_TPR_DESCRIP from SGD_TPR_TPDCUMENTO order by SGD_TPR_DESCRIP";
  $ostmt       = $osql->odb->query($sql);  
  $response = array(
		 "success"    => true
		,"totalCount" => $osqlcount->getCount($paramscount)
		,"rows"       => $ostmt->fetchAll(PDO::FETCH_OBJ)
   );
  $osql->output($response);
}

?>