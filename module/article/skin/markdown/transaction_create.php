<?php
if (!defined('__GOOSE__')) exit();

// 외부에서 불러들인 처리파일이라는 확인값
$isExternalTransaction = true;


// reset $_FILES
$_FILES = $files = null;


// adjust value
$post['title'] = addslashes($post['title']);
$post['content'] = addslashes($post['content']);


// update db
$dbUpdateResult = require_once(__DIR__.'/../'.$this->set['skin'].'/transaction_'.$method.'.php');


// include func
require_once('func.php');


if ($dbUpdateResult['state'] == 'success')
{
	// set article_srl
	$article_srl = $last_srl;

	// get article json
	$article_json = getArticleJSON($article_srl);

	// move file_tmp to file data
	$thumnail_srl = fileUpload($post, $article_srl, $article_json['thumnail']['srl']);

	// upload thumnail image
	if ($thumnail_srl)
	{
		$thumnailUrl = uploadThumnail($post['thumnail_image']);

		$article_json['thumnail']['srl'] = $thumnail_srl;
		$article_json['thumnail']['url'] = $thumnailUrl;
		$json_result = Util::arrayToJson($article_json);

		$result = Spawn::update(array(
			'table' => Spawn::getTableName('article'),
			'where' => 'srl='.$article_srl,
			'data' => array(
				"json='$json_result'"
			)
		));
	}
}


// return
return $dbUpdateResult;