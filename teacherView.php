<?php
$local = $_SERVER['REMOTE_ADDR']=='127.0.0.1' ? 1 : 0;
$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$refer_url = str_replace("elc","elctools",$actual_link);
header('Location: '.$refer_url);
exit();
?>