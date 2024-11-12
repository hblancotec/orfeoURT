<?php
  /** verificacion si el radicado se encuentra en el usuario Actual
    *
    */
  
		$sql = "SELECT 
					R.RADI_USUA_ACTU AS USU,
					R.RADI_DEPE_ACTU AS DEPE,
					R.SGD_SPUB_CODIGO AS PRIVRAD					
				FROM 
					RADICADO R
				WHERE 
					R.RADI_NUME_RADI=$verrad"; 
		# Busca el usuairo Origen para luego traer sus datos.
		$rs = $db->conn->Execute($sql); # Ejecuta la busqueda 
		$verCodusuario = $rs->fields["USU"]; 
		$verDependencia = $rs->fields["DEPE"];  
		$verSeguridadRad = $rs->fields["PRIVRAD"];
		
		// PARA DERIVADOS VALIDAD QUE ESTE ASOCIADO.
		$pendientes = false;
		if (($codusuario != $verCodusuario)){
		    //echo "No son iguales:" . $codusuario . "<>"	. $verCodusuario . "<br/>";
		    $sql = "SELECT id,
	                   convert(char(15), RADI_NUME_RADI) as 'Radicado'
                    FROM   SGD_RG_MULTIPLE
                    WHERE  RADI_NUME_RADI  = $verrad      AND
	 	           area            = $dependencia AND
			   usuario         = $codusuario  AND
                           estatus        <> 'FINALIZADO' ";
			   
	            //echo "No " . $sql . "<br/>";		   
			   
		    # Busca el usuairo Origen para luego traer sus datos.
		    $rs = $db->conn->Execute($sql); # Ejecuta la busqueda
		    $count = 0;
		    while($rs && !$rs->EOF) {
		       $count = $count + 1;
	               $verCodusuario = $rs->fields["USU"]; 
		       $verDependencia = $rs->fields["DEPE"];
		       $rs->MoveNext(); 
	            }   
		    //$verCodusuario = $rs->fields["USU"]; 
		    ///$verDependencia = $rs->fields["DEPE"];
		    
		    
		    if ($count > 0) {
		       //echo "pendientes </br>"; 	 
		       $pendientes = true; //validar cuando no este en pendientes!!!!
		    }	 
		} else {
		    //echo "Son iguales:" . $codusuario . "<>"	. $verCodusuario . "<br/>";
		    $pendientes = true;
		}
		
		//Buscamos nivel de seguridad del expediente en que se encuentra el radicado
		$sqlExp = "SELECT 
					EXP.SGD_EXP_PRIVADO AS PRIVEXP
				FROM 
					RADICADO R, SGD_SEXP_SECEXPEDIENTES SEXP, SGD_EXP_EXPEDIENTE EXP
				WHERE 
					R.RADI_NUME_RADI=$verrad AND R.RADI_NUME_RADI = EXP.RADI_NUME_RADI AND EXP.SGD_EXP_NUMERO = SEXP.SGD_EXP_NUMERO" ; 
		
		$rs = $db->conn->Execute( $sqlExp );
		$verSeguridadExp = $rs->fields["PRIVEXP"];
		//echo "seguridad:" . $verSeguridadExp . "</br>";
		
		//if( ( $verSeguridadExp == 0 || $verSeguridadExp == 2 ) && $codusuario == $verCodusuario && $dependencia == $verDependencia )
		if( ($pendientes) || ($codusuario == $verCodusuario && $dependencia == $verDependencia))
		{		        
			$verradPermisos = "Full";
		}
		elseif ( $verSeguridadExp == 1 && $codusuario == $verCodusuario && $dependencia == $verDependencia && $_SESSION["codusuario"] == 1 ) {		        
			$verradPermisos = "Full";
		}else
		{
			$verradPermisos = "Otro";
			$mostrar_opc_envio = 0;
			$modificar = false;			
		}
		//echo "permisos:" . $verradPermisos . "</br>";
?>  