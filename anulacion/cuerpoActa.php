<?php
    // Cuerpo del acta modificar este cuerpo de acuerdo con la entidad.
    $html =	'<img src="encabezado.jpg" width="520">';
    $html .= "
    <p><p>
    <br><br><br><br>
    <b><center> &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ACTA DE ANULACI&Oacute;N No. $actaNo </center></b><p><br>
    <CENTER><B><br>
    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
    NUMEROS DE RADICACI&Oacute;N DE CORRESPONDENCIA ENVIADA  A&Ntilde;O $ano_hoy</B></CENTER><p><br>
    <CENTER><B>
    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;GRUPO  DE  CORRESPONDENCIA </B></CENTER><p>
    <br>
    En cumplimiento a lo establecido en el  Acuerdo No.  060 del 30 de octubre de 2001  expedido <br>
    por el Archivo General de la Naci&oacute;n, en el cual se establecen pautas para la administraci&oacute;n de<br>
    las  comunicaciones  oficiales  en  las entidades  p&uacute;blicas  y  privadas que cumplen  funciones<br>
    p&uacute;blicas, y  con  base especialmente en el par&aacute;grafo del Art&iacute;culo Quinto, el cual establece que <br>
    Cuando existan errores en la  radicaci&oacute;n y  se  anulen  los n&uacute;meros, se debe dejar constancia <br>
    por escrito, con la respectiva  justificaci&oacute;n y firma del Jefe de la unidad de correspondencia; el <br>
    Coordinador  del  Grupo  de  Correspondencia  del  Departamento  Nacional  de  Planeaci&oacute;n <br>
    procede  a  anular  los  siguientes  n&uacute;meros  de  radicaci&oacute;n  de  $TituloActam  que  no  fueron <br>
    tramitados por las dependencias radicadoras:<br><br><br>
    1.- N&uacute;meros de radicaci&oacute;n de $TituloActam a anular: <br><br>
        $radicadosPdf
    <br><br><br>
    2.- Se deja  copia  de la presente acta en el archivo central de la  Entidad  para  el tr&aacute;mite <br>
         respectivo de la organizaci&oacute;n f&iacute;sica de los archivos.<p><br>
    Se firma la presente el $fecha_hoy
    <p><p><br>
    ______________________________________________________ <br>
    $usua_nomb<BR>
    Coordinador Grupo de Correspondencia.";
?>
