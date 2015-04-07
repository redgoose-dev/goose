<?php
if(!defined("GOOSE")){exit();}

class Goose {

	/**
	 * Util class
	 */
	public $util;

	/**
	 * Spawn class
	 */
	public $spawn;

	/**
	 * tables name
	 */
	public $tablesName;

	/**
	 * api key
	 */
	public $api_key;

	/**
	 * user
	 */
	public $user;

	/**
	 * is admin
	 */
	public $isAdmin;

	/**
	 * pwd
	 */
	private $pwd;

	/**
	 * start time
	 */
	private $startTime;


	/**
	 * user.php file location
	 * 
	 * @return string : location string
	 */
	private function getUserFileLocation()
	{
		return $this->pwd.'/data/config/user.php';
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
	public function isInstalled()
	{
		return file_exists(self::getUserFileLocation());
	}


	/**
	 * Initialization
	 * 
	 * @param String $pwd : absolute location
	 */
	public function init($pwd=PWD)
	{
		$this->pwd = $pwd;
		$this->util = new Util();
		$this->startTime = (__StartTime__) ? __StartTime__ : array_sum(explode(' ', microtime()));

		if (self::isInstalled())
		{
			include(self::getUserFileLocation());

			$this->tablesName = $tablesName;
			unset($tablesName);
			$this->api_key = $api_key;
			unset($api_key);
			$this->user = $user;
			unset($user);
			$this->isAdmin = ($this->user['adminLevel'] == $_SESSION['gooseLevel']) ? true : false;

			$this->spawn = new Spawn($dbConfig, $this->tablesName);
			unset($dbConfig);
		}
	}


	/**
	 * page exit
	 * 
	 */
	public function out()
	{
		if ($this->spawn)
		{
			$this->spawn->disconnect();
		}
		if (is_bool(DEBUG) && DEBUG)
		{
			$end_time = array_sum(explode(' ', microtime()));
			echo "<hr/><p>\n\nTIME : ".($end_time - $this->startTime). "</p>";
		}
		exit;
	}


	/**
	 * custom error
	 * 
	 * @param Number $code : error code
	 * @return void
	*/
	public function error($code=404)
	{
		switch($code)
		{
			case 404:
				if (is_file($this->pwd.'/pages/404.html'))
				{
					require($this->pwd.'/pages/404.html');
				}
				else
				{
					echo "page not found : 404";
				}
				break;

			default:
				echo $code;
				break;
		}
	}


	// singleton function
	protected function __construct() {}
	private function __clone() {}
	private function __wakeup() {}
}
?>