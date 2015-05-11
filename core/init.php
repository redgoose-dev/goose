<?php
/* Copyright Redgoose <http://redgoose.me> */


// set error reporting
if(version_compare(PHP_VERSION, '5.4.0', '<'))
{
	@error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);
}
else
{
	@error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING ^ E_STRICT);
}


if (!defined('__GOOSE__')) exit();


// Set Timezone as server time
if(version_compare(PHP_VERSION, '5.3.0') >= 0)
{
	date_default_timezone_set(@date_default_timezone_get());
}

// set start microtime
if (__GOOSE_DEBUG__)
{
	@define( '__StartTime__', array_sum(explode(' ', microtime())) );
}


// set absolute path
define( '__GOOSE_PWD__', str_replace('core/init.php', '', str_replace('\\', '/', __FILE__)) );


// set versions
define( '__GOOSE_VERSION__', '0.5' );
define( '__GOOSE_MIN_PHP_VERSION__', '5.3.0' );
define( '__GOOSE_RECOMMEND_PHP_VERSION__', '5.5.0' );


// set default module
define( '__GOOSE_DEFAULT_MODULE__', 'layout' );


// set session
session_cache_expire(30);
session_start();
session_save_path(__GOOSE_PWD__);


// load classes
require_once(__GOOSE_PWD__.'core/classes/Util.class.php');
require_once(__GOOSE_PWD__.'core/classes/Goose.class.php');
require_once(__GOOSE_PWD__.'core/classes/Spawn.class.php');
require_once(__GOOSE_PWD__.'core/classes/Module.class.php');
require_once(__GOOSE_PWD__.'core/classes/Router.class.php');


// create Goose Instance
$goose = Goose::getInstance();
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

	// set api_key
	define('__apiKey__', md5($apiKey));

	// set table prefix
	define('__dbPrefix__', $table_prefix);

	// set admin
	$goose->isAdmin = ($accessLevel['admin'] == $_SESSION['goose_level']) ? true : false;

	// set route
	$router = new Router();
	$router->setBasePath(preg_replace('/\/$/', '', __GOOSE_ROOT__));
	require_once(__GOOSE_PWD__.'data/route.map.php');
	$route = $router->matchCurrentRequest();

	// action route
	if ($route)
	{
		$routeParameters = $route->getParameters();
		$routeTarget = $route->getTarget();
		$routeMethod = $route->getMethods();

		$_module = (isset($routeParameters['module'])) ? $routeParameters['module'] : null;
		$_action = (isset($routeParameters['action'])) ? $routeParameters['action'] : null;
		$_method = (isset($routeMethod[0])) ? $routeMethod[0] : null;

		// check access level
		$auth = Module::load('auth', array(
			'action' => $_action,
			'method' => $_method
		));
		$auth->auth($accessLevel['login']);

		// load module
		$modName = ($_module) ? $_module : $basic_module;
		$baseModule = Module::load(
			$modName,
			array(
				'action' => $_action,
				'method' => $_method,
				'params' => array(
					$routeParameters['param0'],
					$routeParameters['param1'],
					$routeParameters['param2'],
					$routeParameters['param3']
				)
			)
		);

		// check module
		if (!$baseModule) Goose::error(101, 'module error');
		if (is_array($baseModule) && $baseModule['state'] == 'error') Goose::error(101, $baseModule['message']);

		// check index method
		if (!method_exists($baseModule, 'index')) Goose::error(101, $modName.'모듈의 index()메서드가 없습니다.');

		// index module
		if (method_exists($baseModule, 'index')) $baseModule->index();
	}
	else
	{
		Goose::error(404);
	}
}
else
{
	define( '__GOOSE_ROOT__', preg_replace('/index.php$/', '', $_SERVER['SCRIPT_NAME']) );
	define('__dbPrefix__', ($_POST['dbPrefix']) ? $_POST['dbPrefix'] : null);

	// load install module
	$install = Module::load('install');

	if ($install->name == 'install')
	{
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$install->transaction();
		}
		else
		{
			$install->form();
		}
	}
	else if ($install['state'] == 'error')
	{
		Goose::error(101, $install['message']);
	}
	else
	{
		Goose::error(101, 'module error');
	}
}


// end goose
Goose::end();