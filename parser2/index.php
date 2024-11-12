<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
//ini_set("include_path", "D:/inetpub/wwwroot/parser2/lib/");

// Include Composer autoloader if not already done.
include 'Smalot/PdfParser/Parser.php';
include 'Smalot/PdfParser/PDFObject.php';
include 'Smalot/PdfParser/Pages.php';
include 'Smalot/PdfParser/Document.php';
include 'Smalot/PdfParser/Element.php';
include 'Smalot/PdfParser/Element/ElementBoolean.php';
include 'Smalot/PdfParser/Element/ElementString.php';
include 'Smalot/PdfParser/Element/ElementArray.php';
include 'Smalot/PdfParser/Element/ElementDate.php';
include 'Smalot/PdfParser/Element/ElementHexa.php';
include 'Smalot/PdfParser/Element/ElementName.php';
include 'Smalot/PdfParser/Element/ElementMissing.php';
include 'Smalot/PdfParser/Element/ElementNumeric.php';
include 'Smalot/PdfParser/Element/ElementXRef.php';
include 'Smalot/PdfParser/Font.php';
include 'Smalot/PdfParser/Header.php';
include 'Smalot/PdfParser/Page.php';
include 'Smalot/PdfParser/XObject/Form.php';
include 'Smalot/PdfParser/XObject/Image.php';
include 'tecnickcom/tcpdf/tcpdf_parser.php';

// Parse pdf file and build necessary objects.
$parser = new \Smalot\PdfParser\Parser();
$pdf= $parser->parseFile('Certificado_ Id25355501_Cl400937_Mail37039146_20200601c244c1206_DocKO_Sr(20206000653401_1).pdf');
 
// Retrieve all details from the pdf file.
$details  = $pdf->getDetails();
 
// Loop over each property to extract values (string or array).
foreach ($details as $property => $value) {
	if (is_array($value)) {
		$value = implode(', ', $value);
	}
	echo $property . ' => ' . $value . "<br />";
}

// Retrieve all pages from the pdf file.
$pages  = $pdf->getPages();
     
// Loop over each page to extract text.
foreach ($pages as $page) {
    echo $page->getText();
}
?>