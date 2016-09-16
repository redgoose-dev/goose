<?php
if (!defined('__GOOSE__')) return false;

// TODO 작업이 필요함
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


// set goose pwd
define( '__GOOSE_PWD__', str_replace('core/lib.php', '', str_replace('\\', '/', __FILE__)) );


// set session
if (defined('__USE_GOOSE_SESSION__'))
{
	session_cache_expire(30);
	session_start();
	session_save_path(__GOOSE_PWD__);
}


// load classes
require_once(__GOOSE_PWD__.'core/classes/Object.class.php');
require_once(__GOOSE_PWD__.'core/classes/Util.class.php');
require_once(__GOOSE_PWD__.'core/classes/Goose.class.php');
require_once(__GOOSE_PWD__.'core/classes/Spawn.class.php');
require_once(__GOOSE_PWD__.'core/classes/Module.class.php');


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

	// set table prefix
	define('__dbPrefix__', $table_prefix);

	// set admin
	$goose->isAdmin = ($accessLevel['admin'] == $_SESSION['goose_level']) ? true : false;

	// set user info
	if ($_SESSION['goose_name'])
	{
		$goose->user = new stdClass();
		$goose->user->srl = $_SESSION['goose_srl'];
		$goose->user->name = $_SESSION['goose_name'];
		$goose->user->email = $_SESSION['goose_email'];
		$goose->user->level = $_SESSION['goose_level'];
	}
	else
	{
		$goose->user = null;
	}
}