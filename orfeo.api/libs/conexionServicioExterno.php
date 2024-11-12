<?php

/*
 * autenticación con auth 0
 * conexión usando valores de BD
 */
/**
 * Clase para maenjar las conexiones a servicios externos, cuya definición se
 * encuentra en la tabla "servicioexterno", de la base de datos.
 */
class servicioExterno
{

    private $db;

    private $token;

    private $tipoConexion;

    private $metodo;

    private $url;

    private $parametros;

    private $respuesta;

    private $adicionalinfo;

    /**
     * Constructor de la clase
     *
     * @param [database] $db
     *            conexión para la BD
     */
    function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Función para ingresar un nuevo servicio a la BD
     *
     * @param string $nombre
     *            Con el que se accedera a la conexión
     * @param string $descripcion
     *            Breve descripción de la conexión
     * @param string $tipoconexion
     *            Sea REST o SOAP
     * @param string $metodo
     *            El metodo de la conexión
     * @param string $url
     *            Url a la que se debe conectar
     * @param string $parametros
     *            JSON con mapeo de parametros a variables de orfeo.
     * @param string $respuesta
     *            JSON con mapeo de variables de orfeo a parametros.
     * @param string $adicionalinfo
     *            En caso que se requiera hacer una modifiación a los valores
     * @return [database] respuesta de la ejecución
     */
    function nuevoServicio($nombre, $descripcion, $tipoconexion, $metodo, $url, $parametros, $respuesta, $adicionalinfo)
    {
        $sql = "insert into servicioexterno (
                nombre,
                descripcion,
                tipoconexion,
                metodo,
                url,
                parametros,
                respuesta,
                adicionalinfo
              )
              values(
                $nombre,
                $descripcion,
                $tipoconexion,
                $metodo,
                $url,
                $parametros,
                $respuesta,
                $adicionalinfo
              )";
        $rs = $this->db->conn->query($sql);
        return $rs;
    }

    function editarServicio()
    {
        $sql = "update servicioexterno
              set  = ''
              where id=";

        $rs = $this->db->conn->query($sql);
    }

    /**
     * Ejecución de la conexión seleccionada,
     * Guarda los valores de definición de la conexión
     * Determina la conexión y el tipo para ejecuta la función necesaria.
     *
     * @param string $nomConexion
     *            Nombre de la conexión a usar, guardado en BD
     * @return [database] Resultado de ejecutar la petición.
     */
    function conexion($nomConexion)
    {
        $sql = "select *
              from servicioexterno
              where nombre = '$nomConexion'";
        $rs = $this->db->conn->query($sql);

        $this->tipoConexion = $rs->fields["TIPOCONEXION"];
        $this->metodo = $rs->fields["METODO"];
        $url = $rs->fields["URL"];

        $pattern = '/^\'/'; // Si la cadena empieza con comilla (').
        if (preg_match($pattern, $url)) {
            $url = eval("return $url;"); // Evalua la variable en la url
        }
        $this->url = $url;

        $this->parametros = $rs->fields["PARAMETROS"];
        $this->respuesta = $rs->fields["RESPUESTA"];
        $this->adicionalinfo = $rs->fields["ADICIONALINFO"];

        // echo "<br>tipoConexion: $this->tipoConexion";
        // echo "<br>metodo: $this->metodo";
        // echo "<br>url: $this->url";
        // echo "<br>parametros: $this->parametros";
        // echo "<br>respuesta: $this->respuesta";
        // echo "<br>adicionalinfo: $this->adicionalinfo";

        $conexion = strtoupper($this->tipoConexion);
        $metodo = strtoupper($this->metodo);

        // echo "<br><br>--------------------------<br><br>";
        if ($conexion == 'REST') {
            // echo "petición REST<br>";
            switch ($metodo) {
                case 'GET':
                    // echo "metodo GET";
                    $rsp = $this->restGet();
                    break;
                case 'POST':
                    // echo "metodo POST";
                    $rsp = $this->restPost();
                    break;
                case 'PUT':
                    // echo "metodo PUT";
                    break;
                case 'DELETE':
                    // echo "metodo DELETE";
                    break;
                default:
                    // echo "metodo desconocido";
                    $rsp = false;
            }
        } elseif ($conexion == 'SOAP') {
            // echo "petición SOAP<br>";
            $rsp = false;
        } else {
            // echo "petición desconocida";
            $rsp = false;
        }

        // echo "<br><br>--------------------------<br><br>";
        return $rsp;
    }

    /**
     * Ejecuta el metodo POST, con los valores almacenados en $parametros
     * Guarda la información como se indica en $respuesta
     *
     * @return [database] Resultado del desglose y ejecución de la conexión.
     */
    private function restPost()
    {
        $url = $this->url;
        $parametros = json_decode($this->parametros, true);
        $respuesta = json_decode($this->respuesta, true);
        $adicionalinfo = json_decode($this->adicionalinfo, true);

        // echo "<br>";
        // echo "parametros: ";
        // var_dump($parametros);
        // echo "<br>";
        // echo "respuesta: ";
        // var_dump($respuesta);
        // echo "<br>";
        // echo "adicionalinfo: ";
        // var_dump($adicionalinfo);

        // TODO: cuadrar para si son peticiones previas aal envio o posteriores
        if ($adicionalinfo) {
            // echo "<br><br>adicionalinfo";
            $prepros = [];
            $postpros = [];
            foreach ($adicionalinfo as $clave => $valor) {
                if ($clave) {
                    $data[$clave] = $valor;
                }
            }
        }

        $data = [];
        if ($parametros) {
            // echo "<br>parametros";
            foreach ($parametros as $clave => $valor) {
                // echo "<br>$clave => $valor";
                $data[$clave] = eval("return $valor;");
            }
        }

        $body = json_encode($data);
        $authorization = $this->token ? 'Authorization: ' . $this->token : null;
        // echo "<br>Authorization: $authorization<br>";

        $ch = curl_init("$url");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            $authorization
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respBody = curl_exec($ch);
        curl_close($ch);

        $resp = json_decode($respBody, true);
        extract($resp); // convierte cada clave del arreglo en una variable

        // hace la conversión para guardar los valores recibidos en las variables
        if ($respuesta) {
            // echo "<br><br>respuesta";
            foreach ($respuesta as $clave => $valor) {
                // echo "<br>$clave => $valor";
                eval("$clave = $$valor;");
            }
        }

        // Realiza los cambios indicados en adicionalinfo si los hay
        if ($adicionalinfo) {
            // echo "<br><br>adicionalinfo";
            foreach ($adicionalinfo as $clave => $valor) {
                // echo "<br>$clave => $valor";
                eval("$valor;");
            }
        }
        return $respBody; // "<div><p>Se ejecuto sin problemas</p></div>";
    }

    /**
     * Ejecuta el metodo GET, con los valores almacenados en $parametros
     * Guarda la información como se indica en $respuesta
     *
     * @return [database] Resultado del desglose y ejecución de la conexión.
     */
    private function restGet()
    {
        $url = $this->url;
        $parametros = json_decode($this->parametros, true);
        $respuesta = json_decode($this->respuesta, true);
        $adicionalinfo = json_decode($this->adicionalinfo, true);

        // echo "<br>";
        // echo "parametros: ";
        // var_dump($parametros);
        // echo "<br>";
        // echo "respuesta: ";
        // var_dump($respuesta);
        // echo "<br>";
        // echo "adicionalinfo: ";
        // var_dump($adicionalinfo);

        $data = [];
        if ($parametros) {
            // echo "<br>parametros";
            foreach ($parametros as $clave => $valor) {
                // echo "<br>$clave => $valor";
                $data[eval("return $clave;")] = eval("return $valor;");
            }
        }

        $authorization = $this->token ? 'Authorization: ' . $this->token : null;
        // echo "<br>Authorization: $authorization";

        // Si hay parametors los adiciona a la url
        $url = $parametros ? $url . '?' . http_build_query($data) : $url;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            $authorization
        ));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $respBody = curl_exec($ch);
        curl_close($ch);

        $resp = json_decode($respBody, true);
        extract($resp); // convierte cada clave del arreglo en una variable

        // hace la conversión para guardar los valores recibidos en las variables
        if ($respuesta) {
            // echo "<br><br>respuesta";
            foreach ($respuesta as $clave => $valor) {
                // echo "<br>$clave => $valor";
                eval("$clave = $$valor;");
            }
        }

        // Realiza los cambios indicados en adicionalinfo si los hay
        if ($adicionalinfo) {
            // echo "<br><br>adicionalinfo";
            foreach ($adicionalinfo as $clave => $valor) {
                // echo "<br>$clave => $valor";
                eval("$valor;");
            }
        }

        return $respBody; // "<div><p>Se ejecuto sin problemas</p></div>";
    }

    function RegistrarMensaje($radicado, $asunto, $texto, $NombreDestinatario, $CorreoDestinatario, $Adjunto, $NombreArchivo, $url)
    {
        
        $xml = '<soapenv:Envelope xmlns:seal="http://www.sealmail.co/" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
               <soapenv:Header><wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd"><wsse:UsernameToken wsu:Id="UsernameToken-56A25A9935843352F317056112217587"><wsse:Username>ventanillabogota@urt.gov.co</wsse:Username><wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest">6FsqxAa7Ll85isw+xuVUsbaKbv8=</wsse:Password><wsse:Nonce EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary">9fzEzlUzKEc4JNmOHuNyWw==</wsse:Nonce><wsu:Created>2024-01-18T20:53:41.758Z</wsu:Created></wsse:UsernameToken></wsse:Security></soapenv:Header>
               <soapenv:Body>
                  <seal:RegistrarMensajeRequest>
                     <seal:idUsuario>ventanillabogota@urt.gov.co</seal:idUsuario>
                     <seal:Asunto>' . $asunto . '</seal:Asunto>
                     <seal:Texto>' . $texto . '</seal:Texto>
                     <seal:NombreDestinatario>' . $NombreDestinatario . '</seal:NombreDestinatario>
                     <seal:CorreoDestinatario>' . $CorreoDestinatario . '</seal:CorreoDestinatario>
                     <seal:Adjunto>' . $Adjunto . '</seal:Adjunto>
                     <seal:NombreArchivo>' . $NombreArchivo . '</seal:NombreArchivo>
                     <seal:Alertas>False</seal:Alertas>
                     <!--Optional:-->
                     <seal:Recordatorio>1</seal:Recordatorio>
                     <!--Optional:-->
                     <seal:CorreoCertificado>false</seal:CorreoCertificado>
                     <!--Optional:-->
                     <seal:FechaVencimiento></seal:FechaVencimiento>
                  </seal:RegistrarMensajeRequest>
               </soapenv:Body>
            </soapenv:Envelope>';

        $body = "<idUsuario>ventanillabogota@urt.gov.co</idUsuario><Asunto>$asunto</Asunto><NombreDestinatario>$NombreDestinatario</NombreDestinatario><CorreoDestinatario>$CorreoDestinatario</CorreoDestinatario><NombreArchivo>$NombreArchivo</NombreArchivo>";
        
        $sqli = "insert into log_servicio(radi_nume_radi,body,date_send,correo) values ($radicado,'$body', GETDATE(), '$CorreoDestinatario')";
        $rs = $this->db->conn->Execute($sqli);
        
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            
            $respBody = curl_exec($ch);
            $info = curl_getinfo($ch);
            $code = $info['http_code'];
            curl_close($ch);
            
            $mensaje = $this->readXML($respBody);
            
            $idMensaje1 = explode("\n", $mensaje)[0];
            $observa1 = explode("\n", $mensaje)[1];
            
            $idMensaje = explode("=", $idMensaje1)[1];
            $observa = explode("=", $observa1)[1];
            
            if ($idMensaje == "") {
                $idMensaje = 0;
            }
            $sqlu = "update log_servicio set response = '$mensaje', date_recibe = GETDATE(), status = '$code', idMensaje = $idMensaje, observacion = '$observa' where radi_nume_radi = $radicado and correo = '$CorreoDestinatario'";
            $rs = $this->db->conn->Execute($sqlu);
    
            if (! empty($code) and $code == 200) {
                if ($idMensaje < 0) {
                    $respuesta = array(
                        'Resultado' => false,
                        'Mensaje' => implode(",", $info),
                        'CodigoRespuesta' => $code
                    );
                } else {
                    $respuesta = array(
                        'Resultado' => true
                    );
                }
            } else {
                $respuesta = array(
                    'Resultado' => false,
                    'Mensaje' => implode(",", $info),
                    'CodigoRespuesta' => $code
                );
            }
        } catch (Exception $ex) {
            $respuesta = array(
                'Resultado' => false,
                'Mensaje' => "Error de interoperabilidad.",
                'CodigoRespuesta' => 0
            );
        }
        return $respuesta;
    }
    
    function readXML($texto){
        preg_match_all("|<ns1:RegistrarMensajeResponse>(.*)</ns1:RegistrarMensajeResponse>|sU", $texto, $items);
        $nodes = array();
        foreach ($items[1] as $key => $item) {
            preg_match("|<ns1:hash>(.*)</ns1:hash>|s", $item, $mensaje);
                
            $nodes = $mensaje[1];
        }
        
        return $nodes;
    }
    
    /*
     
    function createProcessReas($tipoExpediente, $idExpediente, $fechaExpediente, $radi_nume_radi, $fecharadicado, $remitente, $fechaAdenda, $nombreAdenda, $FechaDocumento, $nombreDocumento, $tipoAmenaza, $auth, $url)
    {
        //checkExist = "select true as exists from log_servicio where radi_nume_radi='$radi_nume_radi' and status='201'";
        // $rs = $this->db->conn->Execute($checkExist);
        // if (! $rs->EOF) {
        /// return array(
        // 'Resultado' => false,
        // 'Mensaje' => 'El proceso ya inicio'
        // );
        // }

        $authorization = "Authorization: Bearer $auth";
        $body = array(
            "tipoExpediente" => $tipoExpediente,
            "idExpediente" => $idExpediente,
            "fechaExpediente" => $fechaExpediente,
            "nombreDocumentoRecomendacion" => $nombreDocumento,
            "fechaDocumentoRecomendacion" => $FechaDocumento,
            "nombreAdenda" => $nombreAdenda,
            "fechaAdenda" => $fechaAdenda,
            "radicadoOrfeo" => $radi_nume_radi,
            "fechaRadicadoRecomendacion" => $fecharadicado,
            "remitente" => $remitente,
            "proyecto" => "",
            "tipoAmenaza" => $tipoAmenaza
        );
        
        // Enviar usuario radicador (orfeo, usuario web)
        // Enviar campo PROYECTO
        
        $body = json_encode($body);
        $sqli = "insert into log_servicio(radi_nume_radi,body,date_send)values('$radi_nume_radi','$body|$authorization',now()) RETURNING id;";
        $rs = $this->db->conn->Execute($sqli);
        $ids = $rs->fields['ID'];
        $ch = curl_init("$url");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            $authorization
        ));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        // curl_setopt($ch, CURLOPT_USERPWD, "wbadmin:wbadmin");
        $respBody = curl_exec($ch);
        $info = curl_getinfo($ch);
        $code = $info['http_code'];
        curl_close($ch);
        $resp = json_decode($respBody, true);
        $sqlu = "update log_servicio set response='$respBody | " . implode(",", $info) . "',date_recibe=now(),status='$code' where id='$ids'";
        $rs = $this->db->conn->Execute($sqlu);
        
        if (! empty($code) and $code == 201) {
            $respuesta = array(
                'Resultado' => true
            );
        } else {
            $respuesta = array(
                'Resultado' => false,
                'Mensaje' => $respBody,
                'CodigoRespuesta' => $code
            );
        }
        return $respuesta;
    }
    
    function listaProyectosReas($auth, $tipo_doci, $url, $usuario)
    {
        
        //$authorization = "Authorization: Bearer $auth";
        //$body = array(
        //    "radicadoOrfeo" => $radi_nume_radi,
        //    "idHistorico" => $jsonDatad['HISTORICO'],
        //    "NUPRE" => $jsonDatad['CHIP'],
        //    "numIdentificacion" => $documento,
        //    "tipoDocumento" => $tipo_doci,
        //    "tipoTramite" => $tipoTramite,
        //    "usuario_radicador" => $usuario,
        //    "proyecto" => $proyecto
        //);
        
        //$body = json_encode($body); 
        $ch = curl_init("$url");
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            $authorization
        ));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        // curl_setopt($ch, CURLOPT_USERPWD, "wbadmin:wbadmin");
        $respBody = curl_exec($ch);
        $info = curl_getinfo($ch);
        $code = $info['http_code'];
        curl_close($ch);
        $resp = json_decode($respBody, true);
        
        if (! empty($code) and $code == 200) {
            $respuesta = array(
                'Resultado' => true,
                'Mensaje' => $respBody
            );
        } else {
            $respuesta = array(
                'Resultado' => false,
                'Mensaje' => $respBody,
                'CodigoRespuesta' => $code
            );
        }
        return $respuesta;
    }
    
    function consultarTareasExpediente($auth, $url)
    {
        $authorization = "Authorization: Bearer $auth";
       
        $sqli = "insert into log_servicio(radi_nume_radi,body,date_send)values(0,'$url/$authorization',now()) RETURNING id;";
        $rs = $this->db->conn->Execute($sqli);
        $ids = $rs->fields['ID'];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); 
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        //curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        //curl_setopt($ch, CURLOPT_HTTPGET, true);
        //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        //curl_setopt($ch, CURLOPT_HEADER, true);
        //curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            $authorization
        ));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $respBody = curl_exec($ch);
        $info = curl_getinfo($ch);
        $code = $info['http_code'];
        curl_close($ch);
        $resp = json_decode($respBody, true);
        $sqlu = "update log_servicio set response='$respBody | " . implode(",", $info) . "',date_recibe=now(),status='$code' where id='$ids'";
        $rs = $this->db->conn->Execute($sqlu);
                
        if (! empty($code) and $code == 200) {
            $respuesta = array(
                'Resultado' => true,
                'Mensaje' => $respBody
            );
        } else {
            $respuesta = array(
                'Resultado' => false,
                'Mensaje' => $respBody ."-". curl_error($ch),
                'CodigoRespuesta' => $code
            );
        }
        return $respuesta;
    }
    
    function finlizarTarea($auth, $url, $opcion)
    {
        $authorization = "Authorization: Bearer $auth";
        
        if ($opcion == 1 || $opcion == 2) {
            $body = array(
                "accion" => "Notificar"
            );
        } else if ($opcion == 3 || $opcion == 4) {
            $body = array(
                "accion" => "Respuesta"
            );
        }
        $body = json_encode($body);
        
        $sqli = "insert into log_servicio(radi_nume_radi,body,date_send)values(0,'$url/$authorization',now()) RETURNING id;";
        $rs = $this->db->conn->Execute($sqli);
        $ids = $rs->fields['ID'];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        //curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        //curl_setopt($ch, CURLOPT_HTTPGET, true);
        //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        //curl_setopt($ch, CURLOPT_HEADER, true);
        //curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            $authorization
        ));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        $respBody = curl_exec($ch);
        $info = curl_getinfo($ch);
        $code = $info['http_code'];
        curl_close($ch);
        $resp = json_decode($respBody, true);
        
        $sqlu = "update log_servicio set response='$respBody | " . implode(",", $info) . "',date_recibe=now(),status='$code' where id='$ids'";
        $rs = $this->db->conn->Execute($sqlu);
        
        if (! empty($code) and $code == 200) {
            $respuesta = array(
                'Resultado' => true,
                'Mensaje' => $respBody
            );
        } else {
            $respuesta = array( 
                'Resultado' => false,
                'Mensaje' => $respBody ."-". curl_error($ch),
                'CodigoRespuesta' => $code
            );
        }
        return $respuesta;
    }
    
    */
}
