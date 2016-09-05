<?php
if (!defined('__GOOSE__')) exit();

/**
 * @var array $post
 */

// check post
if (!isset($post)) return [ 'state' => 'error', 'action' => 'back', 'message' => 'post값이 없습니다.' ];
$errorValue = core\Util::checkExistValue($post, [ 'nest_srl', 'name', 'json' ]);
if ($errorValue)
{
	return [
		'state' => 'error',
		'action' => 'back',
		'message' => "[$errorValue]값이 없습니다."
	];
}


// update article
$result = core\Spawn::update([
	'table' => core\Spawn::getTableName('article'),
	'where' => "nest_srl=".(int)$post['nest_srl'],
	'data' => [
		"app_srl=".(int)$post['app_srl']
	]
]);


// update nest
$result = core\Spawn::update([
	'table' => core\Spawn::getTableName($this->name),
	'where' => 'srl='.(int)$post['nest_srl'],
	'data' => [
		"app_srl=".(int)$post['app_srl'],
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
$params = ($_SESSION['app_srl']) ? $_SESSION['app_srl'].'/' : '';
return [
	'state' => 'success',
	'action' => 'redirect',
	'url' => __GOOSE_ROOT__.'/Nest/index/'.$params
];