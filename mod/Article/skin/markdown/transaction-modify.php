<?php
if (!defined('__GOOSE__')) exit();

/** @var array $post */


// 외부에서 불러들인 처리파일이라는 확인값
$isExternalTransaction = true;


// reset $_FILES
$_FILES = $files = null;


// adjust value
$post['title'] = addslashes($post['title']);
$post['content'] = addslashes($post['content']);


// update db
$dbUpdateResult = require_once(__DIR__.'/../default/transaction-modify.php');


// include func
require_once('lib/func.php');


// update attach files
if ($dbUpdateResult['state'] == 'success')
{
	// set thumbnail uploaded
	$thumbnailUploaded = false;

	// set article_srl
	$article_srl = (int)$post['article_srl'];

	// get article json
	$article_json = getArticleJSON($article_srl);
	$new_article_json = $article_json;

	// move file_tmp to file data
	$thumbnail_srl = translateFileData($post, $article_srl, $article_json['thumbnail']['srl']);

	// upload thumbnail image
	if ($post['thumbnail_image'])
	{
		// load file module
		if (file_exists(__GOOSE_PWD__ . $article_json['thumbnail']['url']))
		{
			unlink(__GOOSE_PWD__ . $article_json['thumbnail']['url']);
		}
		// upload
		$thumbnailUrl = uploadThumbnail($_POST['thumbnail_image']);
		// set json
		$new_article_json['thumbnail']['srl'] = $thumbnail_srl;
		$new_article_json['thumbnail']['url'] = $thumbnailUrl;

		$thumbnailUploaded = true;
	}

	// 썸네일 이미지는 이미 존재하고, 썸네일 이미지가 새로 만들어지지 않을때
	if ($article_json['thumbnail']['srl'] && !$thumbnailUploaded)
	{
		// get article item data
		$filesCount = core\Spawn::count([
			'table' => core\Spawn::getTableName('File'),
			'where' => 'article_srl=' . (int)$post['article_srl'] . ' and srl=' . (int)$article_json['thumbnail']['srl']
		]);
		// 썸네일 이미지의 원본 첨부파일이 없으면 삭제한다.
		if (!$filesCount)
		{
			// delete thumbnail file
			if (file_exists(__GOOSE_PWD__ . $article_json['thumbnail']['url']))
			{
				unlink(__GOOSE_PWD__ . $article_json['thumbnail']['url']);
			}
			// set json
			$new_article_json['thumbnail'] = null;
		}
	}

	// convert array to json
	$json_result = core\Util::arrayToJson($new_article_json, true);

	// update article
	$result = core\Spawn::update([
		'table' => core\Spawn::getTableName('article'),
		'where' => 'srl='.(int)$article_srl,
		'data' => [ "json='$json_result'" ]
	]);
}


// return
return $dbUpdateResult;