<?php
if (!defined('__GOOSE__')) exit();

// include func
require_once('func.php');


// set json
$article_json = getArticleJSON($post['article_srl']);


// update db
$dbUpdateResult = require_once(__DIR__.'/../'.$this->set['skin'].'/transaction_'.$method.'.php');

// remove attach files
if ($dbUpdateResult['state'] == 'success')
{
	// remove thumnail image
	if ($article_json['thumnail']['url'] and file_exists(__GOOSE_PWD__.$article_json['thumnail']['url']))
	{
		unlink(__GOOSE_PWD__.$article_json['thumnail']['url']);
	}
}


// return
return $dbUpdateResult;