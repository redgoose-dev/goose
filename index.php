<?php
header("content-type:text/html; charset=utf-8");
session_cache_expire(30);
session_start();
<<<<<<< HEAD
error_reporting(E_ALL & ~E_NOTICE);
=======
>>>>>>> dev-v0.3

define('GOOSE', true);
define('PWD', dirname(__FILE__));

require_once(PWD.'/libs/init.php');
?>
