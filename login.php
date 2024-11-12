<?php
/** FORMULARIO DE LOGIN A ORFEO
 * Aqui se inicia session
 * @PHPSESID		String	Guarda la session del usuario
 * @db 					Objeto  Objeto que guarda la conexion Abierta.
 * @iTpRad				int		Numero de tipos de Radicacion
 * @$tpNumRad	array 	Arreglo que almacena los numeros de tipos de radicacion Existentes
 * @$tpDescRad	array 	Arreglo que almacena la descripcion de tipos de radicacion Existentes
 * @$tpImgRad	array 	Arreglo que almacena los iconos de tipos de radicacion Existentes
 * @query				String	Consulta SQL a ejecutar
 * @rs					Objeto	Almacena Cursor con Consulta realizada.
 * @numRegs		int		Numero de registros de una consulta
 */
// echo $_SERVER['HTTP_REFERER'];
if (! isset($_SERVER['HTTP_REFERER'])) {
    session_start();
    if (isset($_SESSION) && is_array($_SESSION) && count($_SESSION) > 0) {
        $krd = $_SESSION["login"];
        header('Location: indexFrames.php?fechah=' . date("dmy") . "_" . time("hms") . "&" . session_name() . "=" . trim(session_id()) . "&krd=$krd&swLog=1");
    } else
        session_destroy();
}
require_once "./_conf/constantes.php";
require_once "./config.php";
$sinSistema = false;
$permitirAcceso = false;
$usuarios = array();
$usuarios[] = 'mhernandez1';
$usuarios[] = 'ajmartinez';
$usuarios[] = 'jzbala';
$usuarios[] = 'hladino1';

// $usuarios[] = '';
$fechah = date("dmy") . "_" . time("hms");
$ruta_raiz = ".";
$usua_nuevo = 3;
$krd = $_POST['krd'];
$drd = $_POST['drd'];
foreach ($usuarios as $usuario) {
    if ($krd == $usuario) {
        $permitirAcceso = true;
        break;
    }
}

if (! empty($krd)) {
    if ($sinSistema && ! $permitirAcceso) {
        echo "<center><b>Sistema de Gestion Documental Orfeo se encuentra en Mantenimiento!!!</b></center>\n";
        echo "<br><center><b>Por favor Intente el Ingreso Mas Tarde</b></center>\n";
        echo "<center><br><b>Agradecemos su Compresi&oacute;n</b></center>\n";
        exit();
    }
    include ORFEOPATH . "session_orfeo.php";
    require_once ORFEOPATH . "class_control/Mensaje.php";
    if ($usua_nuevo == 0) {
        include ORFEOPATH . "contraxx.php";
        $ValidacionKrd = "NOOOO";
        if ($j = 1)
            die("<center> -- </center>");
    }

    /*
     * if (isset($_POST['action']) && ($_POST['action'] == 'process')) {
     * $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
     * $recaptcha_secret = '6Ld5W60eAAAAAK4GzIQboqFQN8vyUzHR6HUNjc0W';
     * $recaptcha_response = $_POST['recaptcha_response'];
     * $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
     * $recaptcha = json_decode($recaptcha);
     * if($recaptcha->score >= 0.7){
     * // A�ade aqu� el c�digo que desees en el caso de que la validaci�n sea correcta
     * }else{
     * die("<center> no tiene acceso </center>");
     * }
     * }
     */
}
$krd = strtoupper($krd);
$datosEnvio = "$fechah&" . session_name() . "=";
$datosEnvio .= trim(session_id()) . "&krd=$krd&swLog=1";
?>
<html>
<head>
<title>.:: ORFEO, M&oacute;dulo de validaci&oacute;n::.</title>
<link href="estilos/orfeo.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="imagenes/favicon.ico" />
<link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://unpkg.com/bs-brain@2.0.4/components/logins/login-4/assets/css/login-4.css">
<!-- <script
	src='https://www.google.com/recaptcha/api.js?render=6Ld5W60eAAAAADuQ5orpm_2ZyrynYhrD254ObBXH'> 
</script> -->
<!-- <script>
    grecaptcha.ready(function() {
        grecaptcha.execute('6Ld5W60eAAAAADuQ5orpm_2ZyrynYhrD254ObBXH', {action: 'formulario'})
        .then(function(token) {
        var recaptchaResponse = document.getElementById('recaptchaResponse');
        recaptchaResponse.value = token;
        });
    });
