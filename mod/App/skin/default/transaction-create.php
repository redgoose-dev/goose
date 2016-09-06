<?php
if (!defined('__GOOSE__')) exit();

/** @var array $post */


// check user
if (!$this->isAdmin)
{
	return [
		'state' => 'error',
		'action' => 'back',
		'message' => '권한이 없습니다.'
	];
}


// check post
$errorValue = core\Util::checkExistValue($post, [ 'id', 'name' ]);
if ($errorValue)
{
	return [
		'state' => 'error',
		'action' => 'back',
		'message' => '[' . $errorValue . ']값이 없습니다.'
	];
}


// id값 중복검사
$cnt = core\Spawn::count([
	'table' => core\Spawn::getTableName($this->name),
	'where' => 'id=' . $post['id']
]);
if ($cnt > 0)
{
	return [
		'state' => 'error',
		'action' => 'back',
		'message' => '`' . $post['id'] . '`이름의 id가 이미 존재합니다.'
	];
}


// insert data
$result = core\Spawn::insert([
	'table' => core\Spawn::getTableName('app'),
	'data' => [
		'srl' => null,
		'id' => $post['id'],
		'name' => $post['name'],
		'regdate' => date('YmdHis')
	]
]);
if ($result != 'success')
{
	return [
		'state' => 'error',
		'action' => 'back',
		'message' => 'Fail execution database'
	];
}


// redirect url
return array(
	'state' => 'success',
	'action' => 'redirect',
	'url' => __GOOSE_ROOT__ . '/' . $this->name . '/index/'
);