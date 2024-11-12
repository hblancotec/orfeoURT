<script language="JavaScript">
 
    // JavaScript Document
    <!-- Esta funcion esconde el combo de las dependencia e informados -->
    <!-- se activan cuando el menu envie una senal de cambio.-->
    <!-- Cuando existe una senal de cambio el program ejecuta esta -->
    <!-- funcion mostrando el combo seleccionado -->
    
    function changedepesel(enviara) {
        document.form1.codTx.value = enviara;
        document.getElementById('depsel').style.display = 'none';
        document.getElementById('carpper').style.display = 'none';
        document.getElementById('depsel8').style.display = 'none';
        document.getElementById('Enviar').style.display = 'none';
       
        if (enviara == 10) {
            document.getElementById('depsel').style.display = 'none';
            document.getElementById('carpper').style.display = '';
            document.getElementById('depsel8').style.display = 'none';
            MM_swapImage('Image9','','<?=$ruta_raiz?>/imagenes/internas/reasignar.gif',1);
            MM_swapImage('Image10','','<?=$ruta_raiz?>/imagenes/internas/informar.gif',1);
            MM_swapImage('Image11','','<?=$ruta_raiz?>/imagenes/internas/devolver.gif',1);
            MM_swapImage('Image12','','<?=$ruta_raiz?>/imagenes/internas/vobo.gif',1);
            MM_swapImage('Image14','','<?=$ruta_raiz?>/imagenes/internas/NRR.gif',1);
            MM_swapImage('Image13','','<?=$ruta_raiz?>/imagenes/internas/archivar.gif',1);
            document.getElementById('Enviar').style.display = '';
       
        }
        
        //Archivar
        if(enviara==13 ) {
            document.getElementById('depsel').style.display = 'none';
            document.getElementById('depsel8').style.display = 'none';
            document.getElementById('carpper').style.display = 'none';
       
            MM_swapImage('Image10','','<?=$ruta_raiz?>/imagenes/internas/informar.gif',1);
            MM_swapImage('Image11','','<?=$ruta_raiz?>/imagenes/internas/devolver.gif',1);
            MM_swapImage('Image9','','<?=$ruta_raiz?>/imagenes/internas/reasignar.gif',1);
            MM_swapImage('Image12','','<?=$ruta_raiz?>/imagenes/internas/vobo.gif',1);
            MM_swapImage('Image8','','<?=$ruta_raiz?>/imagenes/internas/moverA.gif',1);
            MM_swapImage('Image14','','<?=$ruta_raiz?>/imagenes/internas/NRR.gif',1);        
            envioTx();
        }
        
        //nrr
        if(enviara==16 ) {
            document.getElementById('depsel').style.display = 'none';
            document.getElementById('depsel8').style.display = 'none';
            document.getElementById('carpper').style.display = 'none';
            MM_swapImage('Image10','','<?=$ruta_raiz?>/imagenes/internas/informar.gif',1);
            MM_swapImage('Image11','','<?=$ruta_raiz?>/imagenes/internas/devolver.gif',1);
            MM_swapImage('Image9','','<?=$ruta_raiz?>/imagenes/internas/reasignar.gif',1);
            MM_swapImage('Image12','','<?=$ruta_raiz?>/imagenes/internas/vobo.gif',1);
            MM_swapImage('Image8','','<?=$ruta_raiz?>/imagenes/internas/moverA.gif',1);
            MM_swapImage('Image13','','<?=$ruta_raiz?>/imagenes/internas/archivar.gif',1);
            envioTx();
        }
        
        //Devolver
        if(enviara==12)  {    
            MM_swapImage('Image9','','<?=$ruta_raiz?>/imagenes/internas/reasignar.gif',1);
            MM_swapImage('Image10','','<?=$ruta_raiz?>/imagenes/internas/informar.gif',1);
            MM_swapImage('Image8','','<?=$ruta_raiz?>/imagenes/internas/moverA.gif',1);
            MM_swapImage('Image12','','<?=$ruta_raiz?>/imagenes/internas/vobo.gif',1);
            MM_swapImage('Image14','','<?=$ruta_raiz?>/imagenes/internas/NRR.gif',1);
            MM_swapImage('Image13','','<?=$ruta_raiz?>/imagenes/internas/archivar.gif',1);
            envioTx();
        }         

        //Reasignar
        if(enviara==9 ) {
            document.getElementById('depsel').style.display = '';
            document.getElementById('carpper').style.display = 'none';
            document.getElementById('depsel8').style.display = 'none';
   
            MM_swapImage('Image8','','<?=$ruta_raiz?>/imagenes/internas/moverA.gif',1);
            MM_swapImage('Image10','','<?=$ruta_raiz?>/imagenes/internas/informar.gif',1);
            MM_swapImage('Image11','','<?=$ruta_raiz?>/imagenes/internas/devolver.gif',1);
            MM_swapImage('Image12','','<?=$ruta_raiz?>/imagenes/internas/vobo.gif',1);
            MM_swapImage('Image14','','<?=$ruta_raiz?>/imagenes/internas/NRR.gif',1);
            MM_swapImage('Image13','','<?=$ruta_raiz?>/imagenes/internas/archivar.gif',1);
            document.getElementById('Enviar').style.display = '';
        }

        //Visto bueno
        if(enviara==14) {
            document.getElementById('depsel').style.display = '';
            document.getElementById('carpper').style.display = 'none';
            document.getElementById('depsel8').style.display = 'none';

            MM_swapImage('Image8','','<?=$ruta_raiz?>/imagenes/internas/moverA.gif',1);
            MM_swapImage('Image10','','<?=$ruta_raiz?>/imagenes/internas/informar.gif',1);
            MM_swapImage('Image11','','<?=$ruta_raiz?>/imagenes/internas/devolver.gif',1);
            MM_swapImage('Image9','','<?=$ruta_raiz?>/imagenes/internas/reasignar.gif',1);
            MM_swapImage('Image14','','<?=$ruta_raiz?>/imagenes/internas/NRR.gif',1);
            MM_swapImage('Image13','','<?=$ruta_raiz?>/imagenes/internas/archivar.gif',1);
            document.getElementById('Enviar').style.display = '';
        }

        //Informar
        if(enviara==8) {
            if (document.getElementById('depsel')) {
                document.getElementById('depsel').style.display = 'none';
            }
            document.getElementById('depsel8').style.display = '';
            document.getElementById('carpper').style.display = 'none'; 
            MM_swapImage('Image8','','<?=$ruta_raiz?>/imagenes/internas/moverA.gif',1);
            MM_swapImage('Image11','','<?=$ruta_raiz?>/imagenes/internas/devolver.gif',1);
            MM_swapImage('Image9','','<?=$ruta_raiz?>/imagenes/internas/reasignar.gif',1);
            MM_swapImage('Image12','','<?=$ruta_raiz?>/imagenes/internas/vobo.gif',1);
            MM_swapImage('Image14','','<?=$ruta_raiz?>/imagenes/internas/NRR.gif',1);
            MM_swapImage('Image13','','<?=$ruta_raiz?>/imagenes/internas/archivar.gif',1);
            document.getElementById('Enviar').style.display = '';
        }
    }
</script>
