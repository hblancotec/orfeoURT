<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" / >

	<?php 
    if (isset($this->css)) 
    {
        foreach ($this->css as $css)
        {
            echo '<link rel="stylesheet" href="'.URL.'views/'.$css.'" />';
        }
    }
    if (isset($this->js)) 
    {
        foreach ($this->js as $js)
        {
            echo '<script type="text/javascript" src="'.URL.'views/'.$js.'"></script>';
        }
    }
    ?>
	</head>
    <body>
    </body>
</html>