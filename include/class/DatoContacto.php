<?php
class DatoContacto
{
    private $dato_ciu;
    private $dato_ent;
    private $dato_fun;
    private $dato_oem;
    private $conn;
    private $dato_dir_direccion;

    public function __construct($conn)
    {
       $this->conn=$conn;
       //$this->conn->debug=true;
    }

    public function obtieneDatosReales($codi_dir_drecciones, $rad=false,$dirTipo=false)
    {
        if($codi_dir_drecciones){
             $where=" sgd_dir_codigo = $codi_dir_drecciones";
         }
         else if($rad && $dirTipo){
            $where=" radi_nume_radi=$rad and sgd_dir_tipo=$dirTipo";
         }
        $isql = "select * from sgd_dir_drecciones a where  $where";
        $rs =  $this->conn->Execute($isql);
        $ciu = $rs->fields["SGD_CIU_CODIGO"];
        $oem = $rs->fields["SGD_OEM_CODIGO"];
        $esp = $rs->fields["SGD_ESP_CODI"];
        $fun = $rs->fields["SGD_DOC_FUN"];
        $nom = $rs->fields["SGD_DIR_NOMBRE"];
        //if($nom=="Dignatario")return false;
        if($ciu)
        {
            $this->setdatoCiu($ciu);
            return $this->dato_ciu;
        }
        else if($oem)
        {
            $this->setdatoOem($oem);
            return $this->dato_oem;
        }
        else if($esp)
        {
            $this->setdatoEnt($esp);
            return $this->dato_ent;
        }
        else if($fun)
        {
            $this->setdatoFun($fun);
            return $this->dato_fun;
        }
        return false;
       
    }

     public function obtieneDatosDir($codi_dir_drecciones, $rad=false,$dirTipo=false)
    {
         if($codi_dir_drecciones){
             $where=" sgd_dir_codigo = $codi_dir_drecciones";
         }
         else if($rad && $dirTipo){
            $where=" radi_nume_radi=$rad and sgd_dir_tipo=$dirTipo";
         }
        $isql = "select a.SGD_CIU_CODIGO
                ,a.SGD_OEM_CODIGO
                ,a.SGD_ESP_CODI
                ,a.SGD_DOC_FUN
                ,a.SGD_DIR_NOMBRE AS APELLIDO
                ,a.SGD_DIR_NOMREMDES AS NOMBRE
                ,a.SGD_DIR_DIRECCION AS DIRECCION
                ,b.NOMBRE_CONT AS CONTINENTE
                ,c.NOMBRE_PAIS AS PAIS
                ,d.DPTO_NOMB AS DEPARTAMENTO
                ,e.MUNI_NOMB AS MUNICIPIO
                ,a.SGD_DIR_DOC AS DOCUMENTO
                ,a.SGD_DIR_MAIL AS MAIL
                ,a.SGD_DIR_TIPO
                ,m.MREC_DESC
                ,a.SGD_DIR_TELEFONO AS TELEFONO
                ,a.ID_CONT AS COD_CONTINENTE
                ,a.ID_PAIS AS COD_PAIS
                ,a.DPTO_CODI AS COD_DEPARTAMENTO
                ,a.MUNI_CODI AS COD_MUNICIPIO
                ,a.MREC_CODI AS MEDIO_ENVIO
                ,a.SGD_ANEX_CODIGO AS ANEX_CODIGO
                ,a.estado_envio_fax
                ,a.numero_fax
                ,a.id_fax
                ,a.vobo_fax
                ,a.sgd_dir_codigo
                ,a.SGD_DIR_CODPOSTAL AS CODIGO_POSTAL
                from sgd_dir_drecciones a
                join SGD_DEF_CONTINENTES b ON a.ID_CONT=b.ID_CONT
                join SGD_DEF_PAISES      c ON a.ID_CONT=c.ID_CONT AND a.ID_PAIS=c.ID_PAIS
                join DEPARTAMENTO        d ON a.ID_CONT=d.ID_CONT AND a.ID_PAIS=d.ID_PAIS AND a.DPTO_CODI=d.DPTO_CODI
                join MUNICIPIO          e ON a.ID_CONT=e.ID_CONT AND a.ID_PAIS=e.ID_PAIS AND a.DPTO_CODI=e.DPTO_CODI AND a.MUNI_CODI=e.MUNI_CODI
                left join MEDIO_RECEPCION m on m.MREC_CODI=a.MREC_CODI
                where $where ";
        return $this->dato_dir_direccion =  $this->conn->GetArray($isql);
    }

