<?php
if (!defined('__GOOSE__')) exit();

/**
 * @var array $post
 */


// check post
$errorValue = Util::checkExistValue($post, ['article_srl']);
if ($errorValue)
{
	return [ 'state' => 'error', 'action' => 'back', 'message' => "[$errorValue]값이 없습니다." ];
}


// remove data
$result = Spawn::delete([
	'table' => Spawn::getTableName($this->name),
	'where' => 'srl='.$post['article_srl']
]);
if ($result != 'success')
{
	return [
		'state' => 'error',
		'action' => 'back',
		'message' => 'Fail execution database'
	];
}


// load module
$file = Module::load('file');


// get srls
$data = $file->getItems([
	'field' => 'srl',
	'where' => 'article_srl='.$post['article_srl']
]);
$files = ($data['state'] == 'success') ? $data['data'] : null;
if ($files)
{
	$srls = [];
	foreach($files as $k=>$v)
	{
		$srls[] = (int)$v['srl'];
	}
	$result = $file->actRemoveFile($srls, 'file');
}


// redirect url
$param = ($post['nest_srl']) ? $post['nest_srl'].'/' : '';
$param .= ($post['nest_srl'] && $post['category_srl']) ? $post['category_srl'].'/' : '';
return [
	'state' => 'success',
	'action' => 'redirect',
	'url' => ($post['redir']) ? $post['redir'] : __GOOSE_ROOT__.'/'.$this->name.'/index/'.$param
];