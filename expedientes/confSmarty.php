<?php
require 'smarty/libs/Smarty.class.php';

// Se configuran los parametros de smarty
$smarty = new Smarty();
$smarty->template_dir = ORFEOPATH . 'expedientes/templates';
$smarty->compile_dir = BODEGAPATH . 'tmp';
$smarty->left_delimiter = '<!--{';
$smarty->right_delimiter = '}-->';
?>