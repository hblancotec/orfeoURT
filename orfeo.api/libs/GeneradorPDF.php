<?php
require_once 'dompdf/dompdf_config.inc.php';
require_once 'GeneradorCodigoBarra.php';
class GeneradorPDF 
{    
    
    var $pdf;
    public function __construct() {
        try {
            $this->pdf = new DOMPDF();
        }
        catch (Exception $ex)
        {
            throw new Exception("Error al construir el PDF. Favor comunique el siguiente error:".$ex);
        }
    }
    
    public function generaPDFRespuestaRapida($noRadicado,$path,$contenido,$remitente,$destinatario,$destinatarioCC){
        try
        {
            $objCodificacionEspecial = new CodificacionEspecial();
            
            $this->pdf->add_info('Title','Respuesta de Solicitud PQR');
            $this->pdf->add_info('Subject','Departamento Nacional de Planeacion');
            $this->pdf->add_info('Author','Sistema de Gestion Documental Orfeo');
            $this->pdf->add_info('Keywords','dnp, respuesta, salida, generar');
            
            $objBarras = new GeneradorCodigoBarra('code39',$noRadicado);
            
            $html = "<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='es'><head>
                    <style>
                        @page {
                                margin: 2cm 2cm 3cm 2cm;
                        }

                        body {
                          font-family: sans-serif;
                          text-align: justify;
                        }
                        p {
                            text-align: justify;
                            font-size: 1em;
                            margin: 0.5em;
                            padding: 10px;
                          }
                        #footer {
                            position: fixed;
                            left: 0;
                            right: 0;
                            color: #aaa;
                            font-size: 0.8em;
                            bottom: -65px;
                            border-top: 0.1pt solid #aaa;
                            text-align: center;
                          }
                          
                          #footer table {
                                  width: 100%;
                                  border-collapse: collapse;
                                  border: none;
                          }

                          #footer td {
                            padding: 0;
                            width: 50%;
                          }
                    </style>
                    </head><body>  
                    <div id='footer'>
                        <div class='page-number'>
							Calle 26 # 13  19 C&oacute;digo Postal 110311 Bogot&aacute;, D.C., Colombia   PBX 381 5000  o  www.dnp.gov.co
						</div>
                     </div>
                    <table style='width: 100%;'>
					<tr>
                                <td style='width: 100%;' colspan='2'>
                                    <img width='630' src='public/images/banerPDF.JPG' alt='Logo1'>
                             </td>
					</tr>
                            <tr><td><br><br></td><td></td></tr>
					<tr>
                            <td width='60%'>
                                <b>Bogot&aacute; D.C.,".$this->fechaFormateada(time())."</b>
                            <td style='text-align: right'>
                                <img src='data:image/png;base64,".$objBarras->retornarImgBase64()."' width='200'>
                            </td>
					</tr>
                    <tr>
                            <td></td>
                            <td style='text-align: right'>
                                <b>$noRadicado</b>
                            </td>
					</tr>
					<tr>
                            <td></td>
                            <td style='text-align: right'>
                                <b>Al responder cite este n&uacute;mero</b>
                            </td>
					</tr>
                </table>
				
				<br><br>        
            
				".  str_replace("<br>","<p>",str_replace("<br><br>","</p><p>",str_replace("<i>","",str_replace("</i>","",iconv($objCodificacionEspecial->codificacion($contenido),"UTF-8",$contenido)))))."
        
				<table style='width: 100%; text-align: center; '>
					<tr><td style='width: 100%; text-align: justify'> Atentamente</td></tr>
					<tr><td style='width: 100%; text-align: justify'> ".$_SESSION['depe_nomb']." </td></tr>
					<tr><td style='width: 100%; text-align: justify'> $depe_nomb</td></tr>
					<tr><td style='width: 100%; text-align: justify'> $remitente </td></tr>
					<tr><td style='width: 100%; text-align: justify'> CC -> $destinatarioCC </td></tr>
					<tr>
						<td style='width: 100%; text-align: center'> 
							Si quieres calificar nuestro servicio ingresa a <A href='www.dnp.gov.co/califiquenos'> www.dnp.gov.co/califiquenos </A> <br>
							Tu opini&oacute;n es importante para el DNP <br>
						</td>
					</tr>	
				</table>
			</body></html>";
            $this->pdf->load_html($html);
            $this->pdf->render();
            return file_put_contents(BODEGAPATH.$path, $this->pdf->output());
        }
        catch (Exception $ex){
            
            throw new Exception("Error al construir el PDF. Favor comunique el siguiente error:".$ex);
        }
    }
    
    function fechaFormateada($FechaStamp)
    {
            $ano = date('Y', $FechaStamp);			// Año
            $mes = date('m', $FechaStamp);			// Numero de mes (01-31)
            $dia = date('d', $FechaStamp);			// Dia del mes (1-31)
            $dialetra = date('w', $FechaStamp); // Dia de la semana(0-7)

            switch ($dialetra) {
                    case 0:	$dialetra = "domingo";
                            break;
                    case 1:	$dialetra = "lunes";
                            break;	
                    case 2:	$dialetra = "martes";
                            break;
                    case 3:	$dialetra = "miercoles";
                            break;
                    case 4:	$dialetra = "jueves";
                            break;
                    case 5:	$dialetra = "viernes";
                            break;
                    case 6:	$dialetra = "sabado";
                            break;
            }

            switch ($mes) {
                    case '01':	$mesletra = "enero";
                            break;
                    case '02':	$mesletra = "febrero";
                            break;
                    case '03':	$mesletra = "marzo";
                            break;
                    case '04':	$mesletra = "abril";
                            break;
                    case '05':	$mesletra = "mayo";
                            break;
                    case '06':	$mesletra = "junio";
                            break;
                    case '07':	$mesletra = "julio";
                            break;
                    case '08':	$mesletra = "agosto";
                            break;
                    case '09':	$mesletra = "septiembre";
                            break;
                    case '10':	$mesletra = "octubre";
                            break;
                    case '11':	$mesletra = "noviembre";
                            break;
                    case '12':	$mesletra = "diciembre";
                            break;
            }
            return htmlentities("$dialetra, $dia de $mesletra de $ano");
    }
}

?>
