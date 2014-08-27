<?php
if(!defined("GOOSE")){exit();}

$ipAddress = $_SERVER['REMOTE_ADDR'];
$regdate = date("YmdHis");
$_POST['title'] = htmlspecialchars($_POST['title']);

// get nest
$nest = $goose->spawn->getItem(array(
	'table' => 'nests',
	'where' => 'srl='.$_POST['nest_srl']
));
try {
	$nest['json'] = json_decode(urldecode($nest['json']), true);
} catch(Exception $e) {}


// load skin file
$skin = (isset($nest['json']['articleSkin'])) ? $nest['json']['articleSkin'] : 'basic';

if (is_file(PWD.'/plugins/article/'.$skin.'/transaction.php'))
{
	require_once(PWD.'/plugins/article/'.$skin.'/transaction.php');
}
else if (is_file(PWD.'/plugins/article/basic/transaction.php'))
{
	require_once(PWD.'/plugins/article/basic/transaction.php');
}
else
{
	$goose->util->alert('불러올 페이지가 없습니다.');
	$goose->out();
}
?>