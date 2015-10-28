<?php
if (!defined('__GOOSE__')) exit();

// check user
if (!$this->isAdmin)
{
	return array(
		'state' => 'error',
		'action' => 'back',
		'message' => '권한이 없습니다.'
	);
}


// check post
$errorValue = Util::checkExistValue($post, array('title', 'content'));
if ($errorValue)
{
	return array(
		'state' => 'error',
		'action' => 'back',
		'message' => "[$errorValue]값이 없습니다."
	);
}


// adjust value
if (!$isExternalTransaction)
{
	$post['title'] = htmlspecialchars(addslashes($post['title']));
	$post['content'] = addslashes($post['content']);
}


// insert data
$result = Spawn::insert(array(
	'table' => Spawn::getTableName($this->name),
	'data' => array(
		'srl' => null,
		'app_srl' => (int)$post['app_srl'],
		'nest_srl' => (int)$post['nest_srl'],
		'category_srl' => (int)$post['category_srl'],
		'title' => $post['title'],
		'content' => $post['content'],
		'hit' => 0,
		'json' => $post['json'],
		'ip' => $_SERVER['REMOTE_ADDR'],
		'regdate' => date("YmdHis"),
		'modate' => date("YmdHis")
	)
	,'debug' => false
));
if ($result != 'success')
{
	return array(
		'state' => 'error',
		'action' => 'back',
		'message' => 'Fail execution database'
	);
}


// get last insert srl
$last_srl = Spawn::getLastIdx();


// file upload
if (count($files['upload']))
{
	// load module
	$file = Module::load('file');

	// upload file
	$uploadFiles = $file->actUploadFiles($files['upload'], 'data/upload/original/', 'file', $last_srl);
}


// redirect url
$param = ($post['nest_srl']) ? $post['nest_srl'].'/' : '';
$param .= ($post['nest_srl'] && $post['category_srl']) ? $post['category_srl'].'/' : '';
return array(
	'state' => 'success',
	'action' => 'redirect',
	'url' => ($post['redir']) ? $post['redir'] : __GOOSE_ROOT__.'/article/index/'.$param
);