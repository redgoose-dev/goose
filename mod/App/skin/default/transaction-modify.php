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
$errorValue = core\Util::checkExistValue($post, [ 'id', 'name', 'app_srl' ]);
if ($errorValue)
{
	return [
		'state' => 'error',
		'action' => 'back',
		'message' => '[' . $errorValue . ']값이 없습니다.'
	];
}


// check id
$app = core\Spawn::item([
	'table' => core\Spawn::getTableName($this->name),
	'field' => 'id',
	'where' => "srl=" . (int)$post['app_srl']
]);
if ($app['id'] != $post['id'])
{
	$cnt = core\Spawn::count([
		'table' => core\Spawn::getTableName($this->name),
		'where' => 'id="' . $post['id'] . '"'
	]);
	if ($cnt > 0)
	{
		return [
			'state' => 'error',
			'action' => 'back',
			'message' => '`' . $post['id'] . '`이름의 id가 이미 존재합니다.'
		];
	}
}


// update data
$result = core\Spawn::update([
	'table' => core\Spawn::getTableName($this->name),
	'where' => 'srl=' . (int)$post['app_srl'],
	'data' => [
		"id='$post[id]'",
		"name='$post[name]'"
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