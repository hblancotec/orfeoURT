<?php
$ruta_raiz = "../..";
include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler(ORFEOPATH);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//$db->conn->debug = true;
$ok = false;
#########################################################################################
### SE VERIFICA SI VIENE DEL FORMULARIO DE EDIFICIO
if (isset($_POST['btn_edif'])){
	
	### ACTUALIZACION DATOS DE EDIFICIO
	if ($_POST['btn_edif'] == 'Modificar'){
		$flag = true;
		$sqlEdif = "SELECT	SGD_EDIFICIO_COD,
							SGD_EDIFICIO_NOMB,
							SGD_EDIFICIO_SIGLA,
							SGD_EDIFICIO_DIR,
							SGD_EDIFICIO_CONT,
							SGD_EDIFICIO_PAIS,
							SGD_EDIFICIO_DPTO,
							SGD_EDIFICIO_MPIO,
							SGD_EDIFICIO_ESTADO,
							(	SELECT	COUNT(*) 
								FROM	SGD_PISO_ARCHIVO 
								WHERE	SGD_EDIFICIO_COD = ".$_POST['codEd']." AND 
										SGD_PISO_ESTADO <> 1) AS PISOS
					FROM	SGD_EDIFICIO_ARCHIVO
					WHERE	SGD_EDIFICIO_COD = ".$_POST['codEd'];
		$rsEdif = $db->conn->Execute($sqlEdif);
		
		
		if ($_POST['estEd'] == 2){
			$rest = $rsEdif->fields['PISOS'];
			### SE VERIFICA SI EXISTEN PISOS ACTIVOS QUE DEPENDEN DEL EDIFICIO A INACTIVAR
			if ($rest > 0){
				$flag = false;
				$mensg = "No se puede inactivar el Edificio porque existen secciones activas para el Edificio, por favor verificar.";
			}
		}
		
		### SI SE PUEDE INACTIVAR EL EDIFICIO, ENTRA DE LO CONTRARIO NO SE ACTUALIZA NINGUN REGISTRO
		if ($flag == true) {
		
			### SE VERIFICA SI CAMBIO EL NOMBRE
			if ($rsEdif->fields['SGD_EDIFICIO_NOMB'] != $_POST['nomEd']) {
				$sUp = "SGD_EDIFICIO_NOMB = '".$_POST['nomEd']."'";
				$msg = "nombre de edificio ".$rsEdif->fields['SGD_EDIFICIO_NOMB']." modificado por ".$_POST['nomEd'];
			}

			### SE VERIFICA SI CAMBIA LA SIGLA
			if ($rsEdif->fields['SGD_EDIFICIO_SIGLA'] != $_POST['sigEd']){
				$sUp = $sUp. ", SGD_EDIFICIO_SIGLA = '".$_POST['sigEd']."'";
				$msg = $msg.", sigla de edificio ".$rsEdif->fields['SGD_EDIFICIO_SIGLA']." modificado por ".$_POST['sigEd'];
			}

			### SE VERIFICA SI CAMBIA LA DIRECCIÓN
			if ($rsEdif->fields['SGD_EDIFICIO_DIR'] != $_POST['dirEd']){
				$sUp = $sUp. ", SGD_EDIFICIO_DIR = '".$_POST['dirEd']."'";
				$msg = $msg.", direcci&oacute;n de edificio ".$rsEdif->fields['SGD_EDIFICIO_DIR']." modificado por ".$_POST['dirEd'];
			}

			### SE VERIFICA SI CAMBIA EL ESTADO
			if ($rsEdif->fields['SGD_EDIFICIO_ESTADO'] != $_POST['estEd']){
				$sUp = $sUp. ", SGD_EDIFICIO_ESTADO = ".$_POST['estEd'];
				$msg = $msg.", estado de edificio ".$rsEdif->fields['SGD_EDIFICIO_ESTADO']." modificado por ".$_POST['estEd'];
			}

			if( substr($sUp,0,1) == ",") 
				$sUp = substr($sUp,2);

			if ($sUp){
				$sqlUpEd = "UPDATE	SGD_EDIFICIO_ARCHIVO
							SET		$sUp
							WHERE	SGD_EDIFICIO_COD = ".$_POST['codEd'];
				$rsUpEd = $db->conn->Execute($sqlUpEd);
				$cntAffect = $db->conn->Affected_Rows();

				### SI SE ACTUALIZO CORRECTAMENTE
				if ($cntAffect > 0) {
					$mensg = "Se actualizar&oacute;n correctamente los datos";
					$ok = true;
				}
				else{
					$mensg = "Ocurrio un inconveniente durante la actualizaci&oacute;n, por favor intentelo de nuevo";
				}
			}
		} ### FIN - SI SE PUEDE INACTIVAR EL EDIFICIO...
	} ### FIN - ACTUALIZACION DATOS DE EDIFICIO
	
	### SE REGISTRA UN NUEVO DIFICIO
	elseif ($_POST['btn_edif'] == 'Crear'){
		$insEd = "	INSERT INTO SGD_EDIFICIO_ARCHIVO   (SGD_EDIFICIO_NOMB,
														SGD_EDIFICIO_SIGLA,
														SGD_EDIFICIO_DIR,
														SGD_EDIFICIO_CONT,
														SGD_EDIFICIO_PAIS,
														SGD_EDIFICIO_DPTO,
														SGD_EDIFICIO_MPIO,
														SGD_EDIFICIO_ESTADO )
							VALUES ('".$_POST['nomEd']."',
									'".$_POST['sigEd']."',
									'".$_POST['dirEd']."',
									".$_POST['selCont'].",
									".$_POST['selPais'].",
									".$_POST['selDpto'].",
									".$_POST['selMpio'].",
									".$_POST['estEd'].")";
		$rsIns = $db->conn->Execute($insEd);
		
		### SI SE ACTUALIZO CORRECTAMENTE
		if ($rsIns > 0) {
			$mensg = "Se registro correctamente el Edificio";
			$ok = true;
		}
		else{
			$mensg = "Ocurrio un inconveniente en la creaci&oacute;n, por favor intentelo de nuevo";
		}
	} ### FIN - SE REGISTRA UN NUEVO DIFICIO
}
#########################################################################################



