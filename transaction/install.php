<?php
if(!defined("GOOSE")){exit();}

//header('Content-Type: text/plain; charset=utf-8');

$root = preg_replace('/\/$/', '', $_SERVER['REQUEST_URI']);
$url = 'http://'.$_SERVER['HTTP_HOST'].$root;

/**
 * Check $_POST
 * 
 * @return Boolean : 이상이 없으면 true, 문제가 있으면 false값을 리턴한다.
 */
function checkPost()
{
	global $util;
	foreach($_POST as $k=>$v)
	{
		if (!$v)
		{
			$util->back("[$k] 값이 없습니다.");
			return false;
		}
	}
	if ($_POST['dbPassword'] != $_POST['dbPassword2'])
	{
		$util->back("DB비밀번호와 확인값이 다릅니다.");
		return false;
	}
	if ($_POST['password'] != $_POST['password2'])
	{
		$util->back("관리자 비밀번호와 확인값이 다릅니다.");
		return false;
	}
	return true;
}

/**
 * Create config value
 * 
 * @return String : user.php 내용
 */
function createConfig()
{
	global $root, $url;

	$str = "<?php\n";
	$str .= "if(!defined(\"GOOSE\")){exit();}\n";
	$str .= "\n";
	$str .= "define('ROOT', '$root');\n";
	$str .= "define('URL', '$url');\n";
	$str .= "\n";
	$str .= "\$dbConfig = array('mysql:dbname=".$_POST['dbName'].";host=".$_POST['dbHost']."', '".$_POST['dbId']."', '".$_POST['dbPassword']."');\n";
	$str .= "\$tablesName = array(\n";
	$str .= "\t'articles' => '".$_POST['dbPrefix']."articles',\n";
	$str .= "\t'categories' => '".$_POST['dbPrefix']."categories',\n";
	$str .= "\t'extraKey' => '".$_POST['dbPrefix']."extraKey',\n";
	$str .= "\t'extraVar' => '".$_POST['dbPrefix']."extraVar',\n";
	$str .= "\t'files' => '".$_POST['dbPrefix']."files',\n";
	$str .= "\t'users' => '".$_POST['dbPrefix']."users',\n";
	$str .= "\t'moduleGroups' => '".$_POST['dbPrefix']."moduleGroups',\n";
	$str .= "\t'modules' => '".$_POST['dbPrefix']."modules',\n";
	$str .= "\t'tempFiles' => '".$_POST['dbPrefix']."tempFiles',\n";
	$str .= "\t'jsons' => '".$_POST['dbPrefix']."jsons'\n";
	$str .= ");\n";
	$str .= "\$api_key = \"".$_POST['apiPrefix']."\";\n";
	$str .= "?>";
	return $str;
}

// 파일 만들기
if (checkPost() == true)
{
	// create directory
	$util->createDirectory(PWD."/data", 0777);
	$util->createDirectory(PWD."/data/config", 0755);
	$util->createDirectory(PWD."/data/original", 0777);
	$util->createDirectory(PWD."/data/thumnail", 0777);

	// create user.php
	$fileResult = $util->fop(PWD.'/data/config/user.php', 'w', createConfig());
	if ($fileResult != 'success')
	{
		$util->out();
	}
}
else
{
	$util->out();
}


/*
	Install Database
*/
require_once(PWD.'/data/config/user.php');
require_once(PWD.'/libs/Database.class.php');
require_once(PWD.'/libs/Spawn.class.php');

// create instanse object
$spawn = new Spawn($dbConfig);
$spawn->action("set names utf8");

