<?php
/* Copyright Redgoose <http://redgoose.me> */
if (!defined('__GOOSE__')) exit();


// check php version
require_once 'checkVersion.php';


// set start microtime
if (__GOOSE_DEBUG__)
{
	@define( '__StartTime__', array_sum(explode(' ', microtime())) );
}


// set absolute path
define( '__GOOSE_PWD__', str_replace('bootstrap/init.php', '', str_replace('\\', '/', __FILE__)) );


// set session
$sess_id = (isset($_POST['sess_id'])) ? $_POST['sess_id'] : ( (isset($_GET['sess_id'])) ? $_GET['sess_id'] : null );
if ($sess_id) session_id($sess_id);
session_cache_expire(30);
session_start();


// load autoload
require_once ('autoload.php');


// init blade
define( 'BLADE_CACHE', __GOOSE_PWD__ . 'data/cache' );
define( 'BLADE_VIEW', __GOOSE_PWD__ . 'mod' );
define( 'BLADEONE_MODE', 1);


// create Goose Instance
$goose = core\Goose::getInstance();
$goose->init();


// check install
// TODO : `!`삭제하기
if (!$goose->isInstalled())
{
	// set user config variables
	$dbConfig = null; // array
	$table_prefix = null; // string
	$accessLevel = null; // int
	$basic_module = null; // string

	// load user config file
	require_once(__GOOSE_PWD__.'data/config.php');

	// create and connect database
	$goose->createSpawn();
	$goose->spawn->connect($dbConfig);

	// set api_key
	define('__apiKey__', md5($apiKey));

	// set table prefix
	define('__dbPrefix__', $table_prefix);

	// set admin
	$goose->isAdmin = ($accessLevel['admin'] == $_SESSION['goose_level']) ? true : false;

	// act router module
	$router = new mod\Router\Router();
	$router->init($router->pwd.'map.php', $accessLevel);
}
else
{
	define( '__GOOSE_ROOT__', preg_replace('/\/$/', '', $_SERVER['REQUEST_URI']) );
	define('__dbPrefix__', ($_POST['dbPrefix']) ? $_POST['dbPrefix'] : null);

	// load install module
	$install = new mod\Install\Install();

	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$install->transaction();
	}
	else
	{
		$install->form();
	}
}


// end goose
core\Goose::end();