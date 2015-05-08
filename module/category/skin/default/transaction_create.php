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
$errorValue = Util::checkExistValue($post, array('nest_srl', 'name'));
if ($errorValue)
{
	return array(
		'state' => 'error',
		'action' => 'back',
		'message' => "[$errorValue]값이 없습니다."
	);
}


// get max
$max = "select max(turn) as maximum from ".Spawn::getTableName($this->name);
$max = $this->goose->spawn->db->prepare($max);
$max->execute();
$max = (int)$max->fetchColumn();
$max += 1;


// insert data
$result = Spawn::insert(array(
	'table' => Spawn::getTableName($this->name),
	'data' => array(
		'srl' => null,
		'nest_srl' => $post['nest_srl'],
		'turn' => $max,
		'name' => $post['name'],
		'regdate' => date('YmdHis')
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
	'url' => __GOOSE_ROOT__.$this->name.'/index/'.$params
);