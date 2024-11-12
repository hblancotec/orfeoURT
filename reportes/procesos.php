<?php
session_start();
    $dependencia = $_SESSION["dependencia"];
    $ruta_raiz   = "..";

    if(!$_SESSION['dependencia']) include "$ruta_raiz/rec_session.php";
    include ("$ruta_raiz/_conf/constantes.php");
    include_once "$ruta_raiz/include/db/ConnectionHandler.php";
    $db = new ConnectionHandler("$ruta_raiz");
    define('ADODB_FETCH_ASSOC',2);
    $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

    if(!$fecha_busq) $fecha_busq=date("Y-m-d");
    if(!$fecha_busq2) $fecha_busq2=date("Y-m-d");

    $soloEntidades   = $_POST["soloEntidades"];

    function strValido($string){
               $arr         = array('/[^\w:()\sáéíóúÁÉÍÓÚ=#°\-,.;ñÑ]+/', '/[\s]+/');
               $asu         = preg_replace($arr[0], '',$string);
               return    strtoupper(preg_replace($arr[1], ' ',$asu));
    }
?>
<HEAD>
    <link rel="stylesheet" href="../estilos/orfeo.css">
</HEAD>
<BODY>
    <div    id="spiffycalendar" class="text"></div>
    <link   rel="stylesheet"    type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
    <script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>
    <script language="javascript">
        <!--
        var dateAvailable   = new ctlSpiffyCalendarBox("dateAvailable", "formboton", "fecha_busq","btnDate1","<?=$fecha_busq?>",scBTNMODE_CUSTOMBLUE);
        var dateAvailable2  = new ctlSpiffyCalendarBox("dateAvailable2", "formboton", "fecha_busq2","btnDate1","<?=$fecha_busq2?>",scBTNMODE_CUSTOMBLUE);
        //-->
    </script>
    <form name=formboton  method=POST  action='procesos.php?<?=session_name()."=".session_id().
                                "&krd=$krd&fecha_h=$fechah&fecha_busq=$fecha_busq&fecha_busq2=$fecha_busq2"?>'>

        <table width="99%" border="0" cellspacing="1" cellpadding="0" align="center" class="borde_tab">
            <TR>
                <TD colspan=2 height="30" valign="middle"   class='titulos2' align="center">
                    REPORTE DE TIEMPOS POR AREA PARA LA DEPENDENCIA <?=$dependencia?>
                </TD>
            </TR>
            <TR>
                <td class="titulos5" >
                    DEPENDENCIA
                </td>
	            <td class=listado5 >
                <?php
                    if(!$tdoc) $tdoc = 0;
                    if(!$codserie) $codserie = 0;
                    if(!$tsub) $tsub = 0;
                    $fechah=date("dmy") . " ". time("h_m_s");
                    $fecha_hoy = Date("Y-m-d");
                    $sqlFechaHoy=$db->conn->DBDate($fecha_hoy);
                    $check=1;
                    $fechaf=date("dmy") . "_" . time("hms");
                    $num_car = 4;
                    $nomb_varc = "s.sgd_srd_codigo";
                    $nomb_varde = "s.sgd_srd_descrip";
                    include (ORFEOPATH . "include/query/trd/queryCodiDetalle.php");
                    $querySerie = "select d.depe_nomb, d.depe_codi
                                 from dependencia d, dependencia dc
                                 where dC.depe_codi=$dependencia
                                 and dc.dep_central=d.dep_central
                                 order by d.depe_nomb";
                    $rsD=$db->conn->Execute($querySerie);
                    $comentarioDev = "Muestra las Series Docuementales";
                    include (ORFEOPATH . "include/tx/ComentarioTx.php");
                    print $rsD->GetMenu2("dependenciaC",
                                        $dependenciaC,
                                        "0:Todas las Dependencias de la Direccion",
                                        false,
                                        "",
                                        "onChange='submit()' class='select'");
                 ?>
                </TD>
            </TR>
            <TR>
                <td class="titulos5" >SERIE</td>
                <td class=listado5 >
                <?php
                  if($dependenciaC==0)
                  {
                     $iSqlDep = "select depC.depe_codi from dependencia dep, dependencia depC
                                  where
                                  depC.dep_central= dep.dep_central and  dep.depe_codi=$dependencia";
                  }else {$iSqlDep = $dependenciaC;}
                  
                    if(!$tdoc) $tdoc = 0;
                    if(!$codserie) $codserie = 0;
                    if(!$tsub) $tsub = 0;
                    $fechah=date("dmy") . " ". time("h_m_s");
                    $fecha_hoy = Date("Y-m-d");
                    $sqlFechaHoy=$db->conn->DBDate($fecha_hoy);
                    $check=1;
                    $fechaf=date("dmy") . "_" . time("hms");
                    $num_car = 4;
                    $nomb_varc = "s.sgd_srd_codigo";
                    $nomb_varde = "s.sgd_srd_descrip";
                    include (ORFEOPATH . "include/query/trd/queryCodiDetalle.php");
                    $querySerie = "select distinct ($sqlConcat) as detalle,
                                    s.sgd_srd_codigo
                                 from sgd_mrd_matrird m, sgd_srd_seriesrd s
                                 where m.depe_codi in ($iSqlDep)
                                    and s.sgd_srd_codigo = m.sgd_srd_codigo and
                                    m.sgd_mrd_esta = '1'
                                     and $sqlFechaHoy between s.sgd_srd_fechini and s.sgd_srd_fechfin
                                 order by s.sgd_srd_codigo";
                    $rsD=$db->conn->Execute($querySerie);
                    $comentarioDev = "Muestra las Series Docuementales";
                    include (ORFEOPATH . "include/tx/ComentarioTx.php");
                    print $rsD->GetMenu2("codserie",
                                        $codserie,
                                        "0:-- Seleccione --",
                                        false,
                                        "",
                                        "onChange='submit()' class='select'");
                 ?>
                </TD>
            </TR>
            <TR>
                <td class="titulos5" >SUBSERIE</td>
                <td class=listado5 >
                <?php
                    $nomb_varc = "su.sgd_sbrd_codigo";
                    $nomb_varde = "su.sgd_sbrd_descrip";
                    include (ORFEOPATH . "include/query/trd/queryCodiDetalle.php");
                    $querySub = "select distinct ($sqlConcat) as detalle, su.sgd_sbrd_codigo
                             from sgd_mrd_matrird m, sgd_sbrd_subserierd su
                             where m.depe_codi in ($iSqlDep) and
                                    m.sgd_srd_codigo = '$codserie' and
                                    su.sgd_srd_codigo = '$codserie' and
                                    su.sgd_sbrd_codigo = m.sgd_sbrd_codigo and
                                    m.sgd_mrd_esta = '1' and
                                    $sqlFechaHoy between su.sgd_sbrd_fechini and
                                    su.sgd_sbrd_fechfin
                             order by detalle";
                    $rsSub=$db->conn->Execute($querySub);
                    include (ORFEOPATH . "include/tx/ComentarioTx.php");
                    print $rsSub->GetMenu2("tsub",
                                            $tsub,
                                            "0:-- Seleccione --",
                                            false,
                                            "",
                                            "onChange='submit()' class='select'");
                ?>
                </TD>
            </TR>
            <TR>
                <TD width="125" height="21"  class='titulos5'> 
                    Fecha desde<br>
                </TD>
                <TD width="415" align="right" valign="top" class='listado5'>
                    <script language="javascript">
                        dateAvailable.date = "2003-08-05";
                        dateAvailable.writeControl();
                        dateAvailable.dateFormat="yyyy-MM-dd";
                    </script>
                </TD>
            </TR>
            <TR>
                <TD width="125" height="21"  class='titulos5'> Fecha Hasta<br>
                </TD>
                <TD width="415" align="right" valign="top" class='listado5'>
                    <script language="javascript">
                        dateAvailable2.date = "2003-08-05";
                        dateAvailable2.writeControl();
                        dateAvailable2.dateFormat="yyyy-MM-dd";
                    </script>
                </TD>
            </TR>  
            <TR>
                <td height="26" colspan="2" valign="top" class='titulos5'> 
                    <CENTER>
                        <INPUT TYPE=SUBMIT name=generar_informe Value=' Generar Informe ' class='botones_mediano'>
                    </CENTER>
                </TD>
            </TR>
        </TABLE>
        <br/>
