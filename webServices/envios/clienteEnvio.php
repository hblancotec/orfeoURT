<?php 
/* env�a la  informacion de  env�os hechos por orfeo a mediada que salen*/
/*  Desarrollado Hardy Deimont  Nino Velasquez */

function RegistrarInformacionEnvio ($radiNume,$expediente,$radicadoEnviado,$fechaEnvio,$medioEnvio) {
//$wsdl="http://172.16.36.45:8080/jbpm-services-jbpm-services/JBPMServices?wsdl";
require_once( '../webServices/configWS.php' );
//generacion xml a  enviar
 $arregloDatos[0]='<message>
 <dataSet/>
 <properties>
   <property>
     <name>radicado</name>
     <value class="string">'.$radiNume.'</value>
   </property>
   <property>
     <name>expediente</name>
     <value class="string">'.$expediente.'</value>
   </property>
   <property>
     <name>radicadoEnviado</name>
     <value class="string">'.$radicadoEnviado.'</value>
   </property>
   <property>
     <name>fechaEnvio</name>
     <value class="date">'.$fechaEnvio.'.393 COT</value>
   </property>
   <property>
     <name>medioEnvio</name>
     <value class="string">'.$medioEnvio.'</value>
   </property>
 </properties>
</message>';
    $client = new SoapClient($wsdl, array('location' => "$wsdl",'uri'=> "http://test-uri/",'style'=> SOAP_DOCUMENT,'use'=> SOAP_LITERAL));
       try {    
           $resultObject = $client->__soapCall('RegistrarInformacionEnvio',$arregloDatos );
       } catch (SoapFault $exception) {
                   print_r($exception);
       }
}

/**
 * envia la  informacion de  envio masivo  a  Jbpm 03-03-2009 / produccion 10/03/2009
 *  
 * @author Hardy Deimont Niño Velasquez 
 * @param Numeber $grupo
 * @param Date    $fechaEnvio
 * @param String $medioEnvio
 */

function RegistrarInformacionEnvioMasivo ($grupo,$fechaEnvio,$medioEnvio) {
//$wsdl="http://172.16.36.45:8080/jbpm-services-jbpm-services/JBPMServices?wsdl";
require_once( '../webServices/configWS.php' );
//generacion xml a  enviar
include_once "../include/db/ConnectionHandler.php";
$db = new ConnectionHandler("..",'WS');
 $inicio='<message><dataSet>';
 $fin='</dataSet><properties/></message>';
 $sql="select DISTINCT r.radi_nume_radi RADICADO, r.radi_nume_deri PADRE, e.sgd_exp_numero EXPEDIENTE
from radicado r,  SGD_RENV_REGENVIO s , sgd_exp_expediente e
where r.radi_nume_radi= s.radi_nume_sal and s.radi_nume_grupo=$grupo and e.radi_nume_radi=r.radi_nume_radi";
 $rs=$db->conn->Execute($sql);
 
 	while(!$rs->EOF)
	{
     $cuerpo.='
     <record>
           <value>
 		    <property>
     			<name>radicado</name>
				<value class="string">'.$rs->fields["PADRE"].'</value>
   			</property>
   			<property>
     			<name>expediente</name>
     			<value class="string">'.$rs->fields["EXPEDIENTE"].'</value>
   			</property>
   			<property>
     			<name>radicadoEnviado</name>
     			<value class="string">'.$rs->fields["RADICADO"].'</value>
   			</property>
   			<property>
     			<name>fechaEnvio</name>
     			<value class="date">'.$fechaEnvio.'.393 COT</value>
   			</property>
   			<property>
     			<name>medioEnvio</name>
     			<value class="string">'.$medioEnvio.'</value>
   		  </property>
   		  </value>
     </record>
  ';
   $rs->MoveNext();
}
 $arregloDatos[0]=$inicio.$cuerpo.$fin;

    $client = new SoapClient($wsdl, array('location' => "$wsdl",'uri'=> "http://test-uri/",'style'=> SOAP_DOCUMENT,'use'=> SOAP_LITERAL));
       try {    
           $resultObject = $client->__soapCall('RegistrarInformacionEnvioMasiva',$arregloDatos );
       } catch (SoapFault $exception) {
                   print_r($exception);
       }
}

?>