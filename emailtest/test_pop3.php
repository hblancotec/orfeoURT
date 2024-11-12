<?php
/*
 * test_pop3.php
 *
 * @(#) $Header: /opt2/ena/metal/pop3/test_pop3.php,v 1.7 2006/06/11 14:52:09 mlemos Exp $
 *
 */

?><HTML>
<HEAD>
<TITLE>Test for Manuel Lemos's PHP POP3 class</TITLE>
</HEAD>
<BODY>
<?php

	require("pop3.php");

  /* Uncomment when using SASL authentication mechanisms */
	/*
	require("sasl.php");
	*/

	$pop3=new pop3_class;
	//$pop3->hostname="pop.gmail.com";         /* POP 3 server host name                      */
	//$pop3->hostname="outlook.office365.com";    /* POP 3 server host name                      */
	$pop3->hostname="172.16.1.92";				/* POP 3 server host name                      */
	//$pop3->port=995;                         /* POP 3 server host port,	usually 110 but some servers use other ports Gmail uses 995 */
	//$pop3->port=995;
	$pop3->port=110;
	$pop3->tls=0;                            /* Establish secure connections using TLS      */
	//$user="hollmanlp";                        /* Authentication user name                    */
	//$user="hladino@dnp.gov.co";              /* Authentication user name                    */
	$user="radicacionorfeo";              /* Authentication user name                    */
	//$password="hladino99";                    /* Authentication password                     */
	//$password="DNP201405*";                    /* Authentication password                     */
	$password="Colombia2008";                    /* Authentication password                     */
	$pop3->realm="";                         /* Authentication realm or domain              */
	$pop3->workstation="";                   /* Workstation for NTLM authentication         */
	$apop=0;                                 /* Use APOP authentication                     */
	$pop3->authentication_mechanism="USER";  /* SASL authentication mechanism               */
	$pop3->debug=1;                          /* Output debug information                    */
	$pop3->html_debug=1;                     /* Debug information is in HTML                */
	$pop3->join_continuation_header_lines=1; /* Concatenate headers split in multiple lines */
	
	echo "<b>Inicio script</b> ".date("Ymd H:i:s u")."<br/>";
	echo "<b>Antes abrir conexion servidor pop</b> ".date("Ymd H:i:s u")."<br/>";
	if(($error=$pop3->Open())=="")
	{	echo "<b>Abierta conexion servidor pop</b> ".date("Ymd H:i:s u")."<br/>";
		echo "<PRE>Connected to the POP3 server &quot;".$pop3->hostname."&quot;.</PRE>\n";
		echo "<b>Antes de loguear usuario</b> ".date("Ymd H:i:s u")."<br/>";
		if(($error=$pop3->Login($user,$password,$apop))=="")
		{	echo "<b>Usuario Logueado</b> ".date("Ymd H:i:s u")."<br/>";
			echo "<PRE>User &quot;$user&quot; logged in.</PRE>\n";
			if(($error=$pop3->Statistics($messages,$size))=="")
			{	echo "<b>Leida estadisticas de la cuenta</b> ".date("Ymd H:i:s u")."<br/>";
				echo "<PRE>There are $messages messages in the mail box with a total of $size bytes.</PRE>\n";
				$result=$pop3->ListMessages("",0);
				if(GetType($result)=="array")
				{
					for(Reset($result),$message=0;$message<count($result);Next($result),$message++)
						echo "<PRE>Message ",Key($result)," - ",$result[Key($result)]," bytes.</PRE>\n";
					$result=$pop3->ListMessages("",1);
					if(GetType($result)=="array")
					{
						for(Reset($result),$message=0;$message<count($result);Next($result),$message++)
							echo "<PRE>Message ",Key($result),", Unique ID - \"",$result[Key($result)],"\"</PRE>\n";
						if($messages>0)
						{
							if(($error=$pop3->RetrieveMessage(1,$headers,$body,2))=="")
							{
								echo "<PRE>Message 1:\n---Message headers starts below---</PRE>\n";
								for($line=0;$line<count($headers);$line++)
									echo "<PRE>",HtmlSpecialChars($headers[$line]),"</PRE>\n";
								echo "<PRE>---Message headers ends above---\n---Message body starts below---</PRE>\n";
								for($line=0;$line<count($body);$line++)
									echo "<PRE>",HtmlSpecialChars($body[$line]),"</PRE>\n";
								echo "<PRE>---Message body ends above---</PRE>\n";
								if(($error=$pop3->DeleteMessage(1))=="")
								{
									echo "<PRE>Marked message 1 for deletion.</PRE>\n";
									if(($error=$pop3->ResetDeletedMessages())=="")
									{
										echo "<PRE>Resetted the list of messages to be deleted.</PRE>\n";
									}
								}
							}
						}
						if($error==""
						&& ($error=$pop3->Close())=="")
							echo "<PRE>Disconnected from the POP3 server &quot;".$pop3->hostname."&quot;.</PRE>\n";
						echo "<b>Cerrada conexion servidor pop</b> ".date("Ymd H:i:s u")."<br/>";
						
					}
					else
						$error=$result;
				}
				else
					$error=$result;
			}
		}
	}
	if($error!="")
		echo "<H2>Error: ",HtmlSpecialChars($error),"</H2>";
?>

</BODY>
</HTML>
