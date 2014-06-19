<?
if(!defined("GOOSE")){exit();}

// init Util class
require_once(PWD.'/libs/Util.class.php');
$util = new Util();

// check install
if (!file_exists(PWD."/data/config/user.php"))
{
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		require_once(PWD.'/transaction/install.php');
	}
	else
	{
		require_once(PWD.'/pages/install.php');
	}
	$util->out();
}
?>