<?php
	switch($db->driver)
	{
	case 'mssqlnative':
	case 'oracle':
	case 'oci8':
		$sql="select s.SGD_SRD_CODIGO,s.SGD_EXP_NUMERO,s.SGD_SBRD_CODIGO,s.SGD_PEXP_CODIGO,e.SGD_EXP_NUMERO,s.SGD_SEXP_PAREXP1,s.SGD_SEXP_PAREXP2,s.SGD_SEXP_PAREXP3,s.SGD_SEXP_PAREXP5,e.SGD_EXP_FECH_ARCH,e.SGD_EXP_FECHFIN,r.RADI_NUME_HOJA,e.SGD_EXP_CAJA,e.SGD_EXP_UFISICA,e.SGD_EXP_ISLA,e.RADI_NUME_RADI,c.SGD_DIR_DOC
	,e.SGD_EXP_ESTANTE,e.SGD_EXP_CARPETA, e.SGD_EXP_RETE,r.radi_fech_radi,e.sgd_exp_carro,e.sgd_exp_entrepa, r.radi_path,r.RADI_CUENTAI,r.EESP_CODI
	 from SGD_SEXP_SECEXPEDIENTES s, SGD_EXP_EXPEDIENTE e, radicado r, SGD_DIR_DRECCIONES c where $srds $c $sbrds $d $pross $ef $r $b $x $a $pis $f $caj $g $estan $h $entre $v $caja $t $caja2 $u $foli $k $fecha $i $fechafin $j $titul $l $conse $n $archi $o $depa $p $muni $q";
	if($docu1==3 and $buscar_docu!="")$sql.=" r.RADI_CUENTAI like '%$buscar_docu%' and ";
	if($docu1==2 and $buscar_docu!="")$sql.=" c.SGD_DIR_DOC like '%$buscar_docu%' and ";
	$sql.=" e.SGD_EXP_NUMERO=s.SGD_EXP_NUMERO and r.radi_nume_radi=e.radi_nume_radi and e.SGD_EXP_ESTADO='1' and c.RADI_NUME_RADI=e.RADI_NUME_RADI $orde";

	if($docu1==1 and $buscar_docu!="")$sqld.="select NIT_DE_LA_EMPRESA from BODEGA_EMPRESAS where IDENTIFICADOR_EMPRESA like '$eesp'";


	$sqlca="select c.RADI_NUME_RADI,r.RADI_FECH_RADI,r.RADI_PATH,c.SGD_DIR_DOC,r.RADI_CUENTAI,r.RADI_NUME_HOJA";
	if($docu1==1 and $buscar_docu!="")$sqlca.=",m.NIT_DE_LA_EMPRESA";
	$sqlca.=" from RADICADO r,SGD_DIR_DRECCIONES c";
	if($docu1==1 and $buscar_docu!="")$sqlca.=",BODEGA_EMPRESAS m";
	$sqlca.=" where ";
	if($docu1==1 and $buscar_docu!="")$sqlca.="m.NIT_DE_LA_EMPRESA LIKE '%$buscar_docu%' and r.EESP_CODI=m.IDENTIFICADOR_EMPRESA and c.RADI_NUME_RADI=r.RADI_NUME_RADI";
	elseif($docu1==2 and $buscar_docu!="")$sqlca.="c.SGD_DIR_DOC LIKE '%$buscar_docu%' and r.RADI_NUME_RADI=c.RADI_NUME_RADI";
	elseif($docu1==3 and $buscar_docu!="")$sqlca.="r.RADI_CUENTAI like '%$buscar_docu%' and r.RADI_NUME_RADI=c.RADI_NUME_RADI";
	else $sqlca.="r.RADI_NUME_RADI=c.RADI_NUME_RADI";
	$sqlca.=" order by r.RADI_NUME_RADI";

 	$sqlmin="select e.SGD_EXP_NUMERO,e.SGD_EXP_TITULO,e.SGD_EXP_ASUNTO,e.SGD_EXP_FECH_ARCH,e.SGD_EXP_FECHFIN,
	e.SGD_EXP_CAJA,e.SGD_EXP_UFISICA,e.SGD_EXP_ISLA,e.SGD_EXP_CARPETA,e.SGD_EXP_ESTANTE,e.SGD_EXP_RETE,
	e.sgd_exp_carro,e.sgd_exp_entrepa,	s.SGD_SRD_CODIGO,s.SGD_SBRD_CODIGO,s.SGD_PEXP_CODIGO,s.SGD_SEXP_PAREXP1,s.SGD_SEXP_PAREXP2,s.SGD_SEXP_PAREXP3,
		s.SGD_SEXP_PAREXP5 from SGD_EXP_EXPEDIENTE e, SGD_SEXP_SECEXPEDIENTES s
		where $srds $c $sbrds $d $pross $ef $r $b $pis $f $estan $h $entre $s $caja $t $caja2 $u $foli $k $fecha $i $fechafin $j $param $l $conse $n $archi $o $depa $p $muni $q $x $a s.SGD_EXP_NUMERO=e.SGD_EXP_NUMERO and e.RADI_NUME_RADI LIKE '$radi' and e.SGD_EXP_ESTADO='1'";

 	$sql1="select SGD_EIT_NOMBRE from SGD_EIT_ITEMS where SGD_EIT_COD_PADRE like '3' order by SGD_EIT_NOMBRE";
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

 	$queryDpto = "select DPTO_NOMB,DPTO_CODI FROM DEPARTAMENTO ORDER BY DPTO_NOMB";
 	$queryMuni = "select MUNI_NOMB,MUNI_CODI FROM MUNICIPIO WHERE DPTO_CODI= '$codDpto' ORDER BY MUNI_NOMB";
	break;
	}
?>