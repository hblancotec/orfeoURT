<?php

function checkldapuser($username, $password, $ldapServer) {
    $retorno = "";
    define('ADODB_LANG', 'es');
    require "config.php";
    require "adodb/adodb-exceptions.inc.php";
    require "adodb/adodb.inc.php";

    $username = strtolower($username);
    if ($password) {
        
        //$domain = 'urt.gov.co';
        try {
            $ds = ldap_connect($ldapServer, 389);
            ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
        } catch (Exception $ex)
        {
            echo $ex->getMessage();
        }
        
        $dn = "OU=Administracion Usuarios URT,DC=uaegrtd,DC=local";
        $bind = ldap_bind($ds, $username, $password);
        $isITuser = ldap_search($bind, $dn, '(&(objectClass=User)(sAMAccountName=' . $username. '))');
        if ($bind) {
            //echo("Login correct");
            $retorno = "";
        } else {
            //echo("Login incorrect");
            $retorno = "CREDENCIALES ERRONEAS.";
        }
        /*try {
            $ldap = NewADOConnection('ldapdnp');
            $ldap->SetFetchMode(ADODB_FETCH_ASSOC);
            
            $valida = false;
            $servers = explode(";", $ldapServer);
            foreach ($servers as $server)
            {
                try {
                    $rsLdap = $ldap->Connect($server, $username, $password, $cadenaBusqLDAP);
                    if (!$rsLdap) {
                        $valida = false;
                        continue;
                    } else {
                        $valida = true;
                        break;
                    }
                } catch (exception $e) {
                    $valida = false;
                    continue;
                }
            }
                		
    		if ($valida) {
    			$tmpUsr = (strpos($username, "@") !== FALSE) ? substr($username, 0, strpos($username, "@")) : $username;
    			$filter = "(|(sAMAccountName=$tmpUsr)(mail=$username))";
    			$row = $ldap->Execute( $filter );
    			if ($row->RecordCount() > 0) {
    				$dnUserDA = $row->fields['distinguishedName'];
    				$dnUserDA = is_array($dnUserDA) ? $dnUserDA[0] : $dnUserDA;
    				$rsLdap = $ldap->Connect($server, $dnUserDA, iconv("utf-8", "iso-8859-1", $password), $cadenaBusqLDAP);
    				if ($rsLdap) {
    	
    				} else {
    					$retorno = "CREDENCIALES ERRONEAS.";
    				}
    			} else {
    				$retorno = "USUARIO NO HALLADO EN LDAP.";
    			}
    		} else {
    			$retorno = "ERROR AUTENTICACI&Oacute;N ORFEO AD";
    		}
    		//$ldap->Close();
        } catch (exception $e) {
            $retorno = str_replace("!! LDAPDNP LDAPDNP: ", "", $e->msg);
        }*/
    } else {
        $retorno = "CREDENCIALES ERRONEAS.";
    }

    return $retorno;
}

?>
