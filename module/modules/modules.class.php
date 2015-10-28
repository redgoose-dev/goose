<?php
if (!defined('__GOOSE__')) exit();

/**
 * Module - modules
 */
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
		switch($this->param['action'])
		{
			case 'install':
				$result = self::actInstall();
				if ($result['state'] == 'error')
				{
					Module::afterAction(Array('action' => 'back', 'message' => $result['message']));
				}
				else if ($result['state'] == 'success')
				{
					Module::afterAction(Array('message' => 'install complete', 'action' => 'redirect', 'url' => __GOOSE_ROOT__.'/modules/index/'));
				}
				break;
			case 'uninstall':
				$result = self::actUninstall();

				if ($result['state'] == 'error')
				{
					Module::afterAction(Array('action' => 'back', 'message' => $result['message']));
				}
				else if ($result['state'] == 'success')
				{
					Module::afterAction(Array('message' => 'uninstall complete', 'action' => 'redirect', 'url' => __GOOSE_ROOT__.'/modules/index/'));
				}
				break;
			default:
				self::viewIndex();
				break;
		}
	}

	/**
	 * view - index
	 */
	private function viewIndex()
	{
		// load layout module
		$this->layout = Module::load('layout');

		// set repo
		$repo = Array();

		// get module data
		$result = $this->getModuleIndex('name');
		$repo['modules'] = ($result['data']) ? $result['data'] : array();

		// set pwd_container
		$this->pwd_container = __GOOSE_PWD__.$this->skinPath.'/view_index.html';

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

		$result = Util::getDir(__GOOSE_PWD__.'module/', $key);

		return array( 'state' => 'success', 'data' => $result );
	}

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
		$install = Module::load('install');

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
		$install = Module::load('install');

		// install module
		return $install->unInstallModule($mod);
	}

	/**
	 * get setting
	 *
	 * @param string $modName
	 * @return array
	 */
	private function getSetting($modName=null)
	{
		$loc = Module::existModule($modName);
		$file = Util::checkUserFile($loc['pwd'].'setting.json');
		return Util::jsonToArray(Util::openFile($file), true);
	}

}