    public function setdatoCiu($codi_ciu)
    {
        $sql="select a.SGD_CIU_CEDULA as ID
                    ,a.SGD_CIU_NOMBRE as NOMBRE,".
                    $this->conn->Concat("a.SGD_CIU_APELL1","' '","a.SGD_CIU_APELL2")." as APELLIDO
                    ,a.SGD_CIU_CODIGO
                    ,a.SGD_CIU_DIRECCION as DIRECCION
                    ,a.SGD_CIU_TELEFONO AS TELEFONO
                    ,b.NOMBRE_CONT AS CONTINENTE
                    ,c.NOMBRE_PAIS AS PAIS
                    ,d.DPTO_NOMB AS DEPARTAMENTO
                    ,e.MUNI_NOMB AS MUNICIPIO
                    ,a.SGD_CIU_CODPOSTAL AS CODIGO_POSTAL
              from SGD_CIU_CIUDADANO a
              join SGD_DEF_CONTINENTES b ON a.ID_CONT=b.ID_CONT
              join SGD_DEF_PAISES      c ON a.ID_CONT=c.ID_CONT AND a.ID_PAIS=c.ID_PAIS
              join DEPARTAMENTO        d ON a.ID_CONT=d.ID_CONT AND a.ID_PAIS=d.ID_PAIS AND a.DPTO_CODI=d.DPTO_CODI
              join MUNICIPIO          e ON a.ID_CONT=e.ID_CONT AND a.ID_PAIS=e.ID_PAIS AND a.DPTO_CODI=e.DPTO_CODI AND a.MUNI_CODI=e.MUNI_CODI
              WHERE  SGD_CIU_CODIGO = $codi_ciu";
        $this->dato_ciu= $this->conn->GetArray($sql);
       
    }
    public function setdatoEnt($codi_ent)
    {
        $sql="select a.NIT_DE_LA_EMPRESA as ID
                    ,a.NOMBRE_DE_LA_EMPRESA as NOMBRE
                    ,a.NOMBRE_REP_LEGAL as APELLIDO
                    ,a.IDENTIFICADOR_EMPRESA
                    ,a.DIRECCION as DIRECCION
                    ,a.TELEFONO_1 AS TELEFONO
                    ,b.NOMBRE_CONT AS CONTINENTE
                    ,c.NOMBRE_PAIS AS PAIS
                    ,d.DPTO_NOMB AS DEPARTAMENTO
                    ,e.MUNI_NOMB AS MUNICIPIO
                    ,b.ID_CONT
                    ,c.ID_PAIS
                    ,d.DPTO_CODI
                    ,e.MUNI_CODI
                    ,a.EMAIL
                    ,a.COD_POSTAL AS CODIGO_POSTAL
              from BODEGA_EMPRESAS a
              join SGD_DEF_CONTINENTES b ON a.ID_CONT=b.ID_CONT
              join SGD_DEF_PAISES      c ON a.ID_CONT=c.ID_CONT AND a.ID_PAIS=c.ID_PAIS
              join DEPARTAMENTO        d ON a.ID_CONT=d.ID_CONT AND a.ID_PAIS=d.ID_PAIS AND a.CODIGO_DEL_DEPARTAMENTO=d.DPTO_CODI
              join MUNICIPIO          e ON a.ID_CONT=e.ID_CONT AND a.ID_PAIS=e.ID_PAIS AND a.CODIGO_DEL_DEPARTAMENTO=e.DPTO_CODI AND a.CODIGO_DEL_MUNICIPIO=e.MUNI_CODI
              WHERE  IDENTIFICADOR_EMPRESA = $codi_ent";
        $this->dato_ent= $this->conn->GetArray($sql);
    }
    public function setdatoFun($dep,$docu,$login,$correo)
    {
        if($docu)
            $where="(u.depe_codi=$dep and USUA_DOC = '$docu')";
        else if($login)
            $where="(u.depe_codi=$dep and USUA_LOGIN='$login')";
        else if ($correo)
            $where="(u.depe_codi=$dep and USUA_EMAIL='$correo')";
        else
            return false;
        $sql="select USUA_DOC as ID
                    ,USUA_NOMB as NOMBRE
                    ,'-' as APELLIDO
                    ,USUA_DOC
                    ,d.depe_nomb as DIRECCION
                    ,USUA_EXT AS TELEFONO
                    ,d.ID_CONT
                    ,d.ID_PAIS
                    ,d.DPTO_CODI
                    ,d.MUNI_CODI
                    ,u.USUA_EMAIL AS EMAIL
                    ,d.DEP_CODPOSTAL AS CODIGO_POSTAL
              from USUARIO u
              join DEPENDENCIA d on u.depe_codi=d.depe_codi
              WHERE  $where ";
        $this->dato_fun= $this->conn->GetArray($sql);
    }
    public function setdatoOem($codi_oem)
    {
        $sql="select a.SGD_OEM_NIT as ID
                    ,a.SGD_OEM_OEMPRESA as NOMBRE
                    ,a.SGD_OEM_REP_LEGAL as APELLIDO
                    ,a.SGD_OEM_CODIGO
                    ,a.SGD_OEM_DIRECCION as DIRECCION
                    ,a.SGD_OEM_TELEFONO AS TELEFONO
                    ,b.NOMBRE_CONT AS CONTINENTE
                    ,c.NOMBRE_PAIS AS PAIS
                    ,d.DPTO_NOMB AS DEPARTAMENTO
                    ,e.MUNI_NOMB AS MUNICIPIO
                    ,a.SGD_OEM_CODPOSTAL AS CODIGO_POSTAL
              from SGD_OEM_OEMPRESAS a
              join SGD_DEF_CONTINENTES b ON a.ID_CONT=b.ID_CONT
              join SGD_DEF_PAISES      c ON a.ID_CONT=c.ID_CONT AND a.ID_PAIS=c.ID_PAIS
              join DEPARTAMENTO        d ON a.ID_CONT=d.ID_CONT AND a.ID_PAIS=d.ID_PAIS AND a.DPTO_CODI=d.DPTO_CODI
              join MUNICIPIO          e ON a.ID_CONT=e.ID_CONT AND a.ID_PAIS=e.ID_PAIS AND a.DPTO_CODI=e.DPTO_CODI AND a.MUNI_CODI=e.MUNI_CODI
              WHERE  SGD_OEM_CODIGO = $codi_oem";
          $this->dato_oem= $this->conn->GetArray($sql);
    }
   
