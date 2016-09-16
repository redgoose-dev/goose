<?php
if (!defined('__GOOSE__')) exit();

/** @var array $post */


// check user
if (!$this->isAdmin && ($post['email'] != $_SESSION['goose_email']))
{
	return [
		'state' => 'error',
		'action' => 'back',
		'message' => '권한이 없습니다.'
	];
}


// check post
$errorValue = core\Util::checkExistValue($post, [ 'name', 'level' ]);
if ($errorValue)
{
	return [
		'state' => 'error',
		'action' => 'back',
		'message' => '[' . $errorValue . ']값이 없습니다.'
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


// update data
$result = core\Spawn::update([
	'table' => core\Spawn::getTableName($this->name),
	'where' => 'srl=' . (int)$post['user_srl'],
	'data' => [
		"name='" . $post['name'] . "'",
		"level=" . (int)$post['level'],
		($post['pw']) ? "pw='" . md5($post['pw']) . "'" : null
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