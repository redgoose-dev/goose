<?php
if(!defined("GOOSE")){exit();}

$root = preg_replace('/\/index.php$/', '', $_SERVER['PHP_SELF']);
$url = 'http://'.$_SERVER['HTTP_HOST'].$root;
$error = false;


/**
 * Check $_POST
 * 
 * @return Boolean : 이상이 없으면 true, 문제가 있으면 false값을 리턴한다.
 */
function checkPost()
{
	global $goose;
	foreach($_POST as $k=>$v)
	{
		if (!$v)
		{
			$goose->util->back("[$k] 값이 없습니다.");
			return false;
		}
	}
	if ($_POST['dbPassword'] != $_POST['dbPassword2'])
	{
		$goose->util->back("DB비밀번호와 확인값이 다릅니다.");
		return false;
	}
	if ($_POST['password'] != $_POST['password2'])
	{
		$goose->util->back("관리자 비밀번호와 확인값이 다릅니다.");
		return false;
	}
	return true;
}


/**
 * Create user file value
 * 
 * @param Array $post : post 데이터
 * @param String $dir : user파일위치
 * @return String : 처리결과
 */
function createUserFile($post, $dir)
{
	global $goose, $root, $url;

	$str = "<?php\n";
	$str .= "if(!defined(\"GOOSE\")){exit();}\n";
	$str .= "\n";
	$str .= "define('GOOSE_ROOT', '".$post['root']."');\n";
	$str .= "define('GOOSE_URL', '".$post['url']."');\n";
	$str .= "\n";
	$str .= "\$dbConfig = array('mysql:dbname=".$post['dbName'].";host=".$post['dbHost']."', '".$post['dbId']."', '".$post['dbPassword']."');\n";
	$str .= "\$tablesName = array(\n";
	$str .= "\t'articles' => '".$post['dbPrefix']."articles',\n";
	$str .= "\t'categories' => '".$post['dbPrefix']."categories',\n";
	$str .= "\t'files' => '".$post['dbPrefix']."files',\n";
	$str .= "\t'users' => '".$post['dbPrefix']."users',\n";
	$str .= "\t'nestGroups' => '".$post['dbPrefix']."nestGroups',\n";
	$str .= "\t'nests' => '".$post['dbPrefix']."nests',\n";
	$str .= "\t'tempFiles' => '".$post['dbPrefix']."tempFiles',\n";
	$str .= "\t'jsons' => '".$post['dbPrefix']."jsons'\n";
	$str .= ");\n";
	$str .= "\$api_key = \"".$post['apiPrefix']."\";\n";
	$str .= "\$user = array(\n";
	$str .= "\t'adminLevel' => ".$post['adminLevel']."\n";
	$str .= "\t,'indexCount' => 30\n";
	$str .= "\t,'timezone' => '".$post['timezone']."'\n";
	$str .= "\t,'skinDefault' => 'basic'\n";
	$str .= ");\n";
	$str .= "?>";

	return $goose->util->fop($dir, 'w', $str, 0777);
}


// 파일 만들기
if (checkPost() == true)
{
	// create directory
	$goose->util->createDirectory(PWD."/data", 0777);
	$goose->util->createDirectory(PWD."/data/config", 0755);
	$goose->util->createDirectory(PWD."/data/original", 0777);
	$goose->util->createDirectory(PWD."/data/thumnail", 0777);

	// create user.php
	$_POST['root'] = $root;
	$_POST['url'] = $url;
	$_POST['adminLevel'] = 1;
	if (createUserFile($_POST, PWD.'/data/config/user.php') != 'success')
	{
		echo '<p>Failed to create the file user.php</p>';
		exit;
	}
}
else
{
	exit;
}


/*
	Install Database
*/
require_once(PWD.'/data/config/user.php');
require_once(PWD.'/libs/Database.class.php');
require_once(PWD.'/libs/Spawn.class.php');

// create instanse object
$spawn = new Spawn($dbConfig, $tablesName);
$spawn->action("set names utf8");

