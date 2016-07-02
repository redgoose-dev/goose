<?php
if (!defined('__GOOSE__')) exit();


// check post
$errorValue = Util::checkExistValue($post, array('nest_srl', 'srls'));
if ($errorValue)
{
	return array(
		'state' => 'error',
		'action' => 'back',
		'message' => "[$errorValue]값이 없습니다."
	);
}


// set srls
$srls = explode(',', $post['srls']);


// update db
for ($i=0; $i<count($srls); $i++)
{
	$result = Spawn::update(array(
		'table' => Spawn::getTableName($this->name),
		'where' => 'srl='.(int)$srls[$i],
		'data' => array(
			'turn='.$i
		)
	));
}


// redirect url
$params = ($post['nest_srl']) ? $post['nest_srl'].'/' : '';
return array(
	'state' => 'success',
	'action' => 'redirect',
	'url' => __GOOSE_ROOT__.'/'.$this->name.'/index/'.$params
);