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
	 * admin level
	 */
	public $adminLevel;


	/**
	 * user.php file location
	 * 
	 * @return string : location string
	 */
	private function getUserFileLocation()
	{
		return PWD.'/data/config/user.php';
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
	 */
	public function init()
	{
		$this->util = new Util();

		if (self::isInstalled())
		{
			include(self::getUserFileLocation());

			$this->tablesName = $tablesName;
			unset($tablesName);
			$this->api_key = $api_key;
			unset($api_key);
			$this->adminLevel = $adminLevel;
			unset($adminLevel);

			$this->spawn = new Spawn($dbConfig, $this->tablesName);
			unset($dbConfig);
		}
	}


	/**
	 * page exit
	 */
	public function out()
	{
		if ($this->spawn)
		{
			$this->spawn->disconnect();
		}
		if (DEBUG)
		{
			$end_time = array_sum(explode(' ', microtime()));
			echo "<hr/><p>\n\nTIME : ".($end_time - __StartTime__). "</p>";
		}
		exit;
	}


	/**
	 * costom error (제대로 사용할지는 미정)
	 * 
	 * @anthor : redgoose
	 * 
	 * @param Number $code : 에러코드
	 * @return void
	*/
	public function error($code=404)
	{
		switch($code)
		{
			case 404:
				if (is_file(PWD.'/pages/404.html'))
				{
					require(PWD.'/pages/404.html');
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