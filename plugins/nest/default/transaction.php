<?php
if(!defined("GOOSE")){exit();}


// load basic transaction
require_once(PWD.'/plugins/nest/'.$goose->user['skinDefault'].'/transaction.php');


// delete files
if ($paramAction == 'delete' && $articles)
{
	foreach ($articles as $k=>$v)
	{
		// get file index
		$files = $goose->spawn->getItems(array(
			'field' => 'loc',
			'table' => 'files',
			'where' => 'article_srl='.(int)$v['srl']
		));

		// delete original files
		if (count($files))
		{
			foreach ($files as $k2=>$v2)
			{
				if (file_exists(PWD.$dataOriginalDirectory.$v2['loc']))
				{
					unlink(PWD.$dataOriginalDirectory.$v2['loc']);
				}
			}
		}

		// delete thumnail image
		if ($v['thumnail_url'] and file_exists(PWD.$dataThumnailDirectory.$v['thumnail_url']))
		{
			unlink(PWD.$dataThumnailDirectory.$v['thumnail_url']);
		}

		// delete db files
		$goose->spawn->delete(array(
			'table' => 'files',
			'where' => 'article_srl='.(int)$v['srl']
		));
	}
}
