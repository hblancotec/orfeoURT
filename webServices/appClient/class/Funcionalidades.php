<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Funcionalidades
 *
 * @author omalagon
 */

include_once("../include/class/DatoContacto.php");
///include_once("../class_control/Param_admin.php"); 
class Funcionalidades {
    
    var $cod;
    var $descripcion;
    var $nombre;
    
     function __construct($db)
    {
            $this->cnn = $db;
            $this->cnn->SetFetchMode(ADODB_FETCH_ASSOC);
    }
        
    function Radicar($usuarioRadica,$usuarioDestino,$datosContacto,$asunto,$fechaOficio,$tipoRadicado,$nReferencia,$aplicodi){
        
        $conn=$this->cnn;
        $band=true;
        $ADODB_COUNTRECS = true;
        $tpRads=$this->cnn->Execute("SELECT SGD_TRAD_CODIGO as ID, SGD_TRAD_DESCR AS NOMBRE FROM SGD_TRAD_TIPORAD WHERE SGD_TRAD_CODIGO=$tipoRadicado");
        $ADODB_COUNTRECS = false;
        if($tpRads && $tpRads->RecordCount()==0){
                 $band=false;
                 $msg[] = 'No existe el tipo de radicado';
        }else{
            if(isset($usuarioRadica['documento']))
                $sql = "select * from usuario u join dependencia d on u.depe_codi=d.depe_codi where d.depe_codi=".$usuarioRadica['dependencia']." and usua_doc='".$usuarioRadica['documento']."'";
            else if(isset($usuarioRadica['login']))
                $sql = "select * from usuario u join dependencia d on u.depe_codi=d.depe_codi where d.depe_codi=".$usuarioRadica['dependencia']." and usua_login='".$usuarioRadica['login']."'";
            else if(isset($usuarioRadica['correo']))
                $sql = "select * from usuario u join dependencia d on u.depe_codi=d.depe_codi where d.depe_codi=".$usuarioRadica['dependencia']." and usua_email='".$usuarioRadica['correo']."'";

            $ADODB_COUNTRECS = true;
            $rs = $this->cnn->Execute($sql); 
            $ADODB_COUNTRECS = false;
            if ($rs && $rs->RecordCount()==0) 
            {
                $band=false;
                $msg[] = 'Usuario radicador inexistente.';
            }        
            else
            {
                if ($rs->fields['USUA_ESTA'] == 0)
                {
                    $band=false;
                    $msg[] = 'Usuario radicador inactivo.';
                }
                if ($rs->fields['USUA_PRAD_TP'.$tipoRadicado] == 0)
                {
                    $band=false;
                    $msg[] = 'Usuario radicador no tiene permisos para radicacion';
                }

                if (is_null($rs->fields['DEPE_RAD_TP'.$tipoRadicado]))
                {
                    $band=false;
                    $msg[] = 'Usuario radicador no tiene configurada la dependencia para radicacion ';
                }
                $depe_radi = $rs->fields['DEPE_CODI'];
                $usua_radi = $rs->fields['USUA_CODI'];
                $usua_docu = $rs->fields['USUA_DOC'];
                $usua_nivel = $rs->fields['CODI_NIVEL'];
                $depe_radi_csc = $rs->fields['DEPE_RAD_TP'.$tipoRadicado];
            }
        }
        if(isset($usuarioDestino['documento']))
            $sql = "select * from usuario u join dependencia d on u.depe_codi=d.depe_codi where d.depe_codi=".$usuarioDestino['dependencia']." and usua_doc='".$usuarioDestino['documento']."'";
        else if(isset($usuarioDestino['login']))
            $sql = "select * from usuario u join dependencia d on u.depe_codi=d.depe_codi where d.depe_codi=".$usuarioDestino['dependencia']." and usua_login='".$usuarioDestino['login']."'";
        else if(isset($usuarioDestino['correo']))
            $sql = "select * from usuario u join dependencia d on u.depe_codi=d.depe_codi where d.depe_codi=".$usuarioDestino['dependencia']." and usua_email='".$usuarioDestino['correo']."'";
        
        $ADODB_COUNTRECS = true;
        $rs = $this->cnn->Execute($sql); 
        $ADODB_COUNTRECS = false;
        if ($rs && $rs->RecordCount()==0) 
        {
            $band=false;
            $msg[] = 'Usuario destino inexistente.';
        }        
        else
        {
            if($rs->fields['USUA_CODI']!=1 && $tipoRadicado==2){
                $band=false;
                $msg[] = 'Usuario destino debe ser Jefe!';
            }
            if ($rs->fields['USUA_ESTA'] == 0)
            {
                $band=false;
                $msg[] = 'Usuario destino inactivo.';
            }
            $depe_codi_dest = $rs->fields['DEPE_CODI'];
            $usua_codi_dest = $rs->fields['USUA_CODI'];
            $usua_docu_dest = $rs->fields['USUA_DOC'];
            $usua_nivel_dest = $rs->fields['CODI_NIVEL'];
        }
        
        if(isset($datosContacto['ciudadano']['esCiudadano']) && $datosContacto['ciudadano']['esCiudadano']==1)
        {
            $obj=new DatoContacto($this->cnn);
            if(!$obj->esContinente($datosContacto['ciudadano']['internacionalizacion']['codContinente'])){
                $band=false;
                 $msg[] = 'No existe el continente asociado al contacto Ciudadano';
            }
            if(!$obj->esPais($datosContacto['ciudadano']['internacionalizacion']['codContinente'],
                                     $datosContacto['ciudadano']['internacionalizacion']['codPais'])){
                $band=false;
                 $msg[] = 'No existe el pais asociado al contacto Ciudadano';
            }
            if(!$obj->esDepto($datosContacto['ciudadano']['internacionalizacion']['codContinente'],
                                    $datosContacto['ciudadano']['internacionalizacion']['codPais'],
                                    $datosContacto['ciudadano']['internacionalizacion']['codDepartamento'])){
                $band=false;
                 $msg[] = 'No existe el departamento asociado al contacto Ciudadano ';
            }
            if(!$obj->esMnpio($datosContacto['ciudadano']['internacionalizacion']['codContinente'],
                              $datosContacto['ciudadano']['internacionalizacion']['codPais'],
                               $datosContacto['ciudadano']['internacionalizacion']['codDepartamento'],
                                $datosContacto['ciudadano']['internacionalizacion']['codMunicipio'])){
                $band=false;
                 $msg[] = 'No existe el municipio asociado al contacto Ciudadano';
            }
            unset($obj);
            $Tercero = 0;
            $datos_t = array();
            $datos_t['TDID_CODI'] =($datos_d['SGD_DIR_TDOC']= 0);
            $datos_t['SGD_CIU_NOMBRE'] = substr($datosContacto['ciudadano']['nombre'], 0, 130);
            $datos_t['SGD_CIU_DIRECCION'] =($datos_d['SGD_DIR_DIRECCION']= substr($datosContacto['ciudadano']['direccion'], 0, 145));
            $datos_t['ID_CONT'] =($datos_d['ID_CONT']= $datosContacto['ciudadano']['internacionalizacion']['codContinente']);
            $datos_t['ID_PAIS'] =($datos_d['ID_PAIS']= $datosContacto['ciudadano']['internacionalizacion']['codPais']);
            $datos_t['MUNI_CODI'] =($datos_d['MUNI_CODI']= $datosContacto['ciudadano']['internacionalizacion']['codMunicipio']);
            $datos_t['DPTO_CODI'] =($datos_d['DPTO_CODI'] = $datosContacto['ciudadano']['internacionalizacion']['codDepartamento']);
            $datos_t['SGD_CIU_APELL1'] = substr($datosContacto['ciudadano']['primerApellido'], 0, 45);
            $datos_t['SGD_CIU_APELL2'] = substr($datosContacto['ciudadano']['segundoApellido'], 0, 45);
            $datos_t['SGD_CIU_EMAIL'] = ($datos_d['SGD_DIR_MAIL']=substr($datosContacto['ciudadano']['mail'], 0, 50));
            $datos_t['SGD_CIU_CEDULA'] =($datos_d['SGD_DIR_DOC']= substr($datosContacto['ciudadano']['documento'], 0, 13));
            $datos_t['SGD_CIU_TELEFONO'] = ($datos_d['SGD_DIR_TELEFONO']=substr($datosContacto['ciudadano']['telefono'], 0, 13));
            $datos_d['SGD_DIR_NOMREMDES'] = ($datos_t['SGD_CIU_NOMBRE']." ".$datos_t['SGD_CIU_APELL1']." ".$datos_t['SGD_CIU_APELL2']);
        }
        else if(isset($datosContacto['entidad']['esEntidad']) && $datosContacto['entidad']['esEntidad']==1)
        {
            $Tercero = 1;
            $obj=new DatoContacto($this->cnn);
            $obj->setdatoEnt($datosContacto['entidad']['codEntidad']);
            $datoEnt=$obj->getdatoEnt();
            $datos_d=array();
            if(is_array($datoEnt) && count($datoEnt)>0){
                $datos_d['SGD_DIR_TDOC']= 4;
                $datos_d['SGD_DIR_DIRECCION']=$datoEnt[0]['DIRECCION'] ;
                $datos_d['ID_CONT']=$datoEnt[0]['ID_CONT'] ;
                $datos_d['ID_PAIS']=$datoEnt[0]['ID_PAIS'] ;
                $datos_d['MUNI_CODI']= $datoEnt[0]['MUNI_CODI'];
                $datos_d['DPTO_CODI'] = $datoEnt[0]['DPTO_CODI'];
                $datos_d['SGD_DIR_MAIL']=$datoEnt[0]['EMAIL'];
                $datos_d['SGD_ESP_CODI']=$datoEnt[0]['IDENTIFICADOR_EMPRESA'] ;
                $datos_d['SGD_DIR_TELEFONO']=$datoEnt[0]['TELEFONO'];
                $datos_d['SGD_DIR_NOMREMDES'] = $datoEnt[0]['NOMBRE'];
                $datos_d['SGD_DIR_DOC']=$datoEnt[0]['ID'];
            }
            else{
                $band=false;
                $msg[] = 'No existe entidad relacionada con el codigo enviado!';
            }
            unset($obj);
        }
        else if(isset($datosContacto['empresa']['esEmpresa']) && $datosContacto['empresa']['esEmpresa']==1)
        {    
            $obj=new DatoContacto($this->cnn);
            if(!$obj->esContinente($datosContacto['empresa']['internacionalizacion']['codContinente'])){
                $band=false;
                 $msg[] = 'No existe el continente asociado al contacto empresa';
            }
            if(!$obj->esPais($datosContacto['empresa']['internacionalizacion']['codContinente'],
                                     $datosContacto['empresa']['internacionalizacion']['codPais'])){
                $band=false;
                 $msg[] = 'No existe el pais asociado al contacto empresa';
            }
            if(!$obj->esDepto($datosContacto['empresa']['internacionalizacion']['codContinente'],
                                    $datosContacto['empresa']['internacionalizacion']['codPais'],
                                    $datosContacto['empresa']['internacionalizacion']['codDepartamento'])){
                $band=false;
                 $msg[] = 'No existe el departamento asociado al contacto empresa ';
            }
            if(!$obj->esMnpio($datosContacto['empresa']['internacionalizacion']['codContinente'],
                              $datosContacto['empresa']['internacionalizacion']['codPais'],
                               $datosContacto['empresa']['internacionalizacion']['codDepartamento'],
                                $datosContacto['empresa']['internacionalizacion']['codMunicipio'])){
                $band=false;
                 $msg[] = 'No existe el municipio asociado al contacto empresa';
            }
            unset($obj);
            $Tercero = 2;
            $datos_t = array();
            $datos_t['TDID_CODI'] =$datos_d['SGD_DIR_TDOC']= 4;
            $datos_t['SGD_OEM_OEMPRESA'] = substr($datosContacto['empresa']['nombre'], 0, 130);
            $datos_t['SGD_OEM_DIRECCION'] =$datos_d['SGD_DIR_DIRECCION']= substr($datosContacto['empresa']['direccion'], 0, 145);
            $datos_t['ID_CONT'] =$datos_d['ID_CONT']= $datosContacto['empresa']['internacionalizacion']['codContinente'];
            $datos_t['ID_PAIS'] =$datos_d['ID_PAIS']= $datosContacto['empresa']['internacionalizacion']['codPais'];
            $datos_t['MUNI_CODI'] =$datos_d['MUNI_CODI']= $datosContacto['empresa']['internacionalizacion']['codMunicipio'];
            $datos_t['DPTO_CODI'] =$datos_d['DPTO_CODI'] = $datosContacto['empresa']['internacionalizacion']['codDepartamento'];
            $datos_t['SGD_OEM_SIGLA'] = substr($datosContacto['empresa']['sigla'], 0, 45);
            $datos_t['SGD_OEM_REP_LEGAL'] = substr($datosContacto['empresa']['representanteLegal'], 0, 45);
            $datos_t['SGD_OEM_NIT'] =$datos_d['SGD_DIR_DOC']= substr($datosContacto['empresa']['nit'], 0, 13);
            $datos_d['SGD_DIR_NOMREMDES']=$datosContacto['empresa']['nombre'];
            $datos_t['SGD_OEM_TELEFONO']=$datos_d['SGD_DIR_TELEFONO']=$datosContacto['empresa']['telefono'];
            $datos_t['EMAIL']=$datos_d['SGD_DIR_MAIL']=$datosContacto['empresa']['mail'];
        }
        else if(isset($datosContacto['funcionario']['esFuncionario']) && $datosContacto['funcionario']['esFuncionario']==1)
        {
            $Tercero = 3;
            $Tercero = 1;
            $obj=new DatoContacto($this->cnn);
            $obj->setdatoFun($datosContacto['funcionario']['dependencia'],
                            $datosContacto['funcionario']['documento'],
                            $datosContacto['funcionario']['login'],
                            $datosContacto['funcionario']['correo']);
            $datoFun=$obj->getdatoFun();
            if(is_array($datoFun) && count($datoFun)>0){
                $datos_d['SGD_DIR_TDOC']=0 ;
                $datos_d['SGD_DIR_DIRECCION']= $datoFun[0]['DIRECCION'];
                $datos_d['ID_CONT']= $datoFun[0]['ID_CONT'];
                $datos_d['ID_PAIS']= $datoFun[0]['ID_PAIS'];
                $datos_d['MUNI_CODI']= $datoFun[0]['MUNI_CODI'];
                $datos_d['DPTO_CODI'] = $datoFun[0]['DPTO_CODI'];
                $datos_d['SGD_DIR_MAIL']=$datoFun[0]['EMAIL'];
                $datos_d['SGD_DIR_DOC']= $datos_d['SGD_DOC_FUN']=$datoFun[0]['ID'];
                $datos_d['SGD_DIR_TELEFONO']=$datoFun[0]['TELEFONO'];
                $datos_d['SGD_DIR_NOMREMDES'] = $datoFun[0]['NOMBRE'];
            }
            else{
                $band=false;
                $msg[] = 'No existe el funcionario en datos contacto';
            }
        }
        else{
            $band=false;
            $msg[] = 'No vienen datos de Cotacto!';
        }
        if ($band===true)
        {
            $this->cnn->BeginTrans();
            //1. Generamos radicado.
            //a. Crear registro en tabla radicado.
            $datos_r['CARP_PER'] = 0;
            $datos_r['CARP_CODI'] = $tipoRadicado==2?0:$tipoRadicado;
            $datos_r['TDOC_CODI'] = 0;
            $datos_r['RA_ASUN'] = htmlspecialchars(stripcslashes(substr($asunto, 0, 320)));
            $datos_r['RADI_PATH'] = 'null';
            $datos_r['TRTE_CODI'] = $Tercero;
            $datos_r['MREC_CODI'] = 3;   //Medio de recepcion
            $datos_r['EESP_CODI'] = 'null';   //Identificacion 3a pestana?a
            $datos_r['RADI_FECH_OFIC'] = $this->cnn->DBDate($fechaOficio);
            $datos_r['RADI_USUA_ACTU'] = $tipoRadicado==2?1:$usua_codi_dest;;
            $datos_r['RADI_DEPE_ACTU'] = $depe_codi_dest;
            $datos_r['RADI_FECH_RADI'] = $this->cnn->sysTimeStamp;
            $datos_r['RADI_USUA_RADI'] = $usua_radi;
            $datos_r['RADI_DEPE_RADI'] = $depe_codi_dest;
            $datos_r['CODI_NIVEL'] = $usua_nivel;
            $datos_r['FLAG_NIVEL'] = 1;
            $datos_r['RADI_LEIDO'] = 0;
            $datos_r['SGD_APLI_CODIGO'] = $aplicodi;
            $datos_r['SGD_APLI_REFERENCIA'] = htmlspecialchars(stripcslashes(substr($nReferencia, 0, 16)));
            /// Iniciamos Radicacion ///
            $secNew = $this->cnn->GenID("SECR_TP".$tipoRadicado."_".$depe_radi_csc);
            if ($secNew == FALSE)
            {  
                $band=false;
                $msg[] = "Error al consultar secuencia. <!-- SECR_TP".$tipoRadicado."_".$depe_radi_csc."-->";
            }
            else
            {   
                $band=true;
                $newRadicado = date("Y") . str_pad($depe_radi,3,"0", STR_PAD_LEFT) . str_pad($secNew, 6, "0", STR_PAD_LEFT) . $tipoRadicado;
                $datos_r['RADI_NUME_RADI'] = $newRadicado;
                $tabla = 'RADICADO';
                $sql_r = " INSERT INTO RADICADO ".
                         " (CARP_PER ,CARP_CODI , TDOC_CODI,RA_ASUN,TRTE_CODI,MREC_CODI,RADI_FECH_OFIC,RADI_USUA_ACTU , RADI_DEPE_ACTU , RADI_FECH_RADI ,RADI_USUA_RADI,RADI_DEPE_RADI, CODI_NIVEL,FLAG_NIVEL,RADI_LEIDO, SGD_APLI_CODIGO, SGD_APLI_REFERENCIA  ,RADI_NUME_RADI) ".
                         " VALUES ".
                         " (".$datos_r['CARP_PER'].", ".$datos_r['CARP_CODI'].", ".$datos_r['TDOC_CODI'].", '".htmlspecialchars(stripcslashes(substr($datos_r['RA_ASUN'], 0, 320)))."', ".$datos_r['TRTE_CODI']." , 3       , ".$datos_r['RADI_FECH_OFIC']."   , ".$datos_r['RADI_USUA_ACTU']." , ".$datos_r['RADI_DEPE_ACTU'].", ".$conn->sysTimeStamp.", ".$datos_r['RADI_USUA_RADI']." ,".$datos_r['RADI_DEPE_RADI'].",".$datos_r['CODI_NIVEL'].", ".$datos_r['FLAG_NIVEL']."      , ".$datos_r['RADI_LEIDO']."   , ".$datos_r['SGD_APLI_CODIGO']."  , '".$datos_r['SGD_APLI_REFERENCIA']."',                     $newRadicado)";
                $ok_r = $this->cnn->Execute($sql_r);
                if ($ok_r === FALSE)
                {   $band=false;
                    $msg[] = "Error en la insercion del Radicado! ".$sql_r;
                    $this->cnn->RollbackTrans();
                }
                else
                {
                    //b. Crear registro en tabla historico.
                    $ok_h=$this->registrarHistorico($newRadicado, $depe_radi, $datos_r['RADI_USUA_RADI'], $usua_docu, $datos_r['RADI_DEPE_ACTU'], $datos_r['RADI_USUA_ACTU'],$usua_docu_dest, 'Radicacion Via Web Service', 2);
                    if ($ok_h === false)
                    {   $band=false;
                        $msg[] = "Error en la insercion del Historico! ".$sql_h;
                        $this->cnn->RollbackTrans();
                    }
                    else
                    {//c. Crear registro en tabla de tercero.
                        if($Tercero !=1 && $Tercero!=3)
                        {
                            switch ($Tercero)
                            {
                                case 0:
                                {
                                    $secuencia = $this->cnn->GenID("SEC_CIU_CIUDADANO");
                                    if ($secuencia === false)
                                    {   $this->cnn->RollbackTrans();
                                        $band=false;
                                        $msg[] = "Error al generar la secuencia de ciudadanos!";
                                    }
                                    $datos_t['SGD_CIU_CODIGO'] =$datos_d['SGD_CIU_CODIGO']= $secuencia;
                                    $sgd_ciu_codigo = $secuencia;
                                    $tabla = 'SGD_CIU_CIUDADANO';
                                }break;
                                case 2:
                                {
                                    $secuencia = $this->cnn->GenID("SEC_OEM_OEMPRESAS");
                                    if ($secuencia === false)
                                    {   $this->cnn->RollbackTrans();
                                        $band=false;
                                        $msg[] = "Error al generar la secuencia de empresas!";
                                    }
                                    $datos_t['SGD_OEM_CODIGO'] =$datos_d['SGD_OEM_CODIGO']= $secuencia;
                                    $sgd_oem_codigo = $secuencia;
                                    $tabla = 'SGD_OEM_OEMPRESAS';
                                }break;
                                default:
                                    break;
                            }
                            $sql_t = $this->cnn->GetInsertSQL($tabla, $datos_t, $magicq=false, $force_type=false);
                            $ok_t = $this->cnn->Execute($sql_t);
                        }
                        else
                            $ok_t=true;
                        if ($ok_t === false)
                        {   
                            $band=false;
                            $msg[] = "Error en la insercion del contacto! ".$sql_t;
                        }
                        else
                        {   //d. Crear registro en tabla sgd_dir_drecciones.
                            $nextval = $this->cnn->GenID("sec_dir_direcciones");
                            if ($nextval === false)
                            {   
                                $this->cnn->RollbackTrans();
                                $band=false;
                                $msg[] = "Error en al obtener la secuencia:  sec_dir_direcciones";
                            }
                            //$ADODB_FORCE_TYPE = ADODB_FORCE_NULL;
                            $datos_d['RADI_NUME_RADI'] = $newRadicado;
                            $datos_d['SGD_SEC_CODIGO'] = 0;
                            $datos_d['SGD_DIR_TIPO'] = 1;
                            $datos_d['SGD_DIR_CODIGO'] = $nextval;
                            $tabla = "SGD_DIR_DRECCIONES";
                            $sql_d = $this->cnn->GetInsertSQL($tabla, $datos_d, false, false);
                            $ok_d = $this->cnn->Execute($sql_d);
                            if ($ok_d === false)
                            {   $this->cnn->RollbackTrans();
                                $band=false;
                                $msg[] = "Error en la insercion de la relación del contacto ! ".$sql_d;
                            }
                        }
                    }
                }
            }
            if ($band===true)
            {
                $this->cnn->CommitTrans();
                $msg[]="NoRadicado";
                $msg[]="$newRadicado ..";
                $retorno['band']=TRUE;
                $retorno['msg']=$msg;
                return $retorno;
            }
            else{
                $this->cnn->RollbackTrans();
                $retorno['band']=false;
                $retorno['msg']=$msg;
                return $retorno;
            }
                
        }
        else{
            $this->cnn->RollbackTrans();
            $retorno['band']=false;
            $retorno['msg']=$msg;
            return $retorno;
        }
    }
    
