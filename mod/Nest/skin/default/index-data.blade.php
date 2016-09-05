<?php
if (!defined('__GOOSE__')) exit();

/**
 * @var stdClass $repo
 * @var int $app_srl
 */


/**
 * get app
 *
 * @return array
 */
function getApps()
{
	$result = core\Spawn::items([
		'table' => core\Spawn::getTableName('app'),
		'field' => 'srl,name',
		'order' => 'srl',
		'sort' => 'asc'
	]);

	foreach ($result as $k=>$v)
	{
		$count = core\Spawn::count([
			'table' => core\Spawn::getTableName('nest'),
			'where' => 'app_srl='.(int)$v['srl']
		]);
		$result[$k]['countNest'] = $count;
	}

	return $result;
}

/**
 * get nests
 *
 * @param int $app_srl
 * @return array
 */
function getNests($app_srl=null)
{
	$result = core\Spawn::items([
		'table' => core\Spawn::getTableName('nest'),
		'where' => ($app_srl) ? 'app_srl='.$app_srl : ''
	]);

	foreach ($result as $k=>$v)
	{
		$result[$k]['json'] = core\Util::jsonToArray($v['json'], null, true);
	}
	foreach ($result as $k=>$v)
	{
		// get count of article
		$result[$k]['countArticle'] = core\Spawn::count([
			'table' => core\Spawn::getTableName('article'),
			'where' => 'nest_srl=' . (int)$v['srl']
		]);
		// get app name
		$app = core\Spawn::item([
			'table' => core\Spawn::getTableName('app'),
			'field' => 'srl,name',
			'where' => 'srl=' . (int)$v['app_srl']
		]);
		$result[$k]['appName'] = (isset($app['name'])) ? $app['name'] : null;
		// get category count
		if ($v['json']['useCategory'] == 1)
		{
			$result[$k]['countCategory'] = core\Spawn::count([
				'table' => core\Spawn::getTableName('category'),
				'where' => 'nest_srl=' . (int)$v['srl']
			]);
		}
		$result[$k]['json']['permission'] = (isset($v['json']['permission'])) ? $v['json']['permission'] : 0;
	}

	return $result;
}


// set data
$repo->nests = getNests($app_srl);
$repo->apps = getApps();
$repo->total = core\Spawn::count([
	'table' => core\Spawn::getTableName('nest')
]);