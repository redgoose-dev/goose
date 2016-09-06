<?php

namespace core;

/**
 * Module
 */
class Module {

	const MOD_DIR = 'mod';

	public $installModules;

	/**
	 * init
	 */
	public function __construct() {}

	/**
	 * exist module
	 * 모듈이 존재하는지 체크하고 경로를 반환한다.
	 *
	 * @param string $moduleName
	 * @return array
	 */
	public static function existModule($moduleName)
	{
		$path = self::MOD_DIR . '/' . $moduleName;
		if (is_dir(__GOOSE_PWD__.$path.'.user/'))
		{
			$path .= '.user/';
		}
		else if(is_dir(__GOOSE_PWD__.$path.'/'))
		{
			$path .= '/';
		}
		else
		{
			return [
				'state' => 'error',
				'message' => 'module not found'
			];
		}

		return [
			'state' => 'success',
			'pwd' => __GOOSE_PWD__.$path,
			'path' => $path
		];
	}

	/**
	 * check exist module method
	 *
	 * @param string $moduleName
	 * @param string $methodName
	 * @return boolean
	 */
	public static function existMethod($moduleName, $methodName)
	{
		$mod = Module::load($moduleName);
		return method_exists($mod, $methodName);
	}

	/**
	 * get module
	 *
	 * @param string $modName
	 * @param array $params custom parameter
	 * @return object
	 */
	public static function load($modName, $params=null)
	{
		// set namespace
		$modAddr = 'mod\\' . $modName . '\\' . $modName;

		// check class
		if (!class_exists($modAddr))
		{
			Goose::error(101, 'not found module `' . $modName . '`');
		}

		// init class
		$resultModule = new $modAddr($params);

		return $resultModule;
	}

	/**
	 * initial module
	 *
	 * @param object $instance
	 * @param array $params
	 * @param boolean $install
	 * @return null
	 */
	public static function initModule($instance, $params=null, $install=false)
	{
		global $goose;

		// set name
		$instance->name = (new \ReflectionClass($instance))->getShortName();

		// set path
		$existModule = self::existModule($instance->name);
		if ($existModule['state'] == 'error')
		{
			Goose::error(403, $existModule['message']);
		}
		else
		{
			$instance->pwd = $existModule['pwd'];
			$instance->path = $existModule['path'];
		};

		// set setting data
		$instance->set = Module::getSetting($instance->name);
		if (!$instance->set || !is_array($instance->set))
		{
			Goose::error(403, '[' . $instance->name . '] setting.json파일이 없습니다.');
		}

		// check install
		if (($instance->set['install'] && !in_array($instance->name, $goose->modules)) && !$install)
		{
			Goose::error(403, '[' . $instance->name . '] 인스톨이 필요한 모듈입니다.');
		}

		// check permission
		if (($instance->set['permission'] && $instance->set['permission'] > $_SESSION['goose_level']) && !$goose->isAdmin)
		{
			Goose::error(403, '[' . $instance->name . '] 접근 권한이 없습니다.');
		}

		// set module class
		$instance->set['skin'] = ($instance->set['skin']) ? $instance->set['skin'] : 'default';

		// set isAdmin
		$instance->isAdmin = (($instance->set['adminPermission'] <= $_SESSION['goose_level']) || $goose->isAdmin) ? true : false;

		// set route
		$instance->params = $params;
	}

	/**
	 * install module
	 *
	 * @param string $moduleName
	 * @return array
	 */
	public static function install($moduleName)
	{
		$mod = self::load($moduleName, null, true);

		if (!$mod->set)
		{
			return [
				'state' => 'error',
				'message' => 'not found ['.$moduleName.'->set]'
			];
		}

		if (!$mod->set['install'])
		{
			return [
				'state' => 'error',
				'message' => 'can not install'
			];
		}

		$file = Util::isFile([
			Util::checkUserFile(__GOOSE_PWD__.'MOD/'.$moduleName.'/install.json'),
			Util::checkUserFile(__GOOSE_PWD__.'MOD/'.$moduleName.'.user/install.json')
		]);
		$installData = Util::jsonToArray(Util::openFile($file));

		if (method_exists($mod, 'install'))
		{
			//Util::console($installData);
			$result = $mod->install($installData);

			if ($result == 'success')
			{
				self::setInstallModule('add', $moduleName);
				return [
					'state' => 'success',
					'message' => $result
				];
			}
			else
			{
				if (strpos($result, 'already exists'))
				{
					self::setInstallModule('add', $moduleName);
				}
				return [
					'state' => 'error',
					'message' => $result
				];
			}
		}
		else
		{
			return [
				'state' => 'error',
				'message' => 'not found install method'
			];
		}
	}

	/**
	 * uninstall module
	 *
	 * @param string $moduleName
	 * @return array
	 */
	public static function uninstall($moduleName)
	{
		$mod = self::load($moduleName);

		if (method_exists($mod, 'uninstall'))
		{
			$result = $mod->uninstall($moduleName);

			if ($result['state'] == 'success')
			{
				self::setInstallModule('remove', $moduleName);
				return Array('state' => 'success');
			}
			else
			{
				return Array('state' => 'error', 'message' => $result['message']);
			}
		}
		else
		{
			return Array('state' => 'error', 'message' => 'not found uninstall method');
		}
	}

	/**
	 * get install module
	 *
	 * @return array()
	 */
	public static function getInstallModule()
	{
		return Util::jsonToArray(Util::openFile(__GOOSE_PWD__.'data/modules.json'));
	}

	/**
	 * set install module
	 *
	 * @param string|null $method (add|remove)
	 * @param string $moduleName
	 */
	public static function setInstallModule($method=null, $moduleName=null)
	{
		$error = true;
		$modules = self::getInstallModule();

		switch ($method)
		{
			case 'add':
				if (!array_search($moduleName, $modules))
				{
					array_push($modules, $moduleName);
					$error = false;
				}
				break;
			case 'remove':
				$index = array_search($moduleName, $modules);
				if ($index)
				{
					array_splice($modules, $index, 1);
					$error = false;
				}
				break;
		}

		if (!$error)
		{
			$new_modules = Util::arrayToJson($modules);
			if ($new_modules)
			{
				Util::fop(__GOOSE_PWD__.'data/modules.json', 'w', $new_modules);
			}
		}
	}

	/**
	 * after action
	 *
	 * @param array $result [message, action, data]
	 * @return array
	 */
	public static function afterAction($result=null)
	{
		if ($result['message'])
		{
			Util::alert($result['message']);
		}
		if ($result['print'])
		{
			echo '<p>'.$result['print'].'</p>';
		}
		switch($result['action'])
		{
			case 'redirect':
				if ($result['url'])
				{
					Util::redirect($result['url']);
				}
				return null;
				break;

			case 'back':
					Util::back();
					return null;
				break;
		}
		if ($result['data'])
		{
			return $result['data'];
		}
		return null;
	}

	/**
	 * get setting
	 *
	 * @param string $modName
	 * @return array
	 */
	public static function getSetting($modName=null)
	{
		$loc = self::existModule($modName);
		$settings = Util::mergeJson([
			Util::isFile([ $loc['pwd'].'setting.json' ]),
			Util::isFile([ __GOOSE_PWD__.'data/settings/'.$modName.'.json' ])
		]);
		return $settings;
	}

}
