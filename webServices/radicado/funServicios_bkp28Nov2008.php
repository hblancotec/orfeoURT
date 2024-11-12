<?php
/**
* @param $radiNume, este parametro es el Nuemro de radicado
* @param $usuEmail, este parametro es el correo electronico del usuario
* @param $correo, Correo del usuario
 * @param $destinos, arreglo de destinatarios destinatarios,predio,esp
 * @param $asu, Asunto del radicado
 * @param $med, Medio de radicacion
 * @param $ane, descripcion de anexos
 * @param $coddepe, codigo de la dependencia
 * @param $tpRadicado, tipo de radicado
 * @param $cuentai, cuenta interna del radicado
 * @param $radi_usua_actu, 
 *  @param $destinatarioOrg, arreglo destinatario
 *  @param $predioOrg, arreglo predio
 *  @param $espOrg, arreglo esp
 * @author Hardy Deimont NiÃ±o Velasquez
 * @return El numero del radicado o un mensaje de error en caso de fallo
 */

function modificarRadicado($radiNume,$correo,$destinatarioOrg,$predioOrg,$espOrg,$asu,$med,$ane,$coddepe,
$tpRadicado,$cuentai,$radi_usua_actu,$tip_rem,$tdoc,$tip_doc,$carp_codi,$carp_per)
{
	//Conversiones de datos para compatibilidad con aplicaciones internas
	
	$destinatario = array(
	'documento'=>$destinatarioOrg[0],
	'cc_documento'=>$destinatarioOrg[1],
	'tipo_emp'=>$destinatarioOrg[2],
	'nombre'=>$destinatarioOrg[3],
	'prim_apel'=>$destinatarioOrg[4],
	'seg_apel'=>$destinatarioOrg[5],
	'telefono'=>$destinatarioOrg[6],
	'direccion'=>$destinatarioOrg[7],
	'mail'=>$destinatarioOrg[8],
	'otro'=>$destinatarioOrg[9],
	'idcont'=>$destinatarioOrg[10],
	'idpais'=>$destinatarioOrg[11],
	'codep'=>$destinatarioOrg[12],
	'muni'=>$destinatarioOrg[13]
	);
	$predio = array(
	'documento'=>$predioOrg[0],
	'cc_documento'=>$predioOrg[1],
	'tipo_emp'=>$predioOrg[2],
	'nombre'=>$predioOrg[3],
	'prim_apel'=>$predioOrg[4],
	'seg_apel'=>$predioOrg[5],
	'telefono'=>$predioOrg[6],
	'direccion'=>$predioOrg[7],
	'mail'=>$predioOrg[8],
	'otro'=>$predioOrg[9],
	'idcont'=>$predioOrg[10],
	'idpais'=>$predioOrg[11],
	'codep'=>$predioOrg[12],
	'muni'=>$predioOrg[13]	
	);
	$esp = array(
	'documento'=>$espOrg[0],
	'cc_documento'=>$espOrg[1],
	'tipo_emp'=>$espOrg[2],
	'nombre'=>$espOrg[3],
	'prim_apel'=>$espOrg[4],
	'seg_apel'=>$espOrg[5],
	'telefono'=>$espOrg[6],
	'direccion'=>$espOrg[7],
	'mail'=>$espOrg[8],
	'otro'=>$espOrg[9],
	'idcont'=>$espOrg[10],
	'idpais'=>$espOrg[11],
	'codep'=>$espOrg[12],
	'muni'=>$espOrg[13]
	);
	
	
	try {
		$radi_usua_actu = getInfoUsuario($radi_usua_actu);
		$radi_usua_actu = trim($radi_usua_actu['usua_codi']);
	
		$coddepe = getInfoUsuario($coddepe);
		$coddepe = trim($coddepe['usua_depe']);
	}catch (Exception $e){
		return $e->getMessage();
	}
	
	
	// Fin
	
//	 $ruta_raiz="../";
        global $ruta_raiz;	
	include(RUTA_RAIZ."include/tx/Tx.php") ;
	include(RUTA_RAIZ."include/tx/Radicacion.php") ;
	include(RUTA_RAIZ."class_control/Municipio.php") ;
        include_once(RUTA_RAIZ."/include/tx/Historico.php");
	
	$db = new ConnectionHandler($ruta_raiz) ;
	$tmp_mun = new Municipio($db) ;
	$rad = new Radicacion($db) ;
	$hist = new Historico($db);


	$tmp_mun->municipio_codigo($destinatario["codep"],$destinatario["muni"]) ;
	$rad->radiTipoDeri = $tpRadicado ;
	$rad->radiCuentai = "'".trim($cuentai)."'";
	$rad->eespCodi =  $esp["documento"] ;
	$rad->mrecCodi =  $med;
	$rad->radiFechOfic =  date("Y-m-d");
	if(!$radicadopadre)  $radicadopadre = null;
	$rad->radiNumeDeri = trim($radicadopadre) ;
	$rad->radiPais =  $tmp_mun->get_pais_codi() ;
	$rad->descAnex = $ane ;
	$rad->raAsun = $asu ;
	$rad->radiDepeActu = $coddepe ;
	$rad->radiDepeRadi = $coddepe ;
	$rad->radiUsuaActu = $radi_usua_actu ;
	$rad->trteCodi =  $tip_rem ;

	$rad->tdocCodi=$tdoc ;
	$rad->tdidCodi=$tip_doc ;
	$rad->carpCodi = $carp_codi ;
	$rad->carPer = $carp_per ;
	$rad->trteCodi=$tip_rem ;
	$rad->radiPath = 'null';
	//$rad->ra_asun = $asu;
	if (strlen(trim($aplintegra)) == 0)
			$aplintegra = "0" ;
	$rad->sgd_apli_codi = $aplintegra ;
	$codTx =1 ;
	$flag = 1 ;
	$rad->usuaCodi=14 ;
	$rad->dependencia=trim($coddepe) ;
	$noRad = $rad->updateRadicado($radiNume);
	//$nurad = trim($noRad) ;
	//$sql_ret = $rad->updateRadicado($radiNume,"/".date("Y")."/".$coddepe."/".$noRad.".pdf");
	

	if ($noRad)
	{
	$radicadosSel[0] = $noRad;   
	$hist->insertarHistorico($radicadosSel,  $coddepe , $radi_usua_actu, $coddepe, $radi_usua_actu, "Modificacion Documento. ", $codTx);
		//return "Ok: Radicado No $nurad fue Modificado Correctamente.";		
	}
	$sgd_dir_us2=2;
	
	$conexion = $db;
	
	/*
		Preparacion de variables para llamar el codigo del
		archivo grb_direcciones.php
	*/
	
	$muni_us1 = trim($destinatario['muni']);
	$muni_us2 = trim($predio['muni']);
	$muni_us3 = trim($esp['muni']);
	
	$codep_us1 = trim($destinatario['codep']);
	$codep_us2 = trim($predio['codep']);
	$codep_us3 = trim($esp['codep']);
	
	$grbNombresUs1 = trim($destinatario['nombre']) . " " . trim($destinatario['prim_apel']) . " ". trim($destinatario['seg_apel']);
	$grbNombresUs2 = trim($predio['nombre']) . " " . trim($predio['prim_apel']) . " ". trim($predio['seg_apel']);
	
	$cc_documento_us1 = trim($destinatario['cc_documento']);
	$cc_documento_us2 = trim($predio['cc_documento']);
	
	$documento_us1 = trim($destinatario['documento']);
	$documento_us2 = trim($predio['documento']);
	
	$direccion_us1 = trim($destinatario['direccion']);
	$direccion_us2 = trim($predio['direccion']);
	
	$telefono_us1 = trim($destinatario['telefono']);
	$telefono_us2 = trim($predio['telefono']);
	
	$mail_us1 = trim($destinatario['mail']);
	$mail_us2 = trim($predio['mail']);
	
	$otro_us1 = trim($destinatario['otro']);
	$otro_us2 = trim($predio['otro']);
	
	//************** INSERTAR DIRECCIONES *******************************
	
	if (!$muni_us1) $muni_us1 = NULL;
	if (!$muni_us2) $muni_us2 = NULL;
	if (!$muni_us3) $muni_us3 = NULL;
	
	// Creamos las valores del codigo del dpto y mcpio desglozando el valor del <SELECT> correspondiente.
	if (!is_null($muni_us1))
	{
		$tmp_mun = new Municipio($conexion);
		$tmp_mun->municipio_codigo($codep_us1,$muni_us1);
		$tmp_idcont = $tmp_mun->get_cont_codi();
		$tmp_idpais = $tmp_mun->get_pais_codi();
		$muni_tmp1 = explode("-",$muni_us1);
		switch (count($muni_tmp1))
		{	
			case 4:
			{
				$idcont1 = $muni_tmp1[0];
				$idpais1 = $muni_tmp1[1];
				$dpto_tmp1 = $muni_tmp1[2];
				$muni_tmp1 = $muni_tmp1[3];

			}
			break;
		case 3:
			{
				$idcont1 = $tmp_idcont;
				$idpais1 = $muni_tmp1[0];
				$dpto_tmp1 = $muni_tmp1[1];
				$muni_tmp1 = $muni_tmp1[2];
			}
			break;
		case 2:
			{
				$idcont1 = $tmp_idcont;
				$idpais1 = $tmp_idpais;
				$dpto_tmp1 = $muni_tmp1[0];
				$muni_tmp1 = $muni_tmp1[1];
			}
			break;
		}
		unset($tmp_mun);
		unset($tmp_idcont);
		unset($tmp_idpais);
	}

	if (!is_null($muni_us2))
	{	
		$tmp_mun = new Municipio($conexion);
		$tmp_mun->municipio_codigo($codep_us2,$muni_us2);
		$tmp_idcont = $tmp_mun->get_cont_codi();
		$tmp_idpais = $tmp_mun->get_pais_codi();
		$muni_tmp2 = explode("-",$muni_us2);
		switch (count($muni_tmp2))
		{	
			case 4:
			{	
				$idcont2 = $muni_tmp2[0];
				$idpais2 = $muni_tmp2[1];
				$dpto_tmp2 = $muni_tmp2[2];
				$muni_tmp2 = $muni_tmp2[3];
			}
			break;
		case 3:
			{
				$idcont2 = $tmp_idcont;
				$idpais2 = $muni_tmp2[0];
				$dpto_tmp2 = $muni_tmp2[1];
				$muni_tmp2 = $muni_tmp2[2];
			}
			break;
		case 2:
			{
				$idcont2 = $tmp_idcont;
				$idpais2 = $tmp_idpais;
				$dpto_tmp2 = $muni_tmp2[0];
				$muni_tmp2 = $muni_tmp2[1];
			}
			break;
		}
		unset($tmp_mun);unset($tmp_idcont);unset($tmp_idpais);
	}	
	if (!is_null($muni_us3))
	{	
		$tmp_mun = new Municipio($conexion);
		$tmp_mun->municipio_codigo($codep_us3,$muni_us3);
		$tmp_idcont = $tmp_mun->get_cont_codi();
		$tmp_idpais = $tmp_mun->get_pais_codi();
		$muni_tmp3 = explode("-",$muni_us3);
		switch (count($muni_tmp3))
		{	
			case 4:
			{	
				$idcont3 = $muni_tmp3[0];
				$idpais3 = $muni_tmp3[1];
				$dpto_tmp3 = $muni_tmp3[2];
				$muni_tmp3 = $muni_tmp3[3];
			}
			break;
			case 3:
			{
				$idcont1 = $tmp_idcont;
				$idpais3 = $muni_tmp3[0];
				$dpto_tmp3 = $muni_tmp3[1];
				$muni_tmp3 = $muni_tmp3[2];
			}
			break;
		case 2:
			{
				$idcont3 = $tmp_idcont;
				$idpais3 = $tmp_idpais;
				$dpto_tmp3 = $muni_tmp3[0];
				$muni_tmp3 = $muni_tmp3[1];
			}
			break;
		}
		unset($tmp_mun);unset($tmp_idcont);unset($tmp_idpais);
	}
	
	$newId = false;
	if(!$modificar)
	{
   		$nextval=$conexion->nextId("sec_dir_direcciones");
	}
	if ($nextval==-1)
	{
		return "No se encontro la secuencia sec_dir_direcciones ";
	}
	global $ADODB_COUNTRECS;
	if($documento_us1!='' and !$cc!='')
	{
		$sgd_ciu_codigo=0;
		$sgd_oem_codigo=0;
		$sgd_esp_codigo=0;
		$sgd_fun_codigo=0;
  		if($tipo_emp_us1==0)
  		{	
  			$sgd_ciu_codigo=$documento_us1;
			$sgdTrd = "1";
		}
		if($tipo_emp_us1==1)
		{	
			$sgd_esp_codigo=$documento_us1;
			$sgdTrd = "3";
		}
		if($tipo_emp_us1==2)
		{	
			$sgd_oem_codigo=$documento_us1;
			$sgdTrd = "2";
		}
		if($tipo_emp_us1==6)
		{	
			$sgd_fun_codigo=$documento_us1;
			$sgdTrd = "4";
		}

		$ADODB_COUNTRECS = true;

		$record = array();
		$record['SGD_TRD_CODIGO'] = $sgdTrd;
		$record['SGD_DIR_NOMREMDES'] = $grbNombresUs1;
		$record['SGD_DIR_DOC'] = $cc_documento_us1;
		$record['MUNI_CODI'] = $muni_tmp1;
		$record['DPTO_CODI'] = $dpto_tmp1;
		$record['ID_PAIS'] = $idpais1;
		$record['ID_CONT'] = $idcont1;
		$record['SGD_DOC_FUN'] = $sgd_fun_codigo;
		$record['SGD_OEM_CODIGO'] = $sgd_oem_codigo;
		$record['SGD_CIU_CODIGO'] = $sgd_ciu_codigo;
		$record['SGD_OEM_CODIGO'] = $sgd_oem_codigo;
		$record['SGD_ESP_CODI'] = $sgd_esp_codigo;
		$record['RADI_NUME_RADI'] = $nurad;
		$record['SGD_SEC_CODIGO'] = 0;
		$record['SGD_DIR_DIRECCION'] = $direccion_us1;
		$record['SGD_DIR_TELEFONO'] = trim($telefono_us1);
		$record['SGD_DIR_MAIL'] = $mail_us1;
		$record['SGD_DIR_TIPO'] = 1;
		$record['SGD_DIR_CODIGO'] = $nextval;
		$record['SGD_DIR_NOMBRE'] = $otro_us1;

	$insertSQL = $conexion->conn->Replace("SGD_DIR_DRECCIONES", $record, array('RADI_NUME_RADI','SGD_DIR_TIPO'), $autoquote = true);
	switch ($insertSQL)
	{	case 1:	{	//Insercion Exitosa
					$dir_codigo_new = $nextval;
					$newId=true;
				}break;
		case 2:{	//Update Exitoso
					$newId = false;
				}break;
		case 0:{	//Error Transaccion.
					return  "ERROR: No se ha podido actualizar la informacion de SGD_DIR_DRECCIONES UNO -- $isql --";
				}break;
	}
	unset($record);
	$ADODB_COUNTRECS = false;
}
	// ***********************  us2
if($documento_us2!='')
{
	$sgd_ciu_codigo=0;
    $sgd_oem_codigo=0;
    $sgd_esp_codigo=0;
		$sgd_fun_codigo=0;
  if($tipo_emp_us2==0){
		$sgd_ciu_codigo=$documento_us2;
		$sgdTrd = "1";
	}
	if($tipo_emp_us2==1){
		$sgd_esp_codigo=$documento_us2;
		$sgdTrd = "3";
	}
	if($tipo_emp_us2==2){
		$sgd_oem_codigo=$documento_us2;
		$sgdTrd = "2";
	}
	if($tipo_emp_us2==6){
		$sgd_fun_codigo=$documento_us2;
		$sgdTrd = "4";
	}
	$isql = "select * from sgd_dir_drecciones where radi_nume_radi=$nurad and sgd_dir_tipo=2";
	$rsg=$conexion->query($isql);

    if 	($rsg->EOF)
	{
		//if($newId==true)
			//{
			   $nextval=$conexion->nextId("sec_dir_direcciones");
			//}
			if ($nextval==-1)
			{
				//$db->conn->RollbackTrans();
				return " ERROR:  No se encontrï¿½ la secuencia sec_dir_direcciones ";
			}

		$isql = "insert into SGD_DIR_DRECCIONES(SGD_TRD_CODIGO, SGD_DIR_NOMREMDES, SGD_DIR_DOC, DPTO_CODI, MUNI_CODI,
      			id_pais, id_cont, SGD_DOC_FUN, SGD_OEM_CODIGO, SGD_CIU_CODIGO, SGD_ESP_CODI, RADI_NUME_RADI, SGD_SEC_CODIGO,
      			SGD_DIR_DIRECCION, SGD_DIR_TELEFONO, SGD_DIR_MAIL, SGD_DIR_TIPO, SGD_DIR_CODIGO, SGD_DIR_NOMBRE)
	  			values('$sgdTrd', '$grbNombresUs2', '$cc_documento_us2', $dpto_tmp2, $muni_tmp2, $idpais2, $idcont2,
	  			$sgd_fun_codigo, $sgd_oem_codigo, $sgd_ciu_codigo, $sgd_esp_codigo, $nurad, 0,'".trim($direccion_us2).
	  			"', '".trim($telefono_us2)."', '$mail_us2', 2, $nextval, '$otro_us2')";
   	  $dir_codigo_new = $nextval;
   	  $newId=true;
    }
	 else
	{
	  $newId = false;
		$isql = "update SGD_DIR_DRECCIONES
				set MUNI_CODI=$muni_tmp2, DPTO_CODI=$dpto_tmp2, id_pais=$idpais2, id_cont=$idcont2
				,SGD_OEM_CODIGO=$sgd_oem_codigo
				,SGD_CIU_CODIGO=$sgd_ciu_codigo
				,SGD_ESP_CODI=$sgd_esp_codigo
				,SGD_DOC_FUN=$sgd_fun_codigo
				,SGD_SEC_CODIGO=0
				,SGD_DIR_DIRECCION='$direccion_us2'
				,SGD_DIR_TELEFONO='$telefono_us2'
				,SGD_DIR_MAIL='$mail_us2'
				,SGD_DIR_NOMBRE='$otro_us2'
				,SGD_DIR_NOMREMDES='$grbNombresUs2'
				,SGD_DIR_DOC='$cc_documento_us2'
				,SGD_TRD_CODIGO='$sgdTrd'
			 	where radi_nume_radi=$nurad and SGD_DIR_TIPO=2 ";
	}

	$rsg=$conexion->query($isql);

	if (!$rsg){
		return "ERROR: No se ha podido actualizar la informacion de SGD_DIR_DRECCIONES DOS -- $isql --";
	}

	}

if($documento_us1!='' and $cc!='')
{
	$sgd_ciu_codigo=0;
	$sgd_oem_codigo=0;
	$sgd_esp_codigo=0;
	$sgd_fun_codigo=0;

	//echo "--$sgd_emp_us1--";
	  if($tipo_emp_us1==0){
		$sgd_ciu_codigo=$documento_us1;
		$sgdTrd = "1";
	}
	if($tipo_emp_us1==1){
		$sgd_esp_codigo=$documento_us1;
		$sgdTrd = "3";
	}
	if($tipo_emp_us1==2){
		$sgd_oem_codigo=$documento_us1;
		$sgdTrd = "2";
	}
	if($tipo_emp_us1==6){
		$sgd_fun_codigo=$documento_us1;
		$sgdTrd="4";
	}
	if($newId==true)
		{
		   $nextval=$conexion->nextId("sec_dir_direcciones");
		}
		if ($nextval==-1)
		{
			//$db->conn->RollbackTrans();
			return "No se encontrasena la secuencia sec_dir_direcciones ";
		}
  $num_anexos=$num_anexos+1;
  $str_num_anexos = substr("00$num_anexos",-2);
  $sgd_dir_tipo = "7$str_num_anexos" ;
	$isql = "insert into SGD_DIR_DRECCIONES (SGD_TRD_CODIGO, SGD_DIR_NOMREMDES, SGD_DIR_DOC, MUNI_CODI, DPTO_CODI,
			id_pais, id_cont, SGD_DOC_FUN, SGD_OEM_CODIGO, SGD_CIU_CODIGO, SGD_ESP_CODI, RADI_NUME_RADI, SGD_SEC_CODIGO,
			SGD_DIR_DIRECCION, SGD_DIR_TELEFONO, SGD_DIR_MAIL, SGD_DIR_TIPO, SGD_DIR_CODIGO, SGD_ANEX_CODIGO, SGD_DIR_NOMBRE) ";
	$isql .= "values ('$sgdTrd', '$grbNombresUs1', '$cc_documento_us1', $muni_tmp1, $dpto_tmp1, $idpais1, $idcont1,
						$sgd_fun_codigo, $sgd_oem_codigo, $sgd_ciu_codigo, $sgd_esp_codigo, $nurad, 0, '$direccion_us1',
						'".trim($telefono_us1)."', '$mail_us1', $sgd_dir_tipo, $nextval, '$codigo', '$otro_us7' )";
  $dir_codigo_new = $nextval;
  $nextval++;
  $rsg=$conexion->query($isql);
	if (!$rsg)
	{
		//$conexion->conn->RollbackTrans();
		return "ERROR: No se ha podido actualizar la informacion de SGD_DIR_DRECCIONES TRES -- $isql --";
	}
}

	//*********************** FIN INSERTAR DIRECCIONES **********************
	

	$retval.=$noRad;
	
	return $retval;
}

