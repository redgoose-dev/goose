<?php
if (!defined('__GOOSE__')) exit();

class View extends User
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
		// set repo
		$repo = array();

		// get data
		$data = $this->parent->getItems();
		$repo['user'] = ($data['state'] == 'success') ? $data['data'] : array();

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

		// set user srl
		$user_srl = ($this->param['params'][0]) ? (int)$this->param['params'][0] : null;

		// set repo
		$repo = array();

		// get user data
		$data = $this->parent->getItem(array('where' => 'srl='.$user_srl));
		if ($data['state'] == 'error')
		{
			Util::back($data['message']);
			Goose::end();
		}
		else if ($data['state'] == 'success')
		{
			$repo['user'] = $data['data'];
		}

		// set pwd_container
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

		// set user srl
		$user_srl = ($this->param['params'][0]) ? (int)$this->param['params'][0] : null;

		// set repo
		$repo = array();

		// get user data
		$data = $this->parent->getItem(array('where' => 'srl='.$user_srl));
		if ($data['state'] == 'error')
		{
			Util::back($data['message']);
			Goose::end();
		}
		else if ($data['state'] == 'success')
		{
			$repo['user'] = $data['data'];
		}

		// set container pwd
		$this->pwd_container = __GOOSE_PWD__.$this->skinPath.'view_remove.html';

		require_once($this->layout->getUrl());
	}

}
