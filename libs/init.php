<?
if(!defined("GOOSE")){exit();}

// set start time
if (DEBUG)
{
	@error_reporting(E_ALL);
	@define(__StartTime__, array_sum(explode(' ', microtime())));
}
else
{
	@error_reporting(E_ALL ^ E_NOTICE);
}


// load php files
require_once(PWD.'/libs/variable.php');
require_once(PWD.'/libs/Goose.class.php');
require_once(PWD.'/libs/Util.class.php');
require_once(PWD.'/libs/Database.class.php');
require_once(PWD.'/libs/Spawn.class.php');
require_once(PWD.'/libs/Router.class.php');
require_once(PWD.'/libs/functions.php');


// init goose class
$goose = Goose::getInstance();
$goose->init(PWD);


// session check
$_SESSION = $goose->util->checkArray(
	$_SESSION
	,array('gooseEmail', 'gooseName', 'gooseLevel')
);


// check install
if (!$goose->isInstalled())
{
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		require_once(PWD.'/transaction/install.php');
	}
	else
	{
		require_once(PWD.'/pages/install.php');
	}
	exit;
}


// init router
$router = new Router();

// route setting
$router->setBasePath(GOOSE_ROOT);
require_once(PWD.'/libs/route.maps.php');
$route = $router->matchCurrentRequest();

// route action
if ($route)
{
	$routePapameters = $route->getParameters();
	$routeTarget = $route->getTarget();
	$routeMethod = $route->getMethods();

	$paramController = (isset($routePapameters['controller'])) ? $routePapameters['controller'] : '';
	$paramAction = (isset($routePapameters['action'])) ? $routePapameters['action'] : '';
	$routeTarget['type'] = (isset($routeTarget['type'])) ? $routeTarget['type'] : '';

	switch ($routeTarget['type'])
	{
		case "api":
			$file = PWD.'/api/'. $routePapameters['type'] .'.php';
			if (is_file($file))
			{
				require($file);
			}
			else
			{
				$util->error(404);
				$util->out();
			}
			break;

		default:
			// login check
			if (!$_SESSION['gooseEmail'] and $paramController != 'auth' and !$routeTarget['type'])
			{
				$containerDirectory = PWD.'/pages/auth.php';
				require(PWD.'/pages/layout.php');
				$goose->out();
			}
			if ($paramController == 'auth' and $paramAction == 'logout')
			{
				require(PWD.'/transaction/auth.php');
				$goose->out();
			}

			$paramController = ($paramController) ? $paramController : 'index';
			$subdir = ($routeMethod[0] == 'POST') ? 'transaction' : 'pages';
			$containerDirectory = PWD.'/'.$subdir.'/'.$paramController.'.php';
			if (($routeMethod[0] == 'POST'))
			{
				if (is_file($containerDirectory))
				{
					require($containerDirectory);
				}
				else
				{
					$goose->error(404);
					$goose->out();
				}
			}
			else
			{
				if (is_file($containerDirectory))
				{
					require(PWD.'/pages/layout.php');
				}
				else
				{
					$goose->error(404);
					$goose->out();
				}
			}
			break;
	}
}
else
{
	$goose->error(404);
	$goose->out();
}

$goose->out();
?>