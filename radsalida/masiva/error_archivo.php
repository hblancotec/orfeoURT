<html>
<head>
<title>Resultado del análisis de datos</title>
<link rel="stylesheet" href="../../estilos_totales.css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<script>
function enviar_consulta(){
	
}
</script>

<body bgcolor="#FFFFFF" text="#000000">
	<span class=tituloListado>Se terminó de realizar la verificación de los
		archivos y se encontraron los siguientes errores: </span>
	<BR>
<?php
if (count($auxErrrEnca) > 0) {
    ?>
<p class=etexto>
		<span class='etextomenu'>El archivo CSV no tiene las columnas
			obigatorias :<BR>
  <?php
    $num = count($auxErrrEnca);
    $i = 0;
    
    while ($i < $num) {
        
        $record_id = key($auxErrrEnca);
        echo ("*" . $auxErrrEnca[$record_id] . "*" . "<BR>");
        next($auxErrrEnca);
        ++ $i;
    }
    
    ?>
  </span>
	</p>
	<?php

}
if (count($auxErrCmpCsv) > 0) {
    ?>
<p class=etexto>
		<span class='etextomenu'>Al archivo CSV le faltan algunas columnas obligatorias en sus registros :<BR>
  <?php
    $num = count($auxErrCmpCsv);
    $i = 0;
    
    while ($i < $num) {
        
        $record_id = key($auxErrCmpCsv);
        echo ("*" . $auxErrCmpCsv[$record_id] . "*" . "<BR>");
        next($auxErrCmpCsv);
        ++ $i;
    }
    
    ?>
  </span>
	</p>
		<?php

}
if (count($auxErrorTipo) > 0) {
    ?>
<p class=etexto>
		<span class='etextomenu'>Al archivo CSV no se le definió correctamente
			el campo tipo en los registros :<BR>
  <?php
    $num = count($auxErrorTipo);
    $i = 0;
    
    while ($i < $num) {
        
        $record_id = key($auxErrorTipo);
        echo ("*" . $auxErrorTipo[$record_id] . "*" . "<BR>");
        next($auxErrorTipo);
        ++ $i;
    }
    
    ?>
  </span>
	</p>
	
	
  <?php

}
if (count($auxErrPlant) > 0) {
    ?>
<p class=etexto>
		<span class='etextomenu'>El archivo RTF no tiene los campos
			obigatorios :<br>
	<?php
    $num = count($auxErrPlant);
    $i = 0;
    
    while ($i < $num) {
        
        $record_id = key($auxErrPlant);
        echo ("*" . $auxErrPlant[$record_id] . "*" . "<BR>");
        next($auxErrPlant);
        ++ $i;
    }
}
if (count($auxErrLugar) > 0) {
    ?>
	
  Los siguientes datos de la división política no se encontraron en la base de 
  datos: <BR>
 	<?php
    $num = count($auxErrLugar);
    $i = 0;
    
    while ($i < $num) {
        
        $record_id = key($auxErrLugar);
        echo ("*" . $auxErrLugar[$record_id] . "*" . "<BR>");
        next($auxErrLugar);
        ++ $i;
    }
}

if (count($auxErrESP) > 0) {
    ?>
	
  Los siguientes datos LAS ESP incluidas no se encontraron en la base de 
  datos: <BR>
 	<?php
    $num = count($auxErrESP);
    $i = 0;
    
    while ($i < $num) {
        
        $record_id = key($auxErrESP);
        echo ("*" . $auxErrESP[$record_id] . "*" . "<BR>");
        next($auxErrESP);
        ++ $i;
    }
}
if (count($auxErrorDir) > 0) {
    ?>
  Los siguientes datos de las direcciónes tienen tamaño mayor de 95 caracteres<BR>
   	<?php
    $num = count($auxErrorDir);
    $i = 0;
    
    while ($i < $num) {
        
        $record_id = key($auxErrorDir);
        echo ("*" . $auxErrorDir[$record_id] . "*" . "<BR>");
        next($auxErrorDir);
        ++ $i;
    }
}

if (count($auxErrorNom) > 0) {
    ?>
	 Los siguientes datos de nombres tienen tamaño mayor de 95 caracteres<BR>
   	<?php
    $num = count($auxErrorNom);
    $i = 0;
    
    while ($i < $num) {
        
        $record_id = key($auxErrorNom);
        echo ("*" . $auxErrorNom[$record_id] . "*" . "<BR>");
        next($auxErrorNom);
        ++ $i;
    }
}

if (count($auxErrorAnexo) > 0) {
    ?>
	Los siguientes datos de radicados asociados no existen<BR>
	<?php
    $num = count($auxErrorAnexo);
    $i = 0;
    
    while ($i < $num) {
        
        $record_id = key($auxErrorAnexo);
        echo ("*" . $auxErrorAnexo[$record_id] . "*" . "<BR>");
        next($auxErrorAnexo);
        ++ $i;
    }
}

if (count($auxErrorEmail) > 0) {
    ?>
	Los siguientes datos de correos electr&oacute;nicos tienen errores<BR>
	<?php
    $num = count($auxErrorEmail);
    $i = 0;
    
    while ($i < $num) {
        
        $record_id = key($auxErrorEmail);
        echo ("*" . $auxErrorEmail[$record_id] . "*" . "<BR>");
        next($auxErrorEmail);
        ++ $i;
    }
}

if (count($auxErrorRadP) > 0) {
    ?>
	Los radicados padres no existen<BR>
	<?php
    $num = count($auxErrorRadP);
    $i = 0;
    
    while ($i < $num) {
        
        $record_id = key($auxErrorRadP);
        echo ("*" . $auxErrorRadP[$record_id] . "*" . "<BR>");
        next($auxErrorRadP);
        ++ $i;
    }
}

?>	 
  <br /> POR FAVOR VERIFIQUE LOS DATOS Y REPITA EL PROCESO <BR> <br /> <input
			type="button" name="Submit" value="Menu Masiva" class="ebuttons2"
			onClick="regresar();">
		</span>
	</p>

</body>
</html>