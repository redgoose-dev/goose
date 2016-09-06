<?php
if (!defined('__GOOSE__')) exit();

/** @var array $post */


// check post
$errorValue = core\Util::checkExistValue($post, [ 'nest_srl', 'name', 'json' ]);
if ($errorValue)
{
	return [
		'state' => 'error',
		'action' => 'back',
		'message' => '[' . $errorValue . ']값이 없습니다.'
	];
}

// id값 중복검사
$nest = core\Spawn::item([
	'table' => core\Spawn::getTableName('nest'),
	'field' => 'id',
	'where' => "srl=" . (int)$post['nest_srl']
]);
if ($nest['id'] != $post['id'])
{
	$cnt = core\Spawn::count([
		'table' => core\Spawn::getTableName('nest'),
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


// update article
$result = core\Spawn::update([
	'table' => core\Spawn::getTableName('article'),
	'where' => "nest_srl=" . (int)$post['nest_srl'],
	'data' => [
		"app_srl=" . (int)$post['app_srl']
	]
]);


// update nest
$result = core\Spawn::update([
	'table' => core\Spawn::getTableName('nest'),
	'where' => 'srl=' . (int)$post['nest_srl'],
	'data' => [
		"app_srl=" . (int)$post['app_srl'],
		"id='$post[id]'",
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
$params = ($_SESSION['app_srl']) ? $_SESSION['app_srl'] . '/' : '';
return [
	'state' => 'success',
	'action' => 'redirect',
	'url' => __GOOSE_ROOT__ . '/' . $this->name . '/index/' . $params
];