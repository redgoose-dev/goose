<?php
if (!defined('__GOOSE__')) exit();


/** @var array $post */

// remove article,file data
if ($post['delete_article'])
{
	// get article data
	$articles = core\Spawn::items([
		'table' => core\Spawn::getTableName('Article'),
		'field' => 'srl',
		'where' => 'nest_srl=' . (int)$post['nest_srl'],
	]);
	$file_srls = [];
	foreach($articles as $k=>$v)
	{
		$data = core\Spawn::items([
			'table' => core\Spawn::getTableName('File'),
			'field' => 'srl',
			'where' => 'article_srl=' . (int)$v['srl']
		]);
		foreach($data as $k2=>$v2)
		{
			if ($v2['srl'])
			{
				$file_srls[] = (int)$v2['srl'];
			}
		}
	}

	// remove files
	$file = new mod\File\File();
	$result = $file->actRemoveFile($file_srls);
	if ($result['state'] == 'error') core\Util::back('remove files error');

	// remove articles
	$result = core\Spawn::delete([
		'table' => core\Spawn::getTableName('Article'),
		'where' => 'nest_srl=' . (int)$post['nest_srl']
	]);
}


// remove category data
$result = core\Spawn::delete([
	'table' => core\Spawn::getTableName('Category'),
	'where' => 'nest_srl=' . (int)$post['nest_srl']
]);


// remove nest data
$result = core\Spawn::delete([
	'table' => core\Spawn::getTableName($this->name),
	'where' => 'srl=' . (int)$post['nest_srl']
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
$params = ($_SESSION['app_srl']) ? $_SESSION['app_srl'] . '/' : '';
$redirectUrl = __GOOSE_ROOT__ . '/' . $this->name . '/index/' . $params;

return [
	'state' => 'success',
	'action' => 'redirect',
	'url' => $redirectUrl
];