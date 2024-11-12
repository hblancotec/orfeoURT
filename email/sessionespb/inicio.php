<?php
session_start();

$_SESSION["valor1"] = "asdfadf sf asdf asdf<br>";
$_SESSION["valor12"] = "asdfadf123 sf asdf asdf<br>";
$_SESSION["valor13"] = "asdfadf sfasdf asdf asdf<br>";
$_SESSION["valor14"] = "asdfadf sf asasdfasddf asdf<br>";
$_SESSION["valor15"] = "asdfadf sf asdasdfaf asdf<br>";
$_SESSION["valor16"] = "asdfadf sf asdf asdadfdfassf<br>";
$_SESSION["valor17"] = "asdfadf sf asdf asadf<br>";
$_SESSION["valor18"] = "asdfadf sf asdf asdsdff<br>";
$_SESSION["valor19"] = "asdfadf sf asdf 56564654654asdf<abr>";



?>
<a href='otraPrueba.php'>Pasar a la Otra</a>

<form action='otraPrueba.php' method=post>
   <input type=submit value=enviar >
</form>