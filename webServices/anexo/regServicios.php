<?php
$server->register('anexoRadicadoToRadicado',                                 //nombre del servicio
   array('radiNume' => 'xsd:string',                                    //numero de radicado
   'file' => 'xsd:base64binary',                                       //archivo en base 64
   'filename' => 'xsd:string',                                        //nombre original del archivo
   'correo' => 'xsd:string',                                           //correo electronico
   'descripcion'=>'xsd:string',                                        //descripcion del anexo
   'radiSalida'=>'xsd:string',
   'estadoAnexo'=>'xsd:string'
   ),                                                                //fin parametros del servicio
   array('return' => 'xsd:string'),                                   //retorno del servicio
  $ns                                                              //Elemento namespace para el metod
);
?>