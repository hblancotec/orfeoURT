<?php
 header('Content-Type: application/javascript');
?>
var params = new Object({
    <?php
    echo "dep: ".($_GET['dep']).",";
    echo "krd:'".($_GET['krd'])."',";
    echo "SERVIDOR: '".$_GET['SERVIDOR']."',";
    echo "nmb: '".($_GET['usr'])."'";
    ?>
});
