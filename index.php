<?php
header("content-type:text/html; charset=utf-8");
session_cache_expire(30);
session_start();

define('GOOSE', true);
define('PWD', dirname(__FILE__));
define('ROOT', '/goose');
define('URL', 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

require_once(PWD.'/libs/config.php');
?>
