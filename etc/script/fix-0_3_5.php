<?php
if(!defined("GOOSE")){exit();}

// get article
$nests = $goose->spawn->getItems(array(
	table => 'nests'
	,order => 'srl'
	,sort => 'asc'
));


if (!$nests)
{
	echo "not \$nests";
	$goose->out();
}


foreach($nests as $k=>$v)
{
	$new_json = $goose->util->jsonToArray($v['json'], true);


	$new_json['skin'] = 'default';

	if ($v['thumnailSize'] || $v['thumnailType'])
	{
		$new_json['thumnail'] = array();
	}

	if ($v['thumnailSize'])
	{
		$new_json['thumnail']['size'] = explode("*", $v['thumnailSize']);
	}
	
	if ($v['thumnailType'])
	{
		$new_json['thumnail']['type'] = $v['thumnailType'];
	}

	$json_result = $goose->util->arrayToJson($new_json);

	var_dump($new_json['thumnail']);
	echo "<br/>";

	$update_json = $goose->spawn->update(array(
		'table' => 'nests',
		'where' => 'srl='.$v['srl'],
		'data' => array(
			"json='$json_result'"
		)
	));

	$delete_field = $goose->spawn->action("
		ALTER TABLE `".$goose->tablesName['nests']."`
			DROP `thumnailSize`,
			DROP `thumnailType`
	");

	echo "[$v[srl]] fix completed";
	echo "<hr/>";
}
?>

<br/>
goose v0.3.5 fix DB completed
