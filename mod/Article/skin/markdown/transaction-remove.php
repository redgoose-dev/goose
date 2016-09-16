<?php
if (!defined('__GOOSE__')) exit();

/** @var array $post */


// include func
require_once('lib/func.php');


// set json (db에서 삭제하기전에 article json 데이터를 받아놓기)
$article_json = getArticleJSON($post['article_srl']);


// update db
$dbUpdateResult = require_once(__DIR__.'/../default/transaction-remove.php');


// remove thumbnail image
if ($dbUpdateResult['state'] == 'success')
{
	// remove thumbnail image
	if ($article_json['thumbnail']['url'] and file_exists(__GOOSE_PWD__ . $article_json['thumbnail']['url']))
	{
		unlink(__GOOSE_PWD__ . $article_json['thumbnail']['url']);
	}
}


// return
return $dbUpdateResult;