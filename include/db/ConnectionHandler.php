<?php
    if (!defined('ORFEOPATH')) define('ORFEOPATH' , 'E:/OI_OrfeoPHP7_64/orfeo/');
    class ConnectionHandler {
        //Almacena un error, resultado de una transaccion
        /**
          * ESTA CLASE INICIA LA CONEXION A LA BD SELECCIONADA
          * @$conn  objeto  Variable que almacena la conexion;
          * @$driver char  Variable que almacena la bd Utilizada.
          * @$rutaRaiz char Indica la ruta para encontrar la ubicacion de la raiz de la aplicacion.
          * @$dirOrfeo char Directorio del servidor web en el cual se encuentra instalado Orfeo.
          *
          */
        var $Error;
        var $id_query;
        var $driver;
        var $rutaRaiz;
        var $conn;
        var $entidad;
        var $entidad_largo;
        var $entidad_tel;
        var $entidad_dir;
        var $querySql;
        
           /* Metodo constructor */
        function __construct($ruta_raiz) {
            
            if (!defined('ADODB_ASSOC_CASE')) define('ADODB_ASSOC_CASE',1);
            include_once "adodb/adodb.inc.php";
            include_once "adodb/adodb-paginacion.inc.php";
            include_once "adodb/tohtml.inc.php";
            include (ORFEOPATH . "config.php");
            $msgError = '<hr><font color=red>No se Pudo COnectar a la BD con Driver:';
            $msgError .= $driver;
            $msgError .= ", Usuario : $usuario";
            $msgError .= ", Servidor: $servidor";
            $msgError .= ", Servicio:' $servicio";
            $msgError .= "</font><hr>\n";
            $ADODB_COUNTRECS = false;
            $this->driver = $driver;
            $this->conn  = ADONewConnection($driver);
            $this->rutaRaiz = $ruta_raiz;
            $this->conn->setConnectionParameter('ReturnDatesAsStrings',false);
            $this->conn->setConnectionParameter('CharacterSet', 'UTF-8');
            $conexion = $this->conn->Connect($servidor,$usuario,$contrasena,$servicio);
            if (!$conexion) {
                echo $msgError;
                echo $this->conn->ErrorMsg();
            }
            $this->entidad = $entidad;
            $this->entidad_largo = $entidad_largo;
            $this->entidad_tel = $entidad_tel;
            $this->entidad_dir = $entidad_dir;
        }

        function imagen() {
            switch($this->entidad) {
                case "CRA":
                    $imagen = "png/logoCRA.gif";
                break;
                case "DNP":
                    $imagen = "png/logoDNP.gif";
                break;
                case "SSPD":
                    $imagen = "png/escudoColombia.jpg";
                break;
                default:
                    $imagen = "";
                break;
            }
            return($imagen);
        }

        //  Retorna False en caso de ocurrir error;
        function query($sql) {
            $cursor = $this->conn->Execute($sql);
            return $cursor;
        }

        
        /* Funcion miembro que realiza una consulta a la base de datos y devuelve un record set */

        function getResult($sql) {
            if ($sql == "") {
                $this->Error = "No ha especificado una consulta SQL";
                print($this->Error);
                return 0;
            }
            return ($this->query($sql));
        }


    /* Funcion miembro que ejecuta una instruccion sql a la base de datos. */
    /*
       Funcion miembro que recibe como parametros: nombre de la tabla, un array con los nombres de los campos,
       y un array con los valores respectivamente.
    */

        function insert($table,$record) {
		//return "Entro";
        $temp = array();
        $fieldsnames = array();
        foreach($record as $fieldName=>$field ) {
          $fieldsnames[] = $fieldName;
            $temp[] = $field;
        }
        $sql = "insert into " . $table . "(" . join(",",$fieldsnames) . ") values (" . join(",",$temp) . ")";
        if ($this->conn->debug==true) {
            echo "<hr>(".$this->driver.") $sql<hr>";
        }
            //$this->querySql = $sql;
			//$this->conn->querySql = $sql;
			//return $sql;
            return ($this->conn->Execute($sql));
        }

    /*
       Funcion miembro que recibe como parametros: nombre de la tabla,
       un array con los nombres de los campos
       ,un array con los valores, un array con los nombres de los campo id y
       un array con los valores de los campos id respectivamente.
    */
    function update($table, $record, $recordWhere) {
        $tmpSet = array();
        $tmpWhere = array();
        foreach($record as $fieldName=>$field ) {
            $tmpSet[] = $fieldName . "=" . $field;
        }

        foreach($recordWhere as $fieldName=>$field ) {
            $tmpWhere[] = " " . $fieldName . " = " . $field . " ";
        }
        $sql = "update " . $table ." set " . join(",",$tmpSet) . "    where " . join(" and ",$tmpWhere);
        if ($this->conn->debug==true) {
         echo "<hr>(".$this->driver.") $sql<hr>";
        }
		$this->querySql = $sql;
        return ($this->conn->Execute($sql));
    }

    /*
       Funcion miembro que recibe como parametros: nombre de la tabla, un array con los
       nombres de los campos id, y un array con los valores de los id.
    */
        function delete($table, $record) {
            $temp = array();

            foreach($record as $fieldName=>$field ) {
                $tmpWhere[] = "  " . $fieldName . "=" . $field;
            }
            $sql = "delete from " . $table . " where " . join(" and ",$tmpWhere);

            //print("*** $sql ****");
            if ($this->conn->debug==true) {
                echo "<hr>(".$this->driver.") $sql<hr>";
            }
            return ($this->query($sql));
        }

        function nextId($secName){
            if ($this->conn->hasGenID)
                return $this->conn->GenID($secName);
            else{
                $retorno=-1;

                if ($this->driver=="oracle"){
                    $q= "select $secName.nextval as SEC from dual";
                    $this->conn->SetFetchMode(ADODB_FETCH_ASSOC);
                    $rs=$this->query($q);
                    //$rs!=false &&
                    if  ( !$rs->EOF){
                        $retorno = $rs->fields['SEC'];
                        //print ("Retorna en la funcion de secuencia($retorno)");
                    }
                }
                return $retorno;
            }
        }
    }
?>
