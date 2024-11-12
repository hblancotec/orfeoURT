<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "../../sinacceso.php");
	exit;
}
else {
	extract($_SESSION);
}

if ($_SESSION['usua_admin_sistema'] != 1) {
	die(include "../../sinpermiso.php");
	exit;
}
    define('ORFEOPATH', 'E:/OI_OrfeoPHP7_64/orfeo/');
    $krdOld = $krd;
    $archivoExec = './validar.php';
    $archivoExecCancel = '../formAdministracion.php';
    session_start();
    $ruta_raiz      = "../..";
    $carpetaOld     = $carpeta;
    $tipoCarpOld    = $tipo_carp;
    if(!$tipoCarpOld) $tipoCarpOld = $tipo_carpt;
    if(!$krd) $krd  = $krdOld;
    if(!isset($_SESSION['dependencia'])) include (ORFEOPATH . "rec_session.php");
    $sessionName    = session_name();
    $sessionId      = session_id();
    $entrada        = 0;
    $modificaciones = 0;
    $salida         = 0;
    if(!$fecha_busq) $fecha_busq=date("Y-m-d");
    $tituloCrear = ($usModo == 1) ? "Creacion de Usuario" : "Edicion de Usuario";

    require ORFEOPATH . "config.php";
	require_once ORFEOPATH . "include/db/ConnectionHandler.php";
    require_once "HTML/Template/IT.php";
    $tpl = new HTML_Template_IT(TPLPATH);
    $tpl->loadTemplatefile('formCrearUsuario.tpl');
    $db = new ConnectionHandler(ORFEOPATH);
    if (!defined('ADODB_FETCH_ASSOC'))define('ADODB_FETCH_ASSOC',2);
    $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$encabezado      = "&krd=$krd&dep_sel=$dep_sel&usModo=$usModo";
    $encabezado     .= "&perfil=$perfil&perfilOrig=$perfilOrig&cedula=$cedula";
    $encabezado     .= "&dia=$dia&mes=$mes&ano=$ano&ubicacion=$ubicacion";
    $encabezado     .= "&piso=$piso&extension=$extension&email=$email&email1=$email1&email2=$email2";
    $actionForm      = $archivoExec . '?' . $sessionName . '=' . $sessionId;
    $actionForm     .= $encabezado;
    $hrefCancel      = $archivoExecCancel . '?' . $sessionName . '=' . $sessionId;
    $hrefCancel     .= $encabezado;
    $mostrarDias    = "";    $estiloFila     = array();
    $estiloFila[]   = 'listado2';
    $estiloFila[]   = 'listado1';
    $contEstilo     = 1;
    $usuaDepe       = array();
    $cont           = 0;
    $default        = array();
    $default[0]     = '';
    $default[1]     = 'checked';

    //modo editar
    if ($usModo == 2) {
        if ($valRadio) {
            $usuSelec    = $valRadio;
            $usuario_mat = explode("-",$usuSelec,2);
            $usuDocSel   = $usuario_mat[0];
            $usuLoginSel = $usuario_mat[1];

            // secuencia de seleccion
            $sqlDepe = "SELECT	DEPE_CODI,
								SGD_USD_DEFAULT
						FROM	SGD_USD_USUADEPE
						WHERE	USUA_LOGIN = '$usuLoginSel'";
            $rsDepe = $db->conn->Execute($sqlDepe);

			while (!$rsDepe->EOF) {
				$usuaDepe[$rsDepe->fields["DEPE_CODI"]]["CHECK"] = 'checked';
				$usuaDepe[$rsDepe->fields["DEPE_CODI"]]["DEFAULT"] = $default[$rsDepe->fields["SGD_USD_DEFAULT"]];
				$rsDepe->MoveNext();
			}

            $isql = "SELECT	USUA.*,
							USD.DEPE_CODI
					 FROM	USUARIO USUA,
							SGD_USD_USUADEPE USD
					 WHERE	USUA.USUA_LOGIN = '$usuLoginSel' 
							AND USUA.USUA_LOGIN = USD.USUA_LOGIN 
							AND USUA.USUA_DOC = USD.USUA_DOC";
			$rsCrea = $db->conn->Execute($isql);

            if ($rsCrea->fields["USUA_CODI"] == 1) 
				$perfilOrig = "Jefe";
            else 
				$perfilOrig= "Normal";

			$perfil         = $perfilOrig;
			$nusua_codi 	= $rsCrea->fields["USUA_CODI"];
			$cedula 		= $rsCrea->fields["USUA_DOC"];
			$cedulaYa       = $rsCrea->fields["USUA_DOC"];
			$usuLogin 		= $rsCrea->fields["USUA_LOGIN"];
			$nombre 		= $rsCrea->fields["USUA_NOMB"];
			$dep_sel 		= $rsCrea->fields["DEPE_CODI"];
			$fecha_nacim 	= substr($rsCrea->fields["USUA_NACIM"], 0, 11);
			$dia 			= substr($fecha_nacim, 8, 2);
			$mes 			= substr($fecha_nacim, 5, 2);
			$ano 			= substr($fecha_nacim, 0, 4);
			$ubicacion  	= $rsCrea->fields["USUA_AT"];
			$piso			= $rsCrea->fields["USUA_PISO"];
			$extension		= $rsCrea->fields["USUA_EXT"];
			$email			= trim($rsCrea->fields["USUA_EMAIL"]);
			$email1			= trim($rsCrea->fields["USUA_EMAIL_1"]);
			$email2			= trim($rsCrea->fields["USUA_EMAIL_2"]);
			$permDespla		= $rsCrea->fields["USUA_PERM_DESPLA"];
			$no_trd			= $rsCrea->fields["USUA_NO_TIPIFICA"];
			$usua_activo 	= $rsCrea->fields["USUA_ESTA"];
			$modificaciones	= $rsCrea->fields["USUA_PERM_MODIFICA"];
			$env_correo 	= $rsCrea->fields["USUA_PERM_ENVIOS"];
			$estadisticas   = $rsCrea->fields["SGD_PERM_ESTADISTICA"];
			$impresion	    = $rsCrea->fields["USUA_PERM_IMPRESION"];
			$exp_temas	    = $rsCrea->fields["USUA_PERM_TEM_EXP"];
			$ccalarmas	    = $rsCrea->fields["USUA_PERM_CC_ALAR"];
			$prestamo		= $rsCrea->fields["USUA_PERM_PRESTAMO"];
			$adm_sistema 	= $rsCrea->fields["USUA_ADMIN"];
			$adm_archivo 	= $rsCrea->fields["USUA_ADMIN_ARCHIVO"];
			$usua_nuevoM 	= $rsCrea->fields["USUA_NUEVO"];
			$nivel			= $rsCrea->fields["CODI_NIVEL"];
			$salida 		= $rsCrea->fields["USUA_PRAD_TP1"];
			$masiva 		= $rsCrea->fields["USUA_MASIVA"];
			$dev_correo 	= $rsCrea->fields["USUA_PERM_DEV"];
			$permRadEmail 	= $rsCrea->fields["USUA_PERM_RADEMAIL"];
			$ordena			= $rsCrea->fields["USUA_PERM_ORDENAR"];
			$notifAdm		= $rsCrea->fields["USUA_NOTIF_ADMIN"];

			//INICIO Permisos para acciones masivas de la ventana principal cuerpo.php
			$accMasiva_trd 		= $rsCrea->fields["USUA_MASIVA_TRD"];
			$accMasiva_incluir 	= $rsCrea->fields["USUA_MASIVA_INCLUIR"];
			$accMasiva_prestamo	= $rsCrea->fields["USUA_MASIVA_PRESTAMO"];
			$accMasiva_temas	= $rsCrea->fields["USUA_MASIVA_TEMAS"];			
			//FIN Permisos para acciones masivas de la ventana principal cuerpo.php

            if ($rsCrea->fields["SGD_PANU_CODI"] == 1) $s_anulaciones = 1;
            if ($rsCrea->fields["SGD_PANU_CODI"] == 2) $anulaciones = 1;
            if ($rsCrea->fields["SGD_PANU_CODI"] == 3) {
                $s_anulaciones = 1;
                $anulaciones = 1;
            }

            $usua_publico   = $rsCrea->fields["USUARIO_PUBLICO"];
            $reasigna       = $rsCrea->fields["USUARIO_REASIGNAR"];
            $firma          = $rsCrea->fields["USUA_PERM_FIRMA"];
            $notifica       = $rsCrea->fields["USUA_PERM_NOTIFICA"];
	    	$temas          = $rsCrea->fields["USUA_PERM_TEMAS"];						
            $respuesta      = $rsCrea->fields["USUA_PERM_RESPUESTA"];
            $usua_permexp   = $rsCrea->fields["USUA_PERM_EXPEDIENTE"];
        }
    }
    $perf_1 = "Normal";
    $perf_2 = "Jefe";
	$perf_3 = "Auditor";

    if ($perfil == "Jefe") {
        $perf_1 = "Jefe";
        $perf_2 = "Normal";
		$perf_3 = "Auditor";
    }
	
	if ($perfil == "Auditor") {
        $perf_1 = "Auditor";
        $perf_2 = "Normal";
		$perf_3 = "Jefe";
    }

    include_once (ORFEOPATH . "include/query/envios/queryPaencabeza.php");
    $sqlConcat = $db->conn->Concat($db->conn->substr . "($conversion,1,5) ", "'-'",
                        $db->conn->substr."(DEPE_NOMB,1,30) ");

    $sql = "SELECT $sqlConcat AS DEPE_NOMBRE,
                    DEPE_CODI
            FROM dependencia
            ORDER BY depe_codi";

    $rsDep = $db->conn->Execute($sql);

    while (!$rsDep->EOF) {
        $tpl->setVariable('DEPE_NOMBRE', $rsDep->fields['DEPE_NOMBRE']);
        $tpl->setVariable('DEPE_CODI', $rsDep->fields['DEPE_CODI']);
        $tpl->setVariable('ESTILO_FILA', $estiloFila[$contEstilo % 2]);
        $tpl->setVariable('CHECK', $rsDep->fields['DEPE_CODI']);
        $tpl->setVariable('CHEQUEAR', $usuaDepe[$rsDep->fields['DEPE_CODI']]["CHECK"]);
        $tpl->setVariable('DEFAULT', $rsDep->fields['DEPE_CODI']);
        $tpl->setVariable('DEFECTO', $usuaDepe[$rsDep->fields['DEPE_CODI']]["DEFAULT"]);
        $tpl->parse('row');
        $rsDep->MoveNext();
        $contEstilo++;
    }

    if(!$depeBuscada) $depeBuscada = $dependencia;
    $depeSelect = $rsDep->GetMenu2("dep_sel",
                                    "$dep_sel",
                                    false,
                                    false,
                                    0,
                                    'size="5" class="select" multiple');

    for($i = 0; $i <= 31; $i++) {
        if ($i == 0) {
            $mostrarDias .= "<option value=''>"."". "</option>\n";
        } else {
            if ($i == $dia)	{
                $mostrarDias .= "<option value='$i' selected>$i</option>\n";
            } else $mostrarDias .= "<option value='$i'>$i</option>\n";
        }
    }

	
	
    $meses = array( 0   =>  "Mes",
                    1   =>  "Enero",
                    2   =>  "Febrero",
                    3   =>  "Marzo",
                    4   =>  "Abril",
                    5   =>  "Mayo",
                    6   =>  "Junio",
                    7   =>  "Julio",
                    8   =>  "Agosto",
                    9   =>  "Septiembre",
                    10  =>  "Octubre",
                    11  =>  "Noviembre",
                    12  =>  "Diciembre");

    for($i = 0; $i <= 12; $i++) {
        if ($i == 0) {
            $mostrarMeses .= "<option value=" . "0". ">"."Mes". "</option>\n";
        } else {
            if ($i < 10) $datos = "0".$i;
            else $datos = $i;
            if ($datos == $mes) {
                $mostrarMeses .= "<option value='$i' 'selected'>".$meses[$i]."</option>\n";
            } else $mostrarMeses .= "<option value='$i'>".$meses[$i]."</option>\n";
        }
    }

    $tpl->setVariable('ACTION_FORM',$actionForm);
    $tpl->setVariable('TITULO_FORM',$tituloCrear);
    $tpl->setVariable('PERF_1',     $perf_1);
    $tpl->setVariable('PERF_2',     $perf_2);
	$tpl->setVariable('PERF_3',     $perf_3);
    $tpl->setVariable('DEPE_SELECT',$depeSelect);
    $tpl->setVariable('NOMBRE_JEFE',$nombreJefe);
    $tpl->setVariable('CEDULA_YA',  $cedulaYa);
    $tpl->setVariable('CEDULA',     $cedula);
    $tpl->setVariable('LECTURA',    ($usModo == 1) ? "" : "readonly");
    $tpl->setVariable('USUA_LOGIN', $usuLogin);
    $tpl->setVariable('NOMBRE',     $nombre);
    $tpl->setVariable('MOSTRAR_DIAS', $mostrarDias);
    $tpl->setVariable('MOSTRAR_MESES', $mostrarMeses);
    $tpl->setVariable('ANO',        $ano);
    $tpl->setVariable('UBICACION',  $ubicacion);
    $tpl->setVariable('PISO',       $piso);
    $tpl->setVariable('EXTENSION',  $extension);
    $tpl->setVariable('EMAIL',      $email);
    $tpl->setVariable('EMAIL1',     $email1);
    $tpl->setVariable('EMAIL2',     $email2);
    $tpl->setVariable('ENTRADA',    $entrada);
    $tpl->setVariable('MODIFICACIONES', $modificaciones);
    $tpl->setVariable('MASIVA',     $masiva);
    $tpl->setVariable('IMPRESION',  $impresion);
    $tpl->setVariable('TEMAS_EXPEDIENTES',  $exp_temas);
	$tpl->setVariable('CC_ALARMAS',  $ccalarmas);
	$tpl->setVariable('PERMDESPLA',  $permDespla);
	$tpl->setVariable('NO_TRD',  $no_trd);
    $tpl->setVariable('S_ANULACIONES', $s_anulaciones);
    $tpl->setVariable('ANULACIONES',$anulaciones);
    $tpl->setVariable('ADM_ARCHIVO',$adm_archivo);
    $tpl->setVariable('DEV_CORREO', $dev_correo);
    $tpl->setVariable('ADM_SISTEMA',$adm_sistema);
    $tpl->setVariable('ENV_CORREO', $env_correo);
    $tpl->setVariable('REASIGNAR',  $reasigna);
    $tpl->setVariable('ESTADISTICAS', $estadisticas);
    $tpl->setVariable('USUA_ACTIVO',$usua_activo);
    $tpl->setVariable('USUA_NUEVOM',$usua_nuevoM);
    $tpl->setVariable('NIVEL',      $nivel);
    $tpl->setVariable('USUDOCSEL',  $usuDocSel);
    $tpl->setVariable('USULOGINSEL',$usuLoginSel);
    $tpl->setVariable('PERFILORIG', $perfilOrig);
    $tpl->setVariable('NUSUA_CODI', $nusua_codi);
	$tpl->setVariable('ORDENA',  $ordena);
	$tpl->setVariable('NOTIF_ADMIN',  $notifAdm);
	
	//Inicio Acciones masivas cuerpo.php
	$tpl->setVariable('ACCMASIVA_TRD'		, $accMasiva_trd);
	$tpl->setVariable('ACCMASIVA_INCLUIR'	, $accMasiva_incluir);
	$tpl->setVariable('ACCMASIVA_PRESTAMO'	, $accMasiva_prestamo);
	$tpl->setVariable('ACCMASIVA_TEMAS'		, $accMasiva_temas);
	//Fin Acciones masivas cuerpo.php
    
	$tpl->setVariable('ENLACE_CANCELAR', $hrefCancel);
    $tpl->setVariable('permRadEmail', trim($permRadEmail));
    $tpl->show();
?>
