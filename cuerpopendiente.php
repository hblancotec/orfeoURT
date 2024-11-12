<?php
    include_once ("./config.php");
    $krdOld = $krd;
    $carpetaOld = $carpeta;
    $tipoCarpOld = $tipo_carp;
    if(!$tipoCarpOld) $tipoCarpOld = $tipo_carpt;
    session_start();
    if(!$krd) $krd = $krdOsld;
    $ruta_raiz = ".";
    if(!isset($_SESSION['dependencia'])) include ("./rec_session.php");
    if(!$carpeta) {
        $carpeta = $carpetaOld;
        $tipo_carp = $tipoCarpOld;
    }
    $verrad  = "";
    $_SESSION['numExpedienteSelected'] = null;
    $reporte = $_GET["reporte"];
    set_time_limit(300);

 //se realiza el siguiente filtro por dependencia para
 //evitar que se muestre los radicados   de prueba a los usuarios de
 //produccion
	$depe = $_SESSION['dependencia'];
	if($depe == 900){
		$filtroPru= "";
	}else $filtroPru= "and (d.depe_codi <> 900)";

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="extjs/resources/css/ext-all.css">
<script src="extjs/adapter/ext/ext-base.js"></script>
<script src="extjs/ext-all.js"></script>
<link rel="stylesheet" href="estilos/orfeo.css">
<script src="js/popcalendar.js"></script>
<script src="js/mensajeria.js"></script>
<div id="spiffycalendar" class="text"></div>
</head>

<body bgcolor="#FFFFFF" topmargin="0"">

<?php
    include_once ("./include/db/ConnectionHandler.php");
    include ("./envios/paEncabeza.php");
    if (!$db) $db = new ConnectionHandler($ruta_raiz);
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $encabezado  = "".session_name()."=".session_id();
    $encabezado .= "&krd=$krd&depeBuscada=$depeBuscada";
    $encabezado .= "&filtroSelect=$filtroSelect&tpAnulacion=$tpAnulacion";
    $encabezado .= "&carpeta=$carpeta&tipo_carp=$tipo_carp&chkCarpeta=$chkCarpeta";
    $encabezado .= "&busqRadicados=$busqRadicados&nomcarpeta=$nomcarpeta&agendado=$agendado&";
    $linkPagina  = "$PHP_SELF?$encabezado&orderTipo=$orderTipo&orderNo=$orderNo";
    $encabezado  = "".session_name()."=".session_id();
    $encabezado .= "&adodb_next_page=1&krd=$krd&depeBuscada=$depeBuscada";
    $encabezado .= "&filtroSelect=$filtroSelect&tpAnulacion=$tpAnulacion";
    $encabezado .= "&carpeta=$carpeta&tipo_carp=$tipo_carp&nomcarpeta=$nomcarpeta";
    $encabezado .= "&agendado=$agendado&orderTipo=$orderTipo&orderNo=";

    $sqlFecha = $db->conn->SQLDate("Y-m-d H:i A","b.RADI_FECH_RADI");

// devuelve fecha solo dias, sin horas minutos o segundos
function solo_Dia($dt) {
    $a = gmdate("Y", $dt);
    $m = gmdate("m", $dt);
    $d = gmdate("d", $dt);
    $date  = date('Y/m/d', mktime(0, 0, 0,$m, $d, $a));
    return $date;
}

// devuelve si la fecha es dia habil
function es_Habil($dt, $inhabiles) {
    $a = gmdate("Y", $dt);
    $m = gmdate("m", $dt);
    $d = gmdate("d", $dt);
    $date  = date('D', mktime(0,0,0,$m,$d,$a));
    $habil = True;
    // si es sabado o domingo
    if ($date == 'Sat' or $date == 'Sun') {
        $habil = False;
    }
    else {
        // si esta en festivos
        if (in_array(date("Y/m/d", mktime(0, 0, 0, $m, $d, $a)), $inhabiles)) {
            //echo date("Y/m/d", mktime(0, 0, 0, $m, $d, $a)) . ":" . $inhabiles . "\n";
            $habil = False;
        }
    }
    return $habil;
}

