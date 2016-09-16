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
$errorValue = core\Util::checkExistValue($post, [ 'json_srl', 'name', 'json' ]);
if ($errorValue)
{
	return [
		'state' => 'error',
		'action' => 'back',
		'message' => '[' . $errorValue . ']값이 없습니다.'
	];
}


// insert data
$result = core\Spawn::update([
	'table' => core\Spawn::getTableName($this->name),
	'where' => 'srl=' . (int)$post['json_srl'],
	'data' => [
		"name='$post[name]'",
		"json='$post[json]'"
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
	'url' => __GOOSE_ROOT__ . '/' . $this->name . '/read/' . $post['json_srl'] . '/'
];