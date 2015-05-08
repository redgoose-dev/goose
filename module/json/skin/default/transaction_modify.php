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
$errorValue = Util::checkExistValue($post, array('json_srl', 'name', 'json'));
if ($errorValue)
{
	return array(
		'state' => 'error',
		'action' => 'back',
		'message' => "[$errorValue]값이 없습니다."
	);
}


// insert data
$result = Spawn::update(array(
	'table' => Spawn::getTableName($this->name),
	'where' => 'srl='.(int)$post['json_srl'],
	'data' => array(
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
return array(
	'state' => 'success',
	'action' => 'redirect',
	'url' => __GOOSE_ROOT__.$this->name.'/index/'
);