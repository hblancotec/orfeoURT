<?php
header("Cache-Control: private, no-cache, no-store, must-revalidate, max-age=0, proxy-revalidate, s-maxage=0"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
header("Location: login.php");
?> 