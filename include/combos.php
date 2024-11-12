<?php
define ('ORFEOPATH', 'E:/OI_OrfeoPHP7_64/orfeo/');
    require_once (ORFEOPATH . '_conf/constantes.php');
    //$ruta_raiz = '../..';
    require_once(ORFEOPATH . "include/db/ConnectionHandler.php"); 
    class combo {	
        var $row;
        var $respuesta;
        var $cursor;

        function __construct($cur) {
            $this->cursor=$cur;
        }
        
        function conectar($dbsql,$valu,$tex,$verific,$muestreo,$simple) {
            $this->cursor->conn->SetFetchMode(ADODB_FETCH_ASSOC);
            $rs = $this->cursor->conn->Execute($dbsql);
            
            if ($rs === false) {
                echo "Error Fatal:";
                echo $this->cursor->conn->ErrorMsg();
                exit (1);
            }
            
            //esta opcion permite cargar en un select de html una consulta... tambien
            //se selecciona el campo ke va a actuar como valor y cual desplegado haci como el de verificacion
            
            if ($simple==0) {
                while(!$rs->EOF) {	
                    if(strcmp(trim($verific),trim($rs->fields[$valu]))==0) {
                        $sel="selected";
                    }
                    else $sel ="";
                    
                    echo "<option value='" . $rs->fields[strtoupper($valu)] . "' $sel>" . 
                                $rs->fields[strtoupper($tex)]."</option>\n";
                    $rs->MoveNext();
                }
            }
            $rs->Close();
        }
    }
?>
