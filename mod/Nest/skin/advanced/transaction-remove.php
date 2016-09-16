<?php
if (!defined('__GOOSE__')) exit();

/** @var array $post */


if ($post['delete_article'])
{
	// get article data
	$articles = core\Spawn::items([
		'table' => core\Spawn::getTableName('Article'),
		'field' => 'json',
		'where' => 'nest_srl=' . (int)$post['nest_srl'],
		'jsonField' => ['json']
	]);

	// remove thumbnail urls
	foreach ($articles as $article)
	{
		$url = $article['json']['thumbnail']['url'];
		if (isset($url) && file_exists(__GOOSE_PWD__ . $url))
		{
			@unlink(__GOOSE_PWD__ . $url);
		}
	}
}


// run default skin script
$defaultRemoveScriptResult = require_once(__DIR__.'/../default/transaction-remove.php');

return $defaultRemoveScriptResult;