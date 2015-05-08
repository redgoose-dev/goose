<?php
if (!defined('__GOOSE__')) exit();

class View extends Category
{

	private $parent;

	/**
	 * construct
	 *
	 * @param App $parent
	 */
	public function __construct($parent)
	{
		$this->name = 'view';
		$this->parent = $parent;

		$this->param = $this->parent->param;
		$this->path = $this->parent->path;
		$this->set = $this->parent->set;
		$this->skinPath = $this->parent->skinPath;
	}

	/**
	 * index
	 */
	protected function render()
	{
		// check nest_srl
		if (!$this->param['params'][0])
		{
			Util::back('nest_srl값이 없습니다.');
			Goose::end();
		}

		// create layout module
		$this->layout = Module::load('layout');

		switch ($this->param['action']) {
			case 'create':
				$this->view_create();
				break;
			case 'modify':
				$this->view_modify();
				break;
			case 'remove':
				$this->view_remove();
				break;
			default:
				$this->view_index();
				break;
		}
	}

	/**
	 * check admin
	 */
	private function checkAdmin()
	{
		if (!$this->parent->isAdmin)
		{
			Util::back('권한이 없습니다.');
			Goose::end();
		}
	}

	/**
	 * view - index
	 */
	private function view_index()
	{
		// set nest_srl
		$nest_srl = $this->param['params'][0];

		// set repo
		$repo = array();

		// load modules
		$nest = Module::load('nest');
		$article = Module::load('article');

		// get data
		$data = $this->parent->getItems(array(
			'where' => ($nest_srl) ? 'nest_srl='.$nest_srl : null,
			'order' => 'turn',
			'sort' => 'asc'
		));
		$repo['category'] = ($data['state'] == 'success') ? $data['data'] : array();

		// set article count
		foreach($repo['category'] as $k=>$v)
		{
			$data = $article->getCount(array('where' => 'category_srl='.(int)$v['srl']));
			$repo['category'][$k]['countArticle'] = ($data['state'] == 'success') ? $data['data'] : 0;
		}

		// get nest data
		$data = $nest->getItem(array(
			'field' => 'name',
			'where' => 'srl='.$nest_srl
		));
		$repo['nest'] = ($data['state'] == 'success') ? $data['data'] : array();

		// set pwd_container
		$this->pwd_container = __GOOSE_PWD__.$this->skinPath.'view_index.html';

		require_once($this->layout->getUrl());
	}

	/**
	 * view - create
	 */
	private function view_create()
	{
		// check admin
		$this->checkAdmin();

		// set nest_srl
		$nest_srl = $this->param['params'][0];

		// set repo
		$repo = array();

		// load modules
		$nest = Module::load('nest');

		// get nest data
		$data = $nest->getItem(array(
			'field' => 'name',
			'where' => 'srl='.$nest_srl
		));
		$repo['nest'] = ($data['state'] == 'success') ? $data['data'] : array();

		// set pwd_container
		$this->pwd_container = __GOOSE_PWD__.$this->skinPath.'view_form.html';

		require_once($this->layout->getUrl());
	}

	/**
	 * view - modify
	 */
	private function view_modify()
	{
		// check admin
		$this->checkAdmin();

		// set srl
		$nest_srl = ($this->param['params'][0]) ? (int)$this->param['params'][0] : null;
		$category_srl = ($this->param['params'][1]) ? (int)$this->param['params'][1] : null;

		// set repo
		$repo = array();

		// load modules
		$nest = Module::load('nest');

		// get nest data
		$data = $nest->getItem(array(
			'field' => 'name',
			'where' => 'srl='.$nest_srl
		));
		$repo['nest'] = ($data['state'] == 'success') ? $data['data'] : array();

		// get category data
		$data = $this->parent->getItem(array('where' => 'srl='.$category_srl));
		if ($data['state'] == 'error')
		{
			Util::back($data['message']);
			Goose::end();
		}
		else if ($data['state'] == 'success')
		{
			$repo['category'] = $data['data'];
		}

		// set container pwd
		$this->pwd_container = __GOOSE_PWD__.$this->skinPath.'view_form.html';

		require_once($this->layout->getUrl());
	}

	/**
	 * view - remove
	 */
	private function view_remove()
	{
		// check admin
		$this->checkAdmin();

		// set srl
		$nest_srl = ($this->param['params'][0]) ? (int)$this->param['params'][0] : null;
		$category_srl = ($this->param['params'][1]) ? (int)$this->param['params'][1] : null;

		// set repo
		$repo = array();

		// load modules
		$nest = Module::load('nest');

		// get nest data
		$data = $nest->getItem(array(
			'field' => 'name',
			'where' => 'srl='.$nest_srl
		));
		$repo['nest'] = ($data['state'] == 'success') ? $data['data'] : array();

		// get category data
		$data = $this->parent->getItem(array('where' => 'srl='.$category_srl));
		if ($data['state'] == 'error')
		{
			Util::back($data['message']);
			Goose::end();
		}
		else if ($data['state'] == 'success')
		{
			$repo['category'] = $data['data'];
		}

		// set container pwd
		$this->pwd_container = __GOOSE_PWD__.$this->skinPath.'view_remove.html';

		require_once($this->layout->getUrl());
	}

}