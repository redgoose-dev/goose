<?php
namespace mod\File;
use core, mod, stdClass;
if (!defined('__GOOSE__')) exit();


class View {

	/** @var File $parent */
	private $parent;

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
		// make repo
		$repo = new stdClass();
		$repo->total = core\Spawn::count([
			'table' => core\Spawn::getTableName($this->parent->name)
		]);

		// get files data
		if ($repo->total)
		{
			$pagePerCount = $this->parent->set['pagePerCount'];
			$_GET['page'] = ((isset($_GET['page'])) && $_GET['page'] > 1) ? $_GET['page'] : 1;
			$paginateParameter = [ 'keyword' => (isset($_GET['keyword'])) ? $_GET['keyword'] : '' ];

			$repo->paginate = new core\Paginate($repo->total, $_GET['page'], $paginateParameter, (int)$pagePerCount, 5);

			$repo->file = core\Spawn::items([
				'table' => core\Spawn::getTableName($this->parent->name),
				'order' => 'srl',
				'sort' => 'desc',
				'limit' => [ $repo->paginate->offset, $repo->paginate->size ]
			]);
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