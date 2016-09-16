<?php
if (!defined('__GOOSE__')) exit();

/**
 * @var stdClass $repo
 * @var stdClass $mod
 */


/**
 * Get skin names
 * if the form file brings the skin
 *
 * @param array $skins
 * @param string $path
 * @return array
 */
function getSkinNames($skins, $path)
{
	$return = [];
	foreach ($skins as $item) {
		if (file_exists($path . $item . '/' . 'form.blade.php'))
		{
			$return[] = $item;
		}
	}
	return $return;
}


// set repo
$repo->apps = core\Spawn::items([
	'table' => core\Spawn::getTableName('App'),
	'field' => 'srl,name',
	'order' => 'srl',
	'sort' => 'asc'
]);
$repo->skins = getSkinNames(
	core\Util::getDir(__GOOSE_PWD__ . $mod->path . 'skin/'),
	__GOOSE_PWD__ . $mod->path . 'skin/');
$repo->articleSkins = core\Util::getDir(__GOOSE_PWD__ . 'mod/Article/skin/');