<?php
session_start();
$ruta_raiz = "..";

$krd = (!empty($_POST['krd'])) ? $_POST['krd'] : $_GET['krd'];

if (count($_SESSION) == 0) {
    //die(include "../sinacceso.php");
    //exit();
}

extract($_GET, EXTR_SKIP);
extract($_POST, EXTR_OVERWRITE);
extract($_SESSION, EXTR_OVERWRITE);

if ($_SESSION['usuaPermRadEmail'] != 1) {
   // die(include "../sinpermiso.php");
    //exit();
}

//$CLIENT_ID = "fcf56d33-1e66-4894-b57c-6269498967af";
//$CLIENT_SECRET = "pzp8Q~O0JZ0Wq8VOE.h7kNGD_2ijSufc6sk9Zder";

$CLIENT_ID = "6701f011-7fdb-40c6-9b68-5969512c05e1";
$CLIENT_SECRET = "igk8Q~Aqq0vTFA_8pme5D3FTEYEfinBFGcwRSclk";
$TENANT = "04260e20-234c-4c9f-a9dd-79286b1b70ac";
//$SCOPE = "openid%20profile%20offline_access%20https%3A%2F%2Foutlook.office365.com%2FSMTP.Send%20https%3A%2F%2Foutlook.office365.com%2FPOP.AccessAsUser.All%20https%3A%2F%2Foutlook.office365.com%2FIMAP.AccessAsUser.All";
$SCOPE = "https://outlook.office365.com/IMAP.AccessAsUser.All";
$REDIRECT_URI="https://orfeo.dnp.gov.co/email2/index.php";

//$url2 = "https://login.microsoftonline.com/04260e20-234c-4c9f-a9dd-79286b1b70ac/oauth2/v2.0/authorize?response_type=code&scope=".$SCOPE."&redirect_uri=".$REDIRECT_URI."&client_id=".$CLIENT_ID."&client_secret=".$CLIENT_SECRET."&state=IAFjZEc-Gbx5pPSOj2jnjc-MSiNTyiydFB-rO_BxcuA&prompt=login";
$url2 = "https://login.microsoftonline.com/".$TENANT."/oauth2/v2.0/authorize?krd=$krd&response_type=code&scope=".$SCOPE."&redirect_uri=".$REDIRECT_URI."&client_id=".$CLIENT_ID."&client_secret=".$CLIENT_SECRET."&state=IAFjZEc-Gbx5pPSOj2jnjc-MSiNTyiydFB-rO_BxcuA&prompt=login&response_mode=query";

if (!isset($_GET['code'])) {
    
    header('Location: ' . $url2);
    exit;
    
} elseif (empty($_GET['state'])) {
    unset($_SESSION['oauth2state']);
    unset($_SESSION['provider']);
    exit('Invalid state');
} else {
    unset($_SESSION['provider']);
    
    $CODE = $_GET['code'];
    $SESSION = $_GET['session_state'];
    $STATE = $_GET['state'];
    
    $url = "https://login.microsoftonline.com/$TENANT/oauth2/v2.0/token";
   
    //$data = "client_id=".$CLIENT_ID."&redirect_uri=".$REDIRECT_URI."&client_secret=".$CLIENT_SECRET."&code=".$CODE."&grant_type=authorization_code&scope=$SCOPE";
    $data = "client_id=".$CLIENT_ID."&redirect_uri=".$REDIRECT_URI."&client_secret=".$CLIENT_SECRET."&code=".$CODE."&session_state=".$SESSION."&grant_type=authorization_code&scope=$SCOPE";
    try
    {
        //echo $data;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/x-www-form-urlencoded' ));
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
    }
    catch (Exception $exception)
    {
        var_dump($exception);
    }
    
    $out2 = json_decode($output, true);
    $get_access_token = $out2['access_token'];
    $get_refresh_token = $out2['refresh_token'];
    /*$arraytoreturn = Array(
        'access_token' => $out2['access_token'],
        'refresh_token' => $out2['refresh_token'],
        'expires_in' => $out2['expires_in']
    );*/
    //echo "Get access toke and refresh token in office 365 using PHP<br>";
    //echo "access token :: ".$get_access_token."<br>";
    ///echo "refresh token :: ".$get_refresh_token."<br>";
    $_SESSION['access_token'] = $get_access_token;
    
    $tipo_carp = $tipo_carpp;
    $encabezado = session_name() . "=" . session_id() . "&krd=$krd";
}

?>
<html>
<head>
<title>Email Entrante - OrfeoGPL.org</title>
</head>
<frameset rows="30%,70%" border="10" name="filas">
	<frame name="radicar" src="blanco.html" resize=true />
	<frameset cols="50%,*">
		<frame name="formulario" src="browse_mailbox.php?<?= $encabezado ?>" resize=true />
		<frame name="image" src="image.php?<?= $encabezado ?>" />
	</frameset>
</frameset>
</html>