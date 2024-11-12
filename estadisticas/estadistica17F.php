<?php

$fechaact = date("YmdHis");

$campos = "";
foreach ($_POST['campos'] as $valor) {
    if (strlen($campos) > 5) {
        $campos .= "-";
    }
    $campos .= $valor;
}
if ($SelPqr) {
    $SelPqr = 1;
} else {
    $SelPqr = 0;
}
$temaSelect = 0;

$fecha_ini = str_replace("/", "-", $fecha_ini);
$fecha_fin = str_replace("/", "-", $fecha_fin);

$bat_filename = "E:\OI_OrfeoPHP7_64\orfeo\alarma_estadistica.bat";
$bat_log_filename = "E:\logs\Estadistica_Internet_".$fechaact.".log";
$bat_file = fopen($bat_filename, "w");
if($bat_file) {
    fwrite($bat_file, "@ECHO OFF"."\n");
    fwrite($bat_file, 'E:\php-7.2.16-Win32-VC15-x64\php-win.exe -f E:\OI_OrfeoPHP7_64\orfeo\Estadistica_Internet.php '.$fecha_ini.' '.$fecha_fin.' '.$dependencia_busq.' '.$SelPqr.' '.$temaSelect.' '.$_SESSION["usua_email"].' '.$campos.' >> '.$bat_log_filename."\n");
    //fwrite($bat_file, "php c:\\my_php_process.php >> ".$bat_log_filename."\n");
    fwrite($bat_file, "echo End proces >> ".$bat_log_filename."\n");
    fwrite($bat_file, "EXIT"."\n");
    fclose($bat_file);
}
// Start the process in the background
$exe = "start /b ".$bat_filename;
if( pclose(popen($exe, 'r')) ) {
    return true;
}
return false;

//$cmd = 'E:\php-7.2.16-Win32-VC15-x64\php-win.exe -f E:\OI_OrfeoPHP7_64\orfeo\Estadistica_Internet.php '.$fecha_ini.' '.$fecha_fin.' '.$dependencia_busq.' '.$SelPqr.' '.$temaSelect.' '.$_SESSION["usua_email"].' '.$campos.' >> E:\logs\Estadistica_Internet_'.$fechaact.'.log';
//pclose(popen("start /B ". $cmd, "r")); 

//shell_exec('E:\php-7.2.16-Win32-VC15-x64\php-win.exe -f E:\OI_OrfeoPHP7_64\orfeo\Estadistica_Internet.php '.$fecha_ini.' '.$fecha_fin.' '.$dependencia_busq.' '.$SelPqr.' '.$temaSelect.' '.$_SESSION["usua_email"].' > /dev/null 2>&1 &');
//exec('E:\php-7.2.16-Win32-VC15-x64\php-win.exe -f E:\OI_OrfeoPHP7_64\orfeo\Estadistica_Internet.php '.$fecha_ini.' '.$fecha_fin.' '.$dependencia_busq.' '.$SelPqr.' '.$temaSelect.' '.$_SESSION["usua_email"].' >> E:\logs\Estadistica_Internet_'.$fechaact.'.log');

?>