// create db table
// create table "article"
$result = $spawn->action("
	create table `".$tablesName['articles']."` (
		`srl` bigint(11) not null auto_increment,
		`group_srl` int(11) default null,
		`module_srl` bigint(11) default null,
		`category_srl` bigint(11) default null,
		`thumnail_srl` bigint(11) default null,
		`title` varchar(250) default null,
		`content` longtext,
		`thumnail_url` varchar(250) default null,
		`thumnail_coords` varchar(30) default null,
		`regdate` varchar(14) default null,
		`modate` varchar(14) default null,
		`ipAddress` varchar(15) default null,
		primary key (`srl`),
		unique key `srl` (`srl`)
	) engine=InnoDB default charset=utf8");
if ($result != 'success')
{
	echo "Fail create '".$tablesName['articles']."' table";
	$util->out();
}

// create table "categories"
$result = $spawn->action("
	create table `".$tablesName['categories']."` (
		`srl` bigint(11) not null auto_increment,
		`module_srl` bigint(11) default null,
		`turn` int(11) default null,
		`name` varchar(30) default null,
		`regdate` varchar(25) default null,
		primary key (`srl`),
		unique key `srl` (`srl`)
	) engine=InnoDB default charset=utf8");

// create table "extraKey"
$result = $spawn->action("
	create table `".$tablesName['extraKey']."` (
		`srl` bigint(11) not null auto_increment,
		`module_srl` bigint(11) default null,
		`turn` int(11) default null,
		`keyName` varchar(20) default null,
		`name` varchar(25) default null,
		`info` varchar(250) default null,
		`formType` int(11) default null,
		`defaultValue` varchar(250) default null,
		primary key (`srl`)
	) engine=InnoDB default charset=utf8");

// create table "extraVar"
$result = $spawn->action("
	create table `".$tablesName['extraVar']."` (
		`article_srl` bigint(11) default null,
		`key_srl` bigint(11) default null,
		`value` longtext not null
	) engine=InnoDB default charset=utf8");

// create table "files"
$result = $spawn->action("
	create table `".$tablesName['files']."` (
		`srl` bigint(11) not null auto_increment,
		`article_srl` bigint(11) default null,
		`name` varchar(255) default null,
		`loc` varchar(255) default null,
		primary key (`srl`)
	) engine=InnoDB default charset=utf8");

// create table "jsons"
$result = $spawn->action("
	create table `".$tablesName['jsons']."` (
		`srl` bigint(11) not null auto_increment,
		`name` varchar(250) default null,
		`json` longtext,
		`regdate` varchar(14) default null,
		primary key (`srl`)
	) engine=InnoDB default charset=utf8");

// create table "users"
$result = $spawn->action("
	create table `".$tablesName['users']."` (
		`srl` bigint(11) not null auto_increment,
		`name` varchar(20) default null,
		`email` varchar(50) default null,
		`pw` varchar(32) default null,
		`level` int(1) not null default '0',
		`regdate` varchar(14) default null,
		primary key (`srl`)
	) engine=InnoDB default charset=utf8");

// create table "moduleGroups"
$result = $spawn->action("
	create table `".$tablesName['moduleGroups']."` (
		`srl` bigint(11) not null auto_increment,
		`name` varchar(250) default null,
		`regdate` varchar(25) default null,
		primary key (`srl`)
	) engine=InnoDB default charset=utf8");

// create table "modules"
$result = $spawn->action("
	create table `".$tablesName['modules']."` (
		`srl` bigint(11) not null auto_increment,
		`group_srl` int(11) default null,
		`id` varchar(20) default null,
		`name` varchar(250) default null,
		`thumnailSize` varchar(9) default null,
		`thumnailType` varchar(15) not null default 'crop',
		`listCount` int(11) default null,
		`useCategory` int(1) not null default '0',
		`useExtraVar` int(1) not null default '0',
		`editor` varchar(30) default null,
		`regdate` varchar(14) default null,
		primary key (`srl`)
	) engine=InnoDB default charset=utf8");

// create table "tempFiles"
$result = $spawn->action("
	create table `".$tablesName['tempFiles']."` (
		`srl` bigint(11) not null auto_increment,
		`loc` varchar(255) default null,
		`name` varchar(250) default null,
		`date` varchar(14) default null,
		PRIMARY KEY (`srl`)
	) engine=InnoDB default charset=utf8");


// insert admin info
$result = $spawn->action("
	insert into `".$tablesName['users']."`
		(`srl`, `name`, `email`, `pw`, `level`, `regdate`)
	values
		(
			null,
			'".$_POST['name']."',
			'".$_POST['email']."',
			'".md5($_POST['password'])."',
			'1',
			'".date('YmdHis')."'
		)
	");


/*
	Redirect Index
*/
$util->redirect(ROOT, "Complete install");
?>
