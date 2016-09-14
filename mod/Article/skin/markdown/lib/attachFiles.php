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

// get file data
$files = core\Spawn::items([
	'table' => core\Spawn::getTableName('File'),
	'where' => (($action == 'modify') ? 'article_srl=' . (int)$article_srl : 'ready=1'),
	'order' => 'srl',
	'sort' => 'asc'
]);

// adjust data
$pushData = [];
if (count($files))
{
	foreach ($files as $k=>$v)
	{
		$item = [
			'id' => (int)$v['srl'],
			'srl' => (int)$v['srl'],
			'name' => $v['name'],
			'size' => (int)$v['size'],
			'src' => $v['loc'],
			'type' => $v['type'],
			'ready' => (int)$v['ready']
		];
		$pushData[] = $item;
	}
}

return json_encode($pushData);