// cuenta los dias habiles a partir de una fecha,
// tomando como base un numero de dias especifico
function dias_a_sumar($base, $dias, $inhabiles){
    $dt     = strtotime(solo_Dia(strtotime($base)));
    $dtw    = $dt;
    $asumar = $dias;

    // para los dias a sumar
    for ($y = 1; $y <= ($dias); $y++) {
       if (! es_Habil($dt, $inhabiles)) {
          $asumar++;
       }
       $dt = $dt + 86400; //sumo un dia
    }

    // fecha mas dias habiles en rango
    $newdays = $dtw + (86400 * $asumar);

    // valida siguiente dia habil
    $asumarH = dias_HabilSiguiente($newdays, $inhabiles);
    $asumarH = 0;
    return ($asumar + $asumarH);
}

// cuenta los dias habiles a partir de una fecha,
// tomando como base un numero de dias especifico
function dias_a_salir($base, $dias, $inhabiles) {
    $dt = strtotime(solo_Dia(strtotime($base)));
    $y  = $dias;

    // para los dias a sumar
    while ($y > 1){
       if (es_Habil($dt, $inhabiles)) {
          $y = $y - 1;//resto un dia si es habil
       };
       $dt = $dt + 86400; //sumo un dia
    };

    // si cayo en un dia no habil
    while (!es_Habil($dt, $inhabiles)) {
       $dt = $dt + 86400; //sumo un dia
    };

    return $dt;
}

function dias_HabilSiguiente($base, $inhabiles){
   $dt      = $base;
   $asumar  = 0;
   // dia no cae en dia habil
   while (! es_Habil($dt, $inhabiles)) {
      $dt = $dt + 86400;
      $asumar++;
   }
   return $asumar;
}

