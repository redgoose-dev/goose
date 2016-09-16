<?php
namespace mod\Router;
use core, mod;
if (!defined('__GOOSE__')) exit();


class Router {

	public function __construct($params=[])
	{
		core\Module::initModule($this, $params);

		$this->match = null;
		$this->route = new AltoRouter();
	}


	/**
	 * init
	 *
	 * @param $pwd_map
	 * @param $accessLevel
	 */
	public function init($pwd_map, $accessLevel)
	{
		// init route map
		$this->route->setBasePath(preg_replace('/\/$/', '', __GOOSE_ROOT__));
		require_once(core\Util::checkUserFile($pwd_map));
		$this->match = $this->route->match();

		if ($this->match)
		{
			// set route values
			$_module = (isset($this->match['params']['module'])) ? $this->match['params']['module'] : null;
			$_action = (isset($this->match['params']['action'])) ? $this->match['params']['action'] : null;
			$_method = $_SERVER['REQUEST_METHOD'];

			// check access level
			$auth = new mod\Auth\Auth([
				'action' => $_action,
				'method' => $_method
			]);
			$auth->auth($accessLevel['login']);

			// set module name
			$modName = ($_module) ? $_module : $this->set['basicModule'];
			$modAddr = 'mod\\' . $modName . '\\' . $modName;

			// check exists module
			if (!class_exists($modAddr))
			{
				core\Goose::error(101, 'not found module `' . $modName . '`');
			}

			// init class
			$baseModule = new $modAddr([
				'action' => $_action,
				'method' => $_method,
				'params' => [
					$this->match['params']['param0'],
					$this->match['params']['param1'],
					$this->match['params']['param2'],
					$this->match['params']['param3']
				]
			]);

			// check module
			if (!$baseModule) core\Goose::error(101, 'module error');

			// check index method
			if (!method_exists($baseModule, 'index')) core\Goose::error(101, '`' . $modName . '` 모듈의 index()메서드가 없습니다.');

			// play index module
			if (method_exists($baseModule, 'index')) $baseModule->index();
		}
		else
		{
			core\Goose::error(404);
		}
	}
}