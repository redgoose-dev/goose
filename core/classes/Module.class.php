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
			return array('error' => 'module not found');
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
		if ($existModule['error'])
		{
			return $existModule;
		}
		else
		{
			$pwd = $existModule['pwd'];
			$path = $existModule['path'];
		}

		$pwd_setting = Util::checkUserFile($pwd.'setting.json');
		$pwd_class = Util::checkUserFile($pwd.$moduleName.'.class.php');

		if ($pwd_class && $pwd_setting)
		{
			require_once($pwd_class);
			$settings = Util::jsonToArray(Util::openFile($pwd_setting));

			// check install
			if (($settings['install'] && !in_array($moduleName, $goose->modules)) && !$install)
			{
				return array('error' => '인스톨이 필요한 모듈입니다.');
			}

			// check permission
			if (($settings['permission'] && $settings['permission'] > $_SESSION['goose_level']) && !$goose->isAdmin)
			{
				return array('error' => '접근 권한이 없습니다.');
			}

			// check setting
			if (!$settings)
			{
				return array('error' => 'error setting.json');
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
			return array('error' => '정상적인 모듈이 아닙니다.');
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

		if (!$mod->set['install'])
		{
			return array( 'error' => 'can not install' );
		}

		$file = Util::checkUserFile(__GOOSE_PWD__.$mod->path.'install.json');
		$installData = Util::jsonToArray(Util::openFile($file));
		$result = $mod->install($installData);

		if ($result == 'success')
		{
			self::setInstallModule($moduleName);
			return array( 'error' => null );
		}
		else
		{
			if (strpos($result, 'already exists'))
			{
				self::setInstallModule($moduleName);
			}
			return array( 'error' => $result );
		}
	}

	/**
	 * get install module
	 *
	 */
	public static function getInstallModule()
	{
		return Util::jsonToArray(Util::openFile(__GOOSE_PWD__.'data/modules.json'));
	}

	/**
	 * set install module
	 *
	 * @param string $moduleName
	 */
	public static function setInstallModule($moduleName=null)
	{
		$modules = self::getInstallModule();
		array_push($modules, $moduleName);
		$new_modules = Util::arrayToJson($modules);
		if ($new_modules)
		{
			Util::fop(__GOOSE_PWD__.'data/modules.json', 'w', $new_modules);
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