// cuenta los dias habiles a partir de una fecha
// hasta otra fecha
function dias_habiles($vence, $hoy, $inhabiles){
    $daysSec= 86400;
    $cuenta = 0;
    $sigue  = True;
    $factor = 1;
    $dias   = 0;
    $base   = $hoy;
    $final  = $vence;
    if ($vence < $hoy) {
        $factor = -1;
        $base   = $vence;
        $final  = $hoy;
    }
    //$sigue = False;
    while ($sigue == True) {
        $newdate = $base + ($dias*$daysSec);
        if ($newdate <= $final) {
            if (es_Habil($newdate, $inhabiles)) {
                $cuenta = $cuenta + 1;
            }
        }
        else {
            $sigue = False;
        }
        $dias = $dias + 1;
    }
    return $cuenta * $factor;
}

    $queryFes = "select SGD_FESTIVO AS FESTIVO from SGD_DIAS_FESTIVOS order by 1";
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $inhabiles = array();
    $rsf = $db->conn->Execute($queryFes);
    while (!$rsf->EOF) {
       $inhabiles[] = $rsf->fields["FESTIVO"];
       $rsf->MoveNext();
    }

    $isql     = "
				SELECT
						DISTINCT convert(char(15), r.RADI_NUME_RADI) AS RADICADO
						, r.RADI_FECH_RADI AS FECHA
						, u.USUA_NOMB AS NOMBRE
						, d.DEPE_NOMB AS DEPENDENCIA
						, t.SGD_TPR_DESCRIP AS TIPO
                        , t.sgd_tpr_termino
						, r.RADI_FECHA_VENCE as VENCE
						, r.RADI_DIAS_VENCE as DIASVENCE
                 FROM
				 		RADICADO r
						, USUARIO u
						, DEPENDENCIA d
						, SGD_TPR_TPDCUMENTO t
                 WHERE
				 		r.RADI_FECH_RADI > '2011'
				 		and r.RADI_USUA_ACTU = u.usua_codi	
						and r.RADI_DEPE_ACTU = u.depe_codi
                 		and r.RADI_DEPE_ACTU = d.depe_codi
                 		and t.SGD_TPR_CODIGO = r.tdoc_codi
                 		and r.RADI_NUME_RADI like '%2'
                        and t.SGD_TPR_NOTIFICA = 1
						$filtroPru
                 		and r.RADI_NUME_RADI not in
							(
								SELECT
									DISTINCT r.RADI_NUME_RADI
				              	FROM
									RADICADO r
									, ANEXOS a
									, HIST_EVENTOS h
									, SGD_TPR_TPDCUMENTO t
									
								WHERE
							    	t.SGD_TPR_NOTIFICA = 1
                                    and t.SGD_TPR_CODIGO    = r.TDOC_CODI				                	
					 				and a.ANEX_RADI_NUME 	= r.RADI_NUME_RADI
									and convert(varchar(15), a.RADI_NUME_SALIDA) like '%1'
									and a.RADI_NUME_SALIDA 	= h.RADI_NUME_RADI
									and h.SGD_TTR_CODIGO 	= 42
                 			
                 			    UNION
                 			
                   				SELECT
									DISTINCT r.RADI_NUME_RADI
                   				FROM
									radicado r
									, hist_eventos h
									, SGD_TPR_TPDCUMENTO t
                   				WHERE
                 				    t.SGD_TPR_NOTIFICA   = 1
                                    and t.SGD_TPR_CODIGO = r.TDOC_CODI				                										
									and r.RADI_NUME_RADI = h.RADI_NUME_RADI
									and h.SGD_TTR_CODIGO = 13
                			)						
    		     UNION            	
                		
				 SELECT
				       	DISTINCT convert(char(15), j.RADI_NUME_RADI) AS RADICADO
				        , j.RADI_FECH_RADI AS FECHA
				        , u.USUA_NOMB AS NOMBRE
				        , d.DEPE_NOMB AS DEPENDENCIA
						, t.SGD_TPR_DESCRIP AS TIPO
				        , t.SGD_TPR_TERMINO
						, j.RADI_FECHA_VENCE as VENCE
						, j.RADI_DIAS_VENCE as DIASVENCE
						
				 FROM
							SGD_TPR_TPDCUMENTO t
							, SGD_MRD_MATRIRD  m
							, dependencia d
							, RADICADO j
							, SGD_HMTD_HISMATDOC i
							, usuario u
							
					   WHERE
							t.SGD_TPR_NOTIFICA   = 1
							and j.RADI_FECH_RADI > '2011'
							and j.RADI_USUA_ACTU = u.USUA_CODI	
							and j.RADI_DEPE_ACTU = u.DEPE_CODI
				            and j.RADI_DEPE_ACTU = d.DEPE_CODI
				            and j.RADI_NUME_RADI like '%2'                           		
							and m.SGD_TPR_CODIGO = t.SGD_TPR_CODIGO
							and j.RADI_NUME_RADI = i.RADI_NUME_RADI
							and i.SGD_MRD_CODIGO = m.SGD_MRD_CODIGO
							$filtroPru
							and j.RADI_NUME_RADI not in
											(
												SELECT
													DISTINCT r.RADI_NUME_RADI
								              	FROM
													RADICADO r
													, ANEXOS a
													, HIST_EVENTOS h
													, SGD_TPR_TPDCUMENTO t
													
												WHERE
											    	t.SGD_TPR_NOTIFICA = 1
				                                    and t.SGD_TPR_CODIGO    = r.TDOC_CODI				                	
									 				and a.ANEX_RADI_NUME 	= r.RADI_NUME_RADI
													and convert(varchar(15), a.RADI_NUME_SALIDA) like '%1'
													and a.RADI_NUME_SALIDA 	= h.RADI_NUME_RADI
													and h.SGD_TTR_CODIGO 	= 42
				                 			
				                 			    UNION
				                 			
				                   				SELECT
													DISTINCT r.RADI_NUME_RADI
				                   				FROM
													RADICADO r
													, HIST_EVENTOS h
													, SGD_TPR_TPDCUMENTO t
				                   				WHERE
				                 				    t.SGD_TPR_NOTIFICA   = 1
				                                    and t.SGD_TPR_CODIGO = r.TDOC_CODI				                										
													and r.RADI_NUME_RADI = h.RADI_NUME_RADI
													and h.SGD_TTR_CODIGO = 13
				                			)				
							
					order by r.radi_fech_radi "
?>

