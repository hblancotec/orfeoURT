<?php
/*************************************************************************************/
/* ORFEO GPL:Sistema de Gestion Documental		http://www.orfeogpl.org	     */
/*	Idea Original de la SUPERINTENDENCIA DE SERVICIOS PUBLICOS DOMICILIARIOS     */
/*				COLOMBIA TEL. (57) (1) 6913005  orfeogpl@gmail.com   */
/* ===========================                                                       */
/*                                                                                   */
/* Este programa es software libre. usted puede redistribuirlo y/o modificarlo       */
/* bajo los terminos de la licencia GNU General Public publicada por                 */
/* la "Free Software Foundation"; Licencia version 2. 			             */
/*                                                                                   */
/* Copyright (c) 2005 por :	  	  	                                     */
/* SSPD "Superintendencia de Servicios Publicos Domiciliarios"                       */
/*   Jairo Hernan Losada  jlosada@gmail.com                Desarrollador             */
/*   Sixto Angel Pinzon Lopez --- angel.pinzon@gmail.com   Desarrollador           */
/* C.R.A.  "COMISION DE REGULACION DE AGUAS Y SANEAMIENTO AMBIENTAL"                 */
/*   Liliana Gomez        lgomezv@gmail.com                Desarrolladora            */
/*   Lucia Ojeda          lojedaster@gmail.com             Desarrolladora            */
/* D.N.P. "Departamento Nacional de Planeacion"                                     */
/*   Hollman Ladino       hladino@gmail.com                Desarrollador             */
/*                                                                                   */
/* Colocar desde esta lInea las Modificaciones Realizadas Luego de la Version 3.5    */
/*  Nombre Desarrollador   Correo     Fecha   Modificacion                           */
/*  Supersolidaria                    05-Diciembre-2006 Consulta para listar los     */
/*                                    radicados que fueron archivados                */
/*************************************************************************************/
?>
<?php
/** CONSULTA RADICADOS SIN EXPEDIENTE
	* Reporte de radicados que no est�n incluidos en alg�n expediente.
	* @autor Supersolidaria
	* @version ORFEO 3.7
	* 
	*/
$coltp3Esp = '"'.$tip3Nombre[3][2].'"';	
if(!$orno) $orno=2;
 /**
   * $db-driver Variable que trae el driver seleccionado en la conexion
   * @var string
   * @access public
   */
 /**
   * $fecha_ini Variable que trae la fecha de Inicio Seleccionada  viene en formato Y-m-d
   * @var string
   * @access public
   */
/**
   * $fecha_fin Variable que trae la fecha de Fin Seleccionada
   * @var string
   * @access public
   */
/**
   * $mrecCodi Variable que trae el medio de recepcion por el cual va a sacar el detalle de la Consulta.
   * @var string
   * @access public
   */
switch( $db->driver )
	{
	case 'mssqlnative':
	$isql = '';	
	break;
	case 'oracle':
	case 'oci8':
	case 'oci805':
	case 'ocipo':
	$queryUs  = "SELECT COUNT( DISTINCT( E.RADI_NUME_RADI ) ) AS \"Radicados\",
                     U.USUA_NOMB AS \"Usuario\",
                     MIN( U.USUA_CODI ) AS HID_COD_USUARIO,
                     MIN( U.DEPE_CODI ) AS HID_DEPE_USUA,
                     SUM(R.RADI_NUME_HOJA) AS NUMERO_FOLIOS
                     FROM SGD_EXP_EXPEDIENTE E, USUARIO U, RADICADO R
                     WHERE E.RADI_NUME_RADI=R.RADI_NUME_RADI AND E.RADI_USUA_ARCH=U.USUA_LOGIN
                     AND ".$db->conn->SQLDate( 'Y-m-d', 'E.SGD_EXP_FECH_ARCH' );
        if( $_POST['fechaIni'] != "" && $_POST['fechaInif'] != "" )
        {
            $queryUs .= "BETWEEN '".$_POST['fechaIni']."' AND '".$_POST['fechaInif']."'";
        }
        else if( $_GET['fechaIniSel'] != "" && $_GET['fechaInifSel'] != "" )
        {
            $queryUs .= "BETWEEN '".$_GET['fechaIniSel']."' AND '".$_GET['fechaInifSel']."'";
        }
        else
        {
            $queryUs .= "BETWEEN '".$fechaIni."' AND '".$fechaInif."'";
        }
        if( $_POST['trad'] != 0 )
        {
            $queryUs .= " AND ".$db->conn->substr."( E.RADI_NUME_RADI, -1, 1 ) = ".$_POST['trad'];
        }
        // Dependencia
        // if( $_POST['codigo'] != 0 )
        if( $_POST['codigoUsuario'] != 0 )
        {
            // $queryUs .= " AND U.USUA_CODI = ".$_POST['codigo'];
            $queryUs .= " AND U.USUA_CODI = ".$_POST['codigoUsuario'];
        }
        // Usuario
        if( $_POST['dep_sel'] != 0 )
        {
            $queryUs .= " AND U.DEPE_CODI = ".$_POST['dep_sel'];
        }
       $queryUs .= "  GROUP BY U.USUA_NOMB";
       
       /** CONSULTA PARA VER DETALLES 
         */
        $queryEDetalle  = "SELECT E.RADI_NUME_RADI AS RADICADO,";
        $queryEDetalle .= " R.RADI_PATH as HID_RADI_PATH,";
        $queryEDetalle .= " R.RADI_FECH_RADI AS FECHA_RADICACION,E.SGD_EXP_FECH_ARCH AS FECHA_ARCHIVO,";
        $queryEDetalle .= " U.USUA_NOMB AS USUARIO,D.DEPE_NOMB AS DEPENDENCIA,";
        $queryEDetalle .= " R.RADI_NUME_HOJA AS NUMERO_FOLIOS";
        $queryEDetalle .= " FROM SGD_EXP_EXPEDIENTE E, USUARIO U, RADICADO R, DEPENDENCIA D";
        $queryEDetalle .= " WHERE E.RADI_USUA_ARCH=U.USUA_LOGIN AND D.DEPE_CODI=U.DEPE_CODI";
        $queryEDetalle .= " AND E.RADI_NUME_RADI=R.RADI_NUME_RADI";
        $queryEDetalle .= " AND ".$db->conn->SQLDate( 'Y-m-d', 'E.SGD_EXP_FECH_ARCH' )." BETWEEN '".$_GET['fechaIni']."' AND '".$_GET['fechaInif']."'";
       // Usuario
        if( $_GET['codUs'] != 0 )
        {
            $queryEDetalle .= " AND U.USUA_CODI = ".$_GET['codUs'];
        }
        // Dependencia
        if( $_GET['depeUs'] != 0 )
        {
            $queryEDetalle .= " AND U.DEPE_CODI = ".$_GET['depeUs'];
        }
        //Tipo de Radicado
        if( $_GET['trad'] != 0 )
        {
            $queryEDetalle .= " AND ".$db->conn->substr."( E.RADI_NUME_RADI, -1, 1 ) = ".$_GET['trad'];
        }
        $queryETodosDetalle = $queryEDetalle;
        break;
	}
?>
