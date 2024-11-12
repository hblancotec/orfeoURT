<?php

session_start();
$arrRadCopia = explode("_", $_POST['rad']);
$radicado = $arrRadCopia[0];
$formaEnvio = (int) $_POST['tipEnvio'];
$destino = trim($_POST['destino']);

$nombre = substr(trim($_POST['nombre']), 0, 30);
$dirCod = (int) $_POST['dircod'];
$ruta_raiz = "..";

include_once("$ruta_raiz/include/class/DatoContacto.php");
include_once("$ruta_raiz/include/query/envios/queryEnvia.php");

require "$ruta_raiz/config.php";
require "adodb/adodb.inc.php";
$dsn = $driver . "://$usuario:$contrasena@$servidor/$servicio";
$conn = NewADOConnection($dsn);
if ($conn->connect()) {
    $conn->SetFetchMode(ADODB_FETCH_ASSOC);
    //Validamos que el codEnvio exista y de paso traemos información a grabar en sgd_renv_regenvio
    $sql = "select SGD_DIR_TIPO, SGD_DIR_DIRECCION, SGD_DIR_MAIL, SGD_DIR_TIPO, SGD_DIR_DIRECCION, 
                SGD_DIR_TELEFONO, ID_CONT, ID_PAIS, DPTO_CODI, MUNI_CODI, SGD_DIR_CODPOSTAL 
            from SGD_DIR_DRECCIONES where SGD_DIR_CODIGO=" . $dirCod;
    $rsd = $conn->Execute($sql);

    if ($rsd->RecordCount() > 0) {

        switch ($formaEnvio) {
            case 106: { //Mail 
                    require "$ruta_raiz/class_control/Certimail.php";
                    $objEnvio = new Certimail();
                    $objEnvio->correoe = $rsd->fields['SGD_DIR_MAIL'];
                    if ($objEnvio->validarDatos()) {
                        $sql = "select RADI_PATH from RADICADO where RADI_NUME_RADI=$radicado";
                        $dataPDF = $conn->GetOne($sql);
                        $dataPDF = trim($dataPDF);
                        if (empty($dataPDF)) {
                            $out = array('success' => FALSE,
                                'errors' => array(
                                    'reason' => "Radicado $radicado no tiene imagen principal."
                                )
                            );
                        } else if (strtolower(substr($dataPDF, -3)) != 'pdf') {
                            $out = array('success' => FALSE,
                                'errors' => array(
                                    'reason' => "Imagen del radicado $radicado no es PDF."
                                )
                            );
                        } else {

                            $dataPDF = (substr($dataPDF, 0, 1) == '/') ? $dataPDF : "/$dataPDF";
                            $dataPDF = BODEGAPATH . $dataPDF;
                            require "$ruta_raiz/class_control/correoElectronico.php";
                            $objCorreo = new correoElectronico($ruta_raiz, TRUE);
                            $objCorreo->CharSet = "UTF-8";
                            $objCorreo->FromName = "Notificaciones DNP";
                            $objCorreo->agregarAdjunto($dataPDF, "Radicado " . $radicado . ".pdf");
                            $ok = $objCorreo->enviarCorreo(array($objEnvio->correoe), null, null, "(R" . $_POST['rad'] . ") (c) " . $asunto_certimail . $radicado . ".", $cuerpo_certimail);
                            if ($ok === TRUE) {

                                $objEnvio->conn = $conn;
                                $objEnvio->cedula = $_SESSION['usua_doc'];
                                $objEnvio->formaEnvio = $formaEnvio;
                                $objEnvio->radicado = $radicado;
                                $objEnvio->dirTipo = $rsd->fields['SGD_DIR_TIPO'];
                                $objEnvio->direccion = trim($rsd->fields['SGD_DIR_DIRECCION']);
                                $objEnvio->destino = $destino;
                                $objEnvio->telefono = trim($rsd->fields['SGD_DIR_TELEFONO']);
                                $objEnvio->nombre = $nombre;
                                $objEnvio->codEnvio = $dirCod;
                                $objEnvio->dependencia = $_SESSION['dependencia'];

                                //Validamos internacionalización
                                if (trim($rsd->fields['ID_CONT']) or trim($rsd->fields['ID_PAIS']) or
                                        trim($rsd->fields['DPTO_CODI']) or trim($rsd->fields['MUNI_CODI'])) {   //Para el caso en que la internacionalización no viene. Es posible porque es envío de correo electrónico
                                    //solo se requiere que tenga un email y esto ya fue validado.
                                    $objEnvio->municipio = null;
                                    $objEnvio->departamento = null;
                                    $objEnvio->pais = null;
                                    //$this->continente = null;    //Esto no se guarda en sgd_renv_regenvio
                                } else {
                                    $sql = "select c.NOMBRE_CONT, p.NOMBRE_PAIS, d.DPTO_NOMB, m.MUNI_NOMB
                                    from MUNICIPIO m
                                            inner join SGD_DEF_CONTINENTES c on m.ID_CONT=m.ID_CONT
                                            inner join SGD_DEF_PAISES p on m.ID_CONT=p.ID_CONT and m.ID_PAIS=p.ID_PAIS
                                            inner join DEPARTAMENTO d on d.ID_CONT=m.ID_CONT and d.ID_PAIS=m.ID_PAIS and d.DPTO_CODI=m.DPTO_CODI
                                    where m.MUNI_CODI = " . $rsd->fields['MUNI_CODI'] . " AND m.DPTO_CODI = " . $rsd->fields['DPTO_CODI'] . " and m.ID_PAIS = " . $rsd->fields['ID_PAIS'] . " and m.ID_CONT = " . $rsd->fields['ID_CONT'];
                                    $rsi = $conn->Execute($sql);
                                    if ($rsi->RecordCount() > 0) {
                                        $objEnvio->municipio = $rsd->fields['MUNI_NOMB'];
                                        $objEnvio->departamento = $rsd->fields['DPTO_NOMB'];
                                        $objEnvio->pais = $rsd->fields['NOMBRE_PAIS'];
                                        //$this->continente = $rsd->fields['NOMBRE_CONT'];  //Esto no se guarda en sgd_renv_regenvio
                                    } else {
                                        $objEnvio->municipio = null;
                                        $objEnvio->departamento = null;
                                        $objEnvio->pais = null;
                                        //$this->continente = null; //Esto no se guarda en sgd_renv_regenvio
                                    }
                                }
                                $objEnvio->observacion = iconv('UTF-8','ISO-8859-1', $_POST['observa']);
                                $objEnvio->numguia = null;
                                $objEnvio->codpostal = $rsd->fields['SGD_DIR_CODPOSTAL'];

                                $ok = $objEnvio->enviarRadicado();
                                if ($ok == 1) {
                                    $out = array('success' => TRUE,
                                        'errors' => array(
                                            'reason' => ""
                                        )
                                    );
                                } else {
                                    $out = array('success' => FALSE,
                                        'errors' => array(
                                            'reason' => "Error transacción en BD para radicado $radicado"
                                        )
                                    );
                                }
                            } else {
                                $out = array('success' => FALSE,
                                    'errors' => array(
                                        'reason' => "Error al enviar correo del radicado $radicado."
                                    )
                                );
                            }
                        }
                    } else {
                        $out = array('success' => FALSE,
                            'errors' => array(
                                'reason' => "Correo del radicado $radicado no es estandar."
                            )
                        );
                    }
                }break;
        }
    }
} else
    die("Error al conectar BD");

echo json_encode($out);
?>