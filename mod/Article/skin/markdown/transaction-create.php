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
$dbUpdateResult = require_once(__DIR__.'/../default/transaction-create.php');


// include func
require_once('lib/func.php');


if ($dbUpdateResult['state'] == 'success')
{
	// set article_srl
	$article_srl = $last_srl;

	// get article json
	$article_json = getArticleJSON($article_srl);

	// off ready file data
	$thumbnail_srl = translateFileData($post, $article_srl, $article_json['thumbnail']['srl']);

	// upload thumbnail image
	if ($thumbnail_srl)
	{
		$thumbnailUrl = uploadThumbnail($post['thumbnail_image']);

		$article_json['thumbnail']['srl'] = $thumbnail_srl;
		$article_json['thumbnail']['url'] = $thumbnailUrl;

		$json_result = core\Util::arrayToJson($article_json);
		$result = core\Spawn::update([
			'table' => core\Spawn::getTableName('Article'),
			'where' => 'srl=' . $article_srl,
			'data' => [
				"json='$json_result'"
			]
		]);
	}
}


// return
return $dbUpdateResult;