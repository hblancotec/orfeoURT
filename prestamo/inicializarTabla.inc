<?php
/********* TITULO DE LAS COLUMNAS DE LA TABLA CON LOS RESULTADOS DE LA CONSULTA *********/
         // Oculta o Hace visible la flecha que indica la columna por la cual se ordena el resultado
         $numRta=16; //número de columnas de la respuestas
         for($j=1; $j<$numRta; $j++) {
            if($j==$iSort){ 
               if($sDirection==" DESC ") { $flecha[$j]="<img src='".$ruta_raiz."/iconos/flechadesc.gif' border=0 alt=''>"; }
               else                      { $flecha[$j]="<img src='".$ruta_raiz."/iconos/flechaasc.gif'  border=0 alt=''>"; }
            }
            else{ $flecha[$j]=""; }
         }       
         for($j=1; $j<$numRta; $j++) {
            $href[1][$j]="href=\"javascript: ordenar($j); \"";           //formEnvio.php
            $href[0][$j]="href=\"".$sFileName."?".$form_params.$j."&\""; //prestamo.php
         }
         $j=0;
         if($sFileName=="formEnvio.php") { $j=1; } 
?>
		<thead>
         <tr class="titulos5" align="center">
            <th id="r0"  style="display:<?=$tipoRespuesta[$opcionMenu][0]; ?>">Radicado</th>	 
            <th id="r1"  style="display:<?=$tipoRespuesta[$opcionMenu][1]; ?>">Expediente</th>	 
            <th id="r2"  style="display:<?=$tipoRespuesta[$opcionMenu][2]; ?>">Usuario</th>	 
            <th id="r3"  style="display:<?=$tipoRespuesta[$opcionMenu][3]; ?>">Dependencia</th>	 
            <th id="r4"  style="display:<?=$tipoRespuesta[$opcionMenu][4]; ?>">Fecha<br>Solicitud</th>	 
            <th id="r5"  style="display:<?=$tipoRespuesta[$opcionMenu][5]; ?>">Fecha<br>Prestamo</th>	 			
            <th id="r6"  style="display:<?=$tipoRespuesta[$opcionMenu][6]; ?>">Fecha<br>Cancelaci&oacute;n</th>	 			
            <th id="r7"  style="display:<?=$tipoRespuesta[$opcionMenu][7]; ?>">Fecha<br>Vencimiento</th>	 
            <th id="r8"  style="display:<?=$tipoRespuesta[$opcionMenu][8]; ?>">Fecha<br>Devoluci&oacute;n</th>	 
            <th id="r9"  style="display:<?=$tipoRespuesta[$opcionMenu][9]; ?>" title="<?=$ayudaDifFecha?>">Tiempo<br>Espera</th>	 
            <th id="r11" style="display:<?=$tipoRespuesta[$opcionMenu][11];?>">No Pag.</th>
            <th id="r18" style="display:<?=$tipoRespuesta[$opcionMenu][11];?>">Prestamo Descripcion</th>
            <th id="r12" style="display:<?=$tipoRespuesta[$opcionMenu][12];?>">Estado</th>
            <th id="r13" style="display:<?=$tipoRespuesta[$opcionMenu][13];?>">Usuario<br>Prestamo</th>
            <th id="r14" style="display:<?=$tipoRespuesta[$opcionMenu][14];?>">Usuario<br>Cancela</th>
            <th id="r15" style="display:<?=$tipoRespuesta[$opcionMenu][15];?>">Usuario<br>Recibe</th>
            <th id="r16" style="display:<?=$tipoRespuesta[$opcionMenu][16];?>">Archivado</th>		 		 
            <th id="r17" style="display:<?=$tipoRespuesta[$opcionMenu][17];?>">Ver Hist&oacute;rico</th>
            <th id="r18" style="display:<?=$tipoRespuesta[$opcionMenu][18];?>">Acci&oacute;n</th>
            <th id="r18" style="display:<?=$tipoRespuesta[$opcionMenu][11];?>">Anexos</th>
            <th id="r19" style="display:<?=$tipoRespuesta[$opcionMenu][19];?>"><input type="checkbox" name="rta_" value="checkbox" onClick="javascript: seleccionarRta();"></th>
         </tr> 
		</thead>