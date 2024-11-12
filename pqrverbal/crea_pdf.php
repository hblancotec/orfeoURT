<?php
/**
* Fuente que se encarga de crear un PDF con base en los datos gestionados en index.php.
* Requiere la clase FPDF (http://fpdf.org).
*
* @autor	Grupo Iyunxi Ltda <info@iyu.com.co>
* @version	1.0
*/

$ruta_raiz = "..";
include_once "$ruta_raiz/config.php";

/**
* Ruta donde se encuentra en proyecto FPDF
* @var <string>
*/
//$rutaFPDF = "$ruta_raiz/fpdf_v";

/**
* Ruta donde se encuentra la carpeta font del proyecto FPDF
*/
//define('FPDF_FONTPATH',"$rutaFPDF/font/");

/*
* Invocamos la clase FPDF
*/
require "fpdf/fpdf.php";

/**
 * Acá TOCA TOCA TOCA utilizar el adodb ya que las clases que invocaremos tienen 
 * su lógica asociada al objecto connectionHandler.
 */
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");

if ($db) {
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	// Invocamos la clase Municipio para traer nombres de internacionalizacion
	require "$ruta_raiz/jh_class/funciones_sgd.php";
		
	$a = new LOCALIZACION($cmbDpto,$cmbMcpio,$db);
	$txtDpto = $a->departamento;
	$txtMcpio = $a->municipio;
	
	$sql = "SELECT TDID_DESC AS TIPODESC FROM TIPO_DOC_IDENTIFICACION WHERE TDID_CODI=".$tipoDoc;
	$varTipoDoc = $db->conn->GetOne($sql);
	
	$sql = "SELECT SGD_INFPOB_DESC AS TIPODESC FROM SGD_INF_INFPOB WHERE ID_INFPOB=".$_POST['cmbRazas'];
	$varRaza = $db->conn->GetOne($sql);	
} else {
	die("Error de conexi&oqacute;n a BD");
}

/**
* La clase PDF_PQR extiende la clase FPDF adicionando metodo que imprime codigo de barras y pie de pagina.
*/
class PDF_PQR extends FPDF
{
    /**
    * Funcion que imprime un numero en formato de codigo de barras code3of9
    * @param <integer> $xpos  Posicion x en pixeles
    * @param <integer> $ypos  Posicion y en pixeles
    * @param <integer> $code  Codigo de radicado a imprimir.
    * @param <decimal> $baseline	Espacio ocupado por cada caracter del codigo de barras generado.
    * @param <integer> $height	Alto (tamaño) del codigo de barras generado.
    */
    function Code39($xpos, $ypos, $code=0, $baseline=0.6, $height=8)
    {
        $wide = $baseline;
        $narrow = $baseline / 3 ;
        $gap = $narrow;

        $barChar['0'] = 'nnnwwnwnn';
        $barChar['1'] = 'wnnwnnnnw';
        $barChar['2'] = 'nnwwnnnnw';
        $barChar['3'] = 'wnwwnnnnn';
        $barChar['4'] = 'nnnwwnnnw';
        $barChar['5'] = 'wnnwwnnnn';
        $barChar['6'] = 'nnwwwnnnn';
        $barChar['7'] = 'nnnwnnwnw';
        $barChar['8'] = 'wnnwnnwnn';
        $barChar['9'] = 'nnwwnnwnn';
        $barChar['A'] = 'wnnnnwnnw';
        $barChar['B'] = 'nnwnnwnnw';
        $barChar['C'] = 'wnwnnwnnn';
        $barChar['D'] = 'nnnnwwnnw';
        $barChar['E'] = 'wnnnwwnnn';
        $barChar['F'] = 'nnwnwwnnn';
        $barChar['G'] = 'nnnnnwwnw';
        $barChar['H'] = 'wnnnnwwnn';
        $barChar['I'] = 'nnwnnwwnn';
        $barChar['J'] = 'nnnnwwwnn';
        $barChar['K'] = 'wnnnnnnww';
        $barChar['L'] = 'nnwnnnnww';
        $barChar['M'] = 'wnwnnnnwn';
        $barChar['N'] = 'nnnnwnnww';
        $barChar['O'] = 'wnnnwnnwn';
        $barChar['P'] = 'nnwnwnnwn';
        $barChar['Q'] = 'nnnnnnwww';
        $barChar['R'] = 'wnnnnnwwn';
        $barChar['S'] = 'nnwnnnwwn';
        $barChar['T'] = 'nnnnwnwwn';
        $barChar['U'] = 'wwnnnnnnw';
        $barChar['V'] = 'nwwnnnnnw';
        $barChar['W'] = 'wwwnnnnnn';
        $barChar['X'] = 'nwnnwnnnw';
        $barChar['Y'] = 'wwnnwnnnn';
        $barChar['Z'] = 'nwwnwnnnn';
        $barChar['-'] = 'nwnnnnwnw';
        $barChar['.'] = 'wwnnnnwnn';
        $barChar[' '] = 'nwwnnnwnn';
        $barChar['*'] = 'nwnnwnwnn';
        $barChar['$'] = 'nwnwnwnnn';
        $barChar['/'] = 'nwnwnnnwn';
        $barChar['+'] = 'nwnnnwnwn';
        $barChar['%'] = 'nnnwnwnwn';

        $this->SetFont('Times','',10);
        $this->Text($xpos, $ypos + $height + 4, "Radicado ".$code);
        $this->SetFillColor(0);

        $code = '*'.strtoupper($code).'*';
        for($i=0; $i<strlen($code); $i++)
        {
            $char = $code{$i};
            if(!isset($barChar[$char]))
            {
                $this->Error('Invalid character in barcode: '.$char);
            }
            $seq = $barChar[$char];
            for($bar=0; $bar<9; $bar++)
            {
                if($seq{$bar} == 'n')
                {
                    $lineWidth = $narrow;
                }else{
                    $lineWidth = $wide;
                }
                if($bar % 2 == 0)
                {
                    $this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
                }
                $xpos += $lineWidth;
            }
            $xpos += $gap;
        }
    }