#########################################################################################
### SE VERIFICA SI VIENE DEL FORMULARIO DE SECCIONES Y/O PISOS
if (isset($_POST['btn_piso'])){
	
	### ACTUALIZACION DATOS DE LA SECCION Y/O PISO
	if ($_POST['btn_piso'] == 'Modificar'){
		$sqlPiso = "SELECT	SGD_EDIFICIO_COD,
							SGD_PISO_COD,
							SGD_PISO_DESC,
							SGD_PISO_SIGLA,
							SGD_PISO_ESTADO
					FROM	SGD_PISO_ARCHIVO
					WHERE	SGD_PISO_COD = ".$_POST['codSec'];
		$rsPiso = $db->conn->Execute($sqlPiso);
		
		### SE VERIFICA SI CAMBIO EL NOMBRE
		if ($rsPiso->fields['SGD_PISO_DESC'] != $_POST['nomSec']) {
			$sUp = "SGD_PISO_DESC = '".$_POST['nomSec']."'";
			$msg = "nombre del piso ".$rsPiso->fields['SGD_PISO_DESC']." modificado por ".$_POST['nomSec'];
		}
		
		### SE VERIFICA SI CAMBIA LA SIGLA
		if ($rsPiso->fields['SGD_PISO_SIGLA'] != $_POST['sigSec']){
			$sUp = ", SGD_PISO_SIGLA = '".$_POST['sigSec']."'";
			$msg = $msg.", sigla del piso ".$rsPiso->fields['SGD_PISO_SIGLA']." modificado por ".$_POST['sigSec'];
		}

		### SE VERIFICA SI CAMBIA EL ESTADO
		if ($rsPiso->fields['SGD_PISO_ESTADO'] != $_POST['estSec']){
			$sUp = ", SGD_PISO_ESTADO = ".$_POST['estSec'];
			$msg = $msg.", estado del piso ".$rsPiso->fields['SGD_PISO_ESTADO']." modificado por ".$_POST['estSec'];
		}
		
		if( substr($sUp,0,1) == ",") 
			$sUp = substr($sUp,2);
		
		if ($sUp){
			$sqlUpSe = "UPDATE	SGD_PISO_ARCHIVO
						SET		$sUp
						WHERE	SGD_PISO_COD = ".$_POST['codSec'];
			$rsUpSe = $db->conn->Execute($sqlUpSe);
			$cntAffect = $db->conn->Affected_Rows();

			### SI SE ACTUALIZO CORRECTAMENTE
			if ($cntAffect > 0) {
				$mensg = "Se actualizar&oacute;n correctamente los datos";
				$ok = true;
			}
			else{
				$mensg = "Ocurrio un inconveniente durante la actualizaci&oacute;n, por favor intentelo de nuevo";
			}
		}
	}
	
	### SE REGISTRA UNA NUEVA SECCION O PISO
	elseif ($_POST['btn_piso'] == 'Crear'){
		$insSec = "	INSERT INTO SGD_PISO_ARCHIVO	(SGD_EDIFICIO_COD,
													SGD_PISO_DESC,
													SGD_PISO_SIGLA,
													SGD_PISO_ESTADO	)
							VALUES (".$_POST['selEd'].",
									'".$_POST['nomSec']."',
									'".$_POST['sigSec']."',
									".$_POST['estSec'].")
					SELECT	SCOPE_IDENTITY();";
		$rsSec = $db->conn->Execute($insSec);
		
		### SI SE ACTUALIZO CORRECTAMENTE
		if ($rsSec) {
			$mensg = "Se registro correctamente la Secci&oacute;n y/o Piso";
			
			### SI LA CANTIDAD DE ESTANTES ES > 0; SE CREAN AUTOMATICAMENTE LOS ESTANTES
			if ($_POST['canEst'] > 0 ){
				for($i = 1; $i <= $_POST['canEst']; $i++){
					$insEst = "	INSERT INTO SGD_ESTANTE_ARCHIVO (SGD_PISO_COD, SGD_EST_DESC, SGD_EST_SIGLA)
								VALUES (".$rsSec->fields['COMPUTED'].",'ESTANTE ".$i."', 'ES_".$i."')
								SELECT	@@ROWCOUNT";
					$rsEst = $db->conn->Execute($insEst);
				}
				if ($rsEst->fields['COMPUTED'] > 0) {
					$mensg = $mensg." y se crearon ".$_POST['canEst']." Estantes para la Secci&oacute;n y/o Piso";
					$ok = true;
				}
			}
		}
		else{
			$mensg = "Ocurrio un inconveniente en la creaci&oacute;n, por favor intentelo de nuevo";
		}
	}
}
#########################################################################################



