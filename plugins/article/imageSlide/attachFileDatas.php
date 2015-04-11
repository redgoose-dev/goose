<?php
if(!defined("GOOSE")){exit();}

if ($paramAction == 'create')
{
	$attachFiles = $goose->spawn->getItems(array(
		'table' => 'tempFiles',
		'order' => 'srl',
		'sort' => 'asc'
	));
	$status = 'complete';
	$type = 'session';
}
else if ($paramAction == 'modify')
{
	// modify
	$attachFiles = $goose->spawn->getItems(array(
		'table' => 'files',
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
			,'filetype' => $v['type']
			,'filesize' => $v['size']
			,'status' => $status
			,'type' => $type
		);
		array_push($pushData, $item);
	}
	$pushData = json_encode($pushData);
}
?>