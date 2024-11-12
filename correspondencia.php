<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "./sinacceso.php");
    exit();
} else if (isset($_SESSION['krd'])) {
    $krd = $_SESSION["login"];
    $dependencia = $_SESSION["dependencia"];
} else {
    $krd = $_REQUEST['krd'];
}
include_once ('./_conf/constantes.php');

$ruta_raiz = ".";
if (! isset($_SESSION['dependencia']))
    include (ORFEOPATH . "rec_session.php");
$carpeta = $carpetano;
$tipo_carp = $tipo_carpp;
include_once (ORFEOPATH . "include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$phpSessionJs = session_name() . "=" . session_id();
?>
<html>
<head>
<title>Menu</title>
<link rel="stylesheet" href="./estilos/orfeo.css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script>
	// Variable que guarda la ultima opcion de la barra de
	// herramientas de funcionalidades seleccionada
	function MM_swapImgRestore() { //v3.0
		var i,x,a=document.MM_sr;
		for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++)
			x.src = x.oSrc;
	}

	function MM_preloadImages() { //v3.0
		var d = document;
		if(d.images){ if(!d.MM_p) d.MM_p=new Array();
		var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
		if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
	}

	function MM_findObj(n, d) { //v4.01
		var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
		d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
		if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
		for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
		if(!x && d.getElementById) x=d.getElementById(n); return x;
	}

	function MM_swapImage() { //v3.0
		var i,j=0,x,a=MM_swapImage.arguments;
		document.MM_sr=new Array;
		for(i=0;i<(a.length-2);i+=3) {
			if ((x=MM_findObj(a[i]))!=null){
				document.MM_sr[j++]=x;
				if(!x.oSrc)
					x.oSrc=x.src; x.src=a[i+2];
			}
		}
	}

	function cambioDependencia (dependencia, usuarioLogin) {
		dependencia = 121;
		document.write('<form action="cuerpo.php?<?php echo $phpSessionJs;?>&krd=<?php echo $krd;?>&ascdesc=desFc method="POST" name="form5" target="mainFrame">');
		document.write('<input type="hidden" name="cambioDepen" value="' + true + '">');
		document.write('<input type="hidden" name="condigoDepen" value="' + dependencia + '">');
		document.write("</form>");
		document.form5.submit();
	}

	function reload_window($carpetano,$carp_nomb,$tipo_carp) {
		document.write('<form action="cuerpo.php?<?php echo $phpSessionJs;?>&krd=<?php echo $krd;?>&ascdesc=desFc method="POST" name="form4" target="mainFrame">');
		document.write('<input type="hidden" name="carpetano" value="' + $carpetano + '">');
		document.write('<input type="hidden" name="carp_nomb" value="' + $carp_nomb + '">');
		document.write('<input type="hidden" name="tipo_carpp" value="' + $tipo_carp + '">');
		document.write('<input type="hidden" name="tipo_carpt" value="' + $tipo_carpt + '">');
		document.write("</form>");
		document.form4.submit();
	}

	selecMenuAnt=-1;
	swVePerso = 0;
	numPerso = 0;

	function cambioMenu(img){
		MM_swapImage('plus' + img,'','imagenes/menuraya.gif',1);
		if (selecMenuAnt!=-1 && img!=selecMenuAnt)
			MM_swapImage('plus' + selecMenuAnt,'','imagenes/menu.gif',1);
		selecMenuAnt = img;
		if (swVePerso==1 && numPerso!=img){
			document.getElementById('carpersolanes').style.display="none";
			MM_swapImage('plus' + numPerso,'','imagenes/menu.gif',1);
			swVePerso=0;
		}
	}

	function verPersonales(img){
		if (swVePerso!=1){
			document.getElementById('carpersolanes').style.display="";
			swVePerso=1;
		}
		else{
			document.getElementById('carpersolanes').style.display="none";
			MM_swapImage('plus' + selecMenuAnt,'','imagenes/menu.gif',1);
			selecMenuAnt = img;
			swVePerso=0;
		}
		numPerso = img;
	}

	function verOpcionInformados(img){
		if (swVePerso!=1){
			document.getElementById('carpersolanes').style.display="";
			swVePerso=1;
		}
		else {
			document.getElementById('carpersolanes').style.display="none";
			MM_swapImage('plus' + selecMenuAnt,'','imagenes/menu.gif',1);
			selecMenuAnt = img;
			swVePerso=0;
		}
		numPerso = img;
	}
 </script>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style>
</head>
<body bgcolor="#ffffff">
<?php
$fechah = date("dmy") . "_" . time("hms");
$carpeta = $carpetano;
echo '<form action="correspondencia.php" method="post">';

// Cambia a Mayuscula el login
// krd -- Permite al usuario escribir su login en mayuscula o Minuscula
$numeroa = 0;
$numero = 0;
$numeros = 0;
$numerot = 0;
$numerop = 0;
$numeroh = 0;
$fechah = date("dmy") . time("hms");
$usuaDoc = $_SESSION['usua_doc'];
$countDepe = 0; // Contador de dependencias
$sqlDeps = "SELECT  
                USU.USUA_LOGIN,
                USU.USUA_DOC,
                USU.USUA_ALERTA_DP AS ALERTA,
                USU.USUA_PERM_COINFO AS COINFO,
                DEP.DEPE_CODI,
                USD.SGD_USD_DEFAULT,
                DEP.DEPE_NOMB,
                DEP.DEP_SIGLA
            FROM
                USUARIO USU,
                DEPENDENCIA DEP,
                SGD_USD_USUADEPE USD
            WHERE
                USU.USUA_LOGIN = '$krd' AND
                USU.USUA_DOC = $usuaDoc AND
                USU.USUA_LOGIN = USD.USUA_LOGIN AND
                USU.USUA_DOC = USD.USUA_DOC AND
                DEP.DEPE_CODI = USD.DEPE_CODI";
$nombreTabla = "DEPENDENCIAS";
$depeArreglo = array();
$contDepe = 0;
$rsDeps = $db->conn->Execute($sqlDeps);
$alerta = $rsDeps->fields['ALERTA'];
$autorizaCoinfo = $rsDeps->fields['COINFO'];
// Realiza la consulta del usuarios y de una vez cruza con la tabla dependencia
// Cambio de la consulta para que el usuario pertenezcan a varias dependencias
$isql = "SELECT 
                    a.*,
                    b.depe_nomb
            FROM
                    usuario a,
                    SGD_USD_USUADEPE USD,
                    dependencia b
            WHERE
                    a.USUA_LOGIN ='$krd' AND
                    a.USUA_LOGIN = USD.USUA_LOGIN AND
                    USD.SGD_USD_DEFAULT = 1 AND
                    USD.DEPE_CODI = B.DEPE_CODI";
$rs = $db->conn->Execute($isql);
$phpsession = session_name() . "=" . session_id();

// echo '<font size="1" face="verdana>"';
// Valida Login y contrasena encriptada con funcion md5()
if (trim($rs->fields["USUA_LOGIN"]) == trim($krd)) {
    $contraxx = $rs->fields["USUA_PASW"];
    if (trim($contraxx)) {
        $codusuario = $rs->fields["USUA_CODI"];
        $dependencianomb = $rs->fields["DEPE_NOMB"];
        $fechah = date("dmy") . "_" . time("hms");
        $contraxx = $rs->fields["USUA_PASW"];
        $nivel = $rs->fields["CODI_NIVEL"];
        $iusuario = " and us_usuario='$krd'";
        $perrad = $rs->fields["PERM_RADI"];
        $usuaDoc = $rs->fields["USUA_DOC"];
        $radTp = array();
        $radTp[2] = $rs->fields["USUA_PRAD_TP2"];
        $radTp[1] = $rs->fields["USUA_PRAD_TP1"];
        $radTp[3] = $rs->fields["USUA_PRAD_TP3"];
        $radTp[4] = $rs->fields["USUA_PRAD_TP4"];
        $radTp[5] = $rs->fields["USUA_PRAD_TP5"];
        $radTp[6] = $rs->fields["USUA_PRAD_TP6"];
        $radTp[7] = $rs->fields["USUA_PRAD_TP7"];
        $radTp[8] = $rs->fields["USUA_PRAD_TP8"];
        $radTp[9] = $rs->fields["USUA_PRAD_TP9"];
        // Adicionado as contador
        // si el usuario tiene permiso de radicar el prog. muestra los iconos de radicacion
        include (ORFEOPATH . "menu/menuPrimero.php");
        include (ORFEOPATH . "menu/radicacion.php");
        // include (ORFEOPATH . "menu/reportes.php");
        // Consulta selecciona las carpetas Basicas de DocuImage que son extraidas de la tabla Carp_Codi
        $isql = "SELECT carp_codi, carp_desc
                FROM carpeta
                ORDER BY carp_codi";
        $rs = $db->conn->Execute($isql);
        $addadm = "";
        ?>
<table border="0" cellpadding="0" cellspacing="0" width="160">
		<tr>
			<td><img src="./imagenes/spacer.gif" width="10" height="1" border="0"
				alt=""></td>
			<td><img src="./imagenes/spacer.gif" width="150" height="1"
				border="0" alt=""></td>
			<td><img src="./imagenes/spacer.gif" width="1" height="1" border="0"
				alt=""></td>
		</tr>
		<tr>
			<td colspan="2"><a href="#" onClick="window.location.reload()"> <img
					name="menu_r3_c1" src="./imagenes/menu_r5_c1.gif"
					alt="Presione para actualizar las carpetas." width="148"
					height="31" border="0">
			</a></td>
			<td><img src="imagenes/spacer.gif" width="1" height="25" border="0"
				alt=""></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td valign="top">
				<table width="100%" border="0" cellpadding="0" cellspacing="0"
					bgcolor="c0ccca">
					<tr>
						<td valign="top">
							<table width="150" border="0" cellpadding="0" cellspacing="3"
								bgcolor="#C0CCCA">
<?php
        while (! $rs->EOF) {
            if ($data == "")
                $data = "NULL";
            $numdata = trim($rs->fields["CARP_CODI"]);
            /*$sqlCarpDep = "	SELECT SGD_CARP_DESCR
                            FROM SGD_CARP_DESCRIPCION
                            WHERE SGD_CARP_DEPECODI = $dependencia and
                                            SGD_CARP_TIPORAD = $numdata";
            $rsCarpDesc = $db->conn->Execute($sqlCarpDep);
            $descripcionCarpeta = $rsCarpDesc->fields["SGD_CARP_DESCR"];
            if ($descripcionCarpeta) {
                $data = $descripcionCarpeta;
            } else {
                $data = trim($rs->fields["CARP_DESC"]);
            }*/
            $data = trim($rs->fields["CARP_DESC"]);
            if ($numdata == 11) {
                // Se realiza la cuenta de radicados en Visto Bueno VoBo
                $isql = "	SELECT
							count(*) as CONTADOR 
						FROM
							radicado
						WHERE
							carp_codi = $numdata
                            and carp_per = 0
							and radi_depe_actu = $dependencia
							and radi_usua_actu = $codusuario";
                
                $addadm = "&adm=1";
            } else {
                $isql = "	SELECT
						count(*) as CONTADOR
					FROM
						radicado
					WHERE
						carp_codi=$numdata
                        and carp_per = 0
						and  radi_depe_actu = $dependencia
						and radi_usua_actu = $codusuario";
                // Cuenta los derivados que el usuario tiene en su poder
                if ($numdata == 2) {
                    $ksql = "	SELECT
							count(*) as CONTADOR 
		    			FROM
		    				sgd_rg_multiple
						WHERE
							area = $dependencia
							and usuario = $codusuario
							and estatus = 'ACTIVO'";
                }
                $addadm = "&adm=0";
            }
            if ($carpeta == $numdata) {
                $imagen = "folder_open.gif";
            } else {
                $imagen = "folder_cerrado.gif";
            }
            $flag = 0;
            $rs1 = $db->conn->Execute($isql);
            $numerot = $rs1->fields["CONTADOR"];
            if ($numdata == 2) {
                $rsk = $db->conn->Execute($ksql);
                $contderi = $rsk->fields["CONTADOR"];
                $numerot = $numerot + $contderi;
            }
            if ($flag == 1)
                echo "$isql";
            // if ($numdata == 0 ){$numdata=800;}
            // se agrega condici�n que solo muestra las carpetas de radici�n dependiendo de los permisos habilitados de cada usuario
                if ($radTp[$numdata] > 0 or $numdata > 11 or $numdata == 2) {
                ?>
		  <tr valign="middle">
									<td width="25"><img src="imagenes/menu.gif" width="15"
										height="18" alt='<?=$data ?> ' title='<?=$data ?>'
										name="plus<?=$i?>"></td>
									<td width="125"><a onclick="cambioMenu(<?=$i?>);"
										href='cuerpo.php?<?=$phpsession?>&krd=<?=$krd?>&adodb_next_page=1&fechah=<?php echo "$fechah&nomcarpeta=$data&carpeta=$numdata&tipo_carpt=0&adodb_next_page=1&verrad=0"; ?>'
										class="menu_princ" target="mainFrame"> <?php
                echo "$data($numerot)";
            }
            ?>
									</a></td>
								</tr>
<?php
            $i ++;
            $rs->MoveNext();
        }
        /**
         * PARA ARCHIVOS AGENDADOS NO VENCIDOS
         * (Por.
         * SIXTO 20040302)
         */
        /*
         * $sqlFechaHoy = $db->conn->DBTimeStamp(time());
         * $sqlAgendado = " and (agen.SGD_AGEN_FECHPLAZO >= ".$sqlFechaHoy.")";
         * $isql=" SELECT
         * count(*) as CONTADOR
         * FROM
         * SGD_AGEN_AGENDADOS agen
         * WHERE
         * usua_doc = $usua_doc AND
         * agen.SGD_AGEN_ACTIVO = 1
         * $sqlAgendado";
         * $rs = $db->conn->Execute($isql);
         * $num_exp = $rs->fields["CONTADOR"];
         * $data = "Agendados no vencidos";
         * echo '<tr valign="middle">';
         */
        ?>
		  <!-- <tr> 
		   <td width="25"><img src="imagenes/menu.gif" width="15" height="18" alt='<?=$data ?> ' title='<?=$data ?>' name="plus<?=$i?>"></td>
		   <td width="125">
			<a onclick="cambioMenu(<?=$i?>);" href='cuerpoAgenda.php?<?=$phpsession?>&agendado=1&krd=<?=$krd?>&fechah=<?php echo "$fechah&nomcarpeta=$data&tipo_carpt=0"; ?>' class="menu_princ" target="mainFrame">
		  -->		   
<?php
        // echo "Agendado($num_exp)";
        ?>
	<!--	</a>
	  	   </td>
		  </tr>
	-->
<?php
        /**
         * PARA ARCHIVOS AGENDADOS VENCIDOS
         * (Por.
         * SIXTO 20040302)
         */
        /*
         * $sqlAgendado = "and (agen.SGD_AGEN_FECHPLAZO <= ".$sqlFechaHoy.")";
         * $isql = " SELECT
         * count(*) as CONTADOR
         * FROM
         * SGD_AGEN_AGENDADOS agen
         * WHERE
         * usua_doc=$usua_doc
         * and agen.SGD_AGEN_ACTIVO=1
         * $sqlAgendado";
         * $rs = $db->conn->Execute($isql);
         * $num_exp = $rs->fields["CONTADOR"];
         * $data="Agendados vencidos";
         * $i++;
         */
        ?>
	<!--		 <tr  valign="middle">
      		  <td width="25"><img src="imagenes/menu.gif" width="15" height="18" alt='<?=$data ?>'
      		  title='<?=$data ?>' name="plus<?=$i?>"></td>
	  		  <td width="125"><a onclick="cambioMenu(<?=$i?>);" href='cuerpoAgenda.php?<?=$phpsession?>
	  		  &agendado=2&krd=<?=$krd?>&fechah=<?php echo "$fechah&nomcarpeta=$data&&tipo_carpt=0&adodb_next_page=1";?>'
	  		  class="menu_princ" target="mainFrame"><?php echo "Agendado Vencido(<font color='#990000'>$num_exp</font>)";?>
	    		</a> 
	  		  </td>
			 </tr>
	-->
<?php
        
        $link = '<tr  valign="middle">' . '<td width="25"><img src="imagenes/menu.gif" width="15" height="18" alt=' . $data . ' title=' . $data . 'name="plus' . $i . '"></td>' . '<td width="125"><a class="menu_princ" target="mainFrame" alt="Seleccione una Carpeta ' . 'onclick="cambioMenu(' . $i . ');" href="cuerpopendiente.php? ' . $phpsession . '&krd=' . $krd . '&adodb_next_page=1&fechah=' . $fechah . '&' . 'nomcarpeta=' . $data . '&carpeta=' . $numdata . '&tipo_carpt=0&adodb_next_page=1 ' . '"> Control Documentos</a>' . '</td></tr>';
        if ($alerta == 1) {
            echo $link;
        }
        
        // Coloca el mensaje de Informados y cuenta cuantos registros hay en informados
        $isql = "SELECT
				COUNT(*) as CONTADOR
			FROM
				INFORMADOS
            WHERE
            	depe_codi = $dependencia AND
                usua_codi = $codusuario AND
            	SGD_INFDIR_CODIGO = 0 AND
            	INFO_CODI IS NOT NULL";
        if ($carpeta == $numdata and $tipo_carp == 0) {
            $imagen = "folder_open.gif";
        } else {
            $imagen = "folder_cerrado.gif";
        }
        $rs1 = $db->conn->Execute($isql);
        $numerot = $rs1->fields["CONTADOR"];
        $i ++;
        $data = "Documentos De Informacion";
        ?>
	  <tr valign="middle">
									<td width="25"><img src="imagenes/menu.gif" width="15"
										height="18" alt="<?=$data ?>" title="<?=$data ?>"
										name="plus<?=$i?>"></td>
									<td width="125"><a onclick="cambioMenu(<?=$i?>);"
										href="cuerpoinf.php?<?=$phpsession?>&krd=<?=$krd?>&<?= "mostrar_opc_envio=1&orderNo=2&fechaf=$fechah&carpeta=8&nomcarpeta=Informados&orderTipo=desc&adodb_next_page=1"; ?>"
										class="menu_princ" target="mainFrame"
										title="Documentos De Informacion"> <?php
        
echo "Informados($numerot)";
        $i ++;
        ?>
									</a></td>
								</tr>

<?php
        $link2 = '<tr  valign="middle">' . '<td width="25"><img src="imagenes/menu.gif" width="15" height="18" alt=' . $data . ' title=' . $data . 'name="plus' . $i . '"></td>' . '<td width="125"><a class="menu_princ" target="mainFrame" alt="Seleccione una Carpeta ' . 'onclick="cambioMenu(' . $i . ');" href="registroCoinfo.php? ' . $phpsession . '&krd=' . $krd . '&adodb_next_page=1&fechah=' . $fechah . '&' . 'nomcarpeta=' . $data . '&carpeta=' . $numdata . '&tipo_carpt=0&adodb_next_page=1 ' . '"> Control COINFO</a>' . '</td></tr>';
        if ($autorizaCoinfo == 1) {
            echo $link2;
        }
        
        ?>

    <!-- Informados con carpetas -->
								<tr>
									<td width="25"></td>
									<td width="125">
										<table width="100%" border="0" cellpadding="0" cellspacing="0"
											bgcolor="959E9D" id="carpetaInformados" style="">
											<tr>
												<td width="125">
<?php
        $cont = 0;
        $aCarpInf = array();
        $archivoExecCarp = './listarContCarpInf.php';
        $mostrarCarpeta = '';
        $nombreCarpInf = 'Nueva_Carpeta';
        $nuevaCarpetaInf = '<a class="vinculos" href="formCrearDirectorioInf.php?'; // . $phpsession;
        $nuevaCarpetaInf .= 'krd=' . $krd;
        
        // Aca hay un error por que aparece PHPSESSID sin que se asigne?? por lo tanto
        // la linea donde se encuentra el $phpsession se comentario
        $nuevaCarpetaInf .= ' &fechah=' . $fechah . '&adodb_next_page=1"';
        $nuevaCarpetaInf .= ' class="menu_princ" target="mainFrame"';
        $nuevaCarpetaInf .= ' alt="Creaci&oacute;n Carpetas Informados"';
        $nuevaCarpetaInf .= ' title="Creaci&oacute;n Carpetas Informados"';
        $nuevaCarpetaInf .= '>' . $nombreCarpInf . '</a>' . "\n";
        
        // Mostrando opcion crear nueva carpeta
        echo $nuevaCarpetaInf;
        
        // Busca las carpetas de informados creadas por el usuario
        $sqlInf = "	SELECT 
					INFDIR.SGD_INFDIR_CODIGO,
					INFDIR.SGD_INFDIR_NOMBRE,
					INFDIR.SGD_INFDIR_DESCRIPCION,
					COUNT (INF.RADI_NUME_RADI) AS TOTAL_RADICADOS
				FROM
					SGD_INFDIR_INFORMADOSDIR INFDIR LEFT JOIN INFORMADOS INF ON
					INFDIR.SGD_INFDIR_CODIGO = INF.SGD_INFDIR_CODIGO
				WHERE
					INFDIR.USUA_DOC = '$usuaDoc' AND
					INFDIR.USUA_LOGIN = '$krd'
				GROUP BY
					INFDIR.SGD_INFDIR_CODIGO,
                    INFDIR.SGD_INFDIR_NOMBRE,
                    INFDIR.SGD_INFDIR_DESCRIPCION
                ORDER BY
                	INFDIR.SGD_INFDIR_NOMBRE";
        $rsInf = $db->conn->Execute($sqlInf);
        // Captura las carpetas que tiene el usuario
        while (! $rsInf->EOF) {
            $aCarpInf[$cont]["codigoCarpeta"] = $rsInf->fields["SGD_INFDIR_CODIGO"];
            $aCarpInf[$cont]["nombreCarpeta"] = $rsInf->fields["SGD_INFDIR_NOMBRE"];
            $aCarpInf[$cont]["descCarpeta"] = $rsInf->fields["SGD_INFDIR_DESCRIPCION"];
            $aCarpInf[$cont]["totalRadicados"] = $rsInf->fields["TOTAL_RADICADOS"];
            $cont ++;
            $rsInf->MoveNext();
        }
        
        // Muestra las Carpetas
        foreach ($aCarpInf as $carpeta) {
            $nombreCarpeta = "\t\t\t";
            $nombreCarpeta .= '<tr><td width="125">';
            $nombreCarpeta .= '<img src="' . "../img/carpeta.png " . '" width="' . 12 . '" height="' . 11;
            $nombreCarpeta .= '" alt="' . $carpeta["descCarpeta"] . '" title="';
            $nombreCarpeta .= $carpeta["descCarpeta"] . '" name="' . $carpeta["nombreCarpeta"] . '">';
            $nombreCarpeta .= '<a href="' . $archivoExecCarp . '?' . $phpsession . '&krd=' . $krd;
            $nombreCarpeta .= '&usuaDoc=' . $usuaDoc;
            $nombreCarpeta .= '&carpetaInf=' . $carpeta['codigoCarpeta'] . '"';
            $nombreCarpeta .= ' class="menu_princ" target="mainFrame" alt="' . $carpeta["descCarpeta"] . '"';
            $nombreCarpeta .= ' title="' . $carpeta["descCarpeta"] . '">';
            $nombreCarpeta .= '&nbsp;' . $carpeta["nombreCarpeta"];
            $nombreCarpeta .= '(' . $carpeta["totalRadicados"] . ')</td></tr>' . "\n";
            echo $nombreCarpeta;
        }
        
        $data = "Despliegue de Carpetas Personales";
        ?>
			</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr valign="middle">
									<td width="25"><img src="imagenes/menu.gif" width="15"
										height="18" alt='<?=$data ?> ' title='<?=$data ?>'
										name="plus<?=$i?>"></td>
									<td width="125"><a
										onclick="cambioMenu(<?=$i?>);verPersonales(<?=$i?>);" href='#'
										class="menu_princ" title="Despliegue de Carpetas Personales">PERSONALES</a>
									</td>
								</tr>
								<tr>
									<td></td>
									<td>
										<table width="100%" border="0" cellpadding="0" cellspacing="0"
											bgcolor="959E9D" id="carpersolanes" style="display: none">
											<tr>
												<td><a class="vinculos"
													href="crear_carpeta.php?<?=$phpsession ?>&krd=<?=$krd?>&<?php
        
echo "fechah=$fechah&adodb_next_page=1";
        ?>" class="menu_princ"
														target='mainFrame' title='Creacion de Carpetas Personales'
														>Nueva carpeta</a></td>
											</tr>
<?php
        // Busca las carpetas personales de cada usuario
        // y las coloca contando el numero de documentos en cada carpeta.
        $isql = "SELECT 
				codi_carp,
				desc_carp,
				nomb_carp
			FROM
            	carpeta_per
			WHERE
            	usua_codi = $codusuario AND
				depe_codi = $dependencia
			ORDER BY
            	codi_carp";
        $rs = $db->conn->Execute($isql);
        $imgCarpeta = "../img/carpeta.png";
        while (! $rs->EOF) {
            if ($data == "")
                $data = "NULL";
            $data = trim($rs->fields["NOMB_CARP"]);
            $numdata = trim($rs->fields["CODI_CARP"]);
            $detalle = trim($rs->fields["DESC_CARP"]);
            $data = trim($rs->fields["NOMB_CARP"]);
            $isql = "	SELECT
					count(*) as CONTADOR
				FROM
                	radicado
				WHERE
					carp_per=1 and
					carp_codi=$numdata and
					radi_depe_actu=$dependencia and
					radi_usua_actu=$codusuario ";
            if ($carpeta == $numdata and $tipo_carp == 1) {
                $imagen = "ico_carpeta_personal_abierta.gif";
            } else {
                $imagen = "ico_carpeta_personal_cerrada.gif";
            }
            $rs1 = $db->conn->Execute($isql);
            $numerot = $rs1->fields["CONTADOR"];
            $datap = "$data(Personal)";
            ?>
		   <tr>
												<td height="18"><img src="<?=$imgCarpeta?>" width="12"
													height="11" alt="<?=$detalle?>" title="<?=$detalle?>"> <a
													href="cuerpo.php?<?=$phpsession ?>&krd=<?=$krd?>&<?php
            
echo "fechah=$fechah&nomcarpeta=$data";
            ?>(Personal)<?php
            
echo "&tipo_carp=1&carpeta=$numdata&adodb_next_page=1";
            ?>" title="<?=$detalle?>" class="menu_princ" target="mainFrame"><?php echo "$data($numerot)";?>
             </a></td>
											</tr>
<?php
            $rs->MoveNext();
        }
        ?>
		 </table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
<?php
    }
}
// *TRANSACCIONES DE CURSOR DE CONSULTA PRIMARIA***
if (! $db->imagen()) {
    $logo = $db->imagen();
} else {
    $logo = "../img/escudo.jpg";
    // $logo = "logoEntidad.gif";
}
?>
<table width="90%" border="0" cellspacing="0" cellpadding="0"
		class="t_bordeVerde">
		<tr align="center">
			<td height="50"><img width=140 src='<?=$logo?>'></td>
		</tr>
	</table>
</body>
</html>
