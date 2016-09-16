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


// remove data
$result = core\Spawn::delete([
	'table' => core\Spawn::getTableName($this->name),
	'where' => 'srl=' . $post['user_srl']
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