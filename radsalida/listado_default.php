<?php
//define('FPDF_FONTPATH', '../fpdf/font/');
require "fpdf/fpdf.php";

$fecha_ini = $fecha_busq;
$fecha_fin = $fecha_busq;
$fecha_ini = mktime($hora_ini, $minutos_ini, 00, substr($fecha_ini, 5, 2), substr($fecha_ini, 8, 2), substr($fecha_ini, 0, 4));
$fecha_fin = mktime($hora_fin, $minutos_fin, 59, substr($fecha_fin, 5, 2), substr($fecha_fin, 8, 2), substr($fecha_fin, 0, 4));

$where_fecha = ' a.sgd_renv_fech BETWEEN ' . $db->conn->DBTimeStamp($fecha_ini) .
        ' AND ' . $db->conn->DBTimeStamp($fecha_fin) . ' AND DEPE_CODI= ' . $dependencia;
switch ($db->driver) {
    case 'mssqlnative': $tmp_var = "convert(char(15),a.RADI_NUME_SAL)";
        break;
    case 'oracle':
    case 'oci8': $tmp_var = "a.RADI_NUME_SAL";
        break;
}
$query = "select f.sgd_fenv_descrip, e.sgd_emp_descrip 
			from sgd_fenv_frmenvio f inner join sgd_fenv_empresas e on f.sgd_emp_codigo=e.sgd_emp_codigo
			where sgd_fenv_codigo=$codigo_envio";
$rs = $db->conn->Execute($query);
$nomFormEnvio = $rs->Fields('sgd_fenv_descrip');
$nomEmpEnvio = $rs->Fields('sgd_emp_descrip');

$query = "SELECT SGD_RENV_FECH, $tmp_var as RADI_NUME_SAL, SGD_DIR_TIPO,
				SGD_RENV_NOMBRE, SGD_RENV_DIR, SGD_RENV_PAIS, SGD_RENV_DEPTO,
				SGD_RENV_MPIO, SGD_RENV_TELEFONO, SGD_RENV_MAIL, SGD_RENV_PESO, 
				SGD_RENV_VALOR
		FROM SGD_RENV_REGENVIO a
		WHERE SGD_FENV_CODIGO = $codigo_envio AND $where_fecha AND SGD_RENV_PLANILLA=$no_planilla AND sgd_renv_tipo <2";
$rs = $db->conn->Execute($query);
unset($tmp_var);
$i = 0;

class PDFEnvios extends FPDF {

    // Columna actual
    var $col = 0;
    // Ordenada de comienzo de la columna
    var $y0;
    var $widths;
    var $aligns;
    var $fills;
    var $nombreFormaEnvio;
    var $nombreEmpresaEnvio;
    var $codPlanilla;

    function Header() {
        $this->SetFont('Arial', 'B', 11);
        $this->Image(ORFEOPATH . '/img/escudo.jpg', 20, 12, 50, 0, 'JPG', 'https://www.dnp.gov.co');
        $this->Cell(70, 16, '', 1, 0, 'C');
        //guardamos coordenadas.
        $yb = $this->GetY();
        $xb = $this->GetX();
        ///////////////////////
        $this->Cell(113, 8, $this->nombreEmpresaEnvio, 1, 0, "C");
        $this->Cell(91, 8, "PLANILLA No. " . $this->codPlanilla, 1, 0, "C");
        $this->SetXY($xb, $yb + 8);
        $this->Cell(113, 8, $this->nombreFormaEnvio, 1, 0, "C");
        $this->Cell(91, 8, "Fecha: " . date('d/m/Y'), 1, 1, "C");
        $this->Ln(2);
        // Guardar ordenada
        $this->y0 = $this->GetY();
        $this->SetFont('Arial', '', 6);
    }

