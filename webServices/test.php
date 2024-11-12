<?php
/** 
 *
 * Set tabs to 4 for best viewing.
 * 
 * Latest version is available at http://php.weblogs.com
 *
 * Test GetUpdateSQL and GetInsertSQL.
 */
 
error_reporting(E_ALL);


//Parametros para configuracion servidor
//define('SERVIDOR_DB', 'basesdnp');
//define('USUARIO_DB',  'prueba');
//define('PASSW_DB',    'prueba');
//define('NOMBRE_DB',   'GdOrfeo');

define('SERVIDOR_DB', 'datumbasis');
define('USUARIO_DB',  'orfeo');
define('PASSW_DB',    'orfeoDNP');
define('NOMBRE_DB',   'GdOrfeo');

include 'adodb/adodb.inc.php';


function login($username, $password ) {

	$db = ADONewConnection('mssql'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	
	$db->Connect(SERVIDOR_DB,USUARIO_DB, PASSW_DB, NOMBRE_DB);

	$query= "select * from usuario WHERE  usua_login='$username' AND usua_pasw='".SUBSTR(md5($password),1,26)."'";
    $db->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs = $db->Execute($query);

	
	if ($rs->RecordCount( ) > 0) {
			return array('UsuarioNombre'=>$rs->fields["USUA_NOMB"],
							 'UsuarioLogin'=>$rs->fields["USUA_LOGIN"], 
				             'DocIdent'=>$rs->fields["USUA_DOC"],
     					     'UsaCodigo'=>$rs->fields["USUA_CODI"],
  					         'DepCodigo'=>$rs->fields["DEPE_CODI"],
 	        				 'PerRad'=>$rs->fields["PERM_RADI"]);
	}
	return array();
}

// Define the method as a PHP function
function radicados_usuario($username, $inifecha, $finfecha, $criterio ) {

	$db = ADONewConnection('mssql'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect(SERVIDOR_DB,USUARIO_DB, PASSW_DB, NOMBRE_DB);
    $db->SetFetchMode(ADODB_FETCH_ASSOC);

	$query= "select USUA_CODI, DEPE_CODI, USUA_NOMB, USUA_DOC, USUA_ESTA, PERM_RADI from usuario WHERE  usua_login='$username'";
	$rs = $db->Execute($query);
	if ($rs->RecordCount( ) > 0) {
		$usuaEstado = $rs->fields['USUA_ESTA']; // El usuario esta activo ?
		$permRadicado = $rs->fields['PERM_RADI']; // Tiene pemiso de radicación ?
		$usuaDependencia = $rs->fields['DEPE_CODI']; // Codigo de la dependencia del usuario digitalizador ?
		$usuaCodigo = $rs->fields['USUA_CODI']; // Codigo del usuario digitalizador ?

	    if (($usuaEstado==1) && ($permRadicado==1)){

	         switch ($criterio{0})
	              {   case 'U': {
	              	         $tiporad = $criterio{1} ; 
			                 $sqlWhere = " AND (r.RADI_NUME_RADI like '%$tiporad') AND 
							               (SUBSTRING(CAST(r.RADI_NUME_RADI AS varchar(15)), 5, 3) = $usuaDependencia) AND 
										   (r.RADI_USUA_RADI = '$usuaCodigo') AND 
										   (r.RADI_PATH IS NULL) ";

			                 } break;
					  case 'N': {
						     $tiporad = $criterio{1} ; 
					  	     $nroradicado = substr($criterio, 2); 
							 if ($tiporad==0) {
				 				 $sqlWhere = "  AND RADI_NUME_RADI like '%$nroradicado' ";
                             } else  $sqlWhere = "  AND RADI_NUME_RADI like '%$nroradicado' ";
					  } break ;
					  case 'D': {
						     $tiporad = $criterio{1} ; 
					  	     $coddep = substr($criterio, 2); 
							 $sqlWhere = " AND (RADI_NUME_RADI like '%$tiporad') AND (SUBSTRING(CAST(r.RADI_NUME_RADI AS varchar(15)), 5, 3) = $coddep)  ";
                	  } break ;
				   };

		   $query= "SELECT TOP 200 r.RADI_NUME_RADI, CONVERT(nvarchar(10), r.RADI_FECH_RADI, 103) as FECHA, r.RADI_DEPE_ACTU, r.RA_ASUN, r.RADI_NUME_HOJA, d.DEPE_NOMB
					FROM RADICADO r INNER JOIN
                        DEPENDENCIA d ON r.RADI_DEPE_ACTU = d.DEPE_CODI
					WHERE  (r.RADI_FECH_RADI between CONVERT(datetime,'$inifecha',103) and  CONVERT(datetime,'$finfecha',103))  
						   $sqlWhere  ORDER BY r.RADI_NUME_RADI ";
			
			$rs = $db->Execute($query);
			
			$result= array();
			
			return $result;
	   }    
       else return array();
  }
  else return array();
}

function enviosplanilla( $codplanilla, $fechaini, $fechafin, $zona, $alcance) {

	$db = ADONewConnection('mssql'); # ej. 'mysql' o 'oci8' 
	$db->debug = true;
	$db->Connect(SERVIDOR_DB,USUARIO_DB, PASSW_DB, NOMBRE_DB);
        $db->SetFetchMode(ADODB_FETCH_ASSOC);
		
		// Si la planilla 
		if (!empty($zona) or $zona <> "")  {
		   if ($alcance==0) $sqlWhere.= " SGD_RENV_DEPTO IN ('$zona') AND ";
		   else $sqlWhere.= " SGD_RENV_DEPTO NOT IN ('$zona') AND ";
		}
	    if ($codplanilla <> 0)  $sqlWhere.= "SGD_FENV_CODIGO = $codplanilla AND ";
          
	      $query = "SELECT SGD_RENV_CANTIDAD  ITEM, RADI_NUME_SAL RADICADO, SGD_RENV_NOMBRE DESTINATARIO, SGD_RENV_DIR DIRECCION,
		                 SGD_RENV_MPIO MUNICIPIO, SGD_RENV_DEPTO DEPTO,	SGD_RENV_PESO PESO, SGD_RENV_VALOR VALOR , DEPENDENCIA.DEPE_NOMB REMITENTE,
			              SGD_RENV_REGENVIO.SGD_RENV_FECH FECHA
                    From SGD_RENV_REGENVIO INNER JOIN DEPENDENCIA on (SGD_RENV_REGENVIO.SGD_DEPE_GENERA = DEPENDENCIA.DEPE_CODI)
                     Where $sqlWhere SGD_RENV_FECH between CONVERT(datetime,'$fechaini',103) and  CONVERT(datetime,'$fechafin',103) ";


		$rsenvios = $db->Execute($query);

		$result= array();
		while (!$rsenvios->EOF) {
			$result[] = array('Item'=> $rsenvios->fields["ITEM"],
							  'Radicado'=> $rsenvios->fields["RADICADO"],
							  'Destinatario'=> $rsenvios->fields["DESTINATARIO"],
							  'Direccion'=> $rsenvios->fields["DIRECCION"],
							  'Municipio'=> $rsenvios->fields["MUNICIPIO"],
							  'Remitente'=> $rsenvios->fields["REMITENTE"],
							  'Depto'=> $rsenvios->fields["DEPTO"],
							  'Peso'=> $rsenvios->fields["PESO"],
						          'Valor'=> $rsenvios->fields["VALOR"],
							  'Fecha'=> $rsenvios->fields["FECHA"]);
			$rsenvios->MoveNext();
		}

        return  $result;
}

function tipoenvios() {

	$db = ADONewConnection('mssql'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect(SERVIDOR_DB,USUARIO_DB, PASSW_DB, NOMBRE_DB);
  $db->SetFetchMode(ADODB_FETCH_ASSOC);
	// 
	
	
	$query= "SELECT SGD_FENV_CODIGO, SGD_FENV_DESCRIP	FROM SGD_FENV_FRMENVIO ";
  	$rstipo = $db->Execute($query);

		$result= array();
		while (!$rstipo->EOF) {
			$result[] = array('id'=> $rstipo->fields["SGD_FENV_CODIGO"],
			'detalle'=> $rstipo->fields["SGD_FENV_DESCRIP"]);
			$rstipo->MoveNext();
		}
        return $result;
}

function fechas() {

   // echo $manyana        = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
   // echo $ultimo_mes     = mktime(0, 0, 0, date("m")-1, date("d"),   date("Y"));
   // echo $siguiente_anyo = mktime(0, 0, 0, date("m"),   date("d"),   date("Y")+1);
   //$date = date("Y-m-d");// current date   
   //echo $hoy = strtotime(date("Y-m-d", strtotime($date)) . " +1 day");
  $finfecha = '07/03/2009';
   if ($finfecha ==date("d/m/Y")) {
            $tomorrow = mktime(0, 0, 0, date("m"), date("d")+1, date("Y"));
            $finfecha = date("d/m/Y", $tomorrow);
            echo $finfecha;
          }
   $tomorrow = mktime(0, 0, 0, date("m"), date("d")+1, date("Y"));
   echo "Tomorrow is ".date("m/d/Y", $tomorrow); 

    
}

function anexararchivo( $username, $nroradicado, $anextipo, $tamano, $solectura, $codTrd, $anexdesc )
{

	$db = ADONewConnection('mssql'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect(SERVIDOR_DB,USUARIO_DB, PASSW_DB, NOMBRE_DB);
    $db->SetFetchMode(ADODB_FETCH_ASSOC);
	// 
	$query= "select * from usuario WHERE  usua_login='$username'";
	$rs = $db->Execute($query);
	if ($rs->RecordCount( ) > 0) {
		// Se genera el nro de documentos que se han anexado al un radicado
		$query = 'SELECT count(*) as "CANTRADICADOS" FROM anexos WHERE anex_radi_nume =' .$nroradicado;
		$rscant = $db->Execute($query);
		$numanex = $rscant->fields["CANTRADICADOS"] + 1;
	
	   // Se construe el valor de anex_codigo
		$anexcodigo = $nroradicado .str_pad($numanex,5,"0", STR_PAD_LEFT); 
		$nombarchivo = $nroradicado."_".str_pad($numanex,5,"0", STR_PAD_LEFT).".tif" ;
		// Se arma el arreglo para la inclusion del anexo
		$recordA =array();
		$recordA["SGD_TPR_CODIGO"]     =  $codTrd;
		$recordA["ANEX_RADI_NUME"]	   =  $nroradicado;
		$recordA["ANEX_CODIGO"]		   =  $anexcodigo; //Variable Calculada
		$recordA["ANEX_TIPO"]		   =  $anextipo; 
		$recordA["ANEX_TAMANO"]		   =  $tamano;
		$recordA["ANEX_SOLO_LECT"]	   =  "'".$solectura."'";
		$recordA["ANEX_CREADOR"]	   =  "'".$username."'";  //Variable de usuario
		$recordA["ANEX_DESC"]	       =  "'".$anexdesc."'";
		$recordA["ANEX_NUMERO"]	       =  $numanex;  //Variable calculada
		$recordA["ANEX_NOMB_ARCHIVO"]  =  "'".$nombarchivo."'";
		$recordA["ANEX_BORRADO"]	   =  "'N'";
		$recordA["ANEX_ORIGEN"]		   =  0;
//		$recordA["ANEX_RADI_FECH"]     =  $db->OffsetDate(0,$db->sysTimeStamp);
		$recordA["ANEX_FECH_ANEX"]     =  $db->OffsetDate(0,$db->sysTimeStamp);
		$recordA["ANEX_ESTADO"]		   =  1;
		$recordA["ANEX_DEPE_CREADOR"]  =  $rs->fields["DEPE_CODI"];
		$recordA["SGD_DIR_TIPO"]	   =  1;

		$insertSQL = $db->Replace("ANEXOS", $recordA, "", false);

		If($insertSQL) {
 		    return array('error'=> 'OK',
					             'mensaje'=> 'La operacion fue exitosa');
		} Else  return array('error'=> '01',
					             'mensaje'=> 'No se registro el anexo');

	} else   return array();
}




//fechas();
//tipoenvios();
print_r(enviosplanilla(0,'01/01/2009 12:00:00 pm','09/02/2009 11:30:00 am',"",0));
//print_r(login('scasas',123));
?>