<?php
if (!defined('__GOOSE__')) exit();

/**
 * @var array $post
 * @var object $mod
 */

global $goose;


// check post
$errorValue = core\Util::checkExistValue($post, [ 'nest_srl', 'name' ]);
if ($errorValue)
{
	return [
		'state' => 'error',
		'action' => 'back',
		'message' => '[' . $errorValue . ']값이 없습니다.'
	];
}


// get max number in table
$max = 'select max(turn) as maximum from ' . core\Spawn::getTableName($this->name);
$max = $goose->spawn->db->prepare($max);
$max->execute();
$max = (int)$max->fetchColumn();
$max += 1;


// insert data
$result = core\Spawn::insert([
	'table' => core\Spawn::getTableName($this->name),
	'data' => [
		'srl' => null,
		'nest_srl' => $post['nest_srl'],
		'turn' => $max,
		'name' => $post['name'],
		'regdate' => date('YmdHis')
	]
]);
if ($result != 'success')
{
	return [
		'state' => 'error',
		'action' => 'back',
		'message' => 'Fail execution database'
	];
}


// redirect url
$params = ($post['nest_srl']) ? $post['nest_srl'] . '/' : '';
return [
	'state' => 'success',
	'action' => 'redirect',
	'url' => __GOOSE_ROOT__. '/' .$this->name . '/index/' . $params
];