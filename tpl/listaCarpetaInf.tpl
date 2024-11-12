<html>
	<head><title>{TITULO_FORM}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link rel="stylesheet" href="estilos/orfeo.css">
	<style type="text/css">
	</style>
        <script>
            function window_onload() {
            }

            function markAll() {
                if(document.form1.elements['checkAll'].checked)
                    for(i=10;i<document.form1.elements.length;i++)
                        document.form1.elements[i].checked=1;
                else
                    for(i=10;i<document.form1.elements.length;i++)
                        document.form1.elements[i].checked=0;
            }

            function enviar() {
                document.form1.codTx.value = document.getElementById('enviara').value;
                sw = 0;
                cnt_notinf = 0;
                cnt_inf = 0;
                for(i=3;i<document.form1.elements.length;i++)
                    if (document.form1.elements[i].checked) {
                        sw=1;
                        if (document.form1.elements[i].name[11] == '0')	cnt_notinf += 1;
                        else cnt_inf += 1;
                    }
                if (sw==0) {
                    alert ("Debe seleccionar uno o mas informados");
                    return;
                }
                if (cnt_inf > 0 && cnt_notinf > 0 && document.getElementById('enviara').value == 7) {
                    alert ("Los informados seleccionados ... o todos tienen informador o no tienen informador.");
                    return;
                }
                document.form1.submit();
            }

            function mostrarMover() {
                document.getElementById('moverInformados').style.display = 'visible';
            }

            function moverInformados(){
                document.getElementById('moverInfEstado').value = 'true';
            }
        </script>
        <script>
            function setupDescriptions() {
                var x = navigator.appVersion;
                y = x.substring(0,4);
                if (y>=4) setVariables();
            }
            var x,y,a,b;
            k=0;

            function setVariables(){
                if (navigator.appName == "Netscape") {
                    h=".left=";
                    v=".top=";
                    dS="document.";
                    sD="";
                } else {
                    h=".pixelLeft=";
                    v=".pixelTop=";
                    dS="";
                    sD=".style";
                }
            }

            var isNav = (navigator.appName.indexOf("Netscape") !=-1);
            function popLayer(a){
               k=k+1;
                if (event.button == 2) {
                    document.getElementById('depsel').style.display = "none"; 
                    document.getElementById('depsel8').style.display = "none"; 
                    cerrar = "<table border=0 bgcolor='#CEDFC6' width=100% onClick=hideLayer(-50)><tr><td> " ;
                    cerrar += "<center><a href='sss?PHPSESSID={PHPSESS_ID}'  ><img src='iconos/cerrar.gif' border=0></a><b></b>"; 
                cerrar += "</td></tr> </table></a>";
                desc = "<table width=100%  cellpadding=0 border=0 bgcolor='#CEDFC6'><tr><td>" ;
                desc += cerrar + 	a  + cerrar ;
                desc += "</table>";
                if(isNav) {
                document.object1.document.write(desc);
                document.object1.document.close();
                document.object1.left=x+25;
                document.object1.top=y + y1;
                }
            else {
                var y1 = document.body.scrollTop;
                object1.innerHTML=desc;
                eval(dS+"object1"+sD+h+(x+25));
                eval(dS+"object1"+sD+v+y1);
                document.getElementById('depsel8').style.display = "none";
                document.getElementById('depsel').style.display = "none";
                document.getElementById('enviara').style.display = "none";
               }}
            }
            function hideLayer(a){
            if(isNav) {

            eval(document.object1.top=a);
            document.getElementById('enviara').style.display = "";
            document.getElementById('depsel8').style.display = "";
            }
            else object1.innerHTML="";
            document.getElementById('enviara').style.display = "";
            document.getElementById('depsel8').style.display = "none";
            document.getElementById('depsel').style.display = "";
            }
            function inici()
            {
              k=0;
            }
            function handlerMM(e){
            x = (isNav) ? e.pageX : event.clientX;
            y = (isNav) ? e.pageY : event.clientY;
            }
            if (isNav){
            document.captureEvents(Event.MOUSEMOVE);
            }
            document.onmousemove = handlerMM;
        </script>
	</head>
	<body bgcolor="#FFFFFF" topmargin="0" onLoad="setupDescriptions();window_onload();">
	<p>
	<table border=0 width=100% class="t_bordeGris">
	<tr>
		<td>
			<!-- Inicia tabla de cabezote -->
			<table BORDER=0  cellpad=2 cellspacing='0' WIDTH=98% class='t_bordeGris' valign='top' align='center' >
			<tr>
				<td width='33%' >
	      			<table width='100%' border='0' cellspacing='1' cellpadding='0'>
	      			<tr>
	      				<td height="20" bgcolor="377584"><div align="left" class="titulo1">LISTADO DE SUBCARPETA: </div></td>
	      			</tr>
	      			<tr class="info">
	      				<td height="20">{TIPO_RADICADO} - <b>{CARPETA_INF}</b></td>
	      			</tr>
	      			</table>
	      		</td>
				<td width='33%' >
	      			<table width='100%' border='0' cellspacing='1' cellpadding='0'>
	      			<tr>
	      				<td height="20" bgcolor="377584"><div align="left" class="titulo1">USUARIO </div></td>
	      			</tr>
	      			<tr class="info">
	      				<td height="20">{USUA_LOGIN}</td>
	      			</tr>
	      			</table>
	      		</td>
	      		<td width='34%' >
	      			<table width='100%' border='0' cellspacing='1' cellpadding='0'>
	      			<tr>
	      				<td height="20" bgcolor="377584"><div align="left" class="titulo1">DEPENDENCIA </div></td>
	      			</tr>
	      			<tr class="info">
	      				<td height="20">{DEPE_NOMBRE}</td>
	      			</tr>
	      			</table>
	      		</td>
	      	</tr>
			</table>
			<table width="98%" align="center" cellspacing="0" cellpadding="0">
			<tr class="tablas">
				<td>
				<form name="form_busq_rad" action="cuerpoinf.php?krd=PRUEBA123&PHPSESSID=172o16o1o5oPRUEBA123PHPSESSID=172o16o1o5oPRUEBA123&krd=PRUEBA123&depeBuscada=&filtroSelect=&tpAnulacion=&carpeta=8" method="POST"><input type="hidden" name="PHPSESSID" value="172o16o1o5oPRUEBA123" />
					Buscar radicado(s) inf(s) (Separados por coma)<input name="busq_radicados" type="text" size="70" class="tex_area" value="">

					<input type="submit" value='Buscar ' name=Buscar valign='middle' class='botones' onChange="form_busq_rad.submit()";>
				</form>
				</td>
			</tr>
	 		</table>
			<form name='form1' action='tx/formEnvio.php' method='post'><input type="hidden" name="PHPSESSID" value="172o16o1o5oPRUEBA123" />
			<input name="cambioInf" value="I" type="hidden">
			<br>
			<!-- Finaliza tabla de cabezote --> <!-- Inicia tabla de datos -->
			<input type='hidden' name="contra" value="">
            <input type='hidden' name="sesion" value="">
            <input type='hidden' name="krd" value="{USUA_LOGIN}">
            <input type='hidden' name="drde" value="">
            <input type='hidden' name="subCarpeta" value="{CODIGO_SUBCARP}">
            <table width="98%" align="center" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td colspan="2" height="80">
					<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" class="borde_tab">
					<tr>
						<td width="50%"  class="titulos2">
							<table width="100%" border="0" cellspacing="0" cellpadding="0" align="left">
							<tr class="titulos2">
								<td width="20%" class="titulos2">LISTAR POR:</td>
								<td height="30">
									<a href="./cuerpoinf.php?PHPSESSID=172o16o1o5oPRUEBA123&krd=PRUEBA123&depeBuscada=&filtroSelect=&tpAnulacion=&carpeta=8&orderNo=9&orderTipo=desc&PHPSESSID=172o16o1o5oPRUEBA123" alt="Ordenar Por Leidos">
                                        <span class='leidos'>Le&iacute;dos</span>
                                    </a>&nbsp;
                                    <a href="" alt='Ordenar Por Leidos'>
                                        <span class='no_leidos'>No Le&iacute;dos</span>
                                    </a>
								</td>
							</tr>
							</table>
						</td>
						<td width="50%" align="right" class="titulos2">
	                    <table width="100%">
	                        <tr align="right" id="moverInformados" style="">
	                            <td>
                                    <select name="moverSelect" class="select">
                                        <option value="N">Seleccionar Carpeta</option>
                                        <option value="0">Carpeta Ra&iacute;z (Informados)</option>
                                        <!-- BEGIN carpetas --><option value="{CODIGO_DIR}">{NOMBRE_DIR}</option>
                                        <!-- END carpetas -->
                                    </select>
                                    <input type="submit" name="mover" value="mover" class="botones" onClick="moverInformados();">
	                                <input type="hidden" id="moverInfEstado" name="moverInfEstado" value="false">
	                            </td>
	                        </tr>
	                    </table>
						</td>
					</tr>
					<tr>
						<td colspan="2">
	<table width=100% class=borde_tab border=0 >
        <tr class="tpar">
            <td class="tpar">
                <table cols="10" width="100%"  border="0"  cellpadding="0" cellspacing="5" class="borde_tab">
                    <tr>
                        <th class="titulos3">
                            <a href="{ORDENAR_RADICADO_URL}">
                                <span class="titulos3">Numero Radicado{IMAG1}</span>
                            </a>
                        </th>
                        <th class="titulos3">
                            <a href="{RADICADO_URL}">
                                <span class="titulos3">Fecha Radicado{IMAG2}</span>
                            </a>
                        </th>
                        <th class="titulos3">
                            <a href="{ORDENAR_ASUNTO_URL}">
                                <span class=titulos3>Asunto{IMAG3}</span>
                            </a>
                        </th>
                        <th class="titulos3">
                            <a href="{ORDENAR_TIPODOC_URL}">
                                <span class=titulos3>Tipo Documento{IMAG4}</span>
                            </a>
                        </th>
                        <th class="titulos3">
                            <a href="{ORDERNAR_DIAS}">
                            <span class="titulos3">Dias Restantes{IMAG5}</span>
                            </a>
                        </th>
                        <th class="titulos3">
                            <a href="{ORDERNAR_INF}">
                                <span class="titulos3">Informador{IMAG6}</span>
                            </a>
                        </th>
                        <th class="titulos2" width="1%">
                            <center><input type=checkbox name=checkAll value=checkAll onClick='markAll();' ></center>
                        </th>
                    </tr>
                    <!-- BEGIN row -->
	                <tr class="{ESTILO_ROW}" valign="top">
                        <td>
                            <span class="{ESTILO_LEIDO}">&nbsp;
                                <a href="{RUTA_RADICADO}">
                                    <span class="{ESTILO_LEIDO}">{RADI_NUME_RADI}</span>
                                </a>
                            </span>
                        </td>
                        <td>
                            <span class="{ESTILO_LEIDO}">
                                <a href="./verradicado.php?{VARIABLES}">
                                    <span class="{ESTILO_LEIDO}">{RADI_FECH_RADI}</span>
                                </a>
                            </span>
                        </td>
		                <td><span class="{ESTILO_LEIDO}">{INFO_DESC}</span></td>
		                <td><span class="{ESTILO_LEIDO}">{TIPO_DOC}</span></td>
		                <td><span class="{ESTILO_LEIDO}">{DIAS_RESTANTES}</span></td>
		                <td><span class="{ESTILO_LEIDO}">{INFORMADOR}</span></td>
		                <td>
                            <span class="leidos">
                                <input type="checkbox" name="checkValue[{USUA_DOC}-{RADI_NUME_RADI}]" value="checkValue">
                            </span>
                        </td>
	                </tr>
                    <!-- END row -->
	        </table>
        </form>
    </body>
</html>
