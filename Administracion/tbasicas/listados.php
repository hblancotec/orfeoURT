<?php
/*  Visualizador de Listados.
 * 	Creado por: Ing. Hollman Ladino Paredes.
 * 	Para el proyecto ORFEO.
 *
 * 	Permite la visualizacion general de paises, departemntos, municipios, tarifas, etc.
 * 	Es una idea basica. Aun esta bajo desarrollo.
 */

session_start();
$ruta_raiz = "../..";
if (count($_SESSION) == 0) {
    die(include $ruta_raiz."/sinacceso.php");
    exit;
}
else if (isset($_SESSION['krd'])) {
    $krd = $_SESSION["login"];
}

if ($_SESSION['usua_admin_sistema'] != 1) {
    die(include $ruta_raiz."/sinpermiso.php");
    exit;
}

include "$ruta_raiz/config.php";   // incluir configuracion.
include 'adodb/adodb.inc.php';
include 'adodb/tohtml.inc.php';
$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
$ADODB_COUNTRECS = true;

$error = 0;
$dsn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
$conn = NewADOConnection($dsn);
//$conn->debug=1;

switch ($_GET['var']) {
	case 'tpr' : {
		$titulo = "LISTADO GENERAL DE TIPOS DE RADICADO";
		$tit_columnas = array('C&oacute;digo', 'Nombre');
		$isql = "SELECT SGD_TRAD_CODIGO, SGD_TRAD_DESCR FROM SGD_TRAD_TIPORAD ORDER BY SGD_TRAD_CODIGO";
	}break;
    case 'tar' : {
            $titulo = "LISTADO GENERAL DE TARIFAS";
            $tit_columnas = array('Forma Envio', 'Nal / InterNal.', 'C&oacute;d. Tarifa', 'Desc. Tarifa', 'Urbano/Zona1', 'Regional/Zona2', 'Nacional/Zona3', 'Tray. Especiales');
            $valor1 = $conn->IfNull('SGD_TAR_TARIFAS.SGD_TAR_VALENV1', 'SGD_TAR_TARIFAS.SGD_TAR_VALENV1G1');
            $isql = "SELECT SGD_FENV_FRMENVIO.SGD_FENV_DESCRIP, IIF(SGD_CLTA_CLSTARIF.SGD_CLTA_CODSER=1, 'NACIONAL','INTERNACIONAL'), SGD_CLTA_CLSTARIF.SGD_TAR_CODIGO, SGD_CLTA_CLSTARIF.SGD_CLTA_DESCRIP,
                      SGD_TAR_TARIFAS.SGD_TAR_VALENV1 AS VALOR1, SGD_TAR_TARIFAS.SGD_TAR_VALENV2 AS VALOR2,
                      SGD_TAR_TARIFAS.SGD_TAR_VALENV1G1 AS VALOR3, SGD_TAR_TARIFAS.SGD_TAR_VALENV2G2 AS VALOR4
					FROM SGD_CLTA_CLSTARIF, SGD_TAR_TARIFAS, SGD_FENV_FRMENVIO
					WHERE SGD_CLTA_CLSTARIF.SGD_FENV_CODIGO = SGD_TAR_TARIFAS.SGD_FENV_CODIGO AND
                      SGD_CLTA_CLSTARIF.SGD_TAR_CODIGO = SGD_TAR_TARIFAS.SGD_TAR_CODIGO AND
                      SGD_CLTA_CLSTARIF.SGD_CLTA_CODSER = SGD_TAR_TARIFAS.SGD_CLTA_CODSER AND
					  SGD_CLTA_CLSTARIF.SGD_FENV_CODIGO = SGD_FENV_FRMENVIO.SGD_FENV_CODIGO
					ORDER BY SGD_CLTA_CLSTARIF.SGD_CLTA_CODSER, SGD_CLTA_CLSTARIF.SGD_FENV_CODIGO,
					SGD_CLTA_CLSTARIF.SGD_TAR_CODIGO";
        }break;
    case 'pai' : {
            $titulo = "LISTADO GENERAL DE PAISES";
            $tit_columnas = array('Continente', 'Id Pais', 'Nombre Pais');
            $isql = "SELECT SGD_DEF_CONTINENTES.NOMBRE_CONT, SGD_DEF_PAISES.ID_PAIS, SGD_DEF_PAISES.NOMBRE_PAIS
					FROM SGD_DEF_PAISES, SGD_DEF_CONTINENTES WHERE SGD_DEF_PAISES.ID_CONT = SGD_DEF_CONTINENTES.ID_CONT
					ORDER BY SGD_DEF_CONTINENTES.NOMBRE_CONT, SGD_DEF_PAISES.NOMBRE_PAIS";
        }break;
    case 'dpt' : {
            $titulo = "LISTADO GENERAL DE DEPARTAMENTOS";
            $tit_columnas = array('Continente', 'Nombre Pais', 'Id Dpto', 'Nombre Dpto');
            $isql = "SELECT SGD_DEF_CONTINENTES.NOMBRE_CONT, SGD_DEF_PAISES.NOMBRE_PAIS, DEPARTAMENTO.DPTO_CODI, DEPARTAMENTO.DPTO_NOMB
					FROM SGD_DEF_PAISES, SGD_DEF_CONTINENTES, DEPARTAMENTO
					WHERE SGD_DEF_PAISES.ID_CONT = SGD_DEF_CONTINENTES.ID_CONT AND
						SGD_DEF_PAISES.ID_PAIS = DEPARTAMENTO.id_pais AND
						SGD_DEF_PAISES.ID_CONT = DEPARTAMENTO.id_cont
					ORDER BY SGD_DEF_CONTINENTES.NOMBRE_CONT, SGD_DEF_PAISES.NOMBRE_PAIS, DEPARTAMENTO.DPTO_NOMB";
        }break;
    case 'ctt' : {
            $titulo = "LISTADO GENERAL DE CONTACTOS";
            $tit_columnas = array('Tipo Contacto', 'Empresa/Entidad', 'Id Contacto', 'Nombre Contacto', 'Cargo Contacto', 'Telefono Contacto');
            $isql = "SELECT 'TIPO' = CASE WHEN c.CTT_ID_TIPO = 1 THEN 'Entidad' WHEN c.CTT_ID_TIPO = 2 THEN 'Otras Emp.' END,b.NOMBRE_DE_LA_EMPRESA,c.CTT_ID, c.CTT_NOMBRE, c.CTT_CARGO, c.CTT_TELEFONO
					FROM SGD_DEF_CONTACTOS c, BODEGA_EMPRESAS b
					WHERE c.CTT_ID_EMPRESA = b.IDENTIFICADOR_EMPRESA AND c.CTT_ID_TIPO=1
					UNION
					SELECT 'TIPO' = CASE WHEN c.CTT_ID_TIPO = 1 THEN 'Entidad' WHEN c.CTT_ID_TIPO = 2 THEN 'Otras Emp.' END, 	b.SGD_OEM_OEMPRESA,c.CTT_ID, c.CTT_NOMBRE, c.CTT_CARGO, c.CTT_TELEFONO
					FROM SGD_DEF_CONTACTOS c, SGD_OEM_OEMPRESAS b
					WHERE c.CTT_ID_EMPRESA = b.SGD_OEM_CODIGO AND c.CTT_ID_TIPO=2
					ORDER BY 1,2,4";
        }break;
    case 'bge' : {
            $titulo = "LISTADO GENERAL DE ESP";
            $tit_columnas = array('Empresa', 'Sigla', 'Correo E', 'Teléfonos', 'NIT', 'NIUR', 'Id Empresa');
            $isql = "SELECT NOMBRE_DE_LA_EMPRESA, SIGLA_DE_LA_EMPRESA, EMAIL, TELEFONO_1, NIT_DE_LA_EMPRESA,
					NUIR, IDENTIFICADOR_EMPRESA
					FROM BODEGA_EMPRESAS
					ORDER BY NOMBRE_DE_LA_EMPRESA, SIGLA_DE_LA_EMPRESA";
        }break;
    case 'dpc' : {
            $titulo = "LISTADO GENERAL DE DEPENDENCIAS";
            $tit_columnas = array('Id', 'Nombre', 'Sigla', 'Estado', 'Nombre Dpto');
            $isql = "SELECT DEPE_CODI, DEPE_NOMB, DEP_SIGLA, DEPENDENCIA_ESTADO
                                FROM DEPENDENCIA ORDER BY DEPE_CODI";
        }break;
    case 'med' : {
            $titulo = "LISTADO GENERAL DE FORMAS DE ENVIO";
            $tit_columnas = array('Id', 'Nombre', 'Estado');
            $isql = "SELECT SGD_FENV_CODIGO, SGD_FENV_DESCRIP, SGD_FENV_ESTADO FROM SGD_FENV_FRMENVIO ORDER BY SGD_FENV_CODIGO";
        }break;
    case 'mrp' : {
            $titulo = "LISTADO GENERAL DE MEDIOS DE RECEPCION";
            $tit_columnas = array('Id', 'Nombre', 'Estado', 'Descripcion');
            $isql = "SELECT MREC_CODI, MREC_DESC, MREC_ESTADO, MREC_DESCRIPCION FROM MEDIO_RECEPCION ORDER BY MREC_CODI";
        }break;
    case 'alt' : {
            $titulo = "LISTADO GENERAL DE ALERTAS";
            $tit_columnas = array('Nombre', 'T.Doc', 'D. Termino', 'D. Antes', 'D. Despues', 'Usuario copia',
                'Rad.(1) Genera','Rad.(2) Genera','Rad.(3) Genera','Rad.(4) Genera','Rad.(5) Genera','Rad.(6) Genera','Rad.(7) Genera','Rad.(8) Genera','Rad.(9) Genera',
                'Rad.(1) Detiene','Rad.(2) Detiene','Rad.(3) Detiene','Rad.(4) Detiene','Rad.(5) Detiene','Rad.(6) Detiene','Rad.(7) Detiene','Rad.(8) Detiene','Rad.(9) Detiene',
                'Estado');
            $isql = "SELECT A.SGD_NOMBR_ALER, SGD_TPR_DESCRIP, SGD_DIASTER_ALER, 
			    	SGD_DIASANT_ALER, SGD_DIASDES_ALER, U.USUA_NOMB,
			    	SGD_TRAD1G_ALER, SGD_TRAD2G_ALER, SGD_TRAD3G_ALER, SGD_TRAD4G_ALER, SGD_TRAD5G_ALER, 
					SGD_TRAD6G_ALER, SGD_TRAD7G_ALER, SGD_TRAD8G_ALER, SGD_TRAD9G_ALER, 
					SGD_TRAD1D_ALER, SGD_TRAD2D_ALER, SGD_TRAD3D_ALER, SGD_TRAD4D_ALER, SGD_TRAD5D_ALER, 
					SGD_TRAD6D_ALER, SGD_TRAD7D_ALER, SGD_TRAD8D_ALER, SGD_TRAD9D_ALER, SGD_ESTADO_ALER
			FROM SGD_ALERTAS A 
				INNER JOIN USUARIO U ON A.SGD_USUADOC_ALER = U.USUA_DOC
				INNER JOIN SGD_TPR_TPDCUMENTO D ON A.SGD_TDOC_ALER=D.SGD_TPR_CODIGO";
        }break;
    case 'tmt' : {
            $titulo = "LISTADO GENERAL DE TRAMITES";
            $tit_columnas = array('Nombre', 'Dependencia_Responsable', 'Dependencia_Finaliza', 'Tipos_Radicados_Finalizan');
            $isql = "SELECT T.SGD_NOMBR_TRAM, DR.DEPE_NOMB, DF.DEPE_NOMB, 
					(case when SGD_TRAD1_TRAM=1 then '1 ' else '' end + case when SGD_TRAD2_TRAM=1 then '2 ' else '' end +
					case when SGD_TRAD3_TRAM=1 then '3 ' else '' end + case when SGD_TRAD4_TRAM=1 then '4 ' else '' end +
					case when SGD_TRAD5_TRAM=1 then '5 ' else '' end + case when SGD_TRAD6_TRAM=1 then '6 ' else '' end +
					case when SGD_TRAD7_TRAM=1 then '7 ' else '' end + case when SGD_TRAD8_TRAM=1 then '8 ' else '' end +
					case when SGD_TRAD9_TRAM=1 then '9 ' else '' end) as RAD_GENERA
				FROM SGD_TRAMITES T
					INNER JOIN DEPENDENCIA DR ON T.SGD_DEPRE_TRAM = DR.DEPE_CODI
					INNER JOIN DEPENDENCIA DF ON T.SGD_DEPFI_TRAM = DF.DEPE_CODI
				ORDER BY T.SGD_NOMBR_TRAM";
        }break;
	case 'mime' : {
			$titulo = "LISTADO GENERAL DE TIPOS DE ARCHIVOS";
			$tit_columnas = array('Id','Extensi&oacute;n','Descripci&oacute;n', 'PQR', 'Ordena Anexo');
			$isql = "SELECT ANEX_TIPO_CODI as Id, ANEX_TIPO_EXT as Exte, ANEX_TIPO_DESC as Responsable, ANEX_TIPO_PQR as PQR, ANEX_PERM_TIPIF_ANEXO as ORDANEX FROM ANEXOS_TIPO ORDER BY 1";
		}break;
	case 'cau'	:
		{
			$titulo = "LISTADO GENERAL DE CAUSALES";
			$tit_columnas = array('Id','Nombre', 'Estado');
			$isql =	"SELECT SGD_CAU_CODIGO, SGD_CAU_DESCRIP, SGD_CAU_ESTADO FROM SGD_CAU_CAUSAL ORDER BY 1";
		}break;
	case 'raza'	:
	    {
	        $titulo = "LISTADO GENERAL DE INFORMACI&Oacute;N POBLACIONAL";
	        $tit_columnas = array('Id','Nombre', 'Estado');
	        $isql =	"SELECT ID_INFPOB, SGD_INFPOB_DESC, SGD_INFPOB_ACTIVO FROM SGD_INF_INFPOB ORDER BY 1";
	    }break;
	case 'tmas'	:
		{
			$titulo = "LISTADO GENERAL DE TEMAS";
			$tit_columnas = array('Sector', 'Id', 'Temas', 'Estado', 'PQR', 'PQR_Descripcion');
			$isql =	"SELECT C.SGD_CAU_DESCRIP AS SECTOR, D.SGD_DCAU_CODIGO AS ID, D.SGD_DCAU_DESCRIP AS TEMA, D.SGD_DCAU_ESTADO AS ACTIVA, 
							D.SGD_DCAU_PQR AS ES_PQR, D.SGD_DCAU_PQRDESC AS DESC_PQR
					FROM SGD_DCAU_CAUSAL AS D INNER JOIN
						SGD_CAU_CAUSAL AS C ON D.SGD_CAU_CODIGO = C.SGD_CAU_CODIGO
					ORDER BY D.SGD_DCAU_CODIGO";
		}break;
	case 'usuaPqr'	:
	    {
	        $titulo = "LISTADO DE DEPENCENCIAS Y USUARIOS";
	        $tit_columnas = array('Codigo Dependencia', 'Nombre Dependencia', 'Codigo Usuario', 'Nombre Usuario');
	        $isql =	"SELECT P.DEPE_CODI, D.DEPE_NOMB, U.USUA_CODI, U.USUA_NOMB
                    FROM SGD_PQR_TEMAUSU P INNER JOIN DEPENDENCIA D ON P.DEPE_CODI = D.DEPE_CODI
                    	INNER JOIN USUARIO U ON U.USUA_CODI = P.USUA_CODI AND U.DEPE_CODI = D.DEPE_CODI
                    WHERE SGD_DCAU_CODIGO = " . $_GET["tema"];
	    }break;
	case 'mdd'	:
		{
			$titulo = "LISTADO GENERAL DE MOTIVOS DE DEVOLUCI&Oacute;N";
			$tit_columnas = array('Id','Nombre', 'Estado');
			$isql = 'SELECT SGD_DEVE_CODIGO AS "ID", SGD_DEVE_DESC AS "MOTIVO", SGD_DEVE_ESTADO FROM SGD_DEVE_DEV_ENVIO ORDER BY 1';
		}break;
        case 'eap'	:
            {
                    $titulo = "LISTADO APLICATIVOS EXTERNOS";
                    $tit_columnas = array('Id','Nombre', 'Estado','Usuario Responsable', 'Depndencia Responsable');
                    $isql = 'SELECT  SGD_APLI_CODIGO "ID", SGD_APLI_DESCRIP AS "MOTIVO", SGD_APLI_ESTADO, U.USUA_NOMB, D.DEPE_NOMB 
                            FROM SGD_APLICACIONES A 
                            JOIN USUARIO U ON U.USUA_LOGIN=A.USUA_LOGIN
                            JOIN DEPENDENCIA D ON D.DEPE_CODI=U.DEPE_CODI
                            ORDER BY 1';
            }break;
        case 'metodosWS':
            {
                    $titulo = "LISTADO METODOS PUBLICADOS";
                    $tit_columnas = array('Id','Nombre', 'Estado');
                    $isql = 'SELECT  COD_METODO "ID", NOMBRE , ESTADO 
                            FROM METODOS_WS A 

                            ORDER BY 1';
            }break;
        case 'accionesext':
            {
                    $titulo = "LISTADO ACCIONES EXTERNAS";
                    $tit_columnas = array('Id aplicativo','Aplicativo', ' Id Accion',' Accion', 'Estado');
                    $isql = 'SELECT  AP.SGD_APLI_CODIGO , AP.SGD_APLI_DESCRIP , SGD_ACCION_CODIGO, SGD_ACCION_DESCRIPCION, SGD_ACCION_ESTADO
                            FROM SGD_ACCIONES_EXTERNAS A 
                            JOIN SGD_APLICACIONES AP ON A.SGD_APLI_CODIGO=AP.SGD_APLI_CODIGO
                            ORDER BY 1';
            }break;
         case 'camposext':
            {
                    $titulo = "LISTADO CAMPOS EXTERNOS";
                    $tit_columnas = array('Id aplicativo','Aplicativo', ' Id Campo',' Campo', 'Estado');
                    $isql = 'SELECT  AP.SGD_APLI_CODIGO , AP.SGD_APLI_DESCRIP , SGD_COD_CAMPOEXT, SGD_NOMBRE_CAMPO, SGD_ESTADO_CAMPO
                            FROM SGD_CAMPOS_APPEXT A 
                            JOIN SGD_APLICACIONES AP ON A.SGD_APLI_CODIGO=AP.SGD_APLI_CODIGO
                            ORDER BY 1';
            }break;
    default : {
            $titulo = "LISTADO GENERAL DE MUNICIPIOS";
            $isql = "select	c.nombre_cont as NOMBRE_CONT,p.nombre_pais as NOMBRE_PAIS, d.dpto_nomb as DPTO_NOMB, m.muni_codi as MUNI_CODI, m.muni_nomb as MUNI_NOMB
from municipio m  
	inner join departamento d on m.dpto_codi=d.dpto_codi and m.id_pais=d.id_pais
	inner join sgd_def_paises p on d.id_pais=p.id_pais
	inner join sgd_def_continentes c on p.id_cont=c.id_cont
order by c.nombre_cont, p.nombre_pais, d.dpto_nomb, m.muni_codi ";
        }break;
}

$Rs_clta = $conn->Execute($isql);
$ADODB_COUNTRECS = false;
?>
<html>
    <head>
    	<title><?= $titulo ?></title>
    	<link href="<?php echo $ruta_raiz ?>/estilos/orfeo.css" rel="stylesheet" type="text/css">
    	<style>
    	   table {     font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
    font-size: 12px;    margin: 5px;     width: 100%; text-align: left;    border-collapse: collapse; }

th {     font-size: 13px;     font-weight: normal;     padding: 8px;     background: #006699;
    border-top: 4px solid #aabcfe;    border-bottom: 1px solid #fff; color: #FFFFFF; }

td {    padding: 8px;     background: #e3e8ec;     border-bottom: 1px solid #fff;
    color: #000000;    border-top: 1px solid transparent; }

tr:hover td { background: #d0dafd; color: #339; }
    	</style>
    </head>
    <body>
<?php
rs2htmloriginal($Rs_clta, false, $tit_columnas);
$Rs_clta->Close();
?>
    </body>
</html>