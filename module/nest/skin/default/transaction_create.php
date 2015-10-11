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


// check post
$errorValue = Util::checkExistValue($post, array('name', 'id', 'json'));
if ($errorValue)
{
	return array(
		'state' => 'error',
		'action' => 'back',
		'message' => "[$errorValue]값이 없습니다."
	);
}


// 아이디값 중복 확인
$cnt = Spawn::count(array(
	'table' => Spawn::getTableName('nest'),
	'where' => "id='$post[id]'"
));
if ($cnt > 0)
{
	return array(
		'state' => 'error',
		'action' => 'back',
		'message' => 'id가 이미 존재합니다.'
	);
}


// insert data
$result = Spawn::insert(array(
	'table' => Spawn::getTableName('nest'),
	'data' => array(
		'srl' => null,
		'app_srl' => (int)$post['app_srl'],
		'id' => $post['id'],
		'name' => $post['name'],
		'json' => $post['json'],
		'regdate' => date('YmdHis')
	)
));
if ($result != 'success')
{
	return array(
		'state' => 'error',
		'action' => 'back',
		'message' => 'Fail execution database'
	);
}


// redirect url
$params = ($_SESSION['app_srl']) ? $_SESSION['app_srl'] : '';

return array(
	'state' => 'success',
	'action' => 'redirect',
	'url' => __GOOSE_ROOT__.'/nest/index/'.$params
);