    function  Reasignar($refRadicado,$usuarioReasigna,$usuarioDestino,$comentario,$usuaNivel,$aplicodi){
        
        $conn=$this->cnn;
        $band=true;
        //1. Validar Existe Radicado y obtener datos
        if(isset($refRadicado['nRadicado']))
            $sql="select * from radicado where radi_nume_radi=".$refRadicado['nRadicado'];
        else if(isset($refRadicado['nReferencia']))
            $sql="select * from radicado where SGD_APLI_REFERENCIA='".$refRadicado['nReferencia']."' and SGD_APLI_CODIGO=$aplicodi";
        
        $ADODB_COUNTRECS = true;
        $rsRad = $this->cnn->Execute($sql); 
        $ADODB_COUNTRECS = false;
        if ($rsRad && $rsRad->RecordCount()==0) 
        {
            $band=false;
            $msg[] = 'Referencia a radicado Invalida; Radicado no existe!';
        }
        else{
            //2. Validamos Usuario Reasigna
            if(isset($usuarioReasigna['documento']))
                $sql = "select * from usuario u join dependencia d on u.depe_codi=d.depe_codi where d.depe_codi=".$usuarioReasigna['dependencia']." and usua_doc='".$usuarioReasigna['documento']."'";
            else if(isset($usuarioReasigna['login']))
                $sql = "select * from usuario u join dependencia d on u.depe_codi=d.depe_codi where d.depe_codi=".$usuarioReasigna['dependencia']." and usua_login='".$usuarioReasigna['login']."'";
            else if(isset($usuarioReasigna['correo']))
                $sql = "select * from usuario u join dependencia d on u.depe_codi=d.depe_codi where d.depe_codi=".$usuarioReasigna['dependencia']." and usua_email='".$usuarioReasigna['correo']."'";

            $ADODB_COUNTRECS = true;
            $rs = $this->cnn->Execute($sql); 
            $ADODB_COUNTRECS = false;
            if ($rs && $rs->RecordCount()==0) 
            {
                $band=false;
                $msg[] = 'Usuario Reasigna inexistente.';
            }        
            else
            {
                if ($rs->fields['USUA_ESTA'] == 0)
                {
                    $band=false;
                    $msg[] = 'Usuario Reasigna inactivo.';
                }else{
                    if(isset($refRadicado['nReferencia']))$msgValida=" asociado al No de Referencia ".$refRadicado['nReferencia']." del aplicativo No. $aplicodi";
                    while(!$rsRad->EOF)
                    {//Por COnsultar: Debemos validar si el usuario tiene actualmente el radicado! o si tiene permisos para reasinar
                        if ($rs->fields['USUA_CODI']!=$rsRad->fields['RADI_USUA_ACTU'] || $rs->fields['DEPE_CODI']!=$rsRad->fields['RADI_DEPE_ACTU'])
                        {
                            $band=false;
                            $msg[] = 'Usuario que reasigna no tiene actualmente el radicado!='.$rsRad->fields['RADI_NUME_RADI'].$msgValida;
                        }
                        else{
                            $bandAux=true;
                            $rads[]=$rsRad->fields['RADI_NUME_RADI'];
                        }
                        $rsRad->MoveNext();
                    }
                    $usua_login_reasigna = $rs->fields['USUA_LOGIN'];
                    $depe_reasigna = $rs->fields['DEPE_CODI'];
                    $usua_reasigna = $rs->fields['USUA_CODI'];
                    $usua_docu_reasigna = $rs->fields['USUA_DOC'];
                    $usua_nivel_reasigna = $rs->fields['CODI_NIVEL'];
                }
            }
            //3. Validamos el usuario Destino!
            if(isset($usuarioDestino['documento']))
                $sql = "select * from usuario u join dependencia d on u.depe_codi=d.depe_codi where d.depe_codi=".$usuarioDestino['dependencia']." and usua_doc='".$usuarioDestino['documento']."'";
            else if(isset($usuarioDestino['login']))
                $sql = "select * from usuario u join dependencia d on u.depe_codi=d.depe_codi where d.depe_codi=".$usuarioDestino['dependencia']." and usua_login='".$usuarioDestino['login']."'";
            else if(isset($usuarioDestino['correo']))
                $sql = "select * from usuario u join dependencia d on u.depe_codi=d.depe_codi where d.depe_codi=".$usuarioDestino['dependencia']." and usua_email='".$usuarioDestino['correo']."'";

            $ADODB_COUNTRECS = true;
            $rs = $this->cnn->Execute($sql); 
            $ADODB_COUNTRECS = false;
            if ($rs && $rs->RecordCount()==0) 
            {
                $band=false;
                $msg[] = 'Usuario Destino inexistente';
            }        
            else
            {
                if ($rs->fields['USUA_ESTA'] == 0)
                {
                    $band=false;
                    $msg[] = 'Usuario Destino inactivo!';
                }
                $depe_dest = $rs->fields['DEPE_CODI'];
                $usua_dest = $rs->fields['USUA_CODI'];
                $usua_docu_dest = $rs->fields['USUA_DOC'];
                $usua_nivel_dest = $rs->fields['CODI_NIVEL'];
            }
        }
        if($band===true){
            $this->cnn->BeginTrans();
            if($usuaNivel==1){
                $setNivel = ",CODI_NIVEL=$usua_nivel_dest";
            }
            //4. Se actualiza registro de radicado
            $isql = "update radicado	set
				  RADI_USU_ANTE='$usua_login_reasigna'
				  ,RADI_DEPE_ACTU=$depe_dest
				  ,RADI_USUA_ACTU=$usua_dest
				  ,CARP_CODI=0
				  ,CARP_PER=0
				  ,RADI_LEIDO=0
				  , radi_fech_agend=null
				  ,radi_agend=null
				  $setNivel
			 where radi_depe_actu=$depe_reasigna
			 	   AND radi_usua_actu=$usua_reasigna
				   AND RADI_NUME_RADI=".$rsRad->fields['RADI_NUME_RADI'];
            
            $rs = $this->cnn->Execute($isql);//Debo saber cuantos actualizó!!!
            //if($this->cnn->Affected_Rows())
            $band=false;
            $msg[] = 'Lineas afectadas';  $this->cnn->Affected_Rows();
            
            $this->cnn->RollbackTrans();
            $retorno['band']=false;
            $retorno['msg']=$msg;
            return $retorno;
        }
        else{
            $this->cnn->RollbackTrans();
            $retorno['band']=false;
            $retorno['msg']=$msg;
            return $retorno;
        }
        
    }
    
