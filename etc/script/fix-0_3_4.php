<?php
if(!defined("GOOSE")){exit();}

// get article
$articles = $goose->spawn->getItems(array(
	table => 'articles'
	,order => 'srl'
	,sort => 'asc'
));

if (!$articles)
{
	echo "not \$articles";
	$goose->out();
}


foreach($articles as $k=>$v)
{
	
	// change json
	$new_json = ($v['json']) ? json_decode(urldecode($v['json']), true) : array();

	if ($v['thumnail_srl'] && $v['thumnail_url'])
	{
		$new_json['thumnail'] = array();
		$new_json['thumnail']['srl'] = $v['thumnail_srl'];
		$new_json['thumnail']['url'] = $v['thumnail_url'];
		if ($v['thumnail_coords'])
		{
			$new_json['thumnail']['coords'] = $v['thumnail_coords'];
		}
		if ($new_json['thumnailSize'])
		{
			$new_json['thumnail']['size'] = $new_json['thumnailSize'];
			unset($new_json['thumnailSize']);
		}
	}
	unset($new_json['tag']);

	var_dump($new_json);
	echo "<br/>";

	$json_result = urlencode(json_encode($new_json));


	// update json
	$update_json = $goose->spawn->update(array(
		'table' => 'articles',
		'where' => 'srl='.$v['srl'],
		'data' => array(
			"json='$json_result'"
		)
	));

	$goose->spawn->action("
		ALTER TABLE `".$goose->tablesName['articles']."`
			DROP `thumnail_srl`,
			DROP `thumnail_url`,
			DROP `thumnail_coords`
	");

	echo "[$v[srl]] fix completed";
	echo "<hr/>";
}
?>

fix DB completed