<script type="text/javascript" charset="utf-8">
<?php
    function carga_radicados(&$mydata, &$result, $encabezado, $inhabiles, $rutaRaiz)
    {   $i = 0;
	while($result && !$result->EOF)
        {
            //echo $i . "-";
 	    //******* manejo del radicado *****//
	    $radicado    = "";
	    $nroradicado = $result->fields["RADICADO"];
	    $radicado    = "<span class=$radFileClass>$radicado</span>";

	    //******* manejo fecha radicado *****//
            $fechaRadicado = $result->fields["FECHA"];
            $fechaRadicado = "<span class=" . $radFileClass . ">" . $fechaRadicado . "</span>" .
                             "<a href=" . $rutaRaiz . "/verradicado.php?verrad=" . $nroradicado . "&" .
                             $encabezado . "><span class=" . $radFileClass . "> [---Ver---] </span></a>";
            //***** fin manejo fecha de radicado *****//

	    //******* manejo responsable radicado *****//
	    $responsable = htmlentities($result->fields["NOMBRE"]);
	    $responsable = "<span class=$radFileClass>$responsable</span></a>";
	    //***** fin manejo responsable de radicado *****//

	    //******* manejo entidad dependecia *****//
	    $dependecia = $result->fields["DEPENDENCIA"];
	    $dependecia = "<span class=$radFileClass>$dependecia</span></a>";
	    //***** fin manejo dependecia de radicado *****//

	    //******* manejo tipo radicado *****//
	    $tiporad = $result->fields["TIPO"];
	    $tiporad = "<span class=$radFileClass>$tiporad</span></a>";
	    //***** fin manejo tipo de radicado *****//

	    //******* ID DE RADICADO *****//
	    $id = $result->fields["id"];
	    //***** fin ID DE RADICADO *****//
	        $newdays =0;
	        $diff = 0;

	        $newdays = $result->fields["VENCE"];
            $diff = $result->fields["DIASVENCE"];

            /*date("Y-m-d", $newdays)

            calcula dias habiles desde radicacion, tomando como base los dias de termino 
            $newdays = dias_a_salir($result->fields["FECHA"],
                                    $result->fields["sgd_tpr_termino"],
                                    $inhabiles);
			//fecha de hoy
            $hoy     = strtotime(solo_Dia(time()));

            diferencia
            $diff    = dias_habiles($newdays, $hoy, $inhabiles);
            
            */

	    $numradicado = $nroradicado;
	    $nroradicado = "<span class=select>$nroradicado</span>";
	    $myData[]    = array($nroradicado, $fechaRadicado, $responsable, $dependecia, $tiporad,
                                 $newdays, $diff, $numradicado, $result->fields["FECHA"],
                                 $result->fields["NOMBRE"], $result->fields["DEPENDENCIA"], $result->fields["TIPO"]);

            $i = $i + 1;

	    $result->MoveNext();
	}

	return $myData;
    }

    include_once(ORFEOPATH . "php-ext/php-ext/php-ext.php");
    include_once(NS_PHP_EXTJS_CORE);
    include_once(NS_PHP_EXTJS_DATA);
    include_once(NS_PHP_EXTJS_GRID);

    //echo $isql . "<br/>";

    $result   = $db->conn->Execute($isql);
    $rutaRaiz = $db->rutaRaiz;

    $myData   = array();
    $myData   = carga_radicados($mydata, $result, $encabezado, $inhabiles, $rutaRaiz);
    echo "var myData = " . Javascript::valueToJavascript($myData) . ";";
