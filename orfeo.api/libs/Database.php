<?php
include 'adodb/adodb-exceptions.inc.php'; 
include 'adodb/adodb.inc.php';
class Database 
{
    //Variable para almacenar cualquier mensaje de error!
    var $error="";
    public function __construct($DB_TYPE, $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS)
    {
        try
        {
            $this->conn = NewADOConnection($DB_TYPE);
            $this->conn->SetFetchMode(ADODB_FETCH_ASSOC);
            $this->conn->Connect($DB_HOST,$DB_USER,$DB_PASS,$DB_NAME);
        }
        catch(ADODB_Exception $ex)
        {
            echo  "Error en AdoDB:".$ex->getMessage();
        }
    }
    
    /**
     * select
     * @param string $sql An SQL string
     * @param array $array Paramters to bind
     * @param constant $fetchMode A PDO Fetch mode
     * @return RecordSet
     */
    public function select($sql, $array = array(), $COUNTRECS_ADO=false)
    {
        if(isset($COUNTRECS_ADO) && $COUNTRECS_ADO)
        {
            $ADODB_COUNTRECS=true;
        }
        $sth = $this->conn->prepare($sql);
        $rs = $this->conn->execute($sth,$array);
        $ADODB_COUNTRECS=false;
        return $rs;
    }
    
    /**
     * insert
     * @param string $table A name of table to insert into
     * @param string $data An associative array
     */
    public function insert($table, $data)
    {
        try{
        ksort($data);
            return $this->conn->AutoExecute($table,$data,'INSERT');
        }
        catch(Exception $e){
            
            return false;
        }
    }
    
    /**
     * update
     * @param string $table A name of table to insert into
     * @param string $data An associative array
     * @param string $where the WHERE query part
     */
    public function update($table, $data, $where)
    {
        ksort($data);
        return $this->conn->AutoExecute($table,$data,'UPDATE',$where);
    }
    
    /**
     * delete
     * 
     * @param string $table
     * @param string $where
     * @return integer Affected Rows
     */
    public function delete($table, $where)
    {
        return $this->conn->Execute("DELETE FROM $table WHERE $where ");
    }
    
}