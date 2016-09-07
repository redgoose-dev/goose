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
$errorValue = core\Util::checkExistValue($post, [ 'nest_srl', 'category_srl' ]);
if ($errorValue)
{
	return [
		'state' => 'error',
		'action' => 'back',
		'message' => '[' . $errorValue . ']값이 없습니다.'
	];
}


// remove data
$result = core\Spawn::delete([
	'table' => core\Spawn::getTableName('category'),
	'where' => 'srl=' . $post['category_srl']
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
$params = ($post['nest_srl']) ? $post['nest_srl'] . '/' : '';
return array(
	'state' => 'success',
	'action' => 'redirect',
	'url' => __GOOSE_ROOT__ . '/' . $this->name . '/index/' . $params
);