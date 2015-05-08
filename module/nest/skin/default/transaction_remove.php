<?php
if (!defined('__GOOSE__')) exit();

// check user
if (!$this->isAdmin)
{
	return array(
		'state' => 'error',
		'action' => 'back',
		'message' => '권한이 없습니다.'
	);
}


// remove nest data
$result = Spawn::delete(array(
	'table' => Spawn::getTableName($this->name),
	'where' => 'srl='.(int)$post['nest_srl']
	,'debug' => false
));
if ($result != 'success')
{
	return array(
		'state' => 'error',
		'action' => 'back',
		'message' => 'Fail execution database'
	);
}

var_dump($post['delete_article']);
var_dump($post['delete_file']);
var_dump($post['delete_category']);
// 삭제할 항목을 checkbox를 체크하여 삭제해야한다.
// 기본적으로 체크되어있어야한다.
// remove articles
// remove categories
// remove files


// redirect url
$params = ($_SESSION['app_srl']) ? $_SESSION['app_srl'].'/' : '';
$redirectUrl = __GOOSE_ROOT__.'nest/index/'.$params;
return array(
	'state' => 'success',
	'action' => 'redirect',
	'url' => $redirectUrl
);