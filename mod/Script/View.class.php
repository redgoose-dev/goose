<?php
namespace mod\Script;
use mod, core, stdClass;
if (!defined('__GOOSE__')) exit();


class View
{
	public $parent;

	public function __construct($parent)
	{
		$this->name = 'View';
		$this->parent = $parent;

		// set blade class
		$this->blade = new core\Blade();
	}


	/**
	 * check admin
	 */
	private function checkAdmin()
	{
		if (!$this->parent->isAdmin)
		{
			core\Util::back('권한이 없습니다.');
			core\Goose::end();
		}
	}

	/**
	 * view - index
	 */
	public function view_index()
	{
		$runPath = __GOOSE_PWD__ . $this->parent->runPath;
		$repo = new stdClass();
		$repo->run = [];
		$runDirs = core\Util::getDir(__GOOSE_PWD__.$this->parent->runPath, 'name');

		// repo->run processing
		foreach($runDirs as $k=>$v)
		{
			if (file_exists($runPath . $v['name'] . '/meta.json') && file_exists($runPath . $v['name'] . '/run.php'))
			{
				$repo->run[] = [
					'name' => $v['name'],
					'meta' => core\Util::jsonToArray(core\Util::openFile($runPath . $v['name'] . '/meta.json'), null, true),
					'path' => __GOOSE_ROOT__ . '/' . $this->parent->runPath . $v['name']
				];
			}
		}

		// set skin path
		$this->setSkinPath('index');

		// render page
		$this->blade->render($this->parent->skinAddr . '.index', [
			'mod' => $this->parent,
			'repo' => $repo
		]);
	}

	/**
	 * set skin path
	 *
	 * @param string $type
	 * @param string $userSkin
	 */
	private function setSkinPath($type, $userSkin=null)
	{
		// check blade file
		$bladeResult = core\Blade::isFile(__GOOSE_PWD__ . 'mod', $type, [
			$this->parent->name . '.skin.' . $_GET['skin'],
			$this->parent->name . '.skin.' . $userSkin,
			$this->parent->name . '.skin.' . $this->parent->set['skin'],
			$this->parent->name . '.skin.default'
		]);

		// set blade and file path
		$this->parent->skinAddr = $bladeResult['address'];
		$this->parent->skinPath = 'mod/' . $bladeResult['path'] . '/';
	}
}