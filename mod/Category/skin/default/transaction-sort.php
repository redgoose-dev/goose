<?php
if (!defined('__GOOSE__')) exit();

/** @var array $post */


// check post
$errorValue = core\Util::checkExistValue($post, [ 'nest_srl', 'srls' ]);
if ($errorValue)
{
	$result = [
		'state' => 'error',
		'action' => 'back',
		'message' => '[' . $errorValue . ']값이 없습니다.'
	];
	if ($post['ajax'] === 'true')
	{
		echo json_encode($result);
		core\Goose::end();
	}
	else
	{
		return $result;
	}
}


// set srls
$srls = explode(',', $post['srls']);


// update db
foreach ($srls as $k=>$v)
{
	$updateResult = core\Spawn::update([
		'table' => core\Spawn::getTableName('category'),
		'where' => 'srl=' . (int)$srls[$k],
		'data' => [
			'turn=' . $k
		]
	]);
}


// end transaction
if ($post['ajax'] === 'true')
{
	echo json_encode([ 'state' => 'success' ]);
	core\Goose::end();
}
else
{
	return [
		'state' => 'success',
		'action' => 'redirect',
		'url' => __GOOSE_ROOT__ . '/' . $this->name . '/index/' . (($post['nest_srl']) ? $post['nest_srl'] . '/' : '')
	];
}