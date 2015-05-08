<?php
if (!defined('__GOOSE__')) exit();

// check user
if (!$this->isAdmin && ($post['email'] != $_SESSION['goose_email']))
{
	return array(
		'state' => 'error',
		'action' => 'back',
		'message' => '권한이 없습니다.'
	);
}


// check post
$errorValue = Util::checkExistValue($post, array('name', 'level'));
if ($errorValue)
{
	return array(
		'state' => 'error',
		'action' => 'back',
		'message' => "[$errorValue]값이 없습니다."
	);
}


// check password
if ($post['pw'] && ($post['pw'] != $post['pw2']))
{
	return array(
		'state' => 'error',
		'action' => 'back',
		'message' => '비밀번호와 비밀번호 확인값이 틀립니다.'
	);
}


// update data
$result = Spawn::update(array(
	'table' => Spawn::getTableName($this->name),
	'where' => 'srl='.(int)$post['user_srl'],
	'data' => array(
		"name='".$post['name']."'",
		"level=".(int)$post['level'],
		($post['pw']) ? "pw='".md5($post['pw'])."'" : null
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