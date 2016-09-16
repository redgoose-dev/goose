<?php
namespace mod\Modules;
use core, mod, stdClass;
if (!defined('__GOOSE__')) exit();


class Modules {

	public $name, $set, $params, $isAdmin;
	public $path, $skinPath, $skinAddr;

	public function __construct($params=[])
	{
		core\Module::initModule($this, $params);
	}


	/**
	 * get module index
	 *
	 * @param string $key
	 * @return array
	 */
	public function getModuleIndex($key=null)
	{
		if ($this->name != 'Modules') return [ 'state' => 'error', 'message' => '잘못된 객체로 접근했습니다.' ];
		if (!$this->isAdmin) return [ 'state' => 'error', 'action' => 'back', 'message' => '권한이 없습니다.' ];

		$result = core\Util::getDir(__GOOSE_PWD__ . 'mod/', $key);

		return [
			'state' => 'success',
			'data' => $result
		];
	}

	/**
	 * index
	 */
	public function index()
	{
		// action
		if ($this->params['method'] == 'GET')
		{
			switch($this->params['action'])
			{
				case 'install':
					$result = self::actInstall();
					if ($result['state'] == 'error')
					{
						core\Module::afterAction([
							'action' => 'back',
							'message' => $result['message']
						]);
					}
					else if ($result['state'] == 'success')
					{
						core\Module::afterAction([
							'message' => 'install complete',
							'action' => 'redirect',
							'url' => __GOOSE_ROOT__ . '/modules/index/'
						]);
					}
					break;

				case 'uninstall':
					$result = self::actUninstall();

					if ($result['state'] == 'error')
					{
						core\Module::afterAction([
							'action' => 'back',
							'message' => $result['message']
						]);
					}
					else if ($result['state'] == 'success')
					{
						core\Module::afterAction([
							'message' => 'uninstall complete',
							'action' => 'redirect',
							'url' => __GOOSE_ROOT__ . '/modules/index/'
						]);
					}
					break;

				case 'editSetting':
					$view = new View($this);
					if ($this->params['params'][0])
					{
						$view->view_editSetting($this->params['params'][0]);
					}
					break;

				default:
					$view = new View($this);
					$view->view_index();
					break;
			}
		}
		else if ($this->params['method'] == 'POST')
		{
			switch($this->params['action'])
			{
				case 'editSetting':
					self::editSetting();
					break;
			}
		}
	}


	/**********************************************
	 * PRIVATE FUNCTION
	 *********************************************/

	/**
	 * install
	 */
	private function actInstall()
	{
		if ($this->name != 'Modules') return [ 'state' => 'error', 'message' => '잘못된 객체로 접근했습니다.' ];
		if (!$this->isAdmin) return [ 'state' => 'error', 'action' => 'back', 'message' => '권한이 없습니다.' ];
		if ($this->params['params'][0])
		{
			$mod = $this->params['params'][0];
		}
		else
		{
			return [ 'state' => 'error', 'action' => 'back', 'message' => '모듈값이 없습니다,' ];
		}

		// load module
		$install = core\Module::load('install');

		// install module
		return $install->installModule($mod);
	}

	/**
	 * uninstall
	 */
	private function actUninstall()
	{
		if ($this->name != 'Modules') return [ 'state' => 'error', 'message' => '잘못된 객체로 접근했습니다.' ];
		if (!$this->isAdmin) return [ 'state' => 'error', 'action' => 'back', 'message' => '권한이 없습니다.' ];
		if ($this->params['params'][0])
		{
			$mod = $this->params['params'][0];
		}
		else
		{
			return [ 'state' => 'error', 'action' => 'back', 'message' => '모듈값이 없습니다,' ];
		}

		// load module
		$install = core\Module::load('install');

		// install module
		return $install->unInstallModule($mod);
	}

	/**
	 * edit setting
	 */
	private function editSetting()
	{
		$pwd = __GOOSE_PWD__ . 'data/settings/' . $_POST['module'] . '.json';
		$json = core\Util::jsonToArray($_POST['json']);
		$json = core\Util::arrayToJson($json, false, true, '  ');
		$result = core\Util::fop($pwd, 'w', $json, 0755);

		if ($result)
		{
			core\Util::redirect($_POST['referer']);
		}
		else
		{
			core\Util::back('error');
		}
	}
}