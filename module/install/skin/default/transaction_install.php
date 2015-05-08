<?php
if (!defined('__GOOSE__')) exit();

/**
 * Check $_POST
 *
 * @return Boolean : 이상이 없으면 true, 문제가 있으면 false값을 리턴한다.
 */
function checkPost()
{
	$errorValue = Util::checkExistValue($_POST, array('goose_root', 'goose_url', 'dbId', 'dbName', 'email', 'name'));
	if ($errorValue)
	{

		Util::back("[$errorValue]값이 없습니다.");
		Goose::end();
	}
	if (!$_POST['dbPassword'] || ($_POST['dbPassword'] != $_POST['dbPassword2']))
	{
		Util::back("DB 비밀번호와 확인값이 다릅니다.");
		return false;
	}
	if (!$_POST['password'] || ($_POST['password'] != $_POST['password2']))
	{
		Util::back("관리자 비밀번호와 확인값이 다릅니다.");
		return false;
	}
	return true;
}


// check post data
if ( checkPost() == true )
{
	// create directories
	Util::createDirectory(__GOOSE_PWD__."data", 0755);

	// create config.php
	$tpl_config = $this->tpl_config(array(
		'define' => array(
			'url' =>$_POST['goose_url']
			,'root' => $_POST['goose_root']
		)
		,'db' => array(
			'dbname' => $_POST['dbName']
			,'name' => $_POST['dbId']
			,'password' => $_POST['dbPassword']
			,'host' => $_POST['dbHost']
			,'prefix' => $_POST['dbPrefix']
		)
		,'level' => array(
			'login' => $_POST['loginLevel']
			,'admin' => $_POST['adminLevel']
		)
		,'apiKey' => $_POST['apiPrefix']
		,'timezone' => $_POST['timezone']
		,'basic_module' => 'intro'
	));
	if ($tpl_config != 'success')
	{
		Goose::error(999, 'Failed to create the file data/config.php');
		Goose::end();
	}

	// create modules.json
	if ($this->tpl_modules() != 'success')
	{
		Goose::error(999, 'Failed to create the file data/modules.json');
		Goose::end();
	}

	// create route.map.php
	if ($this->tpl_routeMap() != 'success')
	{
		Goose::error(999, 'Failed to create the file data/route.map.php');
		Goose::end();
	}
}
else
{
	Goose::end();
}


// load config file
require_once(__GOOSE_PWD__.'data/config.php');


// create and connect database
$this->goose->createSpawn();
$this->goose->spawn->connect($dbConfig);
$this->goose->spawn->prefix = $table_prefix;


// set admin
$this->goose->isAdmin = true;


// install modules
echo "<p>Create table - ".$this->installModule('user')."</p>";
echo "<p>Create table - ".$this->installModule('nest')."</p>";
echo "<p>Create table - ".$this->installModule('app')."</p>";
echo "<p>Create table - ".$this->installModule('json')."</p>";
echo "<p>Create table - ".$this->installModule('file')."</p>";
echo "<p>Create table - ".$this->installModule('article')."</p>";
echo "<p>Create table - ".$this->installModule('category')."</p>";


// add admin user
$result = Spawn::insert(array(
	'table' => __dbPrefix__ . 'user',
	'data' => array(
		'srl' => null,
		'email' => $_POST['email'],
		'name' => $_POST['name'],
		'pw' => md5($_POST['password']),
		'level' => $_POST['adminLevel'],
		'regdate' => date("YmdHis")
	)
));
echo "<p>Add admin user - ".(($result == 'success') ? 'Complete' : "ERROR : $result")."</p>";


// add basic navigation on json table
$cnt = Spawn::count(array(
	'table' => Spawn::getTableName('json'),
	'where' => "name='Goose Navigation'"
));
if (!$cnt)
{
	$data = Util::checkUserFile(__GOOSE_PWD__.'core/misc/navigationTree.json');
	$data = Util::openFile($data);
	$data = Util::jsonToArray($data, true, true);
	$data = Util::arrayToJson($data, true);
	$result = Spawn::insert(array(
		'table' => __dbPrefix__ . 'json',
		'data' => array(
			'srl' => null,
			'name' => 'Goose Navigation',
			'json' => $data,
			'regdate' => date("YmdHis")
		)
	));
}
else
{
	$result = '"Goose Navigation" Data already exists.';
}
echo "<p>Add json data - ".(($result == 'success') ? 'Complete' : "ERROR : $result")."</p>";


echo "<hr/>";
echo "<h1>END INSTALL</h1>";
echo "<nav><a href=\"".__GOOSE_ROOT__."\">Go to intro page</a></nav>";