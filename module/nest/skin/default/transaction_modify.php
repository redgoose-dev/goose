<?php
if (!defined('__GOOSE__')) exit();

// check user
if (!$this->isAdmin)
{
	return array(
		'state' => 'error',
		'action' => 'back',
		'message' => '권한이 없습니다.'
	);
}


// check post
$errorValue = Util::checkExistValue($post, array('nest_srl', 'name', 'json'));
if ($errorValue)
{
	return array(
		'state' => 'error',
		'action' => 'back',
		'message' => "[$errorValue]값이 없습니다."
	);
}


// update article
$result = Spawn::update(array(
	'table' => Spawn::getTableName('article'),
	'where' => "nest_srl=".(int)$post['nest_srl'],
	'data' => array(
		"app_srl=".(int)$post['app_srl']
	)
));


// update nest
$result = Spawn::update(array(
	'table' => Spawn::getTableName($this->name),
	'where' => 'srl='.(int)$post['nest_srl'],
	'data' => array(
		"app_srl=".(int)$post['app_srl'],
		"name='$post[name]'",
		"json='$post[json]'"
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
$params = ($_SESSION['app_srl']) ? $_SESSION['app_srl'].'/' : '';
return array(
	'state' => 'success',
	'action' => 'redirect',
	'url' => __GOOSE_ROOT__.'nest/index/'.$params
);