// create db table
// create table "articles"
$result = $spawn->action("
	create table `".$tablesName['articles']."` (
		`srl` bigint(11) not null auto_increment,
		`group_srl` int(11) default null,
		`nest_srl` bigint(11) default null,
		`category_srl` bigint(11) default null,
		`title` varchar(250) default null,
		`content` longtext,
		`regdate` varchar(14) default null,
		`modate` varchar(14) default null,
		`hit` int(11) not null default 0,
		`json` text default null,
		`ipAddress` varchar(15) default null,
		primary key (`srl`),
		unique key `srl` (`srl`)
	) engine=InnoDB default charset=utf8");
if ($result != 'success')
{
	$error = true;
	echo "<p>Fail create '".$tablesName['articles']."' table</p>";
}

// create table "categories"
$result = $spawn->action("
	create table `".$tablesName['categories']."` (
		`srl` bigint(11) not null auto_increment,
		`nest_srl` bigint(11) default null,
		`turn` int(11) default null,
		`name` varchar(30) default null,
		`regdate` varchar(25) default null,
		primary key (`srl`),
		unique key `srl` (`srl`)
	) engine=InnoDB default charset=utf8");
if ($result != 'success')
{
	$error = true;
	echo "<p>Fail create '".$tablesName['categories']."' table</p>";
}

// create table "files"
$result = $spawn->action("
	create table `".$tablesName['files']."` (
		`srl` bigint(11) not null auto_increment,
		`article_srl` bigint(11) default null,
		`name` varchar(255) default null,
		`loc` varchar(255) default null,
		primary key (`srl`)
	) engine=InnoDB default charset=utf8");
if ($result != 'success')
{
	$error = true;
	echo "<p>Fail create '".$tablesName['files']."' table</p>";
}

// create table "jsons"
$result = $spawn->action("
	create table `".$tablesName['jsons']."` (
		`srl` bigint(11) not null auto_increment,
		`name` varchar(250) default null,
		`json` longtext,
		`regdate` varchar(14) default null,
		primary key (`srl`)
	) engine=InnoDB default charset=utf8");
if ($result != 'success')
{
	$error = true;
	echo "<p>Fail create '".$tablesName['jsons']."' table</p>";
}

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
if ($result != 'success')
{
	$error = true;
	echo "<p>Fail create '".$tablesName['users']."' table</p>";
}

// create table "nestGroups"
$result = $spawn->action("
	create table `".$tablesName['nestGroups']."` (
		`srl` bigint(11) not null auto_increment,
		`name` varchar(250) default null,
		`regdate` varchar(25) default null,
		primary key (`srl`)
	) engine=InnoDB default charset=utf8");
if ($result != 'success')
{
	$error = true;
	echo "<p>Fail create '".$tablesName['nestGroups']."' table</p>";
}

// create table "nests"
$result = $spawn->action("
	create table `".$tablesName['nests']."` (
		`srl` bigint(11) not null auto_increment,
		`group_srl` int(11) default null,
		`id` varchar(20) default null,
		`name` varchar(250) default null,
		`listCount` int(11) default null,
		`useCategory` int(1) not null default '0',
		`json` text default null,
		`regdate` varchar(14) default null,
		primary key (`srl`)
	) engine=InnoDB default charset=utf8");
if ($result != 'success')
{
	$error = true;
	echo "<p>Fail create '".$tablesName['nests']."' table</p>";
}

// create table "tempFiles"
$result = $spawn->action("
	create table `".$tablesName['tempFiles']."` (
		`srl` bigint(11) not null auto_increment,
		`loc` varchar(255) default null,
		`name` varchar(250) default null,
		`date` varchar(14) default null,
		primary key (`srl`)
	) engine=InnoDB default charset=utf8");
if ($result != 'success')
{
	$error = true;
	echo "<p>Fail create '".$tablesName['tempFiles']."' table</p>";
}


// insert admin info
$userCount = $spawn->getCount(array(
	'table' => $tablesName['users']
	,'where' => 'email="' . $_POST['email'] . '"'
));
if ($userCount)
{
	$error = true;
	echo '<p>Exist admin user</p>';
}
else
{
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
	if ($result == 'success')
	{
		echo 'Add admin user';
	}
	else
	{
		echo '<p>'.$result.'</p>';
	}
}


/*
	Redirect Index
*/
if (!$error)
{
	$goose->util->redirect(GOOSE_ROOT."/", "Complete install");
}
?>
