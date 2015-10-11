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
$errorValue = Util::checkExistValue($post, array('id', 'name'));
if ($errorValue)
{
	return array(
		'state' => 'error',
		'action' => 'back',
		'message' => "[$errorValue]값이 없습니다."
	);
}


// id값 중복검사
$cnt = Spawn::count(array(
	'table' => Spawn::getTableName($this->name),
	'where' => "id='$post[id]'"
));
if ($cnt > 0)
{
	return array(
		'state' => 'error',
		'action' => 'back',
		'message' => '"'.$post['id'].'"이름의 id가 이미 존재합니다.'
	);
}


// insert data
$result = Spawn::insert(array(
	'table' => Spawn::getTableName($this->name),
	'data' => array(
		'srl' => null,
		'id' => $post['id'],
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
return array(
	'state' => 'success',
	'action' => 'redirect',
	'url' => __GOOSE_ROOT__.'/'.$this->name.'/index/'
);