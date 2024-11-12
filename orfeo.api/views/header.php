<!doctype html>
<html>
<head>
    <title><?php echo ((isset($this->title)) ?  $this->title : 'MVC'); ?></title>
    <link rel="stylesheet" href="<?php echo URL; ?>public/css/default.css" />
    <script type="text/javascript" src="<?php echo URL; ?>public/js/jquery.js"></script>
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

<?php Session::init(); ?>
    
<div id="header">

    <?php if (Session::get('loggedIn') == false):?>
        <a href="<?php echo URL; ?>help">Help</a>
    <?php endif; ?>    
    <?php if (Session::get('loggedIn') == true):?>
        <?php if (Session::get('role') == '1'):?>
        <a href="<?php echo URL; ?>index">Index</a>
        <a href="<?php echo URL; ?>radicado">Radicacion</a>
        <?php endif; ?>
        <a href="<?php echo URL; ?>index/logout">Logout</a>    
    <?php else: ?>
        <a href="<?php echo URL; ?>login">Login</a>
    <?php endif; ?>
</div>
    
<div id="content">
    
    