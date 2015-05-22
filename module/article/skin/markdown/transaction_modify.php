<?php
if (!defined('__GOOSE__')) exit();

// reset $_FILES
$_FILES = $files = null;


// adjust value
$post['title'] = addslashes($post['title']);
$post['content'] = addslashes($post['content']);


// update db
$dbUpdateResult = require_once(__DIR__.'/../'.$this->set['skin'].'/transaction_'.$method.'.php');


// include func
require_once('func.php');


// move data file_tmp to file
if ($dbUpdateResult['state'] == 'success')
{
	// set article_srl
	$article_srl = (int)$post['article_srl'];

	// get article json
	$article_json = getArticleJSON($article_srl);
	$new_article_json = $article_json;

	// move file_tmp to file data
	$thumnail_srl = fileUpload($post, $article_srl, $article_json['thumnail']['srl']);

	// upload thumnail image
	if ($post['thumnail_image'])
	{
		// load file module
		if (file_exists(__GOOSE_PWD__.$article_json['thumnail']['url']))
		{
			unlink(__GOOSE_PWD__.$article_json['thumnail']['url']);
		}
		// upload
		$thumnailUrl = uploadThumnail($_POST['thumnail_image']);
		// set json
		$new_article_json['thumnail']['srl'] = $thumnail_srl;
		$new_article_json['thumnail']['url'] = $thumnailUrl;

		$thumnailUploaded = true;
	}

	// 썸네일 이미지는 있고, 썸네일 이미지가 새로 만들어지지 않을때
	if ($article_json['thumnail']['srl'] && !$thumnailUploaded)
	{
		// get article item data
		$filesCount = Spawn::count(array(
			'table' => Spawn::getTableName('file'),
			'where' => 'article_srl='.(int)$post['article_srl'].' and srl='.(int)$article_json['thumnail']['srl']
		));
		if (!$filesCount)
		{
			// delete thumnail file
			if (file_exists(__GOOSE_PWD__.$article_json['thumnail']['url']))
			{
				unlink(__GOOSE_PWD__.$article_json['thumnail']['url']);
			}
			// set json
			$new_article_json['thumnail'] = array('srl' => '0', 'url' => '', 'coords' => '');
		}
	}

	// convert array to json
	$json_result = Util::arrayToJson($new_article_json, true);

	// update article
	$result = Spawn::update(array(
		'table' => Spawn::getTableName('article'),
		'where' => 'srl='.(int)$article_srl,
		'data' => array( "json='$json_result'" )
	));
}


// return
return $dbUpdateResult;