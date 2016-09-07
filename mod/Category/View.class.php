<?php
namespace mod\Category;
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
	 * check permission
	 *
	 * @param int $permission
	 */
	private function checkAdmin($permission=null)
	{
		$permission = (isset($permission)) ? $permission : $this->parent->set['adminPermission'];
		if (!$this->parent->isAdmin)
		{
			if ($_SESSION['goose_level'] < (int)$permission)
			{
				core\Util::back('권한이 없습니다.');
				core\Goose::end();
			}
		}
	}

	/**
	 * check nest permission
	 *
	 * @param int $permission
	 */
	private function checkNestPermission($permission)
	{
		$permission = (isset($permission)) ? $permission : $this->parent->set['permission'];
		if ($_SESSION['goose_level'] < $permission)
		{
			core\Util::redirect(__GOOSE_ROOT__.'/Nest/index/', '둥지의 권한이 없습니다.');
			core\Goose::end();
		}
	}

	/**
	 * view - index
	 */
	public function view_index()
	{
		// set nest_srl
		$nest_srl = ($this->parent->params['params'][0]) ? (int)$this->parent->params['params'][0] : null;

		// make repo
		$repo = new stdClass();
		$repo->category = core\Spawn::items([
			'table' => core\Spawn::getTableName($this->parent->name),
			'where' => ($nest_srl) ? 'nest_srl=' . $nest_srl : null,
			'order' => 'turn',
			'sort' => 'asc'
		]);
		$repo->nest = ($nest_srl) ? core\Spawn::item([
			'table' => core\Spawn::getTableName('Nest'),
			'field' => 'name,json',
			'where' => 'srl=' . $nest_srl,
			'jsonField' => ['json']
		]) : [];

		// processing data
		foreach($repo->category as $k=>$v)
		{
			$repo->category[$k]['articleCount'] = core\Spawn::count([
				'table' => core\Spawn::getTableName('Article'),
				'where' => 'category_srl=' . (int)$v['srl']
			]);
			$repo->category[$k]['urlParam'] = (($nest_srl) ? $nest_srl . '/' : '') . $v['srl'] . '/';
		}

		// set skin path
		$this->setSkinPath('index');

		// set permission
		$permission = (isset($repo->nest['json']['permission2'])) ? $repo->nest['json']['permission2'] : $this->parent->set['permission'];

		// render page
		$this->blade->render($this->parent->skinAddr . '.index', [
			'root' => __GOOSE_ROOT__,
			'mod' => $this->parent,
			'repo' => $repo,
			'nest_srl' => $nest_srl,
			'permission' => $permission
		]);
	}

	/**
	 * view - create
	 */
	public function view_create()
	{
		// set nest_srl
		$nest_srl = ($this->parent->params['params'][0]) ? (int)$this->parent->params['params'][0] : null;

		// make repo
		$repo = new stdClass();
		$repo->nest = ($nest_srl) ? core\Spawn::item([
			'table' => core\Spawn::getTableName('Nest'),
			'field' => 'name,json',
			'where' => 'srl=' . $nest_srl,
			'jsonField' => ['json']
		]) : [];

		// check permission
		$this->checkAdmin($repo->nest['json']['permission2']);

		// set skin path
		$this->setSkinPath('form');

		// render page
		$this->blade->render($this->parent->skinAddr . '.form', [
			'root' => __GOOSE_ROOT__,
			'mod' => $this->parent,
			'repo' => $repo,
			'nest_srl' => $nest_srl,
			'typeName' => '등록'
		]);
	}

	/**
	 * view - modify
	 */
	public function view_modify()
	{
		// set nest_srl
		$nest_srl = ($this->parent->params['params'][0]) ? (int)$this->parent->params['params'][0] : null;
		$category_srl = ($this->parent->params['params'][1]) ? (int)$this->parent->params['params'][1] : null;

		// make repo
		$repo = new stdClass();
		$repo->nest = ($nest_srl) ? core\Spawn::item([
			'table' => core\Spawn::getTableName('Nest'),
			'field' => 'name,json',
			'where' => 'srl=' . $nest_srl,
			'jsonField' => ['json']
		]) : [];
		$repo->category = ($category_srl) ? core\Spawn::item([
			'table' => core\Spawn::getTableName($this->parent->name),
			'field' => 'name',
			'where' => 'srl=' . $category_srl
		]) : [];

		// check permission
		$this->checkAdmin($repo->nest['json']['permission2']);

		// set skin path
		$this->setSkinPath('form');

		// render page
		$this->blade->render($this->parent->skinAddr . '.form', [
			'root' => __GOOSE_ROOT__,
			'mod' => $this->parent,
			'repo' => $repo,
			'nest_srl' => $nest_srl,
			'category_srl' => $category_srl,
			'typeName' => '수정'
		]);
	}

	/**
	 * view - remove
	 */
	public function view_remove()
	{
		// set nest_srl
		$nest_srl = ($this->parent->params['params'][0]) ? (int)$this->parent->params['params'][0] : null;
		$category_srl = ($this->parent->params['params'][1]) ? (int)$this->parent->params['params'][1] : null;

		// make repo
		$repo = new stdClass();
		$repo->nest = ($nest_srl) ? core\Spawn::item([
			'table' => core\Spawn::getTableName('Nest'),
			'field' => 'name,json',
			'where' => 'srl=' . $nest_srl,
			'jsonField' => ['json']
		]) : [];
		$repo->category = ($category_srl) ? core\Spawn::item([
			'table' => core\Spawn::getTableName($this->parent->name),
			'field' => 'name',
			'where' => 'srl=' . $category_srl
		]) : [];

		// check permission
		$this->checkAdmin($repo->nest['json']['permission2']);

		// set skin path
		$this->setSkinPath('remove');

		// render page
		$this->blade->render($this->parent->skinAddr . '.remove', [
			'root' => __GOOSE_ROOT__,
			'mod' => $this->parent,
			'repo' => $repo,
			'nest_srl' => $nest_srl,
			'category_srl' => $category_srl,
			'typeName' => '삭제'
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