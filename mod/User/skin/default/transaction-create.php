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
$errorValue = core\Util::checkExistValue($post, [ 'name', 'email', 'pw', 'level' ]);
if ($errorValue)
{
	return [
		'state' => 'error',
		'action' => 'back',
		'message' => '[' . $errorValue . ']값이 없습니다.'
	];
}


// check email address
$cnt = core\Spawn::count([
	'table' => core\Spawn::getTableName($this->name),
	'where' => "email='$post[email]'"
]);
if ($cnt > 0)
{
	return [
		'state' => 'error',
		'action' => 'back',
		'message' => '이메일주소가 이미 존재합니다.'
	];
}


// check password
if ($post['pw'] != $post['pw2'])
{
	return [
		'state' => 'error',
		'action' => 'back',
		'message' => '비밀번호와 비밀번호 확인값이 틀립니다.'
	];
}


// insert data
$result = core\Spawn::insert([
	'table' => core\Spawn::getTableName($this->name),
	'data' => [
		'srl' => null,
		'email' => $post['email'],
		'name' => $post['name'],
		'pw' => password_hash($post['pw'], PASSWORD_DEFAULT),
		'level' => $post['level'],
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
return [
	'state' => 'success',
	'action' => 'redirect',
	'url' => __GOOSE_ROOT__ . '/' . $this->name . '/index/'
];