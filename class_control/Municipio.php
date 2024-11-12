<?php
	if (!class_exists('ConnectionHandler'))
    	require_once ORFEOPATH . "include/db/ConnectionHandler.php";

    /**
     * Municipio es la clase encargada de gestionar las operaciones y 
     * los datos basicos referentes a un Municipio 
     * @author      Sixto Angel Pinzon
     * @version     1.0
     */
    class Municipio {
        /**
        * Variables que se corresponde con su par, uno de los campos de la tabla municipio
        * @var integer
        * @access public
        */
        var $muni_codi;
        var $muni_nomb;
        var $dpto_codi;
        var $cont_codi;
        var $pais_codi;
        /**
        * Gestor de las transacciones con la base de datos
        * @var ConnectionHandler
        * @access public
        */
        var $cursor;

        /** 
        * Constructor encargado de obtener la conexion
        * @param	$db	ConnectionHandler es el objeto conexion
        * @return   void
        */
        function __construct($db) {
            $this->cursor = $db;
        }


        /** 
        * Retorna el valor string correspondiente al atributo nombre del Municipio,
        * debe invocarse antes municipio_codigo()
        * @return   string
        */
        function get_muni_nomb() {
            return  $this->muni_nomb;
        }

        /** 
        * Retorna el valor entero correspondiente al atributo codigo del Municipio,
        * debe invocarse antes municipio_codigo()
        * @return   int
        */
        function get_dpto_codi() {
            return $this->dpto_codi;
        }

        /** 
        * Retorna el valor entero correspondiente al atributo codigo del Municipio,
        * debe invocarse antes municipio_codigo()
        * @return   int
        */
        function get_cont_codi() {
            return $this->cont_codi;
        }

        /** 
        * Retorna el valor entero correspondiente al atributo codigo del Municipio,
        * debe invocarse antes municipio_codigo()
        * @return   int
        */
        function get_pais_codi() {
            return $this->pais_codi;
        }

        /** 
        * Carga los datos de la instacia con
        * un codigo de Municipio suministrado
        * @param $codigoDep	int es el codigo del Departamento
        * @param $codigoMun	int	es el codigo del Municipio
        */
        function municipio_codigo($codigoDep,$codigoMun,$idPais=170, $idCont=1) {	
            // Si ingresn parametros validos
            if (strlen(trim($codigoDep))>0 && strlen (trim($codigoMun)) >0 ) {
                if (strpos($codigoMun,'-')) {
                    $codigoMun = explode('-', $codigoMun);
                    $codigo_pai = $codigoMun[0];
                    $codigo_dep = $codigoMun[1];
                    $codigo_mun = $codigoMun[2];
                    $q = "SELECT MUNI_CODI,
                                    MUNI_NOMB,
                                    DPTO_CODI,
                                    ID_PAIS,
                                    ID_CONT
                            FROM MUNICIPIO 
                            WHERE id_pais=$codigo_pai AND
                                    DPTO_CODI=$codigo_dep AND
                                    MUNI_CODI=$codigo_mun AND
                                    ID_CONT=$idCont AND
                                    ID_PAIS=$idPais
                                    ";
                } else {
                    $q= "select * from municipio
                            where muni_codi = $codigoMun and
                                    dpto_codi = $codigoDep and
                                    ID_CONT=$idCont AND
                                    ID_PAIS=$idPais";
                }
                $rs = $this->cursor->conn->Execute($q);
                if  (!$rs->EOF) {
                    $this->muni_codi = rtrim($rs->fields['MUNI_CODI']);
                    $this->dpto_codi = rtrim($rs->fields['DPTO_CODI']);
                    $this->pais_codi = rtrim($rs->fields['ID_PAIS']);
                    $this->cont_codi = rtrim($rs->fields['ID_CONT']);
                    $this->muni_nomb = rtrim($rs->fields['MUNI_NOMB']); 
                }
            } else {
                $this->cont_codi = "";
                $this->pais_codi = "";
                $this->muni_codi = "";
                $this->dpto_codi = ""; 
                $this->muni_nomb = "";
            }
        }
    }
?>
