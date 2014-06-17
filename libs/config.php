<?
if(!defined("GOOSE")){exit();}

require_once(PWD.'/libs/Util.class.php');
require_once(PWD.'/libs/Router.class.php');
require_once(PWD.'/libs/functions.php');

$util = new Util();
$router = new Router();

// route setting
$router->setBasePath(ROOT);
require_once(PWD.'/libs/route.maps.php');
$route = $router->matchCurrentRequest();

if ($route)
{
	$routePapameters = $route->getParameters();
	$routeTarget = $route->getTarget();
	$routeMethod = $route->getMethods();

	$paramController = $routePapameters['controller'];
	$paramAction = $routePapameters['action'];

	if ($routeTarget['type'] == 'install')
	{
		if (file_exists(PWD."/data/config/user.php"))
		{
			header('location:'.ROOT);
		}

		if ($routeMethod[0] == 'POST')
		{
			require_once(PWD . '/transaction/install.php');
		}
		else
		{
			require_once(PWD . '/pages/install.php');
		}
		$util->out();
	}

	if (!file_exists(PWD."/data/config/user.php"))
	{
		header('location:'.ROOT.'/install/');
		exit;
	}

	require_once(PWD.'/libs/init.php');
}
?>