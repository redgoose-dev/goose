<?php
if (!defined('__GOOSE__')) exit();

$files = null;

// check action keyword
if ($this && isset($this->param['action']))
{
	$action = $this->param['action'];
}
else if (!isset($action))
{
	return "[]";
}


switch($action)
{
	case 'create':
		$files = Spawn::items([
			'table' => Spawn::getTableName('file_tmp'),
			'order' => 'srl',
			'sort' => 'asc'
		]);
		$table = 'file_tmp';
		break;
	case 'modify':
		$files = Spawn::items([
			'table' => Spawn::getTableName('file'),
			'where' => 'article_srl='.(int)$article_srl,
			'order' => 'srl',
			'sort' => 'asc'
		]);
		$table = 'file';
		break;
}

// adjust data
$pushData = [];
if (count($files))
{
	foreach ($files as $k=>$v)
	{
		$item = [
			'id' => rand(10000000,99999999),
			'srl' => (int)$v['srl'],
			'name' => $v['name'],
			'size' => (int)$v['size'],
			'src' => $v['loc'],
			'type' => $v['type'],
			'table' => $table
		];
		$pushData[] = $item;
	}
}

return json_encode($pushData);