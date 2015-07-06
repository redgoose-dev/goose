<?php
if (!defined('__GOOSE__')) exit();

/**
 * Goose
 *
 * @property boolean $isAdmin 관리자인지에 대한 값
 * @property array $modules
 * @property Spawn $spawn
 */

class Goose {

	// set variables
	public $modules, $spawn;


	/**
	 * Initialization
	 *
	 */
	public function init()
	{
		// get install modules
		$this->modules = Module::getInstallModule();
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
		// act error
		switch($code)
		{
			case 101:
				// custom error
				$error->render($code, $msg);
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
}