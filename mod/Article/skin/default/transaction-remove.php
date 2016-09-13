<?php
if (!defined('__GOOSE__')) exit();

/** @var array $post */


// check post
$errorValue = core\Util::checkExistValue($post, [ 'article_srl' ]);
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
	'table' => core\Spawn::getTableName($this->name),
	'where' => 'srl=' . (int)$post['article_srl']
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


// get srls
$files = core\Spawn::items([
	'table' => core\Spawn::getTableName('File'),
	'field' => 'srl',
	'where' => 'article_srl=' . $post['article_srl']
]);
if ($files)
{
	$srls = [];
	foreach($files as $k=>$v)
	{
		$srls[] = (int)$v['srl'];
	}
	$result = $file->actRemoveFile($srls);
}


// redirect url
$param = ($post['nest_srl']) ? $post['nest_srl'] . '/' : '';
$param .= ($post['nest_srl'] && $post['category_srl']) ? $post['category_srl'] . '/' : '';
return [
	'state' => 'success',
	'action' => 'redirect',
	'url' => ($post['redir']) ? $post['redir'] : __GOOSE_ROOT__ . '/' . $this->name . '/index/' . $param
];