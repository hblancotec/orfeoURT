<?php
require("classes/csql.php");

if(!isset($_REQUEST["cmd"])) {
	return;
}

$objects = array(
	// LABELS
	"SGD_TEM_NOMBRES"=>array(
		 "table"=>"SGD_TEM_NOMBRES"
		,"idName"=>"ID"
		,"fields"=>array(
			 "ID"
			,"SGD_TEMA_NOMBRE"
			,"SGD_TEMA_ACTIVO"
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
	//fb( $params);
    //fb( $params["data"]);
	$osql->output($osql->saveData($params));
}

function deleteData($osql) {
    global $objects;
    $params = $objects[$_REQUEST["objName"]];
    unset($params["fields"]);
    $params["data"] = json_decode(stripslashes($_REQUEST["data"]));
    //fb( $params);
    //fb( $params["data"]);
    //$osql->output($osql->deleteData($params));
}

?>