<?php
include_once ORFEOPATH . "include/db/ConnectionHandler.php";
include_once ORFEOPATH . "config.php";
include_once ORFEOPATH . "clasesComunes/ConsultasSQL.php";
// contiene funcion que verifica usuario y Password en LDAP
include_once ORFEOPATH . "autenticaLDAP.php";
if (! function_exists('getIpClient'))
    require_once "funcGetIp.php";

$db = new ConnectionHandler(ORFEOPATH);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$objSql = new ConsultasSQL();
// $db->conn->debug = true;
if (! defined('ADODB_ASSOC_CASE'))
    define('ADODB_ASSOC_CASE', 1);

$krd = strtoupper($krd);
$fechah = date("Ymd") . "_" . time("hms");
$check = 1;
$numeroa = 0;
$numero = 0;
$numeros = 0;
$numerot = 0;
$numerop = 0;
$numeroh = 0;
$ValidacionKrd = "";
$cambioDepen = $_POST['cambioDepen'];
$codigoDepen = $_POST['codigoDepen'];

$defaultDep = (! empty($cambioDepen) && $cambioDepen == true) ? "AND USD.SGD_USD_DEFAULT = 1" : "AND USD.DEPE_CODI = $codigoDepen";
if (empty($krd)) {
    echo "<< DEPENDENCIA, USUARIO O CONTRASE&Ntilde;A INCORRECTOS >>";
    exit(1);
}

