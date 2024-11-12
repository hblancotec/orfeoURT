<?php
include "../envios/class.phpmailer.php";
  //$destinatarioT = explode(" ",$mailFrom,10);
  //print_r($destinatarioT);
  //$destinatario = $destinatarioT[(count($destinatarioT)-1)];
  //$datos = array('&lt;','&gt;');
  //$destinatario = str_replace($datos,"", htmlentities($destinatario));
  $pattern="/([\s]*)([_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*([ ]+|)@([ ]+|)([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,}))([\s]*)/i";

  preg_match_all($pattern,$mailFrom, $salida);

  
  $destinatario=$salida[0];
  $destinatario=$destinatario[0];
  print_r($resultado);
  
  
//para el envío en formato HTML
  $mail = new PHPMailer();
  $archivoRadicadoMail = str_replace("../bodega","http://orfeo.dnp.gov.co/bodega",$archivoRadicado);
  $archivoRadicadoMail = str_replace("../../../bodega","http://volimpo/bodega",$archivoRadicado);
  $cuerpo = "<br>$texto
                <br> Se ha recibido su correo y se ha radicado con el $numeroRadicado, el cual tambien puede ser consultado en el portal Web del DNP.</p>
                 <br><br><b><center>Puede Consultarlos el estado en:
                 <a href='http://orfeo.dnp.gov.co/pqr/consulta.php?rad=$numeroRadicado'>http://orfeo.dnp.gov.co/pqr/consulta.php</a><br><br><br>".$respuesta."</b></center><BR>
                 <hr>Documento Recibido<hr>
                 <table>
                 <tr><td>
                 $archivoRadicadoMail
                 </td></tr>
                 </table>";
  $mail->Mailer = "smtp";
  $mail->From = $usuaEmail;
  echo "Destino : ".$destinatario;
  $mail->FromName = $usuaEmail;
  $strServer="172.16.1.92:25";
  $mail->Host = $strServer;
  $mail->Mailer = "smtp";
  $mail->SMTPAuth = "true";
  $mail->Subject = "Se ha recibido su Correo (No. $numeroRadicado)";
  $mail->AltBody = "Para ver el mensaje, porfavor use un visor de E-mail compatibles!";
  $mail->Body = $cuerpo;
  $mail->SMTPOptions = array(
      'ssl' => array(
          'verify_peer' => false,
          'verify_peer_name' => false,
          'allow_self_signed' => true
      )
  );
  $mail->AddAddress($destinatario);
  //$mail->AddAddress("jlosada@gmail.com");
  $mail->IsHTML(true);
  echo "<hr>";
  if(!$mail->Send())
  {
    echo "fallo el Envio de Correo respuesta $mailFrom ->".$destinatario;
  }else{
  echo "Se envio el Correo a $mailFrom ->".$destinatario;
}
?>

