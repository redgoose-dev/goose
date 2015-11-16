<?php
if (!defined('__GOOSE__')) exit();

/**
 * Module - install
 *
 */
class Install {

	public $path, $set, $skinPath;

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
	 * form
	 * 인스톨 폼 출력
	 *
	 */
	public function form()
	{
		$this->skinPath = $this->path.'skin/'.$this->set['skin'].'/';
		require_once(__GOOSE_PWD__.$this->skinPath.'view_form.html');
	}

	/**
	 * transaction
	 * 인스톨 처리
	 *
	 */
	public function transaction()
	{
		if (version_compare(PHP_VERSION, __GOOSE_MIN_PHP_VERSION__, '<'))
		{
			Goose::error(999, 'low php version');
			Goose::end();
		}

		$this->skinPath = $this->path.'skin/'.$this->set['skin'].'/';
		require_once(__GOOSE_PWD__.$this->skinPath.'transaction_install.php');
	}

	/**
	 * template - config.php
	 *
	 * @param array $data
	 * @return string
	 */
	public static function tpl_config($data=null)
	{
		if (!$data)
		{
			return '';
		}

		$str = "<?php\n";
		$str .= "if (!defined('__GOOSE__')) exit();\n";
		$str .= "\n";
		$str .= "define( '__GOOSE_URL__', '".$data['define']['url']."' );\n";
		$str .= "define( '__GOOSE_ROOT__', '".$data['define']['root']."' );\n";
		$str .= "\n";
		$str .= "\$dbConfig = array('mysql:dbname=".$data['db']['dbname'].";host=".$data['db']['host'].";port=".$data['db']['port']."', '".$data['db']['name']."', '".$data['db']['password']."');\n";
		$str .= "\$table_prefix = '".$data['db']['prefix']."';\n";
		$str .= "\n";
		$str .= "\$apiKey = '".$data['apiKey']."';\n";
		$str .= "\$basic_module = '".$data['basic_module']."';\n";
		$str .= "\n";
		$str .= "\$accessLevel = array(\n";
		$str .= "\t'login' => ".$data['level']['login']."\n";
		$str .= "\t,'admin' => ".$data['level']['admin']."\n";
		$str .= ");";

		return Util::fop(__GOOSE_PWD__.'data/config.php', 'w', $str, 0755);
	}

	/**
	 * template - modules.json
	 *
	 * @return string
	 */
	public static function tpl_modules()
	{
		$str = "[]";
		return Util::fop(__GOOSE_PWD__.'data/modules.json', 'w', $str, 0755);
	}

	/**
	 * install module
	 *
	 * @param string $modName module name
	 * @return array
	 */
	public function installModule($modName=null)
	{
		if (!$modName) return Array('state' => 'error', 'message' => 'not found module name');

		$result = Module::install($modName);

		if ($result['state'] == 'error')
		{
			return Array('state' => 'error', "message" => "[$modName] ERROR : $result[message]");
		}
		else if ($result['state'] == 'success')
		{
			return Array('state' => 'success', "message" => "[$modName] $result[message]");
		}
	}

	/**
	 * install module
	 *
	 * @param string $modName module name
	 * @return array
	 */
	public function unInstallModule($modName=null)
	{
		if (!$modName) return Array('state' => 'error', 'message' => 'not found module name');

		$result = Module::uninstall($modName);

		if ($result['state'] == 'success')
		{
			return Array('state' => 'success', "message" => "[$modName] Complete uninstall");
		}
		else if ($result['state'] == 'error')
		{
			return Array('state' => 'error', "message" => "[$modName] ERROR : $result[message]");
		}
	}
}
