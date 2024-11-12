<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "./sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}

//defino una clase para los elementos del campo autocompletar
 class ElementoAutocompletar {
	//propiedades de los elementos
	var $value;
	var $label;

	//constructor que recibe los datos para inicializar los elementos
	function __construct($label, $value){
		$this->label = $label;
		$this->value = $value;
	}
}

//recibo el dato, del cual deseo buscar sugerencias
$datoBuscar = $_GET["term"];
$depe = $_GET["dpn"];
$seri = $_GET["sre"];
$subs = $_GET["sbs"];
$year = $_GET["an"];
$k	  = $_GET["k"];

//Realizo la conexión a la base de datos
$ruta_raiz= "";
include_once ("./config.php");
include_once ("./include/db/ConnectionHandler.php");
$db = new ConnectionHandler(ORFEOPATH);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);


$whereDep = "";
$whereSrd = "";
$whereSbrd = "";
$whereAno = "";

if ($depe)
	$whereDep = " AND DEPE_CODI = ".$depe;


if ($seri && $seri != 99999)
	$whereSrd = " AND SGD_SRD_CODIGO =".$seri;


if($subs && $subs != 99999)
	$whereSbrd = " AND SGD_SBRD_CODIGO =".$subs;


if($year != '55555')
	$whereAno = " AND SGD_SEXP_ANO =".$year;
else 
	$whereAno = " "; //AND SGD_SEXP_ANO = ".Date("Y");


//busco un valor aproximado al parametro recibido
$sql = "SELECT	SGD_SEXP_PAREXP1,
				SGD_EXP_NUMERO	
		FROM	SGD_SEXP_SECEXPEDIENTES
		WHERE	SGD_SEXP_ESTADO = 'FALSE' AND
				SGD_SEXP_PAREXP1 COLLATE SQL_Latin1_General_Cp1_CI_AI LIKE '%".$datoBuscar."%' AND
				dbo.VALIDAR_ACCESO_RADEXP(0,SGD_EXP_NUMERO,'".$k."') = 0
				$whereDep $whereSrd $whereSbrd $whereAno";
$rs = $db->conn->Execute($sql);

//creo el array para los elementos sugeridos
$arrayElementos = array();

//Ciclo para cargar un array con los valores sugeridos

while (!$rs->EOF){
	$valor = $rs->fields['SGD_SEXP_PAREXP1'];
	$dato = $rs->fields['SGD_EXP_NUMERO'].' - '.$rs->fields['SGD_SEXP_PAREXP1'];
	array_push($arrayElementos,new ElementoAutocompletar($dato, $dato));
	$rs->MoveNext();
}

//imprimo en la página la conversión a JSON del array anterior
print_r(json_encode($arrayElementos));
?>