</script> -->

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_validateForm() { //v4.0
  var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
  for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=MM_findObj(args[i]);
    if (val) { nm=val.name; if ((val=val.value)!="") {
      if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
        if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
      } else if (test!='R') { num = parseFloat(val);
        if (isNaN(val)) errors+='- '+nm+' must contain a number.\n';
        if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
          min=test.substring(8,p); max=test.substring(p+1);
          if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
    } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' es requerido.\n'; }
  } if (errors) alert('Asegurese de entrar usuario y password correctos:\n'+errors);
  document.MM_returnValue = (errors == '');
}
// Script Source: CodeLifter.com
// Copyright 2003
// Do not remove this header
//-->
</script>
<script>
isIE=document.all;
isNN=!document.all&&document.getElementById;
isN4=document.layers;
isHot=false;
var tempX = 0;
var tempY = 0;
//alert(isN4);
function ddInit(e){
  hotDog=isIE ? event.srcElement : e.target; 
  topDog=isIE ? "BODY" : "HTML";
  //capa = 
  while (hotDog.id.indexOf("Mensaje")==-1&&hotDog.tagName!=topDog){
    hotDog=isIE ? hotDog.parentElement : hotDog.parentNode;
  } 
  size=hotDog.id.length;
  capa = (hotDog.id.substring(size-1,size)); //returns "exce"
  whichDog=isIE ? document.all.theLayer : document.getElementById("capa"+capa);
  if (hotDog.id.indexOf("Mensaje")!=-1){
    offsetx=isIE ? event.clientX : e.clientX;
    offsety=isIE ? event.clientY : e.clientY;
    nowX=parseInt(whichDog.style.left);
    nowY=parseInt(whichDog.style.top);
    ddEnabled=true;
    document.onmousemove=dd;
  }
}

function dd(e){
  if (!ddEnabled) return;
  whichDog.style.left=isIE ? nowX+event.clientX-offsetx : nowX+e.clientX-offsetx;
  whichDog.style.top=isIE ? nowY+event.clientY-offsety : nowY+e.clientY-offsety;
  return false; 
}

function ddN4(layer){
 isHot=true;
 // if (!isN4) return;
  if (document.layers) isN4=document.layers
   	else if (document.all)  isN4= document.all[layer];
  		else if (document.getElementById)  isN4= document.getElementById(layer); 
  N4 = document.getElementById(layer); 
  //alert (document.all);
 if (document.all) 
  	alert ("hay documento ");
 // N4 = isN4;
 // alert (document.layers);
   //alert ("va...");
  // alert (N4); 
  window.captureEvents(Event.MOUSEDOWN|Event.MOUSEUP);
   N4.onmousedown=function(e){
   tempX = e.pageX;
   tempY = e.pageY; 
  }
  
  isN4.onmousemove=function(e){
    if (isHot){
      if (document.layers){ document.layers[layer].left = e.pageX-tempX;}
	  else if (document.all){document.all[layer].style.left=e.pageX-tempX;}
	  else if (document.getElementById){document.getElementById(layer).style.left=e.pageX-tempX; }
	  // Set ver 
	 if (document.layers){document.layers[layer].top = e.pageY-tempY;}
	 else if (document.all){document.all[layer].style.top=e.pageY-tempY;}
	 else if (document.getElementById){document.getElementById(layer).style.top=e.pageY-tempY}

	 // N4.moveBy( e.pageX-tempX,e.pageY-tempY);
      return false;
    }
  }
  N4.onmouseup=function(){
   // N4.releaseEvents(Event.MOUSEMOVE);
  }
}

function hideMe(layer){
  if (document.layers) document.layers[layer].visibility = 'hide';
   	else if (document.all)  	document.all[layer].style.visibility = 'hidden';
  		else if (document.getElementById) document.getElementById(layer).style.visibility = 'hidden';
}

function showMe(layer){
  if (document.layers) document.layers[layer].visibility = 'show';
   	else if (document.all)  	document.all[layer].style.visibility = 'visible';
  		else if (document.getElementById) document.getElementById(layer).style.visibility = 'visible'; 
}

document.onmousedown=ddInit;
document.onmouseup=Function("ddEnabled=false");

