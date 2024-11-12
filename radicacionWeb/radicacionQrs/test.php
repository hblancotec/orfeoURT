<?php
	include("./funciones2.php");
	$texto = $_POST["asunto"];
	$asunto = formatearTextArea($texto, 1500);
	var_dump($asunto);
        $asunto = text2pdf($asunto);
	var_dump($asunto);
?>
