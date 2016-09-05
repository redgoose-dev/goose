<?php
if (!defined('__GOOSE__')) exit();


// check post
$errorValue = Util::checkExistValue($post, array('nest_srl', 'category_srl', 'name'));
if ($errorValue)
{
	return array(
		'state' => 'error',
		'action' => 'back',
		'message' => "[$errorValue]값이 없습니다."
	);
}


// update data
$result = Spawn::update(array(
	'table' => Spawn::getTableName($this->name),
	'where' => 'srl='.(int)$post['category_srl'],
	'data' => array(
		"name='$post[name]'"
	)
));
if ($result != 'success')
{
	return array(
		'state' => 'error',
		'action' => 'back',
		'message' => 'Fail execution database'
	);
}


// redirect url
$params = ($post['nest_srl']) ? $post['nest_srl'].'/' : '';
return array(
	'state' => 'success',
	'action' => 'redirect',
	'url' => __GOOSE_ROOT__.'/'.$this->name.'/index/'.$params
);