<?php
if(!defined("GOOSE")){exit();}

if ($paramAction == 'create')
{
	$attachFiles = $spawn->getItems(array(
		'table' => $tablesName['tempFiles'],
		'order' => 'srl',
		'sort' => 'asc'
	));
	$status = 'complete';
	$type = 'session';
}
else if ($paramAction == 'modify')
{
	// modify
	$attachFiles = $spawn->getItems(array(
		'table' => $tablesName['files'],
		'where' => 'article_srl='.$article_srl,
		'order' => 'srl',
		'sort' => 'asc'
	));
	$status = 'uploaded';
	$type = 'modify';
}

if (count($attachFiles))
{
	$pushData = array();
	foreach ($attachFiles as $k=>$v)
	{
		$item = array(
			'srl' => $v['srl']
			,'location' => $v['loc']
			,'filename' => $v['name']
			,'status' => $status
			,'type' => $type
		);
		array_push($pushData, $item);
	}
	$pushData = json_encode($pushData);
}
?>