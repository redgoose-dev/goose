<?php
if (!defined('__GOOSE__')) exit();

class Router {

	/**
	 * construct
	 *
	 * @param array $getter
	 */
	public function __construct($getter=array())
	{
		$this->name = $getter['name'];
		$this->goose = $getter['goose'];
		$this->isAdmin = $getter['isAdmin'];
		$this->param = $getter['param'];
		$this->path = $getter['path'];
		$this->set = $getter['set'];
		$this->match = null;

		require_once('vendor/autoload.php');
		$this->route = new AltoRouter();
		$this->route->setBasePath(preg_replace('/\/$/', '', __GOOSE_ROOT__));
	}

	public function init($pwd_map, $accessLevel)
	{
		require_once(Util::checkUserFile($pwd_map));
		$this->match = $this->route->match();

		if ($this->match) {
			$_module = (isset($this->match['params']['module'])) ? $this->match['params']['module'] : null;
			$_action = (isset($this->match['params']['action'])) ? $this->match['params']['action'] : null;
			$_method = $_SERVER['REQUEST_METHOD'];

			// check access level
			$auth = Module::load('auth', array(
				'action' => $_action,
				'method' => $_method
			));
			$auth->auth($accessLevel['login']);

			// load module
			$modName = ($_module) ? $_module : $this->set['basicModule'];
			$baseModule = Module::load(
				$modName,
				array(
					'action' => $_action,
					'method' => $_method,
					'params' => array(
						$this->match['params']['param0'],
						$this->match['params']['param1'],
						$this->match['params']['param2'],
						$this->match['params']['param3']
					)
				)
			);

			// check module
			if (!$baseModule) Goose::error(101, 'module error');
			if (is_array($baseModule) && $baseModule['state'] == 'error') Goose::error(101, $baseModule['message']);

			// check index method
			if (!method_exists($baseModule, 'index')) Goose::error(101, $modName . '모듈의 index()메서드가 없습니다.');

			// index module
			if (method_exists($baseModule, 'index')) $baseModule->index();
		}
		else
		{
			Goose::error(404);
		}
	}
}