<?php
namespace mod\App;
use mod, core, stdClass;
if (!defined('__GOOSE__')) exit();


class View {

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
		// make repo
		$repo = new stdClass();
		$repo->apps = core\Spawn::items([
			'table' => core\Spawn::getTableName('app'),
			'order' => 'srl',
			'sort' => 'asc'
		]);

		// processing repo->apps
		foreach ($repo->apps as $k=>$v)
		{
			$repo->apps[$k]['nestCount'] = core\Spawn::count([
				'table' => core\Spawn::getTableName('nest'),
				'where' => 'app_srl='.(int)$v['srl']
			]);
			$repo->apps[$k]['articleCount'] = core\Spawn::count([
				'table' => core\Spawn::getTableName('article'),
				'where' => 'app_srl='.(int)$v['srl']
			]);
		}

		// set skin path
		$this->setSkinPath('index');

		// render page
		$this->blade->render($this->parent->skinAddr . '.index', [
			'root' => __GOOSE_ROOT__,
			'mod' => $this->parent,
			'repo' => $repo
		]);
	}

	/**
	 * view - create
	 */
	public function view_create()
	{
		// check admin
		$this->checkAdmin();

		// set skin path
		$this->setSkinPath('form');

		// play render page
		$this->blade->render($this->parent->skinAddr . '.form', [
			'mod' => $this->parent,
			'repo' => new stdClass(),
			'action' => $this->parent->params['action'],
			'titleType' => '만들기'
		]);
	}

	/**
	 * view - modify
	 */
	public function view_modify()
	{
		// set app srl
		$app_srl = ($this->parent->params['params'][0]) ? (int)$this->parent->params['params'][0] : null;

		// check admin
		$this->checkAdmin();

		// set repo
		$repo = new stdClass();
		$repo->app = core\Spawn::item([
			'table' => core\Spawn::getTableName('app'),
			'where' => 'srl=' . (int)$app_srl
		]);

		// set skin path
		$this->setSkinPath('form');

		// play render page
		$this->blade->render($this->parent->skinAddr . '.form', [
			'app_srl' => $app_srl,
			'mod' => $this->parent,
			'repo' => $repo,
			'action' => $this->parent->params['action'],
			'titleType' => '수정'
		]);
	}

	/**
	 * view - remove
	 */
	public function view_remove()
	{
		// set app srl
		$app_srl = ($this->parent->params['params'][0]) ? (int)$this->parent->params['params'][0] : null;

		// check admin
		$this->checkAdmin();

		// set repo
		$repo = new stdClass();
		$repo->app = core\Spawn::item([
			'table' => core\Spawn::getTableName('app'),
			'where' => 'srl=' . (int)$app_srl
		]);

		// set skin path
		$this->setSkinPath('remove');

		// play render page
		$this->blade->render($this->parent->skinAddr . '.remove', [
			'app_srl' => $app_srl,
			'mod' => $this->parent,
			'repo' => $repo,
			'action' => $this->parent->params['action'],
			'titleType' => '삭제'
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
