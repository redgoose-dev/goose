<?php
if (!defined('__GOOSE__')) return false;


/**
 * Goose library for external
 *
 * 외부에서 `Goose`를 사용하기 위한 라이브러리
 */


// check php version
require_once 'checkVersion.php';


// set start microtime
if (__GOOSE_DEBUG__)
{
	@define( '__StartTime__', array_sum(explode(' ', microtime())) );
}


// set absolute goose path
define( '__GOOSE_PWD__', str_replace('bootstrap/lib.php', '', str_replace('\\', '/', __FILE__)) );


// set session
if (defined('__USE_GOOSE_SESSION__'))
{
	$sess_id = (isset($_POST['sess_id'])) ? $_POST['sess_id'] : ( (isset($_GET['sess_id'])) ? $_GET['sess_id'] : null );
	if ($sess_id) session_id($sess_id);
	session_cache_expire(30);
	session_start();
	session_save_path(__GOOSE_PWD__);
}


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
	// load user config file
	$config = require_once(__GOOSE_PWD__ . 'data/config.php');

	// create and connect database
	$goose->createSpawn();
	$goose->spawn->connect($config['db']);

	// set table prefix
	define('__dbPrefix__', $config['table_prefix']);
	define('__token__', $config['token']);

	// set admin
	$goose->isAdmin = ($config['accessLevel']['admin'] == $_SESSION['goose_level']) ? true : false;

	// set user info
	if ($_SESSION['goose_name'])
	{
		$goose->user = (object)[];
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
else
{
	echo '<p>goose is not installed.</p>';
}