<?php
if (!defined('__GOOSE__')) exit();

/**
 * @var stdClass $repo
 * @var stdClass $mod
 */


/**
 * get nests
 *
 * @param int $app_srl
 * @return array
 */
function getNests($app_srl)
{
	$result = [];

	// get items
	$nests = core\Spawn::items([
		'table' => core\Spawn::getTableName('nest'),
		'where' => 'app_srl=' . (int)$app_srl,
		'jsonField' => ['json']
	]);

	// processing
	foreach ($nests as $k=>$v)
	{
		// check permission
		$v['json']['permission'] = (isset($v['json']['permission'])) ? $v['json']['permission'] : 0;
		$v['json']['permission2'] = (isset($v['json']['permission2'])) ? $v['json']['permission2'] : 0;
		if ($_SESSION['goose_level'] < $v['json']['permission'])
		{
			$result[] = [ 'allow' => false ];
			continue;
		}

		$v['allow'] = true;
		$v['articleCount'] = core\Spawn::count([
			'table' => core\Spawn::getTableName('article'),
			'where' => 'nest_srl='.(int)$v['srl']
		]);
		if ($v['json']['useCategory'] == 1)
		{
			$v['categoryCount'] = core\Spawn::count([
				'table' => core\Spawn::getTableName('category'),
				'where' => 'nest_srl='.(int)$v['srl']
			]);
		}

		$result[] = $v;
	}

	return $result;
}

/**
 * get sections
 *
 * @return array
 */
function getSections()
{
	$result = [];

	// app
	$apps = core\Spawn::items([
		'table' => core\Spawn::getTableName('app')
	]);
	foreach ($apps as $k=>$v)
	{
		$apps[$k]['count'] = core\Spawn::count([
			'table' => core\Spawn::getTableName('nest'),
			'where' => 'app_srl=' . (int)$v['srl']
		]);
		$apps[$k]['nests'] = getNests($v['srl']);

		$result[] = $apps[$k];
	}

	// not app
	$notAppNestCount = core\Spawn::count([
			'table' => core\Spawn::getTableName('nest'),
			'where' => 'app_srl=0'
	]);
	if ($notAppNestCount > 0)
	{
		$noApp = [
				'name' => 'Not APP',
				'count' => $notAppNestCount,
				'nests' => getNests(0)
		];
		$result[] = $noApp;
	}

	return $result;
}


// set repo
$repo->sections = getSections();
$repo->totalNest = core\Spawn::count([
		'table' => core\Spawn::getTableName('nest')
]);