/* Funcion que retorna los datos de un usuario registrado en Orfeo.
   Recibe como parametro de entrada el nombre de inicio de sesión(login) de orfeo
*/
function getInfoUsuarioOrfeo($usuaOrfeo){

	global $ruta_raiz;
	include_once( $ruta_raiz.'include/db/ConnectionHandler.php' );
		$db = new ConnectionHandler($ruta_raiz);

		$upperUsua=strtoupper($usuaOrfeo);
		//$lowerUsua=strtolower($usuaOrfeo);
		/*
        $sql="SELECT USUA_LOGIN,USUA_DOC,DEPE_CODI,CODI_NIVEL,USUA_CODI,USUA_NOMB, USUA_EMAIL FROM USUARIO 
                        WHERE  USUA_LOGIN='{$upperUsua}' OR USUA_LOGIN='{$lowerUsua}' AND USUA_ESTA=1";
	*/

	$sql = "SELECT USUA_LOGIN,USUA_DOC,DEPE_CODI,CODI_NIVEL,USUA_CODI,USUA_NOMB, USUA_EMAIL
		    FROM USUARIO 
                    WHERE  ".$db->conn->upperCase."( USUA_LOGIN ) = '{$upperUsua}'
		    AND USUA_ESTA = 1";
        $rs=$db->conn->Execute($sql);
                if($rs && !$rs->EOF){
                	$salida['usua_email']=($rs->fields["USUA_EMAIL"]);
                        $salida['usua_doc'] =($rs->fields["USUA_DOC"]);
                        $salida['usua_depe'] =($rs->fields["DEPE_CODI"]);
                        $salida['usua_nivel'] =($rs->fields["CODI_NIVEL"]);
                        $salida['usua_codi'] =($rs->fields["USUA_CODI"]);
                        $salida['usua_nomb'] =($rs->fields["USUA_NOMB"]);
        }else{
        	//throw new Exception("El usuario $usuaLoginMail no existe $sql");//Modificar esta linea en produccion
		$salida['error']="El usuario $usuaOrfeo no existe $sql";
        }

        return $salida;
}
?>