    function Footer() {
        // Pie de pagina
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128);
        $this->Cell(0, 10, 'P�gina ' . $this->PageNo(), 0, 0, 'C');
    }

    function SetWidths($w) {
        //Set the array of column widths
        $this->widths = $w;
    }

    function SetAligns($a) {
        //Set the array of column alignments
        $this->aligns = $a;
    }

    function SetFills($a) {
        //Set the array of fills column
        $this->fills = $a;
    }

    function SetFormEnvio($n) {
        //Configura el nombre de envio para el encabezado del reporte.
        $this->nombreFormaEnvio = $n;
    }

    function SetEmpEnvio($n) {
        //Configura el nombre de la empresa de envio para el encabezado del reporte.
        $this->nombreEmpresaEnvio = $n;
    }

    function SetCodPlanilla($n) {
        //Configura el codigo de l aplanilla para el encabezado del reporte.
        $this->codPlanilla = $n;
    }

    function Row($data) {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            $this->Rect($x, $y, $w, $h);
            //Print the text
            $this->MultiCell($w, 5, $data[$i], 0, $a, $this->fills[$i]);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h) {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function valor($n) {
        return (empty($n)) ? "" : trim($n);
    }

    function NbLines($w, $txt) {
        //Computes the number of lines a MultiCell of width w will take
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l+=$cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                }
                else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }

    function completarBlancos() {
        $this->CheckPageBreak(21);
        $posY = $this->GetY();
        while ((int) $posY <= 165) {
            $this->Row(array('', '', '', '', '', '', '', '', '', '', ''));
            $posY = $this->GetY();
        }
    }

}

$pdf = new PDFEnvios('l', 'mm', 'a4');
$pdf->SetTitle("Listado de Envio");
$pdf->SetAuthor($db->entidad_largo);
$pdf->SetFormEnvio($nomFormEnvio);
$pdf->SetEmpEnvio($nomEmpEnvio);
$pdf->SetCodPlanilla($no_planilla);
$pdf->AddPage();
$pdf->SetFillColor(130, 130, 130);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFills(array(TRUE, TRUE, TRUE, TRUE, TRUE, TRUE, TRUE, TRUE, TRUE, TRUE, TRUE, TRUE));
$pdf->SetWidths(array(5, 20, 39, 40, 32, 32, 28, 17, 30, 11, 20));
$pdf->SetFont('Arial', '', 6);
$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
$pdf->Row(array('', 'Radicado', 'Destinatario', 'Direcci�n', 'Municipio', 'Dpto', 'Pa�s', 'Tel�fono', 'Correo Electr�nico', 'Peso(Gr)', 'Valor'));
$pdf->SetAligns(array('R', 'R', 'L', 'L', 'L', 'L', 'L', 'R', 'L', 'R', 'R'));
$pdf->SetFills(array(FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE));
$pdf->SetTextColor(0, 0, 0);
$vlrTotal = 0;
while (!$rs->EOF) {
    $i++;
    $pdf->Row(array($i, $pdf->valor($rs->Fields('RADI_NUME_SAL')),
        $pdf->valor(substr($rs->Fields('SGD_RENV_NOMBRE'), 0, 25)),
        $pdf->valor(substr($rs->Fields('SGD_RENV_DIR'), 0, 25)),
        $pdf->valor($rs->Fields('SGD_RENV_MPIO')), $pdf->valor($rs->Fields('SGD_RENV_DEPTO')),
        $pdf->valor($rs->Fields('SGD_RENV_PAIS')), $pdf->valor($rs->Fields('SGD_RENV_TELEFONO')),
        $pdf->valor($rs->Fields('SGD_RENV_MAIL')), $pdf->valor($rs->Fields('SGD_RENV_PESO')),
        $pdf->valor($rs->Fields('SGD_RENV_VALOR'))
    ));
    $vlrTotal += $rs->Fields('SGD_RENV_VALOR');
    $rs->MoveNext();
}
$pdf->completarBlancos();
$pdf->Row(array('', '', '', '', '', '', '', '', '', '', $vlrTotal));
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(137, 8, "", "LR", 0, 'C');
$pdf->Cell(137, 8, "", "LR", 1, 'C');
$pdf->Cell(137, 8, strtoupper($_SESSION['usua_nomb']), "LRB", 0, 'C');
$pdf->Cell(137, 8, "FIRMA Y SELLO RECIBIDO EMPRESA DE CORREO", "LRB", 0, 'C');
$fecha = date("YmdHis");
$archivo_labels = "../bodega/pdfs/guias/guia1$fecha.pdf";
$pdf->Output($archivo_labels, 'F');
?>
<TABLE BORDER=0 WIDTH=100% class="titulos2">
    <TR><TD class="listado2" align="center">
    <center><b>Se han enviado <?= $i ?> radicados. <br> 
            <a href='<?= $archivo_labels ?>?<?= date("dmYh") . time("his") ?>' target='<?= date("dmYh") . time("his") ?>'>Abrir Archivo</a></b></center>
</td>
</TR>
</TABLE>
