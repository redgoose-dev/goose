<?php
if (!defined('__GOOSE__')) exit();

$files = null;

switch($this->param['action'])
{
	case 'create':
		$files = Spawn::items(array(
			'table' => Spawn::getTableName('file_tmp'),
			'order' => 'srl',
			'sort' => 'asc'
		));
		$state = 'complete';
		$type = 'session';
		break;
	case 'modify':
		$files = Spawn::items(array(
			'table' => Spawn::getTableName('file'),
			'where' => 'article_srl='.(int)$article_srl,
			'order' => 'srl',
			'sort' => 'asc'
		));
		$state = 'uploaded';
		$type = 'edit';
		break;
}

// adjust data
$pushData = array();
if (count($files))
{
	foreach ($files as $k=>$v)
	{
		$item = array(
			'srl' => $v['srl']
			,'location' => $v['loc']
			,'filename' => $v['name']
			,'filetype' => $v['type']
			,'filesize' => $v['size']
			,'state' => $state
			,'type' => $type
		);
		$pushData[] = $item;
	}
}

return json_encode($pushData);