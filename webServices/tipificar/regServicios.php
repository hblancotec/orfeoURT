<?php
$server->register('tipificarDocumento',
  array(
      'nurad'=>'xsd:string',
      'usuario'=>'xsd:string',
      'codiSRD'=>'xsd:string',
      'codiSBRD'=>'xsd:string',
      'codiTPR'=>'xsd:string'
  ),
  array(
      'return'=>'xsd:string'
  ),
  $ns,
  $ns.'#tipificarDocumento',
  'rpc',
  'encoded',
  'Tipificar un Documento'
);

$server->register('isDocumentoTipificado',
  array(
      'radicado'=>'xsd:string'
  ),
  array(
      'return'=>'xsd:string'
  ),
  $ns,
  $ns.'#documentoTipificado',
  'rpc',
  'encoded',
  'Devuelve true si YA esta Tipificado'
);

$server->register('tiposDocumentales',
  array(
      'serie'=>'xsd:string',
      'dependencia' => 'xsd:string'
  ),
  array(
      'return'=>'tns:Vector'
  ),
  $ns,
  $ns.'#informacionJefe',
  'rpc',
  'encoded',
  'Informacion del Jefe de un usuario'
);
?>