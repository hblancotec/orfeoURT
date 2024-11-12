<?php

//require "Envios.php";

class Certimail extends Envios {

    public function validarDatos() {
        return filter_var($this->correoe, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Realiza el envío del radicado por correo electronico y despues persistencia a BD.
     * @return int 1=Registro bd OK, 0=Registro bd ERROR.
     */
    public function enviarRadicado() {
        $this->conn->StartTrans();
        $nextval = $this->conn->GetOne("Select max(SGD_RENV_CODIGO) as VLRMAX FROM SGD_RENV_REGENVIO");
        $nextval++;

        $pesoArchivo = $this->devolverPeso();
        $this->pesoEnvio = ($pesoArchivo == false) ? 0 : $pesoArchivo;

        $valorEnvio = $this->devolverValorUnitario();

        $sql = "update ANEXOS set ANEX_ESTADO=4, ANEX_FECH_ENVIO= " . $this->conn->sysTimeStamp . " where RADI_NUME_SALIDA =".$this->radicado." and SGD_DIR_TIPO <>7 and SGD_DIR_TIPO ";
        //$sql .= ($this->dirTipo == NULL) ? " is null " : (($this->dirTipo != 1) ? " =7".str_pad($this->dirTipo, 2, 0, STR_PAD_LEFT) : "=1");
        $sql .= ($this->dirTipo == NULL) ? " is null " : (($this->dirTipo != 1) ? " =".$this->dirTipo : "=1");
        $rsu = $this->conn->Execute($sql);

        $sql = "INSERT INTO SGD_RENV_REGENVIO(  USUA_DOC,           SGD_RENV_CODIGO,	SGD_FENV_CODIGO,
                                                    SGD_RENV_FECH,	RADI_NUME_SAL,          SGD_RENV_DESTINO,
                                                    SGD_RENV_TELEFONO,	SGD_RENV_MAIL,          SGD_RENV_PESO,
                                                    SGD_RENV_VALOR,	SGD_RENV_CERTIFICADO,	SGD_RENV_ESTADO,
                                                    SGD_RENV_NOMBRE,	SGD_DIR_CODIGO,         DEPE_CODI,
                                                    SGD_DIR_TIPO,	RADI_NUME_GRUPO,	SGD_RENV_PLANILLA,
                                                    SGD_RENV_DIR,	SGD_RENV_DEPTO,         SGD_RENV_MPIO,
                                                    SGD_RENV_PAIS,	SGD_RENV_OBSERVA,	SGD_RENV_CANTIDAD,
                                                    SGD_RENV_NUMGUIA,	SGD_RENV_CODPOSTAL) VALUES(";
        $sql .= $this->cedula . ", $nextval, '" . $this->formaEnvio . "',";
        $sql .= $this->conn->OffsetDate(0, $this->conn->sysTimeStamp) . ", " . $this->radicado . ", " . (empty($this->destino) ? 'null' : "'".$this->destino."'" ) . ", ";
        $sql .= (empty($this->telefono) ? 'null' : "'".$this->telefono."'") . ", '" . $this->correoe . "', " . $this->pesoEnvio . ", ";
        $sql .= $valorEnvio . ", 0, 1, ";
        $sql .= "'" . $this->nombre . "', " . $this->codEnvio . "," . $this->dependencia . ", ";
        $sql .= $this->dirTipo . ", " . $this->radicado . ", " . (empty($this->planilla) ? 'null' : $this->planilla) . ", '";
        $sql .= $this->direccion . "', " . (empty($this->departamento) ? 'null' : "'" . $this->departamento . "'") . ", ";
        $sql .= (empty($this->municipio) ? 'null' : "'" . $this->municipio . "'") . ", " . (empty($this->pais) ? 'null' : "'" . $this->pais . "'") . ", ";
        $sql .= "'" . $this->observacion . "', 1, " . (empty($this->numguia) ? 'null' : "'" . $this->numguia . "'").",";
        $sql .= (empty($this->codpostal) ? '000000' : "'" . $this->codpostal . "')");
        $rsi = $this->conn->Execute($sql);
        $bandera = $this->conn->CompleteTrans() ? 1 : 0;
        return $bandera;
    }

    /**
     * Retorna el peso del documento principal del radicado en MB.
     * @return float
     */
    private function devolverPeso() {
        $sql = "SELECT RADI_PATH FROM RADICADO WHERE RADI_NUME_RADI=" . $this->radicado;
        $ruta = $this->conn->GetOne($sql);
        return filesize("../bodega/$ruta") / (1024 * 1024); //Retorna peso en MB
    }

    public function devolverValorUnitario() {
        $val = -1;

        if ($this->pesoEnvio >= 0 && $this->pesoEnvio < 5)
            $val = 1500;
        else if ($this->pesoEnvio >= 5 && $this->pesoEnvio < 10)
            $val = 3000;
        else if ($this->pesoEnvio >= 10 && $this->pesoEnvio < 15)
            $val = 4500;
        else $val = 0;

        return $val;
    }

}

?>