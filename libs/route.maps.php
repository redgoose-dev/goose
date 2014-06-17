<?php
if(!defined("GOOSE")){exit();}

$router->map('/');
$router->map('/install/', array('type' => 'install'), array('methods' => 'GET'));
$router->map('/install/', array('type' => 'install'), array('methods' => 'POST'));
$router->map('/api/:type/', array('type' => 'api'));
$router->map('/:controller/');
$router->map('/:controller/:action/', null, array('methods' => 'GET'));
$router->map('/:controller/:action/', null, array('methods' => 'POST'));
$router->map(
	'/:controller/:action/:param0/',
	null,
	array('filters' => array('param0'=>'(\d+)'))
);
$router->map(
	'/:controller/:action/:param0/:param1/',
	null,
	array('filters' => array('param0'=>'(\d+)', 'param1'=>'(\d+)'))
);
$router->map(
	'/:controller/:action/:param0/:param1/:param2/',
	null,
	array('filters' => array('param0'=>'(\d+)', 'param1'=>'(\d+)', 'param2'=>'(\d+)'))
);
$router->map(
	'/:controller/:action/:param0/:prarm1/:param2/:param3/',
	null,
	array('filters' => array('param0'=>'(\d+)', 'param1'=>'(\d+)', 'param2'=>'(\d+)', 'param3'=>'(\d+)'))
);
?>