#########################################################################################
### SE VERIFICA SI VIENE DEL FORMULARIO DE ESTANTES
if (isset($_POST['btn_estante'])){
	
	### ACTUALIZACION DATOS DE ESTANTE
	if ($_POST['btn_estante'] == 'Modificar'){
		$flag = true;
		$sqlE = "SELECT	PI.SGD_EDIFICIO_COD,
						ES.SGD_PISO_COD,
						ES.SGD_EST_COD,
						ES.SGD_EST_DESC,
						ES.SGD_EST_SIGLA,
						ES.SGD_EST_ID,
						ES.SGD_EST_ESTADO,
						(	SELECT	COUNT(*)
							FROM	SGD_EXP_EXPEDIENTE
							WHERE	SGD_EXP_ESTANTE = ".$_POST['codEst']." AND
									SGD_EXP_EDIFICIO = ".$_POST['selEd']." AND
									SGD_EXP_ARCHIVO = ".$_POST['selPiso'].") AS OCUPADOS
				FROM	SGD_ESTANTE_ARCHIVO AS ES
						JOIN SGD_PISO_ARCHIVO AS PI ON
							PI.SGD_PISO_COD = ES.SGD_PISO_COD AND
							PI.SGD_EDIFICIO_COD = ".$_POST['selEd']."
				WHERE	ES.SGD_PISO_COD = ".$_POST['selPiso']."
						AND ES.SGD_EST_COD = ".$_POST['codEst'];
		$rsE = $db->conn->Execute($sqlE);
		
		
		### SE VERIFICA SI CAMBIO EL NOMBRE
		if ($rsE->fields['SGD_EST_DESC'] != $_POST['nomEst']) {
			$sUp = "SGD_EST_DESC = '".$_POST['nomEst']."'";
			$msg = "nombre de edificio ".$rsE->fields['SGD_EST_DESC']." modificado por ".$_POST['nomEst'];
		}

		### SE VERIFICA SI CAMBIA LA SIGLA
		if ($rsE->fields['SGD_EST_SIGLA'] != $_POST['sigEst']){
			$sUp = $sUp.", SGD_EST_SIGLA = '".$_POST['sigEst']."'";
			$msg = $msg.", sigla de edificio ".$rsE->fields['SGD_EST_SIGLA']." modificado por ".$_POST['sigEst'];
		}

		### SE VERIFICA SI CAMBIA EL IDENTIFICADOR DEL ESTANTE
		if ($rsE->fields['SGD_EST_ID'] != $_POST['refEst']){
			$sUp = $sUp.", SGD_EST_ID = '".$_POST['refEst']."'";
			$msg = $msg.", identificador del estante ".$rsE->fields['SGD_EST_ID']." modificado por ".$_POST['refEst'];
		}

		### SE VERIFICA SI CAMBIA EL ESTADO
		if ($rsE->fields['SGD_EST_ESTADO'] != $_POST['estEst']){
			$sUp = $sUp.", SGD_EST_ESTADO = ".$_POST['estEst'];
			$msg = $msg.", estado de estante ".$rsE->fields['SGD_EST_ESTADO']." modificado por ".$_POST['estEst'];
		}

		if( substr($sUp,0,1) == ",") 
			$sUp = substr($sUp,2);

		if ($sUp){
			$sqlUpEs = "UPDATE	SGD_ESTANTE_ARCHIVO
						SET		$sUp
						WHERE	SGD_EST_COD = ".$_POST['codEst'];
			$rsUpEs = $db->conn->Execute($sqlUpEs);
			$cntAffect = $db->conn->Affected_Rows();

			### SI SE ACTUALIZO CORRECTAMENTE
			if ($cntAffect > 0) {
				$mensg = "Se actualizar&oacute;n correctamente los datos";
				$ok = true;
			}
			else{
				$mensg = "Ocurrio un inconveniente durante la actualizaci&oacute;n, por favor intentelo de nuevo";
			}
		}

	} ### FIN - ACTUALIZACION DATOS DE ESTANTE
	
	### SE REGISTRA UN NUEVO ESTANTE
	elseif ($_POST['btn_estante'] === 'Crear'){
		$insEs = "	INSERT INTO SGD_ESTANTE_ARCHIVO 
							(SGD_PISO_COD, SGD_EST_DESC, SGD_EST_SIGLA, SGD_EST_ID, SGD_EST_ESTADO )
					VALUES 	(".$_POST['selPiso'].",
							'".$_POST['nomEst']."',
							'".$_POST['sigEst']."',
							'".$_POST['refEst']."',
							".$_POST['estEst'].")";
		$rsInsE = $db->conn->Execute($insEs);
		### SI SE ACTUALIZO CORRECTAMENTE
		if ($rsInsE) {
			$mensg = "Se registro correctamente el Estante";
			$ok = true;
		}
		else{
			$mensg = "Ocurrio un inconveniente en la creaci&oacute;n, por favor intentelo de nuevo";
		}
	} ### FIN - SE REGISTRA UN NUEVO ESTANTE
}
#########################################################################################
?>