<?php
if (!defined('__GOOSE__')) exit();

/**
 * Module
 *
 */

class Module {

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
	 * @return string
	 */
	public static function existModule($moduleName)
	{
		$path = 'module/'.$moduleName;
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
			return new Object(array('state' => 'error', 'message' => 'module not found'));
		}
		return array(
			'pwd' => __GOOSE_PWD__.$path
			,'path' => $path
		);
	}

	/**
	 * get module
	 *
	 * @param string $moduleName
	 * @param array $params 라우트 정보를 가지고 있다.
	 * @param boolean $install 인스톨용으로 사용되는 모듈인지에 대한 여부
	 * @return object
	 */
	public static function load($moduleName, $params=null, $install=false)
	{
		global $goose;

		$existModule = self::existModule($moduleName);
		if ($existModule['state'] == 'error')
		{
			return $existModule;
		}
		else
		{
			$pwd = $existModule['pwd'];
			$path = $existModule['path'];
		};

		// set setting data
		$tmp_settingOriginal = Util::jsonToArray(Util::openFile($pwd.'setting.json'));
		$tmp_settingUser = Util::jsonToArray(Util::openFile($pwd.'setting.user.json'));
		if ($tmp_settingOriginal && $tmp_settingUser)
		{
			$settings = ($tmp_settingUser) ? Util::extendArray($tmp_settingOriginal, $tmp_settingUser) : $tmp_settingOriginal;
		}
		else
		{
			$settings = ($tmp_settingUser) ? $tmp_settingUser : null;
			$settings = ($tmp_settingOriginal) ? $tmp_settingOriginal : $settings;
		}
		if (!$settings || !is_array($settings)) return new Object(array('state' => 'error', 'message' => '['.$moduleName.'] setting.json파일이 없습니다.'));

		// set module class path
		$pwd_class = Util::checkUserFile($pwd.$moduleName.'.class.php');

		if ($pwd_class)
		{
			require_once($pwd_class);

			// check install
			if (($settings['install'] && !in_array($moduleName, $goose->modules)) && !$install)
			{
				return new Object(array('state' => 'error', 'message' => '['.$moduleName.'] 인스톨이 필요한 모듈입니다.'));
			}

			// check permission
			if (($settings['permission'] && $settings['permission'] > $_SESSION['goose_level']) && !$goose->isAdmin)
			{
				return new Object(array('state' => 'error', 'message' => '['.$moduleName.'] 접근 권한이 없습니다.'));
			}

			// set module class
			$settings['skin'] = ($settings['skin']) ? $settings['skin'] : 'default';
			$tmpModule =  new $moduleName(array(
				'name' => $settings['name'],
				'path' => $path,
				'set' => $settings,
				'goose' => $goose,
				'isAdmin' => (($settings['adminPermission'] <= $_SESSION['goose_level']) || $goose->isAdmin) ? true : false,
				'param' => ($params) ? $params : array()
			));

			// return module
			return $tmpModule;
		}
		else
		{
			return new Object(array('state' => 'error', 'message' => '['.$moduleName.'] 정상적인 모듈이 아닙니다.'));
		}
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
			return array( 'state' => 'error', 'message' => 'not found ['.$moduleName.'->set]' );
		}

		if (!$mod->set['install'])
		{
			return array( 'state' => 'error', 'message' => 'can not install' );
		}

		$file = Util::checkUserFile(__GOOSE_PWD__.'module/'.$moduleName.'/install.json');
		$installData = Util::jsonToArray(Util::openFile($file));

		if (method_exists($mod, 'install'))
		{
			$result = $mod->install($installData);

			if ($result == 'success')
			{
				self::setInstallModule('add', $moduleName);
				return array( 'state' => 'success', 'message' => $result );
			}
			else
			{
				if (strpos($result, 'already exists'))
				{
					self::setInstallModule('add', $moduleName);
				}
				return array( 'state' => 'error', 'message' => $result );
			}
		}
		else
		{
			return Array( 'state' => 'error', 'message' => 'not found install method' );
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
				return false;
				break;

			case 'back':
					Util::back();
					return false;
				break;
		}
		if ($result['data'])
		{
			return $result['data'];
		}
	}

}
