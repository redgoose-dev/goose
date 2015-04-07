<?php
if(!defined("GOOSE")){exit();}


// get article
$nests = $goose->spawn->getItems(array(
	table => 'nests'
));

foreach($nests as $k=>$v)
{
	$v['json'] = json_decode(urldecode($v['json']), true);

	$v['json']['permission'] = 1;

	$result = urlencode(json_encode($v['json']));

	$update_nest = $goose->spawn->update(array(
		'table' => 'nests',
		'where' => 'srl='.$v['srl'],
		'data' => array(
			"json='$result'"
		)
	));

	echo "$v[name] nest가 수정되었습니다.<br>";
}
