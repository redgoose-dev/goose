<?php
if (!defined('__GOOSE__')) exit();


/**
 * @var array $post
 */

// remove article data
if ($post['delete_article'])
{
	$file = new mod\File\File();
	// TODO : Article 모듈 작업 끝내면 작업하기
//	// remove attach files
//	$articles = core\Spawn::items([
//		'table' => core\Spawn::getTableName('article'),
//		'field' => 'srl',
//		'where' => 'nest_srl='.(int)$post['nest_srl'],
//	]);
//	$file_srls = [];
//	foreach($articles as $k=>$v)
//	{
//		$data = $file->getItems([ 'field' => 'srl', 'where' => 'article_srl='.(int)$v['srl'] ]);
//		if ($data['state'] == 'success')
//		{
//			foreach($data['data'] as $k2=>$v2)
//			{
//				if ($v2['srl'])
//				{
//					$file_srls[] = (int)$v2['srl'];
//				}
//			}
//		}
//	}
//	$file->actRemoveFile($file_srls, 'file');
//	$result = core\Spawn::delete([
//		'table' => core\Spawn::getTableName('article'),
//		'where' => 'nest_srl='.(int)$post['nest_srl'],
//		'debug' => false
//	]);
}
exit;


// remove category data
$result = core\Spawn::delete([
	'table' => core\Spawn::getTableName('category'),
	'where' => 'nest_srl='.(int)$post['nest_srl']
]);


// remove nest data
$result = core\Spawn::delete([
	'table' => core\Spawn::getTableName('nest'),
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