    /**
    * Imprime en el pie de pagina la numeracion de esta.
    */
    function Footer()
    {
        //Posicion: a 1,5 cm del final
        $this->SetY(-15);
        //Arial italic 8
        $this->SetFont('Arial','I',8);
        //Numero de pagina
        $this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
    }

}

foreach ($_POST as $key => $var)  ${$key} = $var;

require_once($ruta_raiz."/radsalida/masiva/OpenDocText.class.php");
$odt = new OpenDocText();
$pdf=new PDF_PQR('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetTitle("Documento PQR");
$pdf->SetAuthor("DNP");
$pdf->SetSubject($txtAsunto);
$pdf->SetKeywords("$tipoPQR");
$pdf->SetCreator("Departamento Nacional de Planeación");
$pdf->SetMargins(30, 40, 30);
$pdf->AddPage();
if ($radicado) $pdf->Code39(130,20, $radicado);
$pdf->SetFont('Arial','',10);
ini_set('date.timezone','America/Bogota');
setlocale(LC_TIME, 'es_CO');
$dias = array("Domingo","lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
$meses= array("","enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
$datos = array();
$datos[] = "Fecha del documento: ".$dias[date('w')]." ".date('j'). " de " . $meses[date('n')]. " de ".date('Y');
$datos[] = ""; $datos[] = "";
$datos[] = "Señores:";
$datos[] = "Departamento Nacional de Planeación.";
$datos[] = "$entidad";
$datos[] = "";
$datos[] = "Asunto: $txtAsunto";
$datos[] = "";
$datos[] = "$txtComentario";
$datos[] = "";
$datos[] = "PD: Adicionalmente libremente declaro que:";
$datos[] = "1. La respuesta a este comunicado me sea entregada vía " . ($_POST['tipoResp']=='V' ? "Correo Electrónico." : "Dirección de correspondencia.");
$datos[] = "2. Pertenezco a la población " . $varRaza . ".";
$datos[] = "";
if (is_array($listaAnexos)) {
	$datos[] = "Anexo archivo(s):";
	foreach ($listaAnexos as $key => $value) {
		$datos[] = substr($value, 29);
	}
}
$datos[] = "";
$datos[] = "Atentamente,";
$datos[] = "";
$datos[] = "$txtNombre $txtApellido";
$datos[] = $varTipoDoc." $txtDocumento";
if (!empty($_POST['txtCorreo'])) {
    $datos[] = "C. Electrónico: $txtCorreo";
}
$datos[] = "Dirección: $txtDireccion";
$datos[] = "Código Postal: $CodPostal";
$datos[] = "$txtDpto - $txtMcpio"; $datos[] = ""; $datos[] = "";
$datos[] = "Consulte el estado de su radicado en la dirección Web https://pqrsd.dnp.gov.co/consulta.php";
$cntDatos = count($datos);
foreach ($datos as $key => $valor) {
    $valorN = iconv($odt->codificacion($valor), 'ISO-8859-1',$valor);
    switch ($key) {
        case $cntDatos: {
        		$pdf->SetFont('Times','',10);
                $border = 1;
            }
        case 9:
        case 7:    $pdf->MultiCell(0, 7, $valorN, $border);break;
        default:    $pdf->Cell(40,7,$valorN, 0, 1); break;
    }
}
$pdf->Output("$directorio/$radicado.pdf", 'F');
?>