<?php
if($generar_informe) {
    require_once(ORFEOPATH . "class_control/Dependencia.php");
    require_once(ORFEOPATH . "class_control/usuario.php");
    
    # objeto que contienela historia por radicado  
    class HistoriaRadicado {
      var $radicado;
      var $fecharadicado;
      var $entidad;
      var $asunto;
      var $tipodoc;
      var $dependencia;	
      var $traslados = array();
      var $asocia;
      var $fecha_asocia;
      var $asocia1;
      var $fecha_asocia1;
      function HistoriaRadicado($radicado) {
          $this->radicado = $radicado;	    
      }
    }
    
    class Traslados {        
      var $dependencia;	
      var $fecha;
      var $usuario;
      var $diasTx;
      var $depSigla;
      function Traslados($dependencia, $fecha, $usuario, $diasTx = null, $depSigla = null) {
        $this->dependencia = $dependencia;
        $this->fecha       = $fecha;
        $this->diasTx      = $diasTx;
        $this->depSigla    = $depSigla;
      }
    }	

    $objUs     = new Usuario($db);
    $objDep    = new Dependencia($db);
    $sqlFecha  = $db->conn->SQLDate("d-m-Y","a.HIST_FECH");
    $radFecha  = $db->conn->SQLDate("d-m-Y","r.RADI_FECH_RADI");
    $fecha_ini = $fecha_busq;
    $fecha_fin = $fecha_busq2;
    $fechaIni  = "'$fecha_busq'";
    $fechaFin  = "'$fecha_busq2'";
    $serie     = $codserie;
    $subSerie  = $tsub;
    $isql = "SELECT 
                  r.RADI_NUME_RADI
                  ,r.RADI_FECH_RADI
                  ,h.sgd_ttr_codigo
                  ,tpr.sgd_tpr_descrip
                  , h.hist_fech
                  , h.depe_codi
                  , d.dep_central
                  , h.depe_codi
                  , h.depe_codi_dest
                  , h.hist_fech 
                  , r.RADI_PATH
                  , dCActu.dep_central AS DEP_CELTRAL_ACTU
                  , (select 
                        top 1 anex.RADI_NUME_SALIDA 
                     from 
                        anexos anex 
                     where 
                        anex.anex_radi_nume=r.radi_nume_radi and 
                        right(anex.RADI_NUME_SALIDA , 1)='6') as RADI_NUME_SALIDA6 

                   , (  select 
                            top 1 hdig.HIST_FECH 
                        from 
                            anexos anex, 
                            HIST_EVENTOS hdig 
                        where 
                            anex.anex_radi_nume=r.radi_nume_radi and 
                            right(anex.RADI_NUME_SALIDA , 1)='6' and 
                            hdig.radi_nume_radi=anex.RADI_NUME_SALIDA and 
                            hdig.sgd_ttr_codigo IN(22, 42,61,42) 
                        order by hdig.hist_fech desc) as HIST_FECH_DIGITALIZA6
                  , (   select 
                            top 1 anex.RADI_NUME_SALIDA 
                        from 
                            anexos anex 
                        where 
                            anex.anex_radi_nume=r.radi_nume_radi and 
                            right(anex.RADI_NUME_SALIDA , 1)='1') as RADI_NUME_SALIDA1 
                  , (   select 
                            top 1 hdig.HIST_FECH 
                        from 
                            anexos anex, 
                            HIST_EVENTOS hdig 
                        where 
                            anex.anex_radi_nume=r.radi_nume_radi and 
                            right(anex.RADI_NUME_SALIDA , 1)='1' and 
                            hdig.radi_nume_radi=anex.RADI_NUME_SALIDA and 
                            hdig.sgd_ttr_codigo IN(22, 42,61,42) 
                        order by hdig.hist_fech desc) as HIST_FECH_DIGITALIZA1
                   , (   select 
                            count(1) 
                         from 
                            anexos anex 
                         where 
                            ANEX.ANEX_SALIDA=1 AND 
                            anex.anex_radi_nume=r.radi_nume_radi and 
                            right(anex.RADI_NUME_SALIDA , 1)='1' and 
                            anex.sgd_dir_tipo<10) AS RADIS_NUME_SALIDA1
                   , (  select 
                            count(1) 
                        from 
                            anexos anex 
                        where 
                            ANEX.ANEX_SALIDA=1 AND 
                            anex.anex_radi_nume=r.radi_nume_radi and 
                            right(anex.RADI_NUME_SALIDA , 1)='6' and 
                            anex.sgd_dir_tipo<10) AS RADIS_NUME_SALIDA6
                  , cast((h.hist_fech - r.RADI_FECH_RADI) as numeric(5,1)) as DIASDESDERAD
                  , (   SELECT 
                            top 1 SGD_DIR_NOMREMDES 
                        FROM 
                            SGD_DIR_DRECCIONES dir 
                        WHERE 
                            r.radi_nume_radi = dir.radi_nume_radi and sgd_dir_tipo=1) REMITE
                  , cast((( select 
                                top 1 hdig.HIST_FECH 
                            from 
                                anexos anex, 
                                HIST_EVENTOS hdig 
                            where 
                                anex.anex_radi_nume=r.radi_nume_radi and 
                                right(anex.RADI_NUME_SALIDA , 1)='6' and 
                                hdig.radi_nume_radi=anex.RADI_NUME_SALIDA and 
                                hdig.sgd_ttr_codigo IN(22, 42,61,42) 
                            order by hdig.hist_fech)  - r.RADI_FECH_RADI) as numeric(4,1)) as TOTALDIASRESPUESTA6
                  , cast((( select 
                                top 1 hdig.HIST_FECH 
                            from 
                                anexos anex, 
                                HIST_EVENTOS hdig 
                            where 
                                anex.anex_radi_nume=r.radi_nume_radi and 
                                right(anex.RADI_NUME_SALIDA , 1)='1' and 
                                hdig.radi_nume_radi=anex.RADI_NUME_SALIDA and 
                                hdig.sgd_ttr_codigo IN(22, 42,61,42) 
                            order by hdig.hist_fech)  - r.RADI_FECH_RADI) as numeric(4,1)) as TOTALDIASRESPUESTA1
                  , cast((getdate() - h.hist_fech) as numeric(5,1)) as DIASAHOY
                  , cast((getdate() - r.radi_fech_radi) as numeric(5,1)) as DIASAHOYRAD
                  , dC.depe_nomb as DEPDIRECCION
                  , r.RA_ASUN
                  , u.usua_nomb

             FROM 

                  radicado r, 
                  hist_eventos h, 
                  dependencia d, 
                  dependencia dC, 
                  dependencia dCActu,
                  sgd_tpr_tpdcumento tpr, 
                  usuario u ";
                  if($codserie!=0 or $tsub!=0){
                      $isql .= " ,sgd_rdf_retdocf rdf ";
                  }
                  $isql .= " 

              WHERE 

                  r.radi_nume_radi=h.radi_nume_radi
                  and tpr.sgd_tpr_codigo=r.tdoc_codi
                  and d.dep_central=dC.depe_codi
                  and r.radi_depe_actu=dCActu.depe_codi
                  and r.radi_usua_actu=u.usua_codi
                  and r.radi_depe_actu=u.depe_codi
                  and h.depe_codi=d.depe_codi
                  and r.radi_nume_radi like '%2'
                  and d.depe_codi not in(900)
                  and h.sgd_ttr_codigo in (2,9, 12, 13, 61, 62, 63)
                  and (r.RADI_FECH_RADI BETWEEN $fechaIni and $fechaFin)";
              
            if($tsub!=0){
                $subs  = " and SGD_SbRD_CODIGO=$tsub ";
            }

            if($codserie!=0 ){
                $isql .= "  and rdf.sgd_mrd_codigo in (select 
                                                        sgd_mrd_codigo 
                                                    from 
                                                        sgd_mrd_matrird 
                                                        where 
                                                        SGD_SRD_CODIGO=$codserie 
                                                        $subs 
                                                        and depe_codi in ($iSqlDep))
                            and rdf.radi_nume_radi = r.radi_nume_radi";
            }

            $isql .= " order by r.radi_nume_radi, h.hist_fech";

   include_once (ORFEOPATH . "include/tx/Expediente.php");
   $trdExp = new Expediente($db);
             
   $lastDate      = -1;
   $lastTran      = -1;
   $ultTran       = -1;
   $lastNum       = "";
   $listRadicados = array();
   $serie_grb     = -1;
   $subserie_grb  = -1;
   $rs = $db->conn->Execute($isql);
   $i = 0;
   $contenido = '<?xml version = "1.0" encoding="Windows-1252" standalone="yes"?>';
   $contenido .= " \n";
   $ultimaFecha = "";
   $diasDesdeRadAnt = 0;
   ?>
   
    <table width="95%" border="0" cellspacing="1" cellpadding="0" align="center" class="borde_tab">
        <tr class="titulos5">
            <th>Radicado</th>
            <th>Fecha de<br> Radicado</th>
            <TH>Remite</TH>
            <th>Asunto</th>
            <th>tipoDocumento</th>
            <th>Número Salida <br>Concepto (6)</th>
            <th>Fecha (6) </th>
            <th>Número Salida (1)</th>
            <th>Fecha (1)</th>    
            <th>Total Dias <br> Respuesta</th>
            <th>Usuario Actual</th>
            <th>Depe</th>
            <th>Tiem</th>
            <th>Depe 1</th>
            <th>Tiem 1</th>
            <th>Depe 2</th>
            <th>Tiem 2</th>
            <th>Depe 3</th>
            <th>Tiem 3</th>
            <th colspan=10></th>
        </tr>
   <?php
   $cRadicadoAnt          = $rs->fields["RADI_NUME_RADI"];
   $diasAnteriorHistorico = 0;
   $ik                    = 0;
   $i                     = 0;

   while(!$rs->EOF) {
      $i++;
      $numrad               = $rs->fields["RADI_NUME_RADI"];
      $cDiasAHoy            = $rs->fields["DIASAHOY"];  // Dias desde la fecha del Historico a Hoy dE CADA registro Historico
      $cDiasAHoyRad         = $rs->fields["DIASAHOYRAD"];  // Dias desde la fecha del Historico a Hoy desde la radicacion
      $radiPath             = $rs->fields["RADI_PATH"];
      $cDEPE_NOMB_ACTUAL    = $rs->fields["DEPDIRECCION"];
      $cRADI_DEPE_ACTU      = $rs->fields["RADI_DEPE_ACTU"];
      $cDEP_CELTRAL_ACTU    = $rs->fields["DEP_CELTRAL_ACTU"];
      $totalDias            = $rs->fields["TOTALDIAS"];
      $tipoDocumento        = $rs->fields["SGD_TPR_DESCRIP"];
      $fechaRadicado        = $rs->fields["RADI_FECH_RADI"];
      $diasDesdeRad         = $rs->fields["DIASDESDERAD"];
      $cRadicado            = $rs->fields["RADI_NUME_RADI"];
      $cDEPE_NOMB           = $rs->fields["DEPDIRECCION"];
      $cDEPE_CODI_DIR       = $rs->fields["DEP_CENTRAL"];
      $cRadiNumeSalida6     = $rs->fields["RADI_NUME_SALIDA6"];
      $cRadiNumeSalida1     = $rs->fields["RADI_NUME_SALIDA1"];
      $cHistFechDigitaliza6 = $rs->fields["HIST_FECH_DIGITALIZA6"];
      $cHistFechDigitaliza1 = $rs->fields["HIST_FECH_DIGITALIZA1"];
      $totalDiasRespuesta6  = $rs->fields["TOTALDIASRESPUESTA6"];
      $totalDiasRespuesta1  = $rs->fields["TOTALDIASRESPUESTA1"];
      $numeroDeRadicados1   = $rs->fields["RADIS_NUME_SALIDA1"];
      $numeroDeRadicados6   = $rs->fields["RADIS_NUME_SALIDA6"];
      $cHistDepeCodi        = $rs->fields["HIST_DEPE_CODI"];
      $cUsuaNomb            = $rs->fields["USUA_NOMB"];
      $cAsunto              = $rs->fields["RA_ASUN"];
      $cTipoDoc             = $rs->fields["SGD_TPR_DESCRIP"];
      $diasDesdeRad         = $rs->fields["DIASDESDERAD"];
      $remite               = $rs->fields["REMITE"];

      if($cDEPE_CODI_DIR==999) $diasAnteriorHistorico=0;

      $diasEventoHistorico  = $diasDesdeRad - $diasAnteriorHistoric;
      $diasAnteriores       += $diasEventoHistorico;
      $cDEPE_CODI_DEST      = $rs->fields["DEPE_CODI_DEST"];
	  $cHistFech            = $rs->fields["HIST_FECH"];

	  if(($cHistFechDigitaliza1>=$cHistFech or !$cHistFechDigitaliza1) && ($cHistFechDigitaliza6>=$cHistFech or !$cHistFechDigitaliza6)){
          if($i>=0){
                if($cDEPE_CODI_DIR!='99999'){
                    if((($diasTotalAntesDe999+$diasEventoHistorico)<=$totalDiasRespuesta6 or 
                        ($diasTotalAntesDe999+$diasEventoHistorico)<=$totalDiasRespuesta1)or 
                        (!$cHistFechDigitaliza1 and !$cHistFechDigitaliza6)){
                        $diasDep[$cDEPE_CODI_DIR]+= $diasEventoHistorico-$diasAnteriorHistorico;
                        $diasTotalAntesDe999 += $diasEventoHistorico-$diasAnteriorHistorico;
                    }
                }else{
                    $diasDep[$cDEPE_CODI_DIR]+= 0;
                }
                $dependenciaH[$cDEPE_CODI_DIR] = $cDEPE_NOMB;
          }
          $diasAnteriorHistorico = $diasDesdeRad;
	  }
      $cRadicadoAnt = $cRadicado;

      $rs->MoveNext();
      $cRadicado1 = $rs->fields["RADI_NUME_RADI"];
      if(($cRadicado1 != $cRadicadoAnt)){
        if($totalDiasRespuesta6){
          $totalDiasRespuesta = $totalDiasRespuesta6 ;
        }else if($totalDiasRespuesta1){
          $totalDiasRespuesta = $totalDiasRespuesta1 ;
        }
	  if(!$totalDiasRespuesta && $cRadiNumeSalida1)	{
	  	$totalDiasRespuesta = $totalDiasRespuesta1;
	  }
      $remite  = str_replace( '&', ' ',$remite);
      $cAsunto = str_replace( '&', ' ',$cAsunto);
      $verRadicado = "<a href='$ruta_raiz/verradicado.php?verrad=$cRadicado&".session_name()."=".session_id().
          "&krd=$krd' target='VERRAD$radi_nume_deri_".date("Ymdhi")."'>$fechaRadicado</a>";;
      $radiPath = "<a href='$ruta_raiz/bodega/$radiPath?verrad=$cRadicado&".session_name()."=".session_id().
          "&krd=$krd' target='VERRAD$radi_nume_deri_".date("Ymdhi")."'>$cRadicado</a>";;
      ?>
      <tr class="listado5">
      <td ><?=$radiPath?></td>
      <td ><?=$verRadicado?></td>
      <td ><?=strValido($remite)?></td>
      <td><?=strValido($cAsunto)?></td>
      <td><?=$tipoDocumento?></td>  
      <td ><?=$cRadiNumeSalida6?>
        <?php
       if($numeroDeRadicados6>=2) echo "($numeroDeRadicados6 Rads)";
       ?>
      </td>
      <?php
      if(trim($cRadiNumeSalida6)  and !trim($cHistFechDigitaliza6)) $cHistFechDigitaliza6 = "Sin Reg de Digitalizacion";
      ?>
      <td ><?=$cHistFechDigitaliza6?></td>
      <td ><?=$cRadiNumeSalida1?>
      <?php
      if(trim($cRadiNumeSalida1)  and !trim($cHistFechDigitaliza1)) $cHistFechDigitaliza1 = "Sin Reg de Digitalizacion";
      ?>
      <?php
       if($numeroDeRadicados1>=2) echo "($numeroDeRadicados1 Rads)";
       ?>
      </td>
      <td ><?=$cHistFechDigitaliza1?></td>
      <td ><?php
	  $imp ="";
      if(!$totalDiasRespuesta){
        echo "<FONT COLOR=RED>Sin Respuesta</FONT>";
		 $diferenciaAyerHoy = $cDiasAHoy-$cDiasAAyer;
         if(($diasDep[$cDEPE_CODI_DIR]+$cDiasAHoy)<=$cDiasAHoy and $diasDep[$cDEPE_CODI_DIR]<=$totalDiasRespuesta1) {$diasDep[$cDEPE_CODI_DIR]+= $cDiasAHoy;}
		 
         $dependenciaH[$cDEPE_CODI_DIR] = "<FONT COLOR=GREEN>".$cDEPE_NOMB." </FONT>";
         $cDiasAAyer = $cDiasAHoy;
      }else{
                       
        echo $totalDiasRespuesta;
      }
      ?>
      </td>
      <!-- <td ><?php
      if($totalDiasRespuesta==0){
           $diasTotalAntesDe999 +=$cDiasAHoy;
           echo "<FONT COLOR=RED>".$diasTotalAntesDe999."</FONT>" ;
      }elseif($cDEP_CELTRAL_ACTU!=999){
           $diasTotalAntesDe999 +=$cDiasAHoy;
           echo "<FONT COLOR=green>".$diasTotalAntesDe999."</FONT>" ;
           
      }else{
               echo $diasTotalAntesDe999 ;
      }?> --></td>
      
      <td><?=$cUsuaNomb?></td>
        <?php
        $iTrazo = 0;
        $contenidoTrazo = "";
        $iD=0;
	  	$diasT = 0;
		$iNumDep=0;
		$valueT=0;	
		$iTrazo++;
  		foreach ($dependenciaH as $keyF => $valueT){
		  if($keyF!='999') $diasT += $diasDep[$keyF];
		  if($keyF!='999') $valorUltimo = $diasDep[$keyF];
		  if($keyF!='999') $iNumDep++;
		  IF($numrad=='20106000000312')	{
			}
		}
		if($numrad==20106000000152)print_r($diasDep);

          foreach ($dependenciaH as $key => $value){
		   IF($key!=999){
            $value = str_replace("<FONT COLOR=GREEN>","",$value);
            $value = str_replace("</FONT>","",$value);
            
		
            $contenidoTrazo .= "  <Dependencia_$iTrazo>$value --> </Dependencia_$iTrazo>\n";
             
            $diasDepDir = $diasDep[$key];

			if(($totalDiasRespuesta1>>$diasT && $iNumDep==$iTrazo && $totalDiasRespuesta1 ) ) { 
			
				$diasDepDir=$totalDiasRespuesta1-$diasT ;
			}
			if(($totalDiasRespuesta1<<$diasT && $iNumDep==$iTrazo && $totalDiasRespuesta1 ) ) { 
			
				$diasDepDir=$totalDiasRespuesta1 - $diasT + $valorUltimo ;
			}			
			if($totalDiasRespuesta6>>$diasT && $iNumDep==$iTrazo && $totalDiasRespuesta6 ) {
				$diasDepDir=$totalDiasRespuesta6-$diasT;
			}
			// Caso qeu un radicado no tenga Respuesta ni 1 ni 6.  Y que los dias de la ultima dep. sea menor a la fecha de Hoy.
			if(!$totalDiasRespuesta1 && !$totalDiasRespuesta6  && ($iNumDep==$iTrazo && $cDEP_CELTRAL_ACTU!='999')){
				  if($cDiasAHoyRad>=($diasT+1)) { 
				  $diasDepDir = $cDiasAHoyRad-$diasT+ $valorUltimo; 
				  
				  }
			}
			if(!$totalDiasRespuesta1 && !$totalDiasRespuesta6  && ($iNumDep==$iTrazo && $cDEP_CELTRAL_ACTU=='999'))  $diasDepDir = $diasDep[$key];
			
            $contenidoTrazo .= "  <Tiempo_$iTrazo>x $diasDepDir   </Tiempo_$iTrazo>\n";
       		$cDiasAHoyAnterior = $cDiasAhoy;
            
          ?>
		    <td ><?=$value?></td>
            <td ><?=$diasDepDir ." $imp";?></td>
            <?php
			  $diasDepDir=0;
              if($value!='999') $iTrazo++;
			  }
            }
			unset($diasDepDir );
            unset($diasDep);
            // Aqui se verifica si el radicado Posee o no Derivados
            
            $iSqlDerivados = "SELECT u.USUA_LOGIN, rg.AREA
                                FROM SGD_RG_MULTIPLE rg, usuario u
                              WHERE rg.usuario=u.USUA_CODI
                                AND rg.area=u.DEPE_CODI AND RADI_NUME_RADI=$cRadicado";
            $rsDerivados = $db->conn->Execute($iSqlDerivados);
            $usuarioDerivados = "";
            if($rsDerivados){
               $i=1;
               while(!$rsDerivados->EOF){
                if($i==1)$usuarioDerivados = "Copias:<br>";              
                $usuarioDerivados.= $rsDerivados->fields["USUA_LOGIN"] . "(".$rsDerivados->fields["AREA"].") <br>";
                $i++;
                $rsDerivados->MoveNext();
               }
            }
            ?>
            <TD><?=$usuarioDerivados?></TD>
      </tr>
      <?php
	   $diasDep="";
	   $dependenciaH="";
       unset($diasDep);
       unset($dependenciaH);
       $totalDiasRespuesta = str_replace("<FONT COLOR=RED>","",$totalDiasRespuesta);
       $totalDiasRespuesta = str_replace("</FONT>","",$totalDiasRespuesta);
       $diasTotalAntesDe999 = str_replace("<FONT COLOR=RED>","",$diasTotalAntesDe999);
       $diasTotalAntesDe999 = str_replace("</FONT>","",$diasTotalAntesDe999);
       $contenidoR .= "<Registro>";
       $contenidoR .= " <radicado> $cRadicado</radicado>\n";
       $contenidoR .= " <FechaRadicado>$fechaRadicado</FechaRadicado>\n";
       $contenidoR .= " <Remite>$remite</Remite>\n";
       $contenidoR .= " <Asunto>$cAsunto</Asunto>\n";
       $contenidoR .= " <tipoDocumento>$tipoDocumento</tipoDocumento>\n";
       $contenidoR .= " <Concepto>$cRadiNumeSalida6</Concepto>\n";
       $contenidoR .= " <FechaDigitalizacion6>$cHistFechDigitaliza6</FechaDigitalizacion6>\n";
       $contenidoR .= " <RadicadoSalida>$cRadiNumeSalida6</RadicadoSalida>\n";
       $contenidoR .= " <FechaDigitalizacion1>$cHistFechDigitaliza1</FechaDigitalizacion1>\n";
       $contenidoR .= " <TotalDiasRespuesta>$totalDiasRespuesta</TotalDiasRespuesta>\n";
       $contenidoR .= " <UsuarioActual>$cUsuaNomb</UsuarioActual>\n";
       $contenidoR .= $contenidoTrazo . "\n";
       $contenidoR .= "</Registro>";
       $contenidoR .= "\n";
       $i=0;
       $diasAnteriorHistorico = 0;
       $diasTotalAntesDe999=0;
       $totalDiasRespuesta = 0;
       $totalDiasRespuesta6 = 0;
       $totalDiasRespuesta1 = 0;
       $resumen123 = "";
       $ik ++;
	   $cDiasAHoy=0; $diasDesdeRad=0; $diasAnteriorHistorico=0;  $diasTotalAntesDe999=0;
      }
       $cRadicadoAnt = $cRadicado;
    }
      
        $contenido.= " <ReporteRadicados>\n $contenidoR \n</ReporteRadicados>\n ";
     
        $hora=date("H")."_".date("i")."_".date("s");
        $ddate=date('d');
        $mdate=date('m');
        $adate=date('Y');
        $fecha=$adate."_".$mdate."_".$ddate;
        //guarda el path del archivo generado
        $ruta_raiz = "..";       
        $archivo = $ruta_raiz . "/bodega/masiva//tmp_0"."_$fecha"."_$hora.$salida" ."xls";
        $fp=fopen($archivo,"wb");
        fputs($fp,$contenido);
        fclose($fp);
?>
       
    <tr  align="center" >
        <td colspan="20" height="44" class="titulos5">
           <BR>Se ha generado el archivo <?=strtoupper($salida) ?> con el resultado de la consulta realizada.
           Para obtener el archivo guarde del destino del siguiente v&iacute;nculo<BR> 
           al archivo: <a href="<?=$archivo?>" target="_blank"><?=strtoupper($salida)?> GENERADO</a>.
        </td>
    </tr>
<?php
}     
?>
  </table>
<BODY>
