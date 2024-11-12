#!/bin/bash
#Execute Cliente Java
#example
#java -jar ClienteServiciosJbpmv2.jar /home/orfeodev/hnino/public_html/orfeo_3.6p/webServices/Cliente/Xml/mensaje17-Apr-2009-050441.xml http://172.16.36.108:8080/ServiciosJbpmV2?wsdl AgregarVariablesInstanciaProceso
#echo  $1 $2 $3
export PATH=/home/jdk1.5.0_16/bin:$PATH
/home/jdk1.5.0_16/bin/java  -jar /var/www/orfeo/orfeo_3.6p/webServices/Cliente/ClienteServiciosJbpmv2.jar $1 $2 $3
#/home/jdk1.5.0_16/bin/java  -jar /var/www/orfeo/orfeo_3.6pruebas/webServices/Cliente/ClienteServiciosJbpmv2.jar $1 $2 $3
#echo  $PATH
