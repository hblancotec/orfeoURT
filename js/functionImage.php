<?php 
/**
  * Paggina Cuerpo.php que muestra el contenido de las Carpetas
  * Creado en la SSPD en el año 2008
  * @autor Liliana Gomez
  * @licencia GNU/GPL V 3
  * 
  * Se cambio nombre de Archivo y se mejoraro acceso a Imagenes
  * @autor Jairo Losada 2009-05
  * 
  */?>
<script type="text/javascript">
function funlinkArchivo(numrad,rutaRaiz,tipoAnexo){
	nombreventana="linkVistArch";
	url=rutaRaiz + "/linkArchivo.php?"+"&<?= session_name()."=".trim(session_id()) ?>&numrad="+numrad+"&tipoAnexo="+tipoAnexo;
	ventana = window.open(url,nombreventana,'height=50,width=250');
	//setTimeout(nombreventana.close, 70);
    return;
}
</script>