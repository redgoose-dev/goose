<?php
if (!defined('__GOOSE__')) exit();

/** @var array $post */
/** @var array $files */


// check post
$errorValue = core\Util::checkExistValue($post, [ 'title', 'content' ]);
if ($errorValue)
{
	return [
		'state' => 'error',
		'action' => 'back',
		'message' => '[' . $errorValue . ']값이 없습니다.'
	];
}


// adjust value
if (!$isExternalTransaction)
{
	$post['title'] = htmlspecialchars(addslashes($post['title']));
	$post['content'] = addslashes($post['content']);
}

// insert data
$result = core\Spawn::insert([
	'table' => core\Spawn::getTableName($this->name),
	'data' => [
		'srl' => null,
		'app_srl' => (int)$post['app_srl'],
		'nest_srl' => (int)$post['nest_srl'],
		'category_srl' => (int)$post['category_srl'],
		'user_srl' => $_SESSION['goose_srl'],
		'title' => $post['title'],
		'content' => $post['content'],
		'hit' => 0,
		'json' => $post['json'],
		'ip' => $_SERVER['REMOTE_ADDR'],
		'regdate' => date("YmdHis"),
		'modate' => date("YmdHis")
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


// get last insert srl
$last_srl = core\Spawn::getLastIdx();


// file upload
if ($files['upload'] && $files['upload']['name'][0])
{
	// load module
	$file = new mod\File\File();

	// upload file
	$uploadFiles = $file->actUploadFiles($files['upload'], null, $last_srl, 0);
}


// redirect url
$param = ($post['nest_srl']) ? $post['nest_srl'] . '/' : '';
$param .= ($post['nest_srl'] && $post['category_srl']) ? $post['category_srl'] . '/' : '';
return [
	'state' => 'success',
	'action' => 'redirect',
	'url' => ($post['redir']) ? $post['redir'] : __GOOSE_ROOT__ . '/' . $this->name . '/index/' . $param
];