</script>
<script>
function loginTrue() {
	document.formulario.submit();
}
</script>
</head>
<style type="text/css">
/* Estilos generales del cuerpo */
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
            background: rgba(0, 0, 0, 0.5) url('./img/fondol.jpg') no-repeat center center; /* Imagen de fondo semi-transparente */
            background-size: cover;
            color: #fff;
            position: relative;
        }

        /* Contenedor principal */
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
            position: relative;
        }

        /* Caja de login centrada */
        .login-box {
            background: rgba(255, 255, 255, 0.9); /* Fondo blanco semi-transparente */
            padding: 40px;
            border-radius: 20px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.2);
            z-index: 1; /* Asegura que esté sobre la imagen grande */
        }

        /* Contenedor para la imagen "orfeo2" y la palabra "ORFEO" alineados */
        .header-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
        }

        /* Imagen principal dentro del login */
        .login-image {
            width: 395px;
            height: auto;
            margin-right: 1px;
        }

        /* Texto "ORFEO" alineado con la imagen */
        .login-title {
            font-size: 2.5em;
            font-family: 'Courier New', Courier, monospace;
            color: #007bff;
        }

        /* Estilos de los inputs del formulario */
        .login-box input {
            width: calc(100% - 120px);
            padding: 12px;
            margin: 12px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
            outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        /* Cambia el estilo del input al hacer foco */
        .login-box input:focus {
            border-color: #007bff;
            box-shadow: 0px 0px 5px rgba(0, 123, 255, 0.5);
        }

        /* Estilo del botón */
        .login-box button {
            width: calc(100% - 180px);
            padding: 12px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            margin-top: 15px;
            transition: background-color 0.3s ease;
        }

        /* Efecto hover en el botón */
        .login-box button:hover {
            background-color: #0056b3;
        }

        /* Imagen "urth" diagonal y más grande */
        .urth-image {
            position: absolute;
            top: 15%; /* Posicionada más arriba */
            left: 5%; /* Más hacia la izquierda */
            width: 350px; /* Imagen más grande */
            height: auto;
            transform: rotate(deg); /* Imagen ligeramente rotada para orientación diagonal */
            z-index: 0; /* Detrás del login */
        }

        /* Estilos para pantallas pequeñas */
        @media (max-width: 768px) {
            .login-box {
                width: 100%;
                padding: 20px;
            }

            .login-image {
                width: 60px;
            }

            .login-title {
                font-size: 2em;
            }

            .urth-image {
                width: 200px; /* Reduce el tamaño de la imagen "urth" en pantallas pequeñas */
                left: 5%;
                top: 10%; /* Ajusta la posición en pantallas pequeñas */
                transform: rotate(-10deg); /* Menos rotación en pantallas pequeñas */
            }
        }
</style>
<body valign="center">
	<form name="formulario"
		action='indexFrames.php?fechah=<?=$datosEnvio?>' method="post">
		<input type="hidden" name="orno" value="1" />
<?php
$mostrarLogin = '<script>' . "\n";
$mostrarLogin .= 'loginTrue();' . "\n";
$mostrarLogin .= '</script>' . "\n";
if ($ValidacionKrd == "Si") {
    echo $mostrarLogin;
}
?>
<input type="hidden" name="ornot" value="1" />
	</form>
	<?php 
	
	if ($mensajeError != "") {
	    echo '<script type="text/JavaScript">
	    alert("USUARIO O PASSWORD INCORRECTOS \n INTENTE DE NUEVO");
	    </script>'; 
	}
	
	?>
    <div class="container">
    		
            <!-- Imagen "urth" más grande y colocada en diagonal -->
            <img src="./img/urth.png" alt="Imagen Urth" class="urth-image">
    		            <!-- Caja de login centrada -->
            <div class="login-box">
                <!-- Contenedor para la imagen y el texto alineados horizontalmente -->
                <div class="header-container">
                    <img src="./img/logoorfeo123.jpg" alt="Login Image" class="login-image">
                    <div class="login-title"></div>
                </div>
    
                <!-- Formulario de inicio de sesión -->
                <form autocomplete="off" action="login.php?fechah=<?=$fechah?>" method="post" onSubmit="MM_validateForm('krd','','R','drd','','R');return document.MM_returnValue" name="form33">
                    <input type="text" placeholder="Usuario" name="krd" id="krd" required>
                    <input type="password" placeholder="Contrase&ntilde;a" name="drd" id="drd" required>
                    <button type="submit">Acceder</button>
                </form>
            </div>
        </div>
		<!-- <section class="p-3 p-md-4 p-xl-5">
          <div class="container">
            <div class="card border-light-subtle shadow-sm">
              <div class="row g-0">
                <div class="col-12 col-md-6">
                  <img class="img-fluid rounded-start w-100 h-100 object-fit-cover" loading="lazy" src="./imagenes/Gestion-Documental.webp" alt="BootstrapBrain Logo">
                </div>
                <div class="col-12 col-md-6">
                  <div class="card-body p-3 p-md-4 p-xl-5">
                    <div class="row">
                      <div class="col-12">
                      	<div class="text-center mb-3">
        					<a href="#!"> <img src="./imagenes/logoOrfeo.png" alt="BootstrapBrain Logo"> </a>
        				</div>
                        <div class="mb-5">
                          <h2 class="fs-6 fw-normal text-center text-secondary mb-4">Iniciar sesi&oacute;n con su cuenta</h2>
                        </div>
                      </div>
                    </div>
                    <form action="#!">
                      <div class="row gy-3 gy-md-4 overflow-hidden">
                        <div class="col-12">
                          <label for="krd" class="form-label">Usuario <span class="text-danger">*</span></label>
                          <input type="text" class="form-control" name="krd" id="krd" required>
                        </div>
                        <div class="col-12">
                          <label for="password" class="form-label">Contrase&ntilde;a <span class="text-danger">*</span></label>
                          <input type="password" class="form-control" name="drd" id="drd" value="" required>
                        </div>
                        <div class="col-12">
                          <div class="form-check">
                            &nbsp;
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="d-grid">
                            <button class="btn bsb-btn-xl btn-primary" type="submit">INICIAR SESI&Oacute;N</button>
                          </div>
                        </div>
                        <div class="col-12 text-center">
							<img src="./img/escudo.jpg" alt="BootstrapBrain Logo" width="175" height="57">
						</div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section> 
	</form> -->
</body>
</html>
