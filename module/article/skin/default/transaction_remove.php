<?php
if (!defined('__GOOSE__')) exit();

// check user
if (!$this->isAdmin)
{
	return array('state' => 'error', 'action' => 'back', 'message' => '권한이 없습니다.');
}


// check post
$errorValue = Util::checkExistValue($post, array('article_srl'));
if ($errorValue)
{
	return array('state' => 'error', 'action' => 'back', 'message' => "[$errorValue]값이 없습니다.");
}


// remove data
$result = Spawn::delete(array(
	'table' => Spawn::getTableName($this->name),
	'where' => 'srl='.$post['article_srl']
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


// get srls
$data = $file->getItems(array(
	'field' => 'srl',
	'where' => 'article_srl='.$post['article_srl']
));
$files = ($data['state'] == 'success') ? $data['data'] : null;
if ($files)
{
	$srls = Array();
	foreach($files as $k=>$v)
	{
		$srls[] = (int)$v['srl'];
	}
	$result = $file->actRemoveFile($srls, 'file');
}


// redirect url
$param = ($post['nest_srl']) ? $post['nest_srl'].'/' : '';
$param .= ($post['nest_srl'] && $post['category_srl']) ? $post['category_srl'].'/' : '';
return array(
	'state' => 'success',
	'action' => 'redirect',
	'url' => ($post['redir']) ? $post['redir'] : __GOOSE_ROOT__.'/'.$this->name.'/index/'.$param
);