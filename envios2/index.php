<?php
session_start();
if (!$_SESSION['dependencia'])
    include "$ruta_raiz/rec_session.php";
$varLink = "?krd=" . $_SESSION['krd'];
?>
<!doctype html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>M&oacute;dulo de Env&iacute;s</title>

        <link rel="stylesheet" href="../pqr2/js/themes/redmond/jquery-ui-custom.css">
        <link rel="stylesheet" type="text/css" media="screen" href="./jqGrid/css/ui.jqgrid.css" />
        <link rel="stylesheet" href="./jMenu/jmenu.css" type="text/css" />
        <link rel="stylesheet" href="../estilos/orfeo.css" type="text/css" />

        <script type="text/javascript" src="../pqr2/js/jquery-1.7.1.js"></script>
        <script type="text/javascript" src="../pqr2/js/ui/jquery-ui.js"></script>
        <script type="text/javascript" src="./jMenu/jMenu.jquery.min.js"></script>
        <script type="text/javascript" src="./jqGrid/js/jquery.jqGrid.src.js"></script>
        <script type="text/javascript" src="./jqGrid/i18n/grid.locale-es.js"></script>

        <script type="text/javascript">
            $(document).ready(function(){
                // simple jMenu plugin called
                $("#jMenu").jMenu();
                // more complex jMenu plugin called
                $("#jMenu").jMenu({
                    ulWidth : 'auto',
                    effects : {
                        effectSpeedOpen : 150,
                        effectTypeClose : 'slide'
                    },
                    animatedText : false
                });
            });
        </script>
    </head>
    <body>
        <table width="100%" border="0" cellspacing="5" class="borde_tab">
            <tr class='titulos2'>
                <td>M&Oacute;DULO DE ENV&Iacute;OS &gt;&gt;</td>
                <td>
                    <ul id="jMenu">
                        <li>
                            <a>Env&iacute;o de correspondencia</a>
                            <ul>
                                <li><a href="../envios/cuerpoEnvioNormal.php<?= $varLink . "&estado_sal=3&estado_sal_max=3&nomcarpeta=Radicados Para Envio" ?>">Normal</a></li>
                                <li><a href="certimail.php">CertiM@il</a></li>
                                <li><a href="../envios/cuerpoModifEnvio.php<?= $varLink . "&estado_sal=4&estado_sal_max=4&devolucion=3" ?>">Modificaci&oacute;n Registro de Env&iacute;o</a></li>
                                <li><a href="../envios/cuerpoEnviofax.php<?= $varLink ?>">Fax</a></li>
                                <li><a href="../radsalida/cuerpo_masiva.php<?= $varLink . "&estado_sal=3&estado_sal_max=3" ?>">Masiva</a></li>
                                <li><a href="../radsalida/generar_envio.php<?= $varLink ?>">Generaci&oacute;n de planillas y Gu&iacute;as</a></li>
                                <li><a href="../radsalida/cargar_envio.php<?= $varLink ?>">Cargar No. Gu&iacute;as de env&iacute;o</a></li>
                            </ul>
                        </li>		
                        <li>
                            <a>Devoluciones</a>
                            <ul>
                                <li><a href="../devolucion/dev_corresp.php<?= $varLink . "&estado_sal=4&estado_sal_max=4" ?>">Tiempo de espera</a></li>
                                <li><a href="../devolucion/cuerpoDevOtras.php<?= $varLink . "&estado_sal=4&estado_sal_max=4&devolucion=1" ?>">Otras devoluciones</a></li>
                            </ul>
                        </li>
                        <li>
                            <a>Anulaciones</a>
                            <ul>
                                <li><a href="../anulacion/anularRadicados.php<?= $varLink . "&estado_sal=4&tpAnulacion=2" ?>">Anular radicados</a></li>
                            </ul>
                        <li>
                            <a>Reportes</a>
                            <ul>
                                <li><a href="../reportes/generar_estadisticas_envio.php<?= $varLink . "&estado_sal=4&estado_sal_max=4" ?>">Env&iacute;o de correo</a></li>
                                <li><a href="../reportes/generar_estadisticas.php<?= $varLink . "&estado_sal=4&estado_sal_max=4" ?>">Devoluciones</a></li>
                                <li><a href="../anulacion/cuerpo_RepAnula.php<?= $varLink . "&estado_sal=4&tpAnulacion=2" ?>">Anulaciones</a></li>
                            </ul>
                        </li>
                    </ul>
                </td>
            </tr>
        </table>