    function IncluirRadicadoEnExpediente(){
        
    }
    
    function CrearExpediente(){
        
    }
    
    function AnexarDocumento(){
        
    }
    
    function AsociarImagen(){
        
    }
    
    function TipificarRadicado(){
        
    }
    
    function ModificarRadicado(){
        
    }
    
    function ObtenerExpedientesDeRadicado(){
        
    }
    
    function ObtenerDatosExpediente(){
        
    }
    
    function ObtenerHistorialRadicado(){
        
    }
    
    function EnviarRadicado($refRadicado,$usuarioEnvia,$nCopia,$pesoGramos,$empresaEnvio,$observacion){
        
        $conn=$this->cnn;
        $band=true;
        //1. Validar Existe Radicado y obtener datos
        if(isset($refRadicado['nRadicado']))
            $sql="select * from radicado where radi_nume_radi=".$refRadicado['nRadicado'];
        else if(isset($refRadicado['nReferencia']))
            $sql="select * from radicado where SGD_APLI_REFERENCIA='".$refRadicado['nReferencia']."' and SGD_APLI_CODIGO=$aplicodi";
        
        $ADODB_COUNTRECS = true;
        $rsRad = $this->cnn->Execute($sql); 
        $ADODB_COUNTRECS = false;
        if ($rsRad && $rsRad->RecordCount()==0) 
        {
            $band=false;
            $msg[] = 'Referencia a radicado Invalida; Radicado no existe!';
        }
        
        if(isset($usuarioEnvia['documento']))
            $sql = "select * from usuario u join dependencia d on u.depe_codi=d.depe_codi where d.depe_codi=".$usuarioEnvia['dependencia']." and usua_doc='".$usuarioEnvia['documento']."'";
        else if(isset($usuarioEnvia['login']))
            $sql = "select * from usuario u join dependencia d on u.depe_codi=d.depe_codi where d.depe_codi=".$usuarioEnvia['dependencia']." and usua_login='".$usuarioEnvia['login']."'";
        else if(isset($usuarioEnvia['correo']))
            $sql = "select * from usuario u join dependencia d on u.depe_codi=d.depe_codi where d.depe_codi=".$usuarioEnvia['dependencia']." and usua_email='".$usuarioEnvia['correo']."'";
        
        $ADODB_COUNTRECS = true;
        $rs = $this->cnn->Execute($sql); 
        $ADODB_COUNTRECS = false;
        if ($rs && $rs->RecordCount()==0) 
        {
            $band=false;
            $msg[] = 'Usuario que envia inexistente.';
        }        
        else
        {
            if ($rs->fields['USUA_ESTA'] != 1)
            {
                $band=false;
                $msg[] = 'Usuario que envia inactivo.';
            }
            $depe_codi_dest = $rs->fields['DEPE_CODI'];
            $usua_codi_dest = $rs->fields['USUA_CODI'];
            $usua_docu_dest = $rs->fields['USUA_DOC'];
        }
        
        if($nCopia<=0){
            
            $band=false;
            $msg[] = 'El No de Copia debe ser valido!';
        }
        
        if(!$empresaEnvio){
            
            $band=false;
            $msg[] = 'La empresa de envio deber ser valida!';
        }
        else{
            
            $sql="select * from sgd_fenv_frmenvio where sgd_fenv_codigo=$empresaEnvio";
            $ADODB_COUNTRECS = true;
            $rs = $this->cnn->Execute($sql); 
            $ADODB_COUNTRECS = false;
            if ($rs && $rs->RecordCount()==0) 
            {
                $band=false;
                $msg[] = 'La empresa de envio no existe!';
            }        
            else
            {
                $empresaEnvioCod = $rs->fields['SGD_FENV_CODIGO'];
                $empresaEnvioDescrip = $rs->fields['SGD_FENV_DESCRIP'];
            }
        }
        
        if($band){
        
            $obj=new DatoContacto($this->cnn);
            
            $vecDatosDir=$obj->obtieneDatosDir(false, $rsRad->fields['RADI_NUME_RADI'], $nCopia);
            
            if(is_array($vecDatosDir)){
            
                $isql = "update RADICADO set SGD_EANU_CODIGO=9 where RADI_NUME_RADI =".$rsRad->fields['RADI_NUME_RADI'];
                $this->cnn->Execute($isql);

                $sql = "Select max(sgd_renv_codigo)+1 as MAXVAL from sgd_renv_regenvio ";
                $rs=$this->cnn->Execute($sql);
                if($rs && !$rs->EOF){

                    if($rs->fields['MAXVAL']<=0){
                        $msg[] = 'No se puede obtener el consecutivo del registro de envio, comuniquese con el administrador de Orfeo!';
                        $retorno['band']=false;
                        $retorno['msg']=$msg;
                        return $retorno;
                    }
                    else{

                       $nextval=$rs->fields['MAXVAL'];
                    }
                }
                else{


                    $msg[] = 'No se puede obtener el consecutivo del registro de envio, comuniquese con el administrador de Orfeo!';
                    $retorno['band']=false;
                    $retorno['msg']=$msg;
                    return $retorno;
                }

                $isql = "INSERT INTO SGD_RENV_REGENVIO(USUA_DOC,
                                                                    SGD_RENV_CODIGO,
                                                                    SGD_FENV_CODIGO,
                                                                    SGD_RENV_FECH,
                                                                    RADI_NUME_SAL,
                                                                    SGD_RENV_DESTINO,
                                                                    SGD_RENV_TELEFONO,
                                                                    SGD_RENV_MAIL,
                                                                    SGD_RENV_PESO,
                                                                    SGD_RENV_VALOR,
                                                                    SGD_RENV_CERTIFICADO,
                                                                    SGD_RENV_ESTADO,
                                                                    SGD_RENV_NOMBRE,
                                                                    SGD_DIR_CODIGO,
                                                                    DEPE_CODI,
                                                                    SGD_DIR_TIPO,
                                                                    RADI_NUME_GRUPO,
                                                                    SGD_RENV_PLANILLA,
                                                                    SGD_RENV_DIR,
                                                                    SGD_RENV_DEPTO,
                                                                    SGD_RENV_MPIO,
                                                                    SGD_RENV_PAIS,
                                                                    SGD_RENV_OBSERVA,
                                                                    SGD_RENV_CANTIDAD,
                                                                    SGD_RENV_NUMGUIA,
                                                                    SGD_RENV_CODPOSTAL)
                                                            VALUES('$usua_docu_dest',
                                                                    '$nextval',
                                                                    '$empresaEnvio',
                                                                    " .$this->cnn->OffsetDate(0,$this->cnn->sysTimeStamp).",
                                                                    '".$rsRad->fields['RADI_NUME_RADI']."',
                                                                    '".$vecDatosDir[0]['MUNICIPIO']."',
                                                                    '".$vecDatosDir[0]['NUMERO_FAX']."',
                                                                    '".$vecDatosDir[0]['MAIL']."',
                                                                    '$pesoGramos',
                                                                    0,
                                                                    0,
                                                                    1,
                                                                    '".$vecDatosDir[0]['NOMBRE']." ".$vecDatosDir[0]['APELLIDO']."',
                                                                    '".$vecDatosDir[0]['SGD_DIR_CODIGO']."',
                                                                    '$depe_codi_dest',
                                                                    '$nCopia',
                                                                    '".$rsRad->fields['RADI_NUME_RADI']."',
                                                                    '',
                                                                    '".$vecDatosDir[0]['NOMBRE']."',
                                                                    '".$vecDatosDir[0]['DEPARTAMENTO']."',
                                                                    '".$vecDatosDir[0]['MUNICIPIO']."',
                                                                    '".$vecDatosDir[0]['PAIS']."',
                                                                    '".$observacion."',
                                                                    1,
                                                                    '',
                                                                    '".$vecDatosDir[0]['CODIGO_POSTAL']."')";
                 $rsInsert = $this->cnn->Execute($isql);
                 
            }
            else{
                
                $msg[] = 'No se puede obtener los datos de Direcciones, comuniquese con el administrador de Orfeo.!';
                $retorno['band']=false;
                $retorno['msg']=$msg;
                return $retorno;
            }
        }
        else{
            //$this->cnn->RollbackTrans();
            $retorno['band']=false;
            $retorno['msg']=$msg;
            return $retorno;
        }
        
    }
    
    function ArchivarRadicado(){
        
    }
    
    function registrarHistorico($radicado,$depeOrigen,$usCodOrigen,$usua_docu_origen,$depeDestino,$usCodDestino,$usua_docu_dest,$observacion,$tipoTx){
        
                //$this->cnn->BeginTrans();
		$datos_h["RADI_NUME_RADI"] = $radicado;
                $datos_h["DEPE_CODI"] = $depeOrigen;
                $datos_h["USUA_CODI"] = $usCodOrigen;
                $datos_h["USUA_CODI_DEST"] = $depeDestino;
                $datos_h["DEPE_CODI_DEST"] = $usCodDestino;
                $datos_h["USUA_DOC"] = $usua_docu_origen;
                $datos_h["HIST_DOC_DEST"] = $usua_docu_dest;
                $datos_h["SGD_TTR_CODIGO"] = $tipoTx;
                $datos_h["HIST_OBSE"] = $observacion;
                $datos_h["HIST_FECH"] = $this->cnn->sysTimeStamp;
                $tabla = 'HIST_EVENTOS';
                $sql_h = "INSERT INTO HIST_EVENTOS ".
                         "(RADI_NUME_RADI,DEPE_CODI,USUA_CODI,USUA_CODI_DEST,DEPE_CODI_DEST,USUA_DOC,HIST_DOC_DEST,SGD_TTR_CODIGO,HIST_OBSE,HIST_FECH) ".
                         "VALUES ".
                         "(".$datos_h["RADI_NUME_RADI"].", ".$datos_h["DEPE_CODI"].", ".$datos_h["USUA_CODI"].",".$datos_h["USUA_CODI_DEST"].",".$datos_h["DEPE_CODI_DEST"].", '".$datos_h["USUA_DOC"]."', '".$datos_h["HIST_DOC_DEST"]."', ".$datos_h["SGD_TTR_CODIGO"].", '".$datos_h["HIST_OBSE"]."', ".$datos_h["HIST_FECH"].")";
                $ok_h = $this->cnn->Execute($sql_h);
                if($ok_h){
                    return true;
                }
                else{
                    //$this->cnn->RollbackTrans();
                    return false;
                }
    }
    
 
    
    static function validaVarSeguridad($seguridad){
        $retorno['band']=true;
        //$retorno['msg'][0]="error";
        if(!isset($seguridad['codAplicativo'])){
            $retorno['band']=false;
            $retorno['msg'][]="Se debe envíar la variable \"codAplicativo\" del tipo de dato complejo \"seguridad\" ";
        }
        if(!isset($seguridad['codDependencia'])){
            $retorno['band']=false;
            $retorno['msg'][]="Se debe envíar la variable \"codDependencia\" del tipo de dato complejo \"seguridad\" ";
        }
        if(!isset($seguridad['login'])){
            $retorno['band']=false;
            $retorno['msg'][]="Se debe envíar la variable \"login\" del tipo de dato complejo \"seguridad\" ";
        }
        if(!isset($seguridad['password'])){
            $retorno['band']=false;
            $retorno['msg'][]="Se debe envíar la variable \"password\" del tipo de dato complejo \"seguridad\" ";
        }
        return $retorno;
    }
    
    static function validaVarUsuarioTX($datos, $nombre=""){
        $retorno['band']=true;
        //$retorno['msg'][0]="error";
        if(!isset($datos) && !is_array($datos)){
            $retorno['band']=false;
            $retorno['msg'][]="Se debe envíar la variable $nombre ";
        }
        if(!isset($datos['dependencia'])){
            $retorno['band']=false;
            $retorno['msg'][]="Se debe envíar la variable \"dependencia\" del tipo de dato complejo \"usuarioTX\" para el parametro $nombre";
        }
        else if(!is_numeric($datos['dependencia'])){
            $retorno['band']=false;
            $retorno['msg'][]="La variable \"dependencia\" del tipo de dato complejo \"usuarioTX\" no es numerica para el parametro $nombre";
        }
        else if(strlen(trim($datos['dependencia']))==0){
            $retorno['band']=false;
            $retorno['msg'][]="La variable \"dependencia\" del tipo de dato complejo \"usuarioTX\" no puede estar vacia para el parametro $nombre";
        }
       if(!isset($datos['documento']) && !isset($datos['login']) && !isset($datos['correo'])){
            $retorno['band']=false;
            $retorno['msg'][]="Se debe envíar al menos una de las siguientes variables: \"documento\", \"login\" o \"correo\" validas del tipo de dato complejo \"usuarioTX\" para el parametro $nombre";
        }
        
        else if((strlen(trim($datos['documento']))==0) && (strlen(trim($datos['login']))==0) && (strlen(trim($datos['correo']))==0)){
            $retorno['band']=false;
            $retorno['msg'][]="Se debe envíar al menos una de las siguientes variables: \"documento\", \"login\" o \"correo\" validas del tipo de dato complejo \"usuarioTX\" para el parametro $nombre";
        }
        return $retorno;
    }
    
    static function validaVarDatosContacto($datos){
        $retorno['band']=true;
        //$retorno['msg'][0]="error";
        if((!isset($datos['ciudadano']) or(isset($datos['ciudadano']['esCiudadano']) && ($datos['ciudadano']['esCiudadano'])!=1)) && 
                        (!isset($datos['empresa']) or(isset($datos['ciudadano']['esEmpresa']) && ($datos['ciudadano']['esEmpresa'])!=1))&& 
                        (!isset($datos['entidad']) or(isset($datos['ciudadano']['esEntidad']) && ($datos['ciudadano']['esEntidad'])!=1)) && 
                        (!isset($datos['funcionario']) or(isset($datos['ciudadano']['esFuncionario']) && ($datos['ciudadano']['esFuncionario'])!=1))
                        ){
            $retorno['band']=false;
            $retorno['msg'][]="Se debe envíar al menos una de las siguientes variables: \"ciudadano\", \"empresa\", \"entidad\" o \"funcionario\" validas del tipo de dato complejo \"datosContacto\" ";
        }
        else if(isset($datos['ciudadano'])){
            if(!isset($datos['ciudadano']['esCiudadano'])){
                $retorno['band']=false;
                $retorno['msg'][]="La variable \"ciudadano::esCiudadano\" del tipo de dato complejo \"datosContacto\" debe  ser booleano!:".gettype($datos['ciudadano']['esCiudadano']);
            }
            else if($datos['ciudadano']['esCiudadano']==1){
                
                if(!isset($datos['ciudadano']['nombre']) || (strlen(trim($datos['ciudadano']['nombre']))==0)){
                    $retorno['band']=false;
                    $retorno['msg'][]="La variable \"ciudadano::nombre\"  del tipo de dato complejo \"datosContacto\" es obligatoria";
                }
                if(!isset($datos['ciudadano']['direccion']) || (strlen(trim($datos['ciudadano']['direccion']))==0)){
                    $retorno['band']=false;
                    $retorno['msg'][]="La variable \"ciudadano::direccion\"  del tipo de dato complejo \"datosContacto\" es obligatoria";
                }
                if(!isset($datos['ciudadano']['internacionalizacion'])){
                    $retorno['band']=false;
                    $retorno['msg'][]="Se debe envíar la variable \"ciudadano::internacionalizacion-codContinente-codPais-codDepartamento-codMunicipio\" del tipo de dato complejo \"datosContacto\" ";
                }
                else{
                    if(!isset($datos['ciudadano']['internacionalizacion']['codContinente']) || (strlen(trim($datos['ciudadano']['internacionalizacion']['codContinente']))==0)){
                        $retorno['band']=false;
                        $retorno['msg'][]="Se debe envíar la variable \"ciudadano::internacionalizacion::codContinente\" del tipo de dato complejo \"datosContacto\" ";
                    }
                    if(!isset($datos['ciudadano']['internacionalizacion']['codPais']) || (strlen(trim($datos['ciudadano']['internacionalizacion']['codPais']))==0)){
                        $retorno['band']=false;
                        $retorno['msg'][]="Se debe envíar la variable \"ciudadano::internacionalizacion::codPais\" del tipo de dato complejo \"datosContacto\" ";
                    }
                    if(!isset($datos['ciudadano']['internacionalizacion']['codDepartamento']) || (strlen(trim($datos['ciudadano']['internacionalizacion']['codDepartamento']))==0)){
                        $retorno['band']=false;
                        $retorno['msg'][]="Se debe envíar la variable \"ciudadano::internacionalizacion::codDepartamento\" del tipo de dato complejo \"datosContacto\" ";
                    }
                    if(!isset($datos['ciudadano']['internacionalizacion']['codMunicipio']) || (strlen(trim($datos['ciudadano']['internacionalizacion']['codMunicipio']))==0)){
                        $retorno['band']=false;
                        $retorno['msg'][]="Se debe envíar la variable \"ciudadano::internacionalizacion::codMunicipio\" del tipo de dato complejo \"datosContacto\" ";
                    }
                }
            }
        }
        else if(isset($datos['empresa'])){
            if(!isset($datos['empresa']['esEmpresa'])){
                $retorno['band']=false;
                $retorno['msg'][]="La variable \"empresa::esEmpresa\" del tipo de dato complejo \"datosContacto\" debe  ser booleano!";
            }
            else if($datos['empresa']['esEmpresa']==1){
                
                if(!isset($datos['empresa']['nombre']) || (strlen(trim($datos['empresa']['nombre']))==0)){
                    $retorno['band']=false;
                    $retorno['msg'][]="La variable \"empresa::nombre\"  del tipo de dato complejo \"datosContacto\" es obligatoria";
                }
                if(!isset($datos['empresa']['direccion']) || (strlen(trim($datos['empresa']['direccion']))==0)){
                    $retorno['band']=false;
                    $retorno['msg'][]="La variable \"empresa::direccion\"  del tipo de dato complejo \"datosContacto\" es obligatoria";
                }
                if(!isset($datos['empresa']['internacionalizacion'])){
                    $retorno['band']=false;
                    $retorno['msg'][]="Se debe envíar la variable \"empresa::internacionalizacion-codContinente-codPais-codDepartamento-codMunicipio\" del tipo de dato complejo \"datosContacto\" ";
                }
                else{ 
                    if(!isset($datos['empresa']['internacionalizacion']['codContinente']) || (strlen(trim($datos['empresa']['internacionalizacion']['codContinente']))==0)){
                    $retorno['band']=false;
                    $retorno['msg'][]="Se debe envíar la variable \"empresa::internacionalizacion::codContinente\" del tipo de dato complejo \"datosContacto\" ";
                    }
                    else if(!isset($datos['empresa']['internacionalizacion']['codPais']) || (strlen(trim($datos['empresa']['internacionalizacion']['codPais']))==0)){
                        $retorno['band']=false;
                        $retorno['msg'][]="Se debe envíar la variable \"empresa::internacionalizacion::codPais\" del tipo de dato complejo \"datosContacto\" ";
                    }
                    else if(!isset($datos['empresa']['internacionalizacion']['codDepartamento']) || (strlen(trim($datos['empresa']['internacionalizacion']['codDepartamento']))==0)){
                        $retorno['band']=false;
                        $retorno['msg'][]="Se debe envíar la variable \"empresa::internacionalizacion::codDepartamento\" del tipo de dato complejo \"datosContacto\" ";
                    }
                    else if(!isset($datos['empresa']['internacionalizacion']['codMunicipio']) || (strlen(trim($datos['empresa']['internacionalizacion']['codMunicipio']))==0)){
                        $retorno['band']=false;
                        $retorno['msg'][]="Se debe envíar la variable \"empresa::internacionalizacion::codMunicipio\" del tipo de dato complejo \"datosContacto\" ";
                    }
                }
            }
        }
        else if(isset($datos['entidad'])){
            if(!isset($datos['entidad']['esEntidad'])){
                $retorno['band']=false;
                $retorno['msg'][]="La variable \"entidad::esEntidad\" del tipo de dato complejo \"datosContacto\" debe  ser booleano!";
            }
            else if($datos['entidad']['esEntidad']==1){
                
                if(!isset($datos['entidad']['codEntidad'])){
                    $retorno['band']=false;
                    $retorno['msg'][]="La variable \"entidad::codEntidad\"  del tipo de dato complejo \"datosContacto\" es obligatoria";
                }
                if(!is_numeric($datos['entidad']['codEntidad'])){
                    $retorno['band']=false;
                    $retorno['msg'][]="La variable \"entidad::codEntidad\"  del tipo de dato complejo \"datosContacto\" debe ser numerica";
                }
            }
        }
        else if(isset($datos['funcionario'])){
            if(!isset($datos['funcionario']['esFuncionario'])){
                $retorno['band']=false;
                $retorno['msg'][]="La variable \"funcionario::esFuncionario\" del tipo de dato complejo \"datosContacto\" debe  ser booleano!";
            }
            else if($datos['funcionario']['esFuncionario']==1){
                
                if(!isset($datos['funcionario']['dependencia'])){
                $retorno['band']=false;
                $retorno['msg'][]="Se debe envíar la variable \"funcionario::dependencia\" del tipo de dato complejo \"datosContacto\" ";
                }
                else if(!is_numeric($datos['funcionario']['dependencia'])){
                    $retorno['band']=false;
                    $retorno['msg'][]="La variable \"funcionario::dependencia\" del tipo de dato complejo \"datosContacto\" no es numerica ";
                }
                else if(strlen(trim($datos['funcionario']['dependencia']))==0){
                    $retorno['band']=false;
                    $retorno['msg'][]="La variable \"funcionario::dependencia\" del tipo de dato complejo \"datosContacto\" no puede estar vacia ";
                }
                if(!isset($datos['funcionario']['documento']) && !isset($datos['funcionario']['login']) && !isset($datos['funcionario']['correo'])){
                    $retorno['band']=false;
                    $retorno['msg'][]="Se debe envíar al menos una de las siguientes variables: \"funcionario::documento\", \"funcionario::login\" o \"funcionario::correo\" validas del tipo de dato complejo \"datosContacto\" ";
                }
                else if((strlen(trim($datos['funcionario']['documento']))==0) && (strlen(trim($datos['funcionario']['login']))==0) && (strlen(trim($datos['funcionario']['correo']))==0)){
                    $retorno['band']=false;
                    $retorno['msg'][]="Se debe envíar al menos una de las siguientes variables: \"funcionario::documento\", \"funcionario::login\" o \"funcionario::correo\" validas del tipo de dato complejo \"datosContacto\" ";
                }
            }
        }
        return $retorno;
    }
    
    static function validaVarRefRadicado($datos){
        $retorno['band']=true;
        //$retorno['msg'][0]="error";
        if(!isset($datos) && !is_array($datos)){
            $retorno['band']=false;
            $retorno['msg'][]="Se debe envíar la variable \"refRadicado\" ";
        }
        else{ 
            if(strlen(trim($datos['nReferencia']))==0 && strlen(trim($datos['nRadicado']))==0){
                $retorno['band']=false;
                $retorno['msg'][]="Se debe envíar al menos una de las siguientes variables: \"refRadicado:nReferencia\" o  \"refRadicado:nRadicado\"";
            }
        }
        return $retorno;
    }
    
    static function validaVarRefExpediente($datos){
        $retorno['band']=true;
        //$retorno['msg'][0]="error";
        if(!!isset($datos) && !is_array($datos)){
            $retorno['band']=false;
            $retorno['msg'][]="Se debe envíar la variable \"refExpediente\" ";
        }
        else{ 
            if(strlen(trim($datos['nReferencia']))==0 && strlen(trim($datos['nExpediente']))==0){
                $retorno['band']=false;
                $retorno['msg'][]="Se debe envíar al menos una de las siguientes variables: \"refExpediente:nReferencia\" o  \"refExpediente:nExpediente\"";
            }
        }
        return $retorno;
    }
    
    static function validaVarRefAnexo($datos){
        $retorno['band']=true;
        //$retorno['msg'][0]="error";
        if(!isset($datos) && !is_array($datos)){
            $retorno['band']=false;
            $retorno['msg'][]="Se debe envíar la variable \"refExpediente\" ";
        }
        else{ 
            if(strlen(trim($datos['nReferencia']))==0 && strlen(trim($datos['nAnexo']))==0){
                $retorno['band']=false;
                $retorno['msg'][]="Se debe envíar al menos una de las siguientes variables: \"refAnexo:nReferencia\" o  \"refAnexo:nAnexo\"";
            }
        }
        return $retorno;
    }
    
    /**
     * Funcion para comprobar una fecha.
     * @author http://blog.patoroco.net/2007/09/funcion-is_date/
     * @param date Fecha enviada, formato YYYY-MM-DD
     * @return boolean true si tiene el formato en caso contrario false.
     * @access private
     */
    static function is_date($fecha)
    {   $band = TRUE;
        //Comprueba si la cadena introducida es de la forma Y/m/D (1920/04/15)
        if (ereg("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $fecha, $bloques))
        {
            if (($bloques[2]>12)|($bloques[2]<1))
            {
                $band = FALSE;
            }
            if (($bloques[2]==4)|($bloques[2]==6)|($bloques[2]==9)|($bloques[2]==11))
            {
                $dias_mes = 30;
            }else
            {
                if ($bloques[2]==2)
                {   //febrero
                    if((($bloques[1]%4==0)&(!($bloques[1]%100==0)))|($bloques[1]%400==0))
                    {
                        $dias_mes = 29;
                    }else{
                        $dias_mes = 28;
                    }
                }else
                {
                    $dias_mes = 31;
                }
            }
            if (($bloques[3]<1)|($bloques[3]>$dias_mes))
            {
                $band = FALSE;
            }
        }
        else
        {
            $band = FALSE;
        }
        return $band;
    }
}

?>