    public function getTip3Habilitado($tipoRad,$tipo3)
    {
        if($tipo3==1 and $tipoRad<>2)$where=" sgd_dir_tipo=$tipo3 and sgd_tip3_codigo=2";
        else if($tipo3==1 and $tipoRad==2)$where=" sgd_dir_tipo=$tipo3 and sgd_tip3_codigo=1";
        else $where=" sgd_dir_tipo=$tipo3 ";
        $query = "select sgd_tpr_tp$tipoRad as verif from SGD_TIP3_TIPOTERCERO  where $where";
        $rs = $this->conn->Execute($query);
        if($rs) return $rs->fields['VERIF'];
        else return 0;
    }
   
    public function pasaDir1ToDir7($dir1,$dir7)
    {
        $vecDatos7=$this->obtieneDatosDir($dir7);
       
        $oemp=$vecDatos7[0]['SGD_OEM_CODIGO']?$vecDatos7[0]['SGD_OEM_CODIGO']:'null';
        $entidad=$vecDatos7[0]['SGD_ESP_CODI']?$vecDatos7[0]['SGD_ESP_CODI']:'null';
        $ciu=$vecDatos7[0]['SGD_CIU_CODIGO']?$vecDatos7[0]['SGD_CIU_CODIGO']:'null';
        $fun=$vecDatos7[0]['SGD_DOC_FUN']?$vecDatos7[0]['SGD_DOC_FUN']:'null';
        /*$sql="update sgd_dir_drecciones  set
              SGD_CIU_CODIGO =".$ciu."
                ,SGD_OEM_CODIGO =".$oemp."
                ,SGD_ESP_CODI =".$entidad."
                ,SGD_DOC_FUN =".$fun."
                ,SGD_DIR_NOMBRE ='".$vecDatos7[0]['APELLIDO']."'
                ,SGD_DIR_NOMREMDES ='".$vecDatos7[0]['NOMBRE']."'
                ,SGD_DIR_DIRECCION ='".$vecDatos7[0]['DIRECCION']."'
                ,SGD_DIR_DOC ='".$vecDatos7[0]['DOCUMENTO']."'
                ,SGD_DIR_MAIL  ='".$vecDatos7[0]['MAIL']."'
                ,SGD_DIR_TELEFONO  ='".$vecDatos7[0]['TELEFONO']."'
                ,ID_CONT  =".$vecDatos7[0]['COD_CONTINENTE']."
                ,ID_PAIS  =".$vecDatos7[0]['COD_PAIS']."
                ,DPTO_CODI  =".$vecDatos7[0]['COD_DEPARTAMENTO']."
                ,MUNI_CODI  =".$vecDatos7[0]['COD_MUNICIPIO']."
                ,MREC_CODI  =".$vecDatos7[0]['MEDIO_ENVIO']."
                ,SGD_ANEX_CODIGO  ='".$vecDatos7[0]['ANEX_CODIGO']."'
              where sgd_dir_codigo= $dir1
            ";
        $rs=$this->conn->Execute($sql);
        if($rs){
          $sqlDel="delete from sgd_dir_drecciones where sgd_dir_codigo=$dir7";  
          $this->conn->Execute($sqlDel);
        }*/
       
    }

