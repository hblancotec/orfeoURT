<html>
    <head>
        <title>{NOMBRESISTEMA}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link rel="shortcut icon" href="/favicon.ico">
        <script>
        function cerrar_ventana() {
            window.close();
        }
        </script>
    </head>
        <frameset rows="75,864*" frameborder="NO" border="0" framespacing="0" cols="*">
            <frame name="topFrame" scrolling="NO" noresize src="{TOP_ARCHIVO}?{DATOS_SESION}">
            <frameset cols="115,900" border="0" framespacing="0" rows="*">
                <frame name="leftFrame" scrolling="AUTO" src="{LEFT_ARCHIVO}?{DATOS_SESION}" marginwidth="0" marginheight='0'>
                <frame name="mainFrame" src="{MAIN_ARCHIVO}?{DATOS_SESION_LOG}" scrolling="AUTO">
            </frameset>
        </frameset>
</html>