$sqlDeps = sprintf("SELECT  USU.USUA_LOGIN,
                        USU.USUA_DOC,
                        DEP.DEPE_CODI,
                        USD.SGD_USD_DEFAULT,
                        DEP.DEPE_NOMB
                    FROM    USUARIO USU,
                            DEPENDENCIA DEP,
                            SGD_USD_USUADEPE USD
                    WHERE   USU.USUA_LOGIN = %s AND
                            USU.USUA_LOGIN = USD.USUA_LOGIN AND
                            USU.USUA_DOC = USD.USUA_DOC AND
                            DEP.DEPE_CODI = USD.DEPE_CODI
                            AND USD.SGD_USD_DEFAULT = 1
                    ORDER BY USD.SGD_USD_DEFAULT DESC,
                            DEP.DEPE_NOMB", $objSql->prepararValorSql($krd));

$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$rsDeps = $db->conn->Execute($sqlDeps);
if ($rsDeps && ! $rsDeps->EOF) {
    $dependencia = $rsDeps->fields['DEPE_CODI'];
    $cedula = $rsDeps->fields['USUA_DOC'];
    $query = "SELECT a.SGD_TRAD_CODIGO, a.SGD_TRAD_DESCR, a.SGD_TRAD_ICONO FROM SGD_TRAD_TIPORAD a ORDER BY a.SGD_TRAD_CODIGO";
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $rs = $db->conn->Execute($query);
    $varQuery = $query;
    $comentarioDev = ' Busca todos los tipos de Radicado Existentes ';
    $iTpRad = 0;
    $queryTip3 = "";
    $tpNumRad = array();
    $tpDescRad = array();
    $tpImgRad = array();
    while (! $rs->EOF) {
        $numTp = $rs->fields["SGD_TRAD_CODIGO"];
        $sqlCarpDep = "SELECT SGD_CARP_DESCR
                        FROM SGD_CARP_DESCRIPCION
                        WHERE SGD_CARP_DEPECODI = $dependencia AND
                                SGD_CARP_TIPORAD = $numTp";

        $rsCarpDesc = $db->conn->Execute($sqlCarpDep);
        $descripcionCarpeta = $rsCarpDesc->fields["SGD_CARP_DESCR"];

        if ($descripcionCarpeta) {
            $descTp = $descripcionCarpeta;
        } else {
            $descTp = $rs->fields["SGD_TRAD_DESCR"];
        }

        $imgTp = $rs->fields["SGD_TRAD_ICONO"];
        $queryTRad .= ",a.USUA_PRAD_TP$numTp";
        $queryDepeRad .= ",b.DEPE_RAD_TP$numTp";
        $queryTip3 .= ",a.SGD_TPR_TP$numTp";
        $tpNumRad[$iTpRad] = $numTp;
        $tpDescRad[$iTpRad] = $descTp;
        $tpImgRad[$iTpRad] = $imgTp;
        $iTpRad ++;
        $rs->MoveNext();
    }
    /**
     * BUSQUEDA DE ICONOS Y NOMBRES PARA LOS TERCEROS (Remitentes/Destinarios) AL RADICAR
     *
     * @param $tip3[][][] Array
     *            Contiene los tipos de radicacion existentes.
     *            En la primera dimencion indica la posicion dependiendo del tipo de rad.
     *            (ej. salida -> 1, ...). En la segunda dimension
     *            almacenara los datos de nombre del tipo de rad. inidicado,
     *            Para la tercera dimencion indicara la descripcion del tercero y en la cuarta dim.
     *            contiene el nombre del archio imagen del tipo de tercero.
     */

    $query = "SELECT a.SGD_DIR_TIPO, a.SGD_TIP3_CODIGO, a.SGD_TIP3_NOMBRE,
                    a.SGD_TIP3_DESC, a.SGD_TIP3_IMGPESTANA $queryTip3 
                FROM SGD_TIP3_TIPOTERCERO a";
    $rs = $db->conn->Execute($query);

    while (! $rs->EOF) {
        $dirTipo = $rs->fields["SGD_DIR_TIPO"];
        $nombTip3 = $rs->fields["SGD_TIP3_NOMBRE"];
        $descTip3 = $rs->fields["SGD_TIP3_DESC"];
        $imgTip3 = $rs->fields["SGD_TIP3_IMGPESTANA"];
        for ($iTp = 0; $iTp < $iTpRad; $iTp ++) {
            $numTp = $tpNumRad[$iTp];
            $campoTip3 = "SGD_TPR_TP$numTp";
            $numTpExiste = $rs->fields[$campoTip3];
            if ($numTpExiste >= 1) {
                $tip3Nombre[$dirTipo][$numTp] = $nombTip3;
                $tip3desc[$dirTipo][$numTp] = $descTip3;
                $tip3img[$dirTipo][$numTp] = $imgTip3;
                // echo "<hr> $ tip3img[$dirTipo][$numTp] =". $tip3img[$dirTipo][$numTp] ."<hr>";
            }
        }
        $rs->MoveNext();
    }

    if ($recOrfeo == "Seguridad") {
        $krdses = str_replace(".", "", $krd);
        $queryRec = "AND USUA_SESION='" . str_replace(".", "o", $_SERVER['REMOTE_ADDR']) . "o$krdses' ";
    } else {
        // Consulta rapida para saber si el usuario se autentica por LDAP o por DB
        $myQuery = "SELECT USUA_AUTH_LDAP from USUARIO where USUA_LOGIN ='$krd'";
        $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $db->conn->Execute($myQuery);
        $autenticaPorLDAP = $rs->fields['USUA_AUTH_LDAP'];

        if ($autenticaPorLDAP == 0) {
            $queryRec = "AND (USUA_PASW ='" . SUBSTR(md5($drd), 1, 26) . "' or USUA_NUEVO=0)";
        } else {
            $queryRec = '';
        }
    }

    // Analiza la opcion de que se trate de un requerimieento de sesion desde una maquina segura
    if ($_SERVER['REMOTE_ADDR'] == $host_log_seguro) {
        $REMOTE_ADDR = $ipseguro;
        $queryRec = "";
        $swSessSegura = 1;
    }

    $query = "SELECT a.*,
                USD.DEPE_CODI,
				b.DEPE_NOMB,
				a.USUA_ESTA,
				a.USUA_CODI,
				a.USUA_LOGIN,
				b.DEPE_CODI_TERRITORIAL,
				b.DEPE_CODI_PADRE,
				a.USUA_PERM_ENVIOS,
				a.USUA_PERM_MODIFICA,
				a.USUA_PERM_EXPEDIENTE,
				a.USUA_EMAIL,
				a.USUA_AUTH_LDAP,
                a.USUA_PERM_RADEMAIL,
                a.USUA_ADM_SERVWEB,
                b.DEP_SIGLA,
				a.USUA_NOTIF_ADMIN
				$queryTRad
				$queryDepeRad
			FROM USUARIO a,
				DEPENDENCIA b,
                SGD_USD_USUADEPE USD
			WHERE a.USUA_LOGIN =" . $objSql->prepararValorSql($krd) . " AND
                a.USUA_LOGIN = USD.USUA_LOGIN AND
                USD.SGD_USD_DEFAULT = 1 AND
				USD.DEPE_CODI = b.DEPE_CODI
				$queryRec";

    /**
     * Procedimiento forech que encuentra los numeros de secuencia para las radiciones
     *
     * @param
     *            tpDepeRad[] array Muestra las dependencias que contienen las secuencias para radicacion.
     */
    $varQuery = $query;
    $comentarioDev = ' Busca Permisos de Usuarios ...';
    // include "$ruta_raiz/include/tx/ComentarioTx.php";
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $rs = $db->conn->Execute($query);
    // Si no se autentica por LDAP segun los permisos de DB
    if (! $autenticaPorLDAP) {
        if (trim($rs->fields["USUA_LOGIN"]) == $krd) {
            $validacionUsuario = '';
        } else { // Password no concuerda con el de la DB, luego no puede ingresar
            $mensajeError = "CREDENCIALES ERRONEAS";
            $validacionUsuario = 'No Pasa Validacion Base de Datos';
        }
    } else { // El usuario tiene Validacion por LDAP
        $correoUsuario = $rs->fields['USUA_EMAIL'];
        // Verificamos que tenga correo en la DB, si no tiene no se puede validar por LDAP
        // No tiene correo, entonces error LDAP
        if ($correoUsuario == '') {
            $validacionUsuario = 'No Tiene Correo';
            $mensajeError = "EL USUARIO NO TIENE CORREO ELECTR&Oacute;NICO REGISTRADO";
        } else {
            // Tiene correo, luego lo verificamos por LDAP
            $validacionUsuario = checkldapuser($correoUsuario, $drd, $ldapServer);
            $mensajeError = $validacionUsuario;
        }
    }

    if (! $validacionUsuario) {
        $perm_radi_salida_tp = 0;
        foreach ($tpNumRad as $key => $valueTp) {
            $campo = "DEPE_RAD_TP$valueTp";
            $campoPer = "USUA_PRAD_TP$valueTp";
            $tpDepeRad[$valueTp] = $rs->fields[$campo];
            $tpPerRad[$valueTp] = $rs->fields[$campoPer];
            if ($tpPerRad[$valueTp] >= 1) {
                $perm_radi_salida_tp = 1;
            }
            $tpDependencias .= "<" . $rs->fields[$campo] . ">";
        }

        // include "$ruta_raiz/include/tx/ComentarioTx.php";
        if (trim($rs->fields["USUA_LOGIN"]) == $krd) {
            if (trim($rs->fields["USUA_ESTA"]) == 1) {
                $fechah = date("dmy") . "_" . time("hms");
                $dependencia = $rs->fields["DEPE_CODI"];
                $dependencianomb = $rs->fields["DEPE_NOMB"];
                $codusuario = $rs->fields["USUA_CODI"];
                $usua_doc = $rs->fields["USUA_DOC"];
                $usua_nomb = $rs->fields["USUA_NOMB"];
                $usua_rol = $rs->fields["SGD_ROL_CODI"];
                $usua_piso = $rs->fields["USUA_PISO"];
                $usua_nacim = $rs->fields["USUA_NACIM"];
                $usua_ext = $rs->fields["USUA_EXT"];
                $usua_at = $rs->fields["USUA_AT"];
                $usua_nuevo = $rs->fields["USUA_NUEVO"];
                $usua_email = $rs->fields["USUA_EMAIL"];
                $usua_email1 = $rs->fields["USUA_EMAIL_1"];
                $usua_email2 = $rs->fields["USUA_EMAIL_2"];
                $dep_sigla = $rs->fields["DEP_SIGLA"];
                $nombusuario = $rs->fields["USUA_NOMB"];
                $contraxx = $rs->fields["USUA_PASW"];
                $depe_nomb = $rs->fields["DEPE_NOMB"];
                $crea_plantilla = $rs->fields["USUA_ADM_PLANTILLA"];
                $usua_admin_archivo = $rs->fields["USUA_ADMIN_ARCHIVO"];
                $usua_perm_trd = $rs->fields["USUA_PERM_TRD"];
                $usua_perm_estadistica = $rs->fields["SGD_PERM_ESTADISTICA"];
                $usua_admin_sistema = $rs->fields["USUA_ADMIN_SISTEMA"];
                $perm_radi = $rs->fields["PERM_RADI"];
                // $perm_radi_sal = $rs->fields["PERM_RADI_SAL"];
                // 1 asignar radicado, 2 carpeta Impresion, 3 uno y 2.
                $usua_perm_impresion = $rs->fields["USUA_PERM_IMPRESION"];
                $usua_perm_tem_exp = $rs->fields["USUA_PERM_TEM_EXP"];
                $usua_perm_despla = $rs->fields["USUA_PERM_DESPLA"];
                $usua_perm_cc_alar = $rs->fields["USUA_PERM_CC_ALAR"];
                $perm_tipif_anexo = $rs->fields["PERM_TIPIF_ANEXO"];
                $perm_borrar_anexo = $rs->fields["PERM_BORRAR_ANEXO"];
                $perm_recv_notifrenv = $rs->fields["USUA_PERMNOTREENVIO"];
                // $perm_recv_emailnohabil = $rs->fields["USUA_PERM_REENVIO_EMAILNOHABIL"];
                if ($usua_perm_impresion == 1) {
                    if ($perm_radi_salida_tp >= 1)
                        $perm_radi_sal = 3;
                    else
                        $perm_radi_sal = 1;
                } else {
                    if ($perm_radi_salida_tp >= 1)
                        $perm_radi_sal = 1;
                }
                $usua_perm_pqrverbal = $rs->fields["USUA_PRAD_PQRVERBAL"];
                $usua_masiva = $rs->fields["USUA_MASIVA"];
                $depe_codi_padre = $rs->fields["DEPE_CODI_PADRE"];
                $usua_perm_numera_res = $rs->fields["USUA_PERM_NUMERA_RES"];
                $depe_codi_territorial = $rs->fields["DEPE_CODI_TERRITORIAL"];
                $usua_perm_dev = $rs->fields["USUA_PERM_DEV"];
                $usua_perm_anu = $rs->fields["SGD_PANU_CODI"];
                $usua_perm_envios = $rs->fields["USUA_PERM_ENVIOS"];
                $usua_perm_modifica = $rs->fields["USUA_PERM_MODIFICA"];
                $usuario_reasignacion = $rs->fields["USUARIO_REASIGNAR"];
                $usua_perm_sancionad = $rs->fields["USUA_PERM_SANCIONADOS"];
                $usua_perm_intergapps = $rs->fields["USUA_PERM_INTERGAPPS"];
                $usua_perm_firma = $rs->fields["USUA_PERM_FIRMA"];
                $usua_perm_prestamo = $rs->fields["USUA_PERM_PRESTAMO"];
                $usua_perm_notifica = $rs->fields["USUA_PERM_NOTIFICA"];
                $usuaPermExpediente = $rs->fields["USUA_PERM_EXPEDIENTE"];
                $usuaPermRadEmail = $rs->fields["USUA_PERM_RADEMAIL"];
                $perm_servweb = $rs->fields["USUA_ADM_SERVWEB"];
                // Permisos masiva
                $accMasiva_trd = $rs->fields["USUA_MASIVA_TRD"];
                $accMasiva_incluir = $rs->fields["USUA_MASIVA_INCLUIR"];
                $accMasiva_prestamo = $rs->fields["USUA_MASIVA_PRESTAMO"];
                $accMasiva_temas = $rs->fields["USUA_MASIVA_TEMAS"];
                $identificacion = $rs->fields["IDENTIFICACION"];
                // Permisos fin
                $repMailRadExp = $rs->fields["USUA_NOTIF_RADEXP"];
                $modificatrd = $rs->fields["USUA_MODIFICA_TRD"];
                $retipificatrd = $rs->fields["USUA_RETIPIFICA_TRD"];
                $anexarCorreo = $rs->fields["USUA_ANEXA_CORREO"];
                $modificarTipoDoc = $rs->fields["USUA_MODIFICA_TIPODOC"];
                // Traemos el campo que indica si el usuario puede utilizar el administrador de flujos o no
                $usua_perm_adminflujos = $rs->fields["USUA_PERM_ADMINFLUJOS"];
                $usua_perm_notifAdmin = $rs->fields["USUA_NOTIF_ADMIN"];
                $usuaPermTemas = $rs->fields["USUA_PERM_TEMAS"];
                $mostrar_opc_envio = 0;
                $repNotifCorreo = $rs->fields["USUA_NOTIF_CORREO"];
                // if($drd){$drde=md5($drd);}
                // cerrar session
                $nivelus = $rs->fields["CODI_NIVEL"];

                $isql = "SELECT b.MUNI_NOMB FROM DEPENDENCIA a,MUNICIPIO b
						WHERE a.MUNI_CODI = b.MUNI_CODI
							and a.DPTO_CODI = b.DPTO_CODI
							and a.MUNI_CODI = b.MUNI_CODI
							and a.DPTO_CODI = '$dependencia'";
                $rs = $db->conn->Execute($isql);
                $depe_municipio = $rs->fields["MUNI_NOMB"];

                // Consulta que anade los nombres y codigos de carpetas del Usuario
                $isql = "SELECT CARP_CODI, CARP_DESC FROM CARPETA";
                $rs = $db->conn->Execute($isql);
                $iC = 0;
                while (! $rs->EOF) {
                    $iC = $rs->fields["CARP_CODI"];
                    $descCarpetasGen[$iC] = $rs->fields["CARP_DESC"];
                    $rs->MoveNext();
                }
                $isql = "SELECT CODI_CARP, DESC_CARP
                        FROM CARPETA_PER WHERE USUA_CODI=$codusuario AND DEPE_CODI = $dependencia";
                $rs = $db->conn->Execute($isql);
                // $rs = $db->conn->Execute($query);
                $iC = 0;
                while (! $rs->EOF) {
                    $iC = $rs->fields["CODI_CARP"];
                    $descCarpetasPer[$iC] = $rs->fields["DESC_CARP"];
                    $rs->MoveNext();
                }

                // Busca si el usuario puede integrar aplicativos
                $sqlIntegraApp = "SELECT a.SGD_APLI_DESCRIP, a.SGD_APLI_CODI, u.SGD_APLUS_PRIORIDAD
                                 FROM SGD_APLI_APLINTEGRA a, SGD_APLUS_PLICUSUA  u
                                 WHERE u.USUA_DOC = '$usua_doc' AND a.SGD_APLI_CODI <> 0 AND
                                     a.SGD_APLI_CODI =  u.SGD_APLI_CODI";
                $rsIntegra = $db->conn->Execute($sqlIntegraApp);

                if ($rsIntegra && ! $rsIntegra->EOF)
                    $usua_perm_intergapps = 1;

                // Fin Consulta de carpetas
                /*
                 * Creada por HLP. *
                 * Query para construir $cod_local. La cual contiene ID_CONTinente+ID_PAIS+id_dpto+id_mncpio. *
                 * Si $cod_local=0, significa que NO hay un municipio como local. ORFEO DEBE TENER localidad. *
                 */
                $ADODB_COUNTRECS = true;

                $isql = "SELECT d.ID_CONT, d.ID_PAIS, d.DPTO_CODI, d.MUNI_CODI, m.MUNI_NOMB
                        FROM DEPENDENCIA d, MUNICIPIO m
                        WHERE d.ID_CONT = m.ID_CONT AND d.ID_PAIS = m.ID_PAIS AND
                            d.DPTO_CODI = m.DPTO_CODI AND d.MUNI_CODI = m.MUNI_CODI AND
                            d.DEPE_CODI = $dependencia";
                $rs_cod_local = $db->conn->Execute("$isql");
                $ADODB_COUNTRECS = false;

                if ($rs_cod_local && ! $rs_cod_local->EOF) {
                    $cod_local = $rs_cod_local->fields['ID_CONT'] . "-" . str_pad($rs_cod_local->fields['ID_PAIS'], 3, 0, STR_PAD_LEFT) . "-" . str_pad($rs_cod_local->fields['DPTO_CODI'], 3, 0, STR_PAD_LEFT) . "-" . str_pad($rs_cod_local->fields['MUNI_CODI'], 3, 0, STR_PAD_LEFT);
                    $depe_municipio = $rs_cod_local->fields["MUNI_NOMB"];
                    $rs_cod_local->Close();
                } else {
                    $cod_local = 0;
                    $depe_municipio = "CONFIGURAR EN SESSION_ORFEO.PHP";
                }
                if ($_SESSION['access_token']) {
                    $get_access_token = $_SESSION['access_token'];
                }
                if ($_SESSION['eMailRemitente']) {
                    $eMailRemitente = $_SESSION['eMailRemitente'];
                }
                if ($_SESSION['eMailNombreRemitente']) {
                    $eMailNombreRemitente = $_SESSION['eMailNombreRemitente'];
                }

                $recOrfeoOld = $recOrfeo;
                $krdses = str_replace(".", "", $krd);
                session_id(str_replace(".", "o", $_SERVER['REMOTE_ADDR']) . "o$krdses");
                session_start();
                $recOrfeo = $recOrfeoOld;
                $fechah = date("Ymd") . "_" . time("hms");
                $carpeta = 0;
                $dirOrfeo = str_replace("login.php", "", $PHP_SELF);
                $_SESSION["entidad"] = $entidad;

                $_SESSION['access_token'] = $get_access_token;
                $_SESSION['eMailRemitente'] = $eMailRemitente;
                $_SESSION['eMailNombreRemitente'] = $eMailNombreRemitente;

                if ($archivado_requiere_exp)
                    $_SESSION["archivado_requiere_exp"] = true;

                $_SESSION["dirOrfeo"] = $dirOrfeo;
                $_SESSION["usua_perm_pqrverbal"] = $usua_perm_pqrverbal;
                $_SESSION["usua_doc"] = trim($usua_doc);
                $_SESSION["dependencia"] = $dependencia;
                $_SESSION["codusuario"] = $codusuario;
                $_SESSION["rolUsuario"] = $usua_rol;
                $_SESSION["depe_nomb"] = $depe_nomb;
                $_SESSION["cod_local"] = $cod_local;
                $_SESSION["depe_municipio"] = $depe_municipio;
                $_SESSION["usua_doc"] = $usua_doc;
                $_SESSION["usua_email"] = $usua_email;
                $_SESSION["usua_email_1"] = $usua_email1;
                $_SESSION["usua_email_2"] = $usua_email2;
                $_SESSION["dep_sigla"] = $dep_sigla;
                $_SESSION["usua_at"] = $usua_at;
                $_SESSION["usua_ext"] = $usua_ext;
                $_SESSION["usua_piso"] = $usua_piso;
                $_SESSION["usua_nacim"] = $usua_nacim;
                $_SESSION["usua_nomb"] = $usua_nomb;
                $_SESSION["usua_nuevo"] = $usua_nuevo;
                $_SESSION["usua_admin_archivo"] = $usua_admin_archivo;
                $_SESSION["usua_masiva"] = $usua_masiva;
                $_SESSION["usua_perm_dev"] = $usua_perm_dev;
                $_SESSION["usua_perm_anu"] = $usua_perm_anu;
                $_SESSION["usua_perm_numera_res"] = $usua_perm_numera_res;
                $_SESSION["perm_radi_sal"] = $perm_radi_sal;
                $_SESSION["depecodi"] = $dependencia;
                $_SESSION["fechah"] = $fechah;
                $_SESSION["crea_plantilla"] = $crea_plantilla;
                // $_SESSION["verrad"] = 0;
                $_SESSION["menu_ver"] = 3;
                $_SESSION["depe_codi_padre"] = $depe_codi_padre;
                $_SESSION["depe_codi_territorial"] = $depe_codi_territorial;
                $_SESSION["nivelus"] = $nivelus;
                $_SESSION["tpNumRad"] = $tpNumRad;
                $_SESSION["tpDescRad"] = $tpDescRad;
                $_SESSION["tpImgRad"] = $tpImgRad;
                $_SESSION["tpDepeRad"] = $tpDepeRad;
                $_SESSION["tpPerRad"] = $tpPerRad;
                $_SESSION["usua_perm_envios"] = $usua_perm_envios;
                $_SESSION["usua_perm_modifica"] = $usua_perm_modifica;
                $_SESSION["usuario_reasignacion"] = $usuario_reasignacion;
                $_SESSION["descCarpetasGen"] = $descCarpetasGen;
                $_SESSION["tip3Nombre"] = $tip3Nombre;
                $_SESSION["tip3desc"] = $tip3desc;
                $_SESSION["tip3img"] = $tip3img;
                $_SESSION["usua_admin_sistema"] = $usua_admin_sistema;
                $_SESSION["perm_radi"] = $perm_radi;
                $_SESSION["usua_perm_sancionad"] = $usua_perm_sancionad;
                $_SESSION["usua_perm_impresion"] = $usua_perm_impresion;
                $_SESSION["usua_perm_tem_exp"] = $usua_perm_tem_exp;
                $_SESSION["usua_perm_despla"] = $usua_perm_despla;
                $_SESSION["usua_perm_cc_alar"] = $usua_perm_cc_alar;
                $_SESSION["usua_perm_intergapps"] = $usua_perm_intergapps;
                $_SESSION["usua_perm_estadistica"] = $usua_perm_estadistica;
                $_SESSION["usua_perm_trd"] = $usua_perm_trd;
                $_SESSION["usua_perm_firma"] = $usua_perm_firma;
                $_SESSION["usua_perm_prestamo"] = $usua_perm_prestamo;
                $_SESSION["usua_perm_notifica"] = $usua_perm_notifica;
                $_SESSION["usuaPermExpediente"] = $usuaPermExpediente;
                $_SESSION["perm_tipif_anexo"] = $perm_tipif_anexo;
                $_SESSION["perm_borrar_anexo"] = $perm_borrar_anexo;
                $_SESSION["autentica_por_LDAP"] = $autenticaPorLDAP;
                $_SESSION["usuaPermRadEmail"] = $usuaPermRadEmail;
                $_SESSION["perm_servweb"] = $perm_servweb;
                $_SESSION["USUA_MASIVA_TRD"] = $accMasiva_trd;
                $_SESSION["USUA_MASIVA_INCLUIR"] = $accMasiva_incluir;
                $_SESSION["USUA_MASIVA_PRESTAMO"] = $accMasiva_prestamo;
                $_SESSION["USUA_MASIVA_TEMAS"] = $accMasiva_temas;
                $_SESSION["USUAPERMTEMAS"] = $usuaPermTemas;
                if ($archivado_requiere_exp) {
                    $_SESSION["archivado_requiere_exp"] = $archivado_requiere_exp;
                }
                $_SESSION["PERMRECNOTIFRENV"] = $perm_recv_notifrenv;
                $_SESSION["repNotifCorreo"] = $repNotifCorreo;
                $_SESSION["usua_email_fe"] = $correo_facelec;
                $_SESSION["modificatrd"] = $modificatrd;
                $_SESSION["retipificatrd"] = $retipificatrd;
                $_SESSION["anexarCorreo"] = $anexarCorreo;
                $_SESSION["modificaTipodoc"] = $modificarTipoDoc;
                // Se pone el permiso de administracion de flujos en la sesion para su posterior consulta
                $_SESSION["usua_perm_adminflujos"] = $usua_perm_adminflujos;
                $_SESSION["usua_perm_notifAdmin"] = $usua_perm_notifAdmin;
                $_SESSION["krd"] = $krd;
                $_SESSION["login"] = $krd;
                $_SESSION["identificacion"] = $identificacion;
                // $_SESSION["mostrar_opc_envio"]=$mostrar_opc_envio;
                $nomcarpera = "ENTRADA";
                if (! $orno)
                    $orno = 0;
                $query = "update USUARIO set USUA_SESION ='" . substr(session_id(), 0, 30) . "',USUA_FECH_SESION=sysdate where  USUA_LOGIN =" . $objSql->prepararValorSql($krd) . " ";
                $recordSet["USUA_SESION"] = " '" . substr(session_id(), 0, 30) . "' ";
                $recordSet["USUA_FECH_SESION"] = $db->conn->OffsetDate(0, $db->conn->sysTimeStamp);
                $recordWhere["USUA_LOGIN"] = $objSql->prepararValorSql($krd);
                $db->update("USUARIO", $recordSet, $recordWhere);
                $sql = "INSERT INTO SGD_HIST_AUTENTICACION (USUA_LOGIN, USUA_DOC, DIR_IP, SGD_TTR_CODIGO, AGENTE, OBSERVACION) VALUES ('" . strtoupper($_SESSION["krd"]) . "','" . $_SESSION["usua_doc"] . "','" . getIpClient() . "', 45, '" . $_SERVER['HTTP_USER_AGENT'] . "', null)";
                $isql = $db->conn->Execute($sql);
                $ValidacionKrd = "Si";
            } else {
                $mensajeError = "";
                $ValidacionKrd = "Errado .... jejejejejejejej";
                if ($recOrfeo == "loginWeb") {
                    ?>
<script language="JavaScript" type="text/JavaScript">
					alert("EL USUARIO <?=$krd?> ESTA INACTIVO \n por favor consulte con el administrador del sistema");
				</script>
<?php
                } else
                    die(include (ORFEOPATH . "paginaError.php"));
            }
        } else {
            if ($recOrfeo == "loginWeb") {
                ?>
<script language="JavaScript" type="text/JavaScript">
				alert("USUARIO O PASSWORD INCORRECTOS \n INTENTE DE NUEVO");
			</script>
<?php
            } else {
                $ValidacionKrd = "Errado .... jejejejejejejej";
                if ($recOrfeo == "Seguridad")
                    die(include ORFEOPATH . "paginaError.php");
                
			     $mensajeError = '<b>
                            	<center>
                            		<font face="Arial, Helvetica, sans-serif" size="2" color="#888888">USUARIO
                            			O CONTRASE&Ntilde;A INCORRECTOS<BR> <BR>INTENTE DE NUEVO
                            		</font>
                            	</center>
                            </b>';
            }
        }
    } else {
        if ($recOrfeo == "loginWeb") {
            ?>
<script language="JavaScript" type="text/JavaScript">
            alert("USUARIO O PASSWORD INCORRECTOS \n INTENTE DE NUEVO");
		</script>
<?php
        } else {
            $ValidacionKrd = "Errado .... jejejejejejejej";
            if ($recOrfeo == "Seguridad")
                die(include ORFEOPATH . "paginaError.php");
            if (! $autenticaPorLDAP) {
                if ($_SERVER['PHP_SELF'] == "/login.php") {
                    $sql = "INSERT INTO SGD_HIST_AUTENTICACION (USUA_LOGIN, USUA_DOC, DIR_IP, SGD_TTR_CODIGO, AGENTE, OBSERVACION) VALUES ('" . strtoupper($_POST['krd']) . "','" . $cedula . "','" . getIpClient() . "', 44, '" . $_SERVER['HTTP_USER_AGENT'] . "', 'Error BD.')";
                    $isql = $db->conn->Execute($sql);
                }
                
                $mensajeError = "<b><center>
                    		<font face='Arial, Helvetica, sans-serif' size='2' color='#888888'>FALLA
                    			AUTENTICACI&Oacute;N <br> <br><?=$mensajeError?>
                      			<br> <br>INTENTE DE NUEVO 1
                    		</font>
                    	</center></b>";
            } else {
                if ($_SERVER['PHP_SELF'] == "/login.php") {
                    $sql = "INSERT INTO SGD_HIST_AUTENTICACION (USUA_LOGIN, USUA_DOC, DIR_IP, SGD_TTR_CODIGO, AGENTE, OBSERVACION) VALUES ('" . strtoupper($_POST['krd']) . "','" . $cedula . "','" . getIpClient() . "', 44, '" . $_SERVER['HTTP_USER_AGENT'] . "', 'Error LDAP.')";
                    $isql = $db->conn->Execute($sql);
                }
                $mensajeError = "<b><center>
            		<font face='Arial, Helvetica, sans-serif' size='2' color='#888888'>FALLA
            			AUTENTICACI&Oacute;N <br> <br><?=$mensajeError?>
              			<br> <br>INTENTE DE NUEVO 2
            		</font>
            	</center></b>";
            }
        }
    }
} else {
		$mensajeError = "<b><center>
                		<font face='Arial, Helvetica, sans-serif' size='2' color='#888888'>FALLA
                			AUTENTICACI&Oacute;N <br> <br>.CREDENCIALES ERRONEAS. <br> <br>INTENTE
                			DE NUEVO 3
                		</font>
                	</center></b>";
}
?>