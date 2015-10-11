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
	return array('state' => 'error', 'action' => 'back', 'message' => "[$errorValue]값이 없습니다.");
}


// adjust value
if (!$isExternalTransaction)
{
	$post['title'] = htmlspecialchars(addslashes($post['title']));
	$post['content'] = addslashes($post['content']);
}


// update data
$result = Spawn::update(array(
	'table' => Spawn::getTableName($this->name),
	'where' => 'srl='.(int)$post['article_srl'],
	'data' => array(
		"app_srl=".(int)$post['app_srl'],
		"nest_srl=".(int)$post['nest_srl'],
		"category_srl=".(int)$post['category_srl'],
		"title='".$post['title']."'",
		"content='$post[content]'",
		"json='$post[json]'",
		"modate='".date("YmdHis")."'"
	)
));
if ($result != 'success')
{
	return array(
		'state' => 'error',
		'action' => 'back',
		'message' => 'Fail execution database'
	);
}


// load module
$file = Module::load('file');


// remove files
if (count($post['removeFiles']))
{
	$result = $file->actRemoveFile($post['removeFiles'], 'file');
}


// file upload
if (count($files['upload']))
{
	$result = $file->actUploadFiles($files['upload'], 'data/upload/original/', 'file', $post['article_srl']);
}


// redirect url
$param = ($post['category_srl']) ? $post['category_srl'].'/' : '';
$param .= ($post['article_srl']) ? $post['article_srl'].'/' : '';
$param .= ($post['m']) ? '?m=1' : '';
return array(
	'state' => 'success',
	'action' => 'redirect',
	'url' =>  __GOOSE_ROOT__.'/article/read/'.$param
);