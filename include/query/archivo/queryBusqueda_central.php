<?php
	switch($db->driver)
	{
	case 'mssqlnative':
	case 'oracle':
	case 'oci8':
		$sql="select * from SGD_ARCHIVO_CENTRAL where $srds $c $sbrds $d $pross $ef $r $b $x $a $zon $f $carro $g $cara $i $estan $h $entre $v $caja $t $orden $k $depe $l $fecha $j $fecha2 $w $deman $n $demant $m $docu $o $inder $p $mata $q SGD_ARCHIVO_ID !=0 $orde";

	$sqla="select usua_admin_archivo from usuario where usua_login like '$krd'";

 	$sql1="select SGD_EIT_NOMBRE from SGD_EIT_ITEMS where SGD_EIT_COD_PADRE like '$buscar_zona' order by SGD_EIT_NOMBRE";
	$sql6="select SGD_EIT_NOMBRE from SGD_EIT_ITEMS where SGD_EIT_COD_PADRE like '$buscar_ufisica' order by SGD_EIT_NOMBRE";
	$sql7="select SGD_EIT_NOMBRE from SGD_EIT_ITEMS where SGD_EIT_COD_PADRE like '$buscar_estan' order by SGD_EIT_NOMBRE";
	$sql8="select SGD_EIT_NOMBRE from SGD_EIT_ITEMS where SGD_EIT_COD_PADRE like '$buscar_entre' order by SGD_EIT_NOMBRE";
	$sql9="select SGD_EIT_NOMBRE from SGD_EIT_ITEMS where SGD_EIT_COD_PADRE like '$buscar_caja' order by SGD_EIT_NOMBRE";
 	$sql2="select SGD_EIT_SIGLA from SGD_EIT_ITEMS where SGD_EIT_CODIGO like '$caja1'";
 	$sql3="select SGD_EIT_SIGLA from SGD_EIT_ITEMS where SGD_EIT_CODIGO like '$estante1'";
 	$sql4="select SGD_EIT_SIGLA from SGD_EIT_ITEMS where SGD_EIT_CODIGO like '$piso1'";
 	$sql5="select SGD_EIT_SIGLA from SGD_EIT_ITEMS where SGD_EIT_CODIGO like '$archiva1'";
	$sql10="select SGD_EIT_SIGLA from SGD_EIT_ITEMS where SGD_EIT_CODIGO like '$carro1'";
	$sql11="select SGD_EIT_SIGLA from SGD_EIT_ITEMS where SGD_EIT_CODIGO like '$entrepa1'";
	break;
	}
?>