?>
   Ext.onReady(function(){

    var xg = Ext.grid;

    // create the data store
    var store = new Ext.data.SimpleStore({
        fields: [
           {name: "radicado"},
           {name: "fecha"},
           {name: "responsable"},
           {name: "area"},
           {name: "tipo"},
           {name: "vence"},
           {name: "dias"}
        ]
    });


    //alert(myData);

    store.setDefaultSort('fecha', 'desc');
    store.loadData(myData);

    var sm = new xg.CheckboxSelectionModel();
    // create the Grid
    grid = new Ext.grid.GridPanel({
        store: store,
        columns: [sm,
            {id: 'radicado',    header: "Numero de Radicado", width: 130, sortable: true,  dataIndex: 'radicado'},
            {id: 'fecha',       header: "Fecha Radicado",     width: 180, sortable: true,  dataIndex: 'fecha'},
            {id: 'responsable', header: "Responsable",        width: 200, sortable: true,  dataIndex: 'responsable'},
            {id: 'area',        header: "Dependencia",        width: 200, sortable: true,  dataIndex: 'area'},
            {id: 'tipo',        header: "Tipo Documento",     width: 200, sortable: true,  dataIndex: 'tipo'},
            {id: 'vence',       header: "Vence",              width:  80, sortable: true,  dataIndex: 'vence'},
            {id: 'dias',        header: "Dias",               width:  50, sortable: true,  dataIndex: 'dias'}
        ],
        //stripeRows: true,
        height:700,
        width:1080,
	sm: sm,
        title:'Pendientes Entidad Con Notificacion'
        //id:'datas'
    });

    //seleccionados = Ext.DomHelper.append(document.form1,{tag:'input', id:'seleccionados',   name:'seleccionados',   value:'', type:'hidden'});
    //noraiz        = Ext.DomHelper.append(document.form1,{tag:'input', id:'noraiz',          name:'noraiz',          value:"", type:'hidden'});
    //noidraiz      = Ext.DomHelper.append(document.form1,{tag:'input', id:'noidraiz',        name:'noidraiz',        value:"", type:'hidden'});

    grid.render('grid-example1');
});
</script>

<table>
  <tr>
    <td align="center" height="40" cellspacing="50" cellpadding="50" class="borde_tab">
      <a  onclick="" href='cuerpopendiente.php?<?=$phpsession ?>&krd=<?=$krd?>&reporte=1' alt='Reporte'> - Generar Reporte Pendientes - </a>
    </td>
  </tr>
</table>

<?php

if ($reporte == 1) {
       $contenido = "";
       $contenido .= '<?xml version="1.0" encoding="iso-8859-1"?>';
       $contenido .= "<Pendientes>\n ";
       foreach ($myData as $item) {
           $contenido .= " <Radicado>\n ";
           $radicado   = "R:" . strval($item[7]);
	   $contenido .= "  <Nro_Radicado>"   . $radicado . "</Nro_Radicado>\n ";
	   $contenido .= "  <Fecha_Radicado>" . $item[8]  . "</Fecha_Radicado>\n ";
	   $contenido .= "  <Responsable>"    . $item[9]  . "</Responsable>\n ";
           $contenido .= "  <Dependencia>"    . $item[10] . "</Dependencia>\n ";
           $contenido .= "  <Tipo>"           . $item[11] . "</Tipo>\n ";
	   $contenido .= "  <Vence>"          . $item[5]  . "</Vence>\n" ;
	   $contenido .= "  <Dias>"           . $item[6]  . "</Dias>\n ";
	   $contenido .= "  </Radicado>\n ";
       }
       $contenido .= "</Pendientes>\n ";

       $hora=date("H")."_".date("i")."_".date("s");
       // var que almacena el dia de la fecha
       $ddate=date('d');
       // var que almacena el mes de la fecha
       $mdate=date('m');
       // var que almacena el ano de la fecha
       $adate=date('Y');
       // var que almacena  la fecha formateada
       $fecha=$adate."_".$mdate."_".$ddate;
       //guarda el path del archivo generado
       //$ruta_raiz = "..";
       //$archivo = $ruta_raiz . "/bodega/masiva//tmp_0"."_$fecha"."_$hora" .".xls";
       $archivo = "bodega/masiva//tmp_0"."_$fecha"."_$hora" .".xls";
       $fp=fopen($archivo,"wb");
       fputs($fp,$contenido);
       fclose($fp);
?>

<table>
  <tr>
    <td height="84" class="listado2">
      Para obtener el archivo guarde del destino del siguiente v&iacute;nculo
      al archivo: <a href="<?=$archivo?>" target="_blank">GENERADO</a>
    </td>
  </tr>
</table>

<?php
}
?>

<div id="grid-example1"></div>

</body>
</html>
