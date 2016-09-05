<?php

namespace core;
use eftec\bladeone;


/**
 * Goose
 *
 * @property boolean $isAdmin 관리자인지에 대한 값
 * @property array $modules
 * @property Spawn $spawn
 * @property object $blade
 */
class Goose {

	// set variables
	public $modules, $spawn, $blade;


	/**
	 * Initialization
	 *
	 */
	public function init()
	{
		// get install modules
		$this->modules = Module::getInstallModule();

		// init blade template
		self::initBlade();
	}

	/**
	 * return static goose object (singleton)
	 *
	 * @return object instance
	 */
	public static function getInstance()
	{
		static $theInstance = null;
		if(!$theInstance)
		{
			$theInstance = new Goose();
		}
		return $theInstance;
	}

	/**
	 * create spawn
	 */
	public function createSpawn()
	{
		$this->spawn = new Spawn();
	}

	/**
	 * Installed check
	 *
	 * @return boolean
	 */
	public static function isInstalled()
	{
		return file_exists(__GOOSE_PWD__.'data/config.php');
	}

	/**
	 * end goose
	 *
	 * @param boolean $is_log 디버그 로그 출력여부를 정한다.
	 */
	public static function end($is_log=true)
	{
		global $goose;

		if (define(__GOOSE_DEBUG__) && $is_log)
		{
			$endTime = array_sum(explode(' ', microtime()));
			$time = $endTime - __StartTime__;
			echo "\n\n<p style='border-top:2px dashed #999;padding:1em;'>time : $time</p>";
		}

		if ($goose->spawn)
		{
			$goose->spawn->disconnect();
		}
		exit;
	}

	/**
	 * error
	 *
	 * @param int $code error code
	 * @param string $msg error message
	 */
	public static function error($code=null, $msg=null, $url_home=__GOOSE_ROOT__)
	{
		$error = Module::load('error');
		$url_home .= (preg_match('/\/$/', $url_home)) ? '' : '/';

		// act error
		switch($code)
		{
			case 101:
				// custom error
				$error->render($code, $msg, $url_home);
				self::end();
				break;
			case 403:
				$error->render($code, 'permission denied', $url_home);
				self::end();
				break;
			case 404:
				// page not found
				$error->render(404, 'page not found', $url_home);
				self::end();
				break;
			case 909:
				// box error
				break;
		}
	}

	/**
	 * error box
	 *
	 * @param int $code error code
	 * @param string $msg error message
	 */
	public static function errorbox($code=null, $msg=null)
	{
		$error = Module::load('error');
		$error->box($code, $msg);
	}

	private function initBlade()
	{
		include __GOOSE_PWD__ . "vendor/BladeOne/BladeOne.php";
		$this->blade = new bladeone\BladeOne(BLADE_VIEW, BLADE_CACHE);
	}
}