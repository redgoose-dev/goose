<?php
/* Copyright Redgoose <http://redgoose.me> */
if (!defined('__GOOSE__')) exit();


// set versions
define( '__GOOSE_MIN_PHP_VERSION__', '5.4.0' );
define( '__GOOSE_RECOMMEND_PHP_VERSION__', '5.5.0' );


// check php version
if(version_compare(PHP_VERSION, __GOOSE_MIN_PHP_VERSION__) <= 0)
{
	echo "The current php version ".PHP_VERSION.". Please upgrade to ".__GOOSE_RECOMMEND_PHP_VERSION__." or later.";
	exit;
}


// set error reporting
if(version_compare(PHP_VERSION, '5.4.0', '<'))
{
	@error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);
}
else
{
	@error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING ^ E_STRICT);
}


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


// set session
session_cache_expire(30);
session_start();
session_save_path(__GOOSE_PWD__);


// load classes
require_once(__GOOSE_PWD__.'core/classes/Util.class.php');
require_once(__GOOSE_PWD__.'core/classes/Goose.class.php');
require_once(__GOOSE_PWD__.'core/classes/Spawn.class.php');
require_once(__GOOSE_PWD__.'core/classes/Module.class.php');
require_once(__GOOSE_PWD__.'core/classes/Object.class.php');


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

	// act router module
	$router = Module::load('router');
	$router->init(__GOOSE_PWD__.$router->path.'map.php', $accessLevel);
}
else
{
	define( '__GOOSE_ROOT__', preg_replace('/\/$/', '', $_SERVER['REQUEST_URI']) );
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