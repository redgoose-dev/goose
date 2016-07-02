<?php
if (!defined('__GOOSE__')) exit();

/**
 * @var array $post
 */


// remove article data
if ($post['delete_article'])
{
	$file = Module::load('file');

	// remove attach files
	$articles = Spawn::items([
		'table' => Spawn::getTableName('article'),
		'field' => 'srl',
		'where' => 'nest_srl='.(int)$post['nest_srl'],
	]);
	$file_srls = [];
	foreach($articles as $k=>$v)
	{
		$data = $file->getItems([ 'field' => 'srl', 'where' => 'article_srl='.(int)$v['srl'] ]);
		if ($data['state'] == 'success')
		{
			foreach($data['data'] as $k2=>$v2)
			{
				if ($v2['srl'])
				{
					$file_srls[] = (int)$v2['srl'];
				}
			}
		}
	}
	$file->actRemoveFile($file_srls, 'file');
	$result = Spawn::delete([
		'table' => Spawn::getTableName('article'),
		'where' => 'nest_srl='.(int)$post['nest_srl'],
		'debug' => false
	]);
}


// remove category data
if ($post['delete_category'])
{
	$result = Spawn::delete([
		'table' => Spawn::getTableName('category'),
		'where' => 'nest_srl='.(int)$post['nest_srl'],
		'debug' => false
	]);
}


// remove nest data
$result = Spawn::delete([
	'table' => Spawn::getTableName($this->name),
	'where' => 'srl='.(int)$post['nest_srl'],
	'debug' => false
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
$redirectUrl = __GOOSE_ROOT__.'/nest/index/'.$params;
return [
	'state' => 'success',
	'action' => 'redirect',
	'url' => $redirectUrl
];