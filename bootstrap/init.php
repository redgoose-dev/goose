<?php
/* Copyright redgoose <http://redgoose.me> */
if (!defined('__GOOSE__')) exit();


// check php version
require_once 'checkVersion.php';


// set start micro time
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
if ($goose->isInstalled())
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
	// 인스톨이 안된 상태에서 다른 경로로 접속해 있다면 첫페이지로 강제이동
	if ($_SERVER['PHP_SELF'] !== $_SERVER['SCRIPT_NAME'])
	{
		$rootUrl = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
		Header('Location:'.$rootUrl);
		core\Goose::end();
	}

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