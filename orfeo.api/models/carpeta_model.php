<?php

class Carpeta_Model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
        /**
     * Se encarga de retornar el listado de radicados de un Usuario especifico y una carpeta especifica
     * @POST
     */
    function listadoRadicadosEnCarpetaJSON()
    {
        $parametros=Array("usuarioActual"=>$_SESSION['krd'],
                          "fechaInicio"=>"2006/01/01"
                        );
        $objCodificacionEspecial = new CodificacionEspecial();
        //Se configuran todos los paramteros que seran enviados al SP.
        if(isset($_GET['sort']))
        {
            $parametros['campoOrden']=$_GET['sort'];
        }
        else{
            $parametros['campoOrden']='';
        }
        if(isset($_GET['dir']))
        {
            $parametros['tipoOrden']=$_GET['dir'];
        }
        else
        {
             $parametros['tipoOrden']='';
        }
        if(isset($_GET['limit']))
        {
            $parametros['NoRegistrosPagina']=$_GET['limit'];
        }
        else{
            $parametros['NoRegistrosPagina']=0;
        }
        if(isset($_GET['page']))
        {
            $parametros['NoPaginaResultado']=$_GET['page'];
        }
        else{
             $parametros['NoPaginaResultado']=0;
        }
        if(isset($_GET['callback']))
        {
            $callBack=$_GET['callback'];
        }
        if(isset($_GET['codigoCarpeta']) && strlen($_GET['codigoCarpeta'])>0)
        {
           $parametros['codigoCarpeta']=$_GET['codigoCarpeta'];
        }
        else{
            $parametros['codigoCarpeta']=2;
        }
        if(isset($_GET['carpetaPersonal']) && strlen($_GET['carpetaPersonal'])>0)
        {
            $parametros['carpetaPersonal']=$_GET['carpetaPersonal'];
        }
        else{
            $parametros['carpetaPersonal']=0;
        }

        if(isset($_GET['codigoDepenendenciaActual']))
        {
            $parametros['codigoDepenendenciaActual']=$_GET['codigoDepenendenciaActual'];
        }
        else{
            $parametros['codigoDepenendenciaActual']=0;
        }
        if(isset($_GET['codigoTipoDocumental']))
        {
            $parametros['codigoTipoDocumental']=$_GET['codigoTipoDocumental'];
        }
        else{
            $parametros['codigoTipoDocumental']=0;
        }
        if(isset($_GET['tipoRadicado']))
        {
            $parametros['tipoRadicado']=$_GET['tipoRadicado'];
        }
        else{
            $parametros['tipoRadicado']=0;
        }
        if(isset($_GET['nombreContacto']))
        {
            $parametros['nombreContacto']=$_GET['nombreContacto'];
        }
        else{
            $parametros['nombreContacto']='';
        }
        if(isset($_GET['NoRadicado']) && strlen(trim($_GET['NoRadicado']))>0)
        {
            $parametros['NoRadicado']=$_GET['NoRadicado'];
        }
        else{
            $parametros['NoRadicado']= "";
        }
        
        
        if(isset($_SESSION))
        {
            if($_SESSION['codusuario'])
            {
                $codusuario=$_SESSION['codusuario'];
            }
             if($_SESSION['dependencia'])
            {
                $dependencia=$_SESSION['dependencia'];
            }
            try {//Construimos el llamado al SP en SQL Server.
                $st="declare @fech1 datetime
                                    declare @fech2 datetime
                                    DECLARE @return_value int

                                    set @fech1 = cast('".$parametros['fechaInicio']."' as datetime)
                                    set @fech2 = getdate()

                                    EXEC	@return_value = [dbo].[RADICADO_SGD_DIR_DRECCIONESConsultarConFiltro]
                                                 @NoRadicado = '".$parametros['NoRadicado']."',
                                                    @nombreContacto = '".$parametros['nombreContacto']."',
                                                    @tipoRadicado = ".$parametros['tipoRadicado'].",
                                                    @fechaInicio = @fech1,
                                                    @fechaFin = @fech2,
                                                    @codigoTipoDocumental = ".$parametros['codigoTipoDocumental'].",
                                                    @codigoDepenendenciaActual = ".$parametros['codigoDepenendenciaActual'].",
                                                    @NoPaginaResultado = ".$parametros['NoPaginaResultado'].",
                                                    @campoOrden = '".$parametros['campoOrden']."',
                                                    @tipoOrden = '".$parametros['tipoOrden']."',
                                                    @NoRegistrosPorPagina = ".$parametros['NoRegistrosPagina'].",
                                                    @usuarioActual = '".$parametros['usuarioActual']."',
                                                    @codigoCarpeta = ".$parametros['codigoCarpeta'].",
                                                    @carpetaPersonal = ".$parametros['carpetaPersonal']."";
                
                            $rs=$this->db->conn->GetArray($st);
                            if($rs && is_array($rs) && count($rs)>0)
                            {
                                $dataFromRS=array();
                                //$cont=0;
                                require_once 'radicado_model.php';
                                $objRad = new Radicado_Model();
                                
                                
                                
                                //Se extrae la informacion de la carpeta a consultar.
                                if($parametros['carpetaPersonal']==0){
                                    $detalleCarpeta = $this->detalleCarpeta($parametros['codigoCarpeta']);
                                    $nombreCarpeta= iconv($detalleCarpeta['CARP_DESC'], 'UTF-8', $detalleCarpeta['CARP_DESC']);
                                }else{
                                    $detalleCarpeta = $this->detalleCarpetaPersonal($parametros['codigoCarpeta'], $codusuario,$dependencia);
                                    $nombreCarpeta= "(Personal)".iconv($detalleCarpeta['DESC_CARP'], 'UTF-8', $detalleCarpeta['DESC_CARP']);
                                }
                                
                                foreach ($rs as $r => $data)
                                {
                                    /**foreach ($data as $key => $dato) {
                                        $data[$key] = iconv($objCodificacionEspecial->codificacion($dato), "UTF-8",$objCodificacionEspecial->codificacion($dato));
                                    }*/
                                    
                                    $dataFromRS[$r]=$data;
                                    //$dataFromRS[$r]['icons']="<a href='./bodega/".$data['RADI_PATH']."'>";
                                    $datosRadicado=$objRad->datosRadicado($data['RADI_NUME_RADI']);//se extrae la informacion del radicado para validaciones adicionales
                                    $datosExpedienteRadicado=$objRad->datosExpedienteRadicado($data['RADI_NUME_RADI']);//se extrae la informacion del radicado para validaciones adicionales
                                    $textCarpeta = "Carpeta Actual: " . $nombreCarpeta . " -- Numero de Hojas :" . $datosRadicado['RADI_NUME_HOJA'];
                                    
                                    //Encabezado para ver detalles del radicado
                                    
                                    $encabezado = "".session_name()."=".session_id()."&krd=".$_SESSION['krd']."&".session_name()."=".session_id()."&carpeta=".$_GET['codigoCarpeta']."&tipo_carp=".$_GET['carpetaPersonal']."&nomcarpeta=$nombreCarpeta";
                                    
                                    
                                    //Zona de validaciones por registro
                                    //
                                    $imgEstado     ="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                                    $imgTp         ="";
                                    $imgExpediente ="";
                                    $derivado      ="&nbsp;&nbsp;";
                                    //Validamos si se encuentra anulado el radicado
                                    if($data['SGD_EANU_CODIGO']== 2){
                                        $imgTp       = "./iconos/anulacionRad.gif";
                                        $textCarpeta = " ** RADICADO ANULADO ** ".$textCarpeta;
                                        $imgEstado = "<img src='$imgTp' width=18 height=18 alt='$textCarpeta' title='$textCarpeta'>";
                                    }
                                    //Verifica si se obtuvieron datos detallados del Radicado
                                    if($datosRadicado && is_array($datosRadicado)){
                                        
                                        if($datosRadicado['RADI_TIPO_DERI']==0 && $datosRadicado['RADI_NUME_DERI']!=0){
                                          $imgTp = "./iconos/anexos.gif";
                                          $imgEstado = "<img src='$imgTp' width=18 height=18 alt='$textCarpeta' title='$textCarpeta'>";
                                        }
                                    }
                                    //Verifica si se obtuvieron datos del Expediente
                                    if($datosExpedienteRadicado && is_array($datosExpedienteRadicado)){
                                        if(count($datosExpedienteRadicado)>0){
                                            $imgExpediente = "<img src='./iconos/folder_open.gif' width=18 height=18 alt='$textCarpeta' title='$textCarpeta'>";
                                        }
                                    }
                                    
                                    //Verifica si el registro se encuentra leido
                                    
                                    if ($data['SGD_PRIORIDAD'] > 0) {
                                        $radFileClass = "priorizados";
                                        $colorlink = "style='color:#FF0000;'";
                                    } else {
                                        if ($data['RADI_LEIDO']){
                                            $radFileClass = "leidos";
                                            $colorlink = "style='color:#006699;'";
                                        } else
                                        {
                                            $radFileClass = "no_leidos";
                                            $colorlink = "style='color:#488F03;'";
                                        }
                                    }
                                    if($data['DERIVADO']==1)
                                    {
                                       $dataFromRS[$r]['raiz']='';
                                       $derivado="<span class='$radFileClass'>*</span>";
                                    }else{
                                       $dataFromRS[$r]['raiz']='raiz';
                                    }
                                    
                                    $icons="";
                                    //ruta para la imagen escaneada
                                    $path = trim("" . $data['RADI_PATH']);
                                    if ($path != '')
                                    {
                                        $punto     = strrpos($path, '.') + 1;
                                        $interroga = strpos($path, '?');
                                        $dif       = $punto-$interroga;
                                        $ext       = substr($path, $punto, $dif);

                                        $icons =  $ext .  $imgExpediente  ;
                                        $icons = "$derivado $imgEstado<a href='./bodega/$path' target='_blank'>$icons</a>";
                                    }
                                    else{
                                        $icons = $derivado . $imgEstado . $imgExpediente ;
                                    }
                                    
                                    $date = date_create($dataFromRS[$r]['RADI_FECH_RADI']);
                                    
                                    $dataFromRS[$r]['idCiudadano']=$dataFromRS[$r]['idCiudadano'];
                                    $dataFromRS[$r]['idEmpresa']=$dataFromRS[$r]['idEmpresa'];
                                    $dataFromRS[$r]['idEntidad']=$dataFromRS[$r]['idEntidad'];
                                    $dataFromRS[$r]['loginFuncionario'] = iconv($dataFromRS[$r]['loginFuncionario'], "UTF-8",$dataFromRS[$r]['loginFuncionario']);
                                    $dataFromRS[$r]['nroradicado'] = $dataFromRS[$r]['RADI_NUME_RADI'];
                                    $tmpChasetAsun = $objCodificacionEspecial->codificacion($dataFromRS[$r]['RA_ASUN']);
                                    $dataFromRS[$r]['asunto'] = iconv($tmpChasetAsun, "UTF-8",htmlentities($tmpChasetAsun, null, $tmpChasetAsun));
                                    $dataFromRS[$r]['dirmail'] = iconv($objCodificacionEspecial->codificacion($dataFromRS[$r]['SGD_DIR_MAIL']), "UTF-8", $dataFromRS[$r]['SGD_DIR_MAIL']);
                                    $dataFromRS[$r]['nombrecontacto'] = iconv($objCodificacionEspecial->codificacion(htmlentities($dataFromRS[$r]['SGD_DIR_NOMREMDES'])),"UTF-8",htmlentities($dataFromRS[$r]['SGD_DIR_NOMREMDES']));
                                    $dataFromRS[$r]['icons'] =  $icons;
                                    $dataFromRS[$r]['RADI_NUME_RADI'] ="<span class='$radFileClass'>".$dataFromRS[$r]['RADI_NUME_RADI']."</span>";
                                    //$dataFromRS[$r]['RADI_FECH_RADI'] ="<span class='$radFileClass'><a href='verradicado.php?verrad=".$dataFromRS[$r]['nroradicado']."'>".$dataFromRS[$r]['RADI_FECH_RADI']."</a></span>";
                                    $dataFromRS[$r]['RADI_FECH_RADI'] ="<span class='$radFileClass'><a href='verradicado.php?verrad=".$dataFromRS[$r]['nroradicado']."' $colorlink>".date_format($date,"Y-m-d H:i:s")."</a></span>";
                                    $dataFromRS[$r]['RA_ASUN'] = "<span class='$radFileClass'>".iconv($tmpChasetAsun, "UTF-8",htmlentities($dataFromRS[$r]['RA_ASUN'], null, $tmpChasetAsun))."</span>";
                                    $dataFromRS[$r]['SGD_TPR_DESCRIP'] = "<span class='$radFileClass'>".iconv("ISO-8859-1","UTF-8",$dataFromRS[$r]['SGD_TPR_DESCRIP'])."</span>";
                                    //$dataFromRS[$r]['SGD_TPR_DESCRIP'] = "<span class='$radFileClass'>".iconv($objCodificacionEspecial->codificacion($dataFromRS[$r]['SGD_TPR_DESCRIP']),"UTF-8",$dataFromRS[$r]['SGD_TPR_DESCRIP'])."</span>";
                                    $dataFromRS[$r]['RADI_USU_ANTE'] = "<span class='$radFileClass'>".$dataFromRS[$r]['RADI_USU_ANTE']."</span>";
                                    $dataFromRS[$r]['RADI_FECHA_VENCE'] = "<span class='$radFileClass'>".$dataFromRS[$r]['RADI_FECHA_VENCE']."</span>";
                                    $dataFromRS[$r]['RADI_DIAS_VENCE'] = "<span class='$radFileClass'>".$dataFromRS[$r]['RADI_DIAS_VENCE']."</span>";
                                    $dataFromRS[$r]['HID'] = "<img src='' width=20 height=20 alt='' title=''>";
                                    $dataFromRS[$r]['modenvio']= $dataFromRS[$r]['SGD_FENV_MODALIDAD'];
                                    $dataFromRS[$r]['SGD_DIR_MAIL']= iconv($objCodificacionEspecial->codificacion($dataFromRS[$r]['SGD_DIR_MAIL']), "UTF-8", $dataFromRS[$r]['SGD_DIR_MAIL']);
                                    unset($dataFromRS[$r]['SGD_DIR_NOMREMDES']);   //Elimino variables ya que no se trataron con htmentities y traen tildados
                                    unset($dataFromRS[$r]['SGD_DIR_DIRECCION']);
                                    //$cont++;
                                }
                                $datos['NoRegistrosPagina']= $rs[0]['totalRows'];
                                $datos['datosGenerales']=$dataFromRS;
                               //$json= json_encode($datos);
                            }
                            else{
                                $datos['NoRegistrosPagina']=0;
                                $datos['datosGenerales']=null;
                            }
            }
            catch (ADODB_Exception $ex)
            {
                echo "Error:". $ex->getMessage();
            }
        }
        else{ 
        
            return false;
        }
        $tmpJE = json_encode($datos);
        if (json_last_error() !== JSON_ERROR_NONE) {
            die(json_last_error_msg()); //Malformed UTF-8 characters, possibly incorrectly encoded
        }
        if(isset($callBack))
        {
            header('Content-Type: application/javascript');
            return "$callBack(".$objCodificacionEspecial->jsonRemoveUnicodeSequences($tmpJE).");";
        }
        else
        {
            return $objCodificacionEspecial->jsonRemoveUnicodeSequences($tmpJE);
        }
        //var_dump($ret);
    }
    /**
     * 
     * @param integer $cod_carpeta
     * @return Array: Datos detallados de la carpeta
     */
    function detalleCarpeta($cod_carpeta){
        $sql="select CARP_CODI, CARP_DESC from carpeta where carp_codi=?";
        $rs=$this->db->select($sql,array($cod_carpeta),false );
        if($rs && !$rs->EOF){
            return $rs->fields;
        }else{
            return false;
        }
    }
    
    /**
     * 
     * @param integer $cod_carpeta
     * @param integer $usua_codi
     * @param integer $depe_codi
     * @return Array: datos tedallados de la carpeta personal.
     */
    function detalleCarpetaPersonal($cod_carpeta, $usua_codi, $depe_codi){
        $sql="select USUA_CODI, DEPE_CODI, NOMB_CARP, DESC_CARP, CODI_CARP, USUA_DOC from carpeta_per where codi_carp=? and usua_codi=? and depe_codi=?";
        $rs=$this->db->select($sql,array($cod_carpeta,$usua_codi,$depe_codi),false );
        if($rs && !$rs->EOF){
            return $rs->fields;
        }else{
            return false;
        }
    }
}
?>
