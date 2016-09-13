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


// update data
$result = core\Spawn::update([
	'table' => core\Spawn::getTableName($this->name),
	'where' => 'srl='.(int)$post['article_srl'],
	'data' => [
		"app_srl=" . (int)$post['app_srl'],
		"nest_srl=" . (int)$post['nest_srl'],
		"category_srl=" . (int)$post['category_srl'],
		"title='" . $post['title'] . "'",
		"content='$post[content]'",
		"json='$post[json]'",
		"modate='" . date("YmdHis") . "'"
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


/**
 * @var mod\File\File $file
 */
$file = new mod\File\File();


// remove files
if (count($post['removeFiles']))
{
	$result = $file->actRemoveFile($post['removeFiles']);
}


// file upload
if (count($files['upload']))
{
	$result = $file->actUploadFiles($files['upload'], null, $post['article_srl'], 0);
}


// redirect url
$param = ($post['category_srl']) ? $post['category_srl'] . '/' : '';
$param .= ($post['article_srl']) ? $post['article_srl'] . '/' : '';
$param .= ($post['m']) ? '?m=1' : '';
return [
	'state' => 'success',
	'action' => 'redirect',
	'url' => ($post['redir']) ? $post['redir'] : __GOOSE_ROOT__ . '/' . $this->name . '/read/' . $param
];