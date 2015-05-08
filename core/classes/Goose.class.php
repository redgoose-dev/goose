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
	 * create spawn
	 *
	 * @param array $config
	 */
	public function createSpawn($config)
	{
		$this->spawn = new Spawn($config);
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
	 */
	public static function end()
	{
		global $goose;

		if (__GOOSE_DEBUG__)
		{
			$endTime = array_sum(explode(' ', microtime()));
			$time = $endTime - __StartTime__;
			echo "<hr>";
			echo "<p>$time</p>";
		}

		if ($goose->spawn)
		{
			$goose->spawn->disconnect();
		}
		exit;
	}

	/**
	 * error goose
	 *
	 * @param number $code error code
	 * @param string $msg error message
	 */
	public static function error($code=null, $msg=null)
	{
		switch($code)
		{
			case 404:
				echo "404 not found";
				break;
			case 999:
				echo "ERROR : \"$msg\"";
				break;
		}
	}
}
