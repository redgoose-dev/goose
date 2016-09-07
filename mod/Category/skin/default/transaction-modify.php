<?php
if (!defined('__GOOSE__')) exit();

/** @var array $post */


// check post
$errorValue = core\Util::checkExistValue($post, [ 'nest_srl', 'category_srl', 'name' ]);
if ($errorValue)
{
	return [
		'state' => 'error',
		'action' => 'back',
		'message' => '[' . $errorValue . ']값이 없습니다.'
	];
}


// update data
$result = core\Spawn::update([
	'table' => core\Spawn::getTableName('category'),
	'where' => 'srl=' . (int)$post['category_srl'],
	'data' => [
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
$params = ($post['nest_srl']) ? $post['nest_srl'].'/' : '';
return [
	'state' => 'success',
	'action' => 'redirect',
	'url' => __GOOSE_ROOT__ . '/' . $this->name . '/index/' . $params
];