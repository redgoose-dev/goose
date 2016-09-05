<?php
namespace mod\Modules;
use core;
if (!defined('__GOOSE__')) exit();


class Modules {

	public $name, $goose, $set, $param, $layout, $isAdmin;
	public $path, $skinPath, $pwd_container;

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

		$this->skinPath = $this->path.'skin/'.$this->set['skin'].'/';
	}

	/**
	 * index
	 */
	public function index()
	{
		// action
		if ($this->param['method'] == 'GET')
		{
			switch($this->param['action'])
			{
				case 'install':
					$result = self::actInstall();
					if ($result['state'] == 'error')
					{
						core\Module::afterAction(Array('action' => 'back', 'message' => $result['message']));
					}
					else if ($result['state'] == 'success')
					{
						core\Module::afterAction(Array('message' => 'install complete', 'action' => 'redirect', 'url' => __GOOSE_ROOT__.'/modules/index/'));
					}
					break;
				case 'uninstall':
					$result = self::actUninstall();

					if ($result['state'] == 'error')
					{
						core\Module::afterAction(Array('action' => 'back', 'message' => $result['message']));
					}
					else if ($result['state'] == 'success')
					{
						core\Module::afterAction(Array('message' => 'uninstall complete', 'action' => 'redirect', 'url' => __GOOSE_ROOT__.'/modules/index/'));
					}
					break;
				default:
					self::viewIndex();
					break;
			}
		}
		else if ($this->param['method'] == 'POST')
		{
			switch($this->param['action'])
			{
				case 'editSetting':
					self::editSetting();
					break;
			}
		}
	}


	/**********************************************
	 * VIEW AREA
	 *********************************************/

	/**
	 * view
	 */
	private function viewIndex()
	{
		global $goose;

		// load layout module
		$this->layout = core\Module::load('layout');

		// set repo
		$repo = [];

		switch($this->param['action'])
		{
			case 'editSetting':
				// check module
				$_module = $this->param['params'][0];
				if (core\Module::existModule($_module)['state'] != 'success')
				{
					core\Goose::error(404);
				}

				// set setting data
				$repo['setting'] = core\Module::getSetting($_module);

				// check permission
				if (!$goose->isAdmin && !($_SESSION['goose_level'] >= $repo['setting']['adminPermission']))
				{
					core\Util::back('You do not have permission.');
					core\Goose::end();
				}

				// set pwd_container
				$this->pwd_container = __GOOSE_PWD__.$this->skinPath.'/view_editSetting.html';
				break;

			default:
				// get module data
				$result = $this->getModuleIndex('name');
				$repo['modules'] = ($result['data']) ? $result['data'] : [];

				// set pwd_container
				$this->pwd_container = __GOOSE_PWD__.$this->skinPath.'/view_index.html';
				break;
		}

		// require layout
		require_once($this->layout->getUrl());
	}


	/**********************************************
	 * API AREA
	 *********************************************/

	/**
	 * get module index
	 *
	 * @param string $key
	 * @return array
	 */
	public function getModuleIndex($key=null)
	{
		if ($this->name != 'modules') return array( 'state' => 'error', 'message' => '잘못된 객체로 접근했습니다.' );
		if (!$this->isAdmin) return array('state' => 'error', 'action' => 'back', 'message' => '권한이 없습니다.');

		$result = core\Util::getDir(__GOOSE_PWD__.'module/', $key);

		return array( 'state' => 'success', 'data' => $result );
	}


	/**********************************************
	 * PRIVATE FUNCTION
	 *********************************************/

	/**
	 * install
	 */
	private function actInstall()
	{
		if ($this->name != 'modules') return array( 'state' => 'error', 'message' => '잘못된 객체로 접근했습니다.' );
		if (!$this->isAdmin) return array('state' => 'error', 'action' => 'back', 'message' => '권한이 없습니다.');
		if ($this->param['params'][0])
		{
			$mod = $this->param['params'][0];
		}
		else
		{
			return array('state' => 'error', 'action' => 'back', 'message' => '모듈값이 없습니다,');
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
		if ($this->name != 'modules') return array( 'state' => 'error', 'message' => '잘못된 객체로 접근했습니다.' );
		if (!$this->isAdmin) return array('state' => 'error', 'action' => 'back', 'message' => '권한이 없습니다.');
		if ($this->param['params'][0])
		{
			$mod = $this->param['params'][0];
		}
		else
		{
			return array('state' => 'error', 'action' => 'back', 'message' => '모듈값이 없습니다,');
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
		$pwd = __GOOSE_PWD__.'data/settings/'.$_POST['module'].'.json';
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