<?php
if (!defined('__GOOSE__')) exit();

/**
 * @var stdClass $repo
 * @var object $mod
 */


/**
 * get articles
 *
 * @param int $totalPrintCount
 * @return array
 */
function getArticles($totalPrintCount)
{
	// get articles
	$article = core\Spawn::items([
		'table' => core\Spawn::getTableName('Article'),
		'limit' => [ 0, $totalPrintCount ],
		'jsonField' => 'json'
	]);

	foreach ($article as $k=>$v)
	{
		$nest = core\Spawn::item([
			'table' => core\Spawn::getTableName('Nest'),
			'field' => 'name,json',
			'where' => 'srl='.(int)$v['nest_srl'],
			'jsonField' => 'json'
		]);

		$article[$k]['nest'] = $nest;

		if ($nest['json']['useCategory'] && $v['category_srl'])
		{
			$category = core\Spawn::item([
				'table' => core\Spawn::getTableName('Category'),
				'field' => 'name',
				'where' => 'srl='.(int)$v['category_srl']
			]);
			$article[$k]['categoryName'] = ($category['name']) ? $category['name'] : null;
		}
	}

	return $article;
}


// set repo
$repo->articles = getArticles($mod->set['pagePerCount']);
