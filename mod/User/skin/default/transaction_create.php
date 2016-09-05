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
$errorValue = Util::checkExistValue($post, array('name', 'email', 'pw', 'level'));
if ($errorValue)
{
	return array(
		'state' => 'error',
		'action' => 'back',
		'message' => "[$errorValue]값이 없습니다."
	);
}


// 중복 이메일주소 감사
$cnt = Spawn::count(array(
	'table' => Spawn::getTableName($this->name),
	'where' => "email='$post[email]'"
));
if ($cnt > 0)
{
	return array(
		'state' => 'error',
		'action' => 'back',
		'message' => '이메일주소가 이미 존재합니다.'
	);
}


// check password
if ($post['pw'] != $post['pw2'])
{
	return array(
		'state' => 'error',
		'action' => 'back',
		'message' => '비밀번호와 비밀번호 확인값이 틀립니다.'
	);
}


// insert data
$result = Spawn::insert(array(
	'table' => Spawn::getTableName($this->name),
	'data' => array(
		'srl' => null,
		'email' => $post['email'],
		'name' => $post['name'],
		'pw' => md5($post['pw']),
		'level' => $post['level'],
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