    public function esContinente($codContinente){
        $sql="select count(*) as cantidad from SGD_DEF_CONTINENTES where ID_CONT=".$codContinente;
        $rs=$this->conn->Execute($sql);
        if($rs && $rs->fields['CANTIDAD']>0)
            return true;
        else 
            return false;
    }
    public function esPais($codContinente,$codPais){
         $sql="select count(*) as cantidad from SGD_DEF_PAISES  where ID_CONT=$codContinente AND ID_PAIS=$codPais";
        $rs=$this->conn->Execute($sql);
        if($rs && $rs->fields['CANTIDAD']>0)
            return true;
        else 
            return false;
    }
    public function esDepto($codContinente,$codPais,$codDepto){
         $sql="select count(*) as cantidad from DEPARTAMENTO   where ID_CONT=$codContinente AND ID_PAIS=$codPais AND DPTO_CODI=$codDepto";
        $rs=$this->conn->Execute($sql);
        if($rs && $rs->fields['CANTIDAD']>0)
            return true;
        else 
            return false;
    }
    public function esMnpio($codContinente,$codPais,$codDepto,$codMnpio){
         $sql="select count(*) as cantidad from MUNICIPIO where ID_CONT=$codContinente AND ID_PAIS=$codPais AND DPTO_CODI=$codDepto AND MUNI_CODI=$codMnpio";
        $rs=$this->conn->Execute($sql);
        if($rs && $rs->fields['CANTIDAD']>0)
            return true;
        else 
            return false;
    }
    
    public function getDatosMunicipio($codContinente,$codPais,$codDepto,$codMnpio){
        $sql="select * from MUNICIPIO where ID_CONT=$codContinente AND ID_PAIS=$codPais AND DPTO_CODI=$codDepto AND MUNI_CODI=$codMnpio";
        return  $this->conn->GetArray($sql);
    }
    
    public function getdatoCiu()
    {
        return $this->dato_ciu;
    }
    public function getdatoEnt()
    {
        return $this->dato_ent;
    }
    public function getdatoFun()
    {
        return $this->dato_fun;
    }
    public function getdatoOem()
    {
        return $this->dato_oem;
    }
    
    public function actualizaDatosDirEnvioFax($dir_codigo,$radicado,$dir_tipo=false,$tipoEnvio=false,$estado=false,$numero_fax=false, $id_fax=false,$voBoFax=false){
        
        if($tipoEnvio){
            $set[]="mrec_codi= $tipoEnvio";
        }
        if($estado!==false){
            $set[]="estado_envio_fax=$estado";
        }if($numero_fax!==false){
            $set[]="numero_fax=$numero_fax";
        }
        if($id_fax!==false){
            $set[]="id_fax=$id_fax";
        }
        if($voBoFax!==false){
            $set[]="vobo_fax=$voBoFax";
        }
        $stUpdate=implode(",", $set);
        
        if($dir_codigo)
        {
            $where=" sgd_dir_codigo=$dir_codigo";
        }
        else if($radicado && $dir_tipo){
            $where=" radi_nume_radi=$radicado and sgd_dir_tipo=$dir_tipo";
        }
        if(count($set)>0 && $where){
            $sql="update sgd_dir_drecciones set $stUpdate where $where";
            $rs=$this->conn->Execute($sql);
            if($rs)
                return true;
        }
        else
            return false;
    }
    
    public function verificaVoBoEnvioFax($radicado){
        
        $sql="select VOBO_FAX from SGD_DIR_DRECCIONES where MREC_CODI=2 AND RADI_NUME_RADI=".$radicado;
				$this->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs=$this->conn->Execute($sql);
        if($rs && !$rs->EOF)
        {
          
            While(!$rs->EOF){
                
                if($rs->fields['VOBO_FAX']==0){
                    return false;
                }
                $rs->MoveNext();
            }
            return true;
        }
        else 
        {
            return true;
        }
    }
}

?>
