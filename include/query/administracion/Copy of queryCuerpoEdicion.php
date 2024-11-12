
<?php
	$sqlConcat = $db->conn->Concat("usu.usua_doc","'-'","usu.usua_login");
        if($busqRadicados){
            $busqRadicados = strtoupper($busqRadicados);
            $whereUsuario = "( usu.usua_login like '%$busqRadicados%' or usu.usua_nomb like '%$busqRadicados%') and ";
        }
        if($depActual==true ){
            $whereDependencia = "";
        }else{
            $whereDependencia = "usd.depe_codi = " . $dep_sel . " and    ";
        }
	switch($db->driver) {
        case 'mssqlnative':
            
            
            $isql = "SELECT
                            	usu.usua_nomb as 'Nombre', 
                        	usd.usua_login as 'Usuario', 
                        	dep.depe_nomb as 'Dependencia',
                            " . $sqlConcat  . " AS CHR_USUA_DOC
                        FROM
                                usuario usu,
                                SGD_USD_USUADEPE USD,
                                dependencia DEP
                        where
                                $whereUsuario
                                $whereDependencia
                                usu.usua_doc = usd.usua_doc and
                                usu.usua_login like usd.usua_login and
                                usd.depe_codi = dep.depe_codi

                        ORDER BY " . $order . " " . $orderTipo;
            break;
        case 'oracle':
        case 'oci8':
            $isql = "select 
                    u.usua_nomb      		AS NOMBRE
                    ,u.usua_login	     	AS USUARIO
                    ,d.depe_nomb			AS DEPENDENCIA
                    ," . $sqlConcat  . " 	AS CHR_USUA_DOC
                from usuario u, dependencia d 
                where u.depe_codi = " . $dep_sel .
                " AND u.depe_codi = d.depe_codi " . $dependencia_busq2 . "
                order by " . $order . " " . $orderTipo;
	}
?>
