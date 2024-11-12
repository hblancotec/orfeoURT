<?php
class diasContador{
  var $db;
  var $inhabiles;
	function __construct($db){
		$this->db = $db;
		$queryFes = "SELECT SGD_FESTIVO AS FESTIVO FROM SGD_DIAS_FESTIVOS";
		$inhabiles = array();
		$rsf = $db->conn->Execute($queryFes);
		while (!$rsf->EOF) {
		    $inhabiles[] = $rsf->fields["FESTIVO"];
		    $rsf->MoveNext();
		}
		$this->inhabiles =  $inhabiles;
	}
	// devuelve fecha solo dias, sin horas minutos o segundos
	function solo_Dia($dt) {
	    $a = gmdate("Y", $dt);
	    $m = gmdate("m", $dt);
	    $d = gmdate("d", $dt);
	    $date = date('Y/m/d', mktime(0, 0, 0, $m, $d, $a));
	    return $date;
	}
	
	// devuelve si la fecha es dia habil
	function es_Habil($dt) {
	    $a = gmdate("Y", $dt);
	    $m = gmdate("m", $dt);
	    $d = gmdate("d", $dt);
	    $date = date('D', mktime(0, 0, 0, $m, $d, $a));
	    $habil = True;
	    // si es sabado o domingo
	    if ($date == 'Sat' or $date == 'Sun') {
	        $habil = False;
	    } else {
	        // si esta en festivos
	        if (in_array(date("Y/m/d", mktime(0, 0, 0, $m, $d, $a)), $this->inhabiles)) {
	            $habil = False;
	        }
	    }
	    return $habil;
	}
	
	// cuenta los dias habiles a partir de una fecha,
	// tomando como base un numero de dias especifico
	function dias_a_sumar($base, $dias) {
	    $dt = strtotime($this->solo_Dia(strtotime($base)));
	    $dtw = $dt;
	    $asumar = $dias;
	    
	    // para los dias a sumar
	    for ($y = 1; $y <= ($dias); $y++) {
	        if (!$this->es_Habil($dt, $this->inhabiles)) {
	            $asumar++;
	        }
	        $dt = $dt + 86400; //sumo un dia
	    }
	    
	    // fecha mas dias habiles en rango
	    $newdays = $dtw + (86400 * ($asumar - 1));
	    
	    // valida siguiente dia habil
	    $asumarH = $this->dias_HabilSiguiente($newdays, $this->inhabiles);
	    return ($asumar + $asumarH);
	}

	function dias_HabilSiguiente($base) {
	    $dt = $base;
	    $asumar = 0;
	    // dia no cae en dia habil
	    while (!$this->es_Habil($dt, $this->inhabiles)) {
	        $dt = $dt + 86400;
	        $asumar++;
	    }
	    return $asumar;
	}
	
	// cuenta los dias habiles a partir de una fecha,
	// tomando como base un numero de dias especifico
	function dias_a_salir($base, $dias) {
	    $dt = strtotime(solo_Dia(strtotime($base)));
	    $y = $dias;
	    
	    // para los dias a sumar
	    while ($y > 1) {
	        if ($this->es_Habil($dt, $this->inhabiles)) {
	            $y = $y - 1;//resto un dia si es habil
	        }
	        ;
	        $dt = $dt + 86400; //sumo un dia
	    }
	    ;
	    
	    // si cayo en un dia no habil
	    while (!$this->es_Habil($dt)) {
	        $dt = $dt + 86400; //sumo un dia
	    }
	    ;
	    
	    return $dt;
	}

	// cuenta los dias habiles a partir de una fecha
	// hasta otra fecha
	function dias_habiles($vence, $hoy) {
	    $daysSec = 86400;
	    $cuenta = 0;
	    $sigue = True;
	    $factor = 1;
	    $dias = 0;
	    $base = $hoy;
	    $final = $vence;
	    if ($vence < $hoy) {
	        $factor = -1;
	        $base = $vence;
	        $final = $hoy;
	    }
	    //$sigue = False;
	    while ($sigue == True) {
	        $newdate = $base + ($dias * $daysSec);
	        if ($newdate <= $final) {
	            if ($this->es_Habil($newdate)) {
	                $cuenta = $cuenta + 1;
	            }
	        } else {
	            $sigue = False;
	        }
	        $dias = $dias + 1;
	    }
	    return $cuenta * $factor;
	}

}