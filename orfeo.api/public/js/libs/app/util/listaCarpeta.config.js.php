<?php
 header('Content-Type: application/javascript');
?>
var config = new Object({
    controller:'listaCarpeta',
    xtype:'listacarpetagrid',
    requires: 'ExtMVC.view.listaCarpeta.listaCarpetaGrid',
    <?php
    echo "codigoCarpeta:'".(isset($_GET['carpeta'])?$_GET['carpeta']:"0")."',";
    echo "carpetaPersonal:'".(isset($_GET['tipo_carpt'])?$_GET['tipo_carpt']:"0")."',";
    echo "pathMVC: '".(isset($_GET['pathMVC'])?$_GET['pathMVC']:"")."',";
    echo "NoRadicado: '".(isset($_GET['NoRadicado'])?$_GET['NoRadicado']:"0")."'";
    ?>
});
