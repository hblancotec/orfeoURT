<html>
    <head>
       <title>Archivo - Manejo de prestamos y devoluciones</title>
       <link rel="stylesheet" href="../estilos/orfeo.css" type="text/css">
    </head>
    <body class="PageBODY">
       <form method="post" action="prestamo.php" name="menu"> 
          <input type="hidden" name="opcionMenu" value="1">      
          <input type="hidden" name="sFileName" value="{FILE_NAME}"> 
          <input type="hidden"  value="{USUA_LOGIN}" name="krd">
          <input type="hidden" value=" " name="radicado">  	          
          <script>
             // Inicializa la opcion seleccionada
             function seleccionar(i) {
                document.menu.opcionMenu.value=i;
                document.menu.submit();
             }
             var opcionM='{OPCION_MENU}';		 		 
             if(opcionM!=""){ seleccionar(opcionM); }
          </script>	  	  	  
          <table width="31%" border="0" cellpadding="0" cellspacing="5" class="borde_tab" align="center">
             <tr>
                <td class="titulos4" align="center">PRESTAMO Y CONTROL DE DOCUMENTO</td>
             </tr>
             <!-- BEGIN row -->
             <tr>
                <td class="listado2">{OPCION}. <a class="vinculos" href="javascript:seleccionar({OPCION});">{TITULO}</a></td>
             </tr>
             <!-- END row -->
          </table>
       </form>  
    </body>
</html>
