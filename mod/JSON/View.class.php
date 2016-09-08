<?php
namespace mod\JSON;
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
		$repo->json = core\Spawn::items([
			'table' => core\Spawn::getTableName($this->parent->name),
			'field' => 'srl,name,regdate'
		]);

		// set skin path
		$this->setSkinPath('index');

		// render page
		$this->blade->render($this->parent->skinAddr . '.index', [
			'mod' => $this->parent,
			'repo' => $repo
		]);
	}

	/**
	 * view - read
	 */
	public function view_read()
	{
//		// set json srl
//		$json_srl = ($this->param['params'][0]) ? (int)$this->param['params'][0] : null;
//
//		// set repo
//		$repo = array();
//
//		// get json data
//		$data = $this->parent->getItem(array('where' => 'srl='.$json_srl));
//		$repo['json'] = ($data['state'] == 'success') ? $data['data'] : array();
//		if ($data['state'] == 'error')
//		{
//			Util::back($data['message']);
//			Goose::end();
//		}
//		else if ($data['state'] == 'success')
//		{
//			$repo['json'] = $data['data'];
//		}
//
//		// set pwd_container
//		$this->pwd_container = __GOOSE_PWD__.$this->skinPath.'view_read.html';
//
//		require_once($this->layout->getUrl());
	}

	/**
	 * view - create
	 */
	public function view_create()
	{
//		// check admin
//		$this->checkAdmin();
//
//		// set pwd_container
//		$this->pwd_container = __GOOSE_PWD__.$this->skinPath.'view_form.html';
//
//		require_once($this->layout->getUrl());
	}

	/**
	 * view - modify
	 */
	public function view_modify()
	{
//		// check admin
//		$this->checkAdmin();
//
//		// set json srl
//		$json_srl = ($this->param['params'][0]) ? (int)$this->param['params'][0] : null;
//
//		// set repo
//		$repo = array();
//
//		// get json data
//		$data = $this->parent->getItem(array('where' => 'srl='.$json_srl));
//		if ($data['state'] == 'error')
//		{
//			Util::back($data['message']);
//			Goose::end();
//		}
//		else if ($data['state'] == 'success')
//		{
//			$repo['json'] = $data['data'];
//		}
//
//		// set container pwd
//		$this->pwd_container = __GOOSE_PWD__.$this->skinPath.'view_form.html';
//
//		require_once($this->layout->getUrl());
	}

	/**
	 * view - remove
	 */
	public function view_remove()
	{
//		// check admin
//		$this->checkAdmin();
//
//		// set json srl
//		$json_srl = ($this->param['params'][0]) ? (int)$this->param['params'][0] : null;
//
//		// set repo
//		$repo = array();
//
//		$data = $this->parent->getItem(array(
//			'field' => 'srl,name',
//			'where' => 'srl='.$json_srl
//		));
//		$repo['json'] = ($data['state'] == 'success') ? $data['data'] : array();
//		if ($data['state'] == 'error')
//		{
//			Util::back($data['message']);
//			Goose::end();
//		}
//		else if ($data['state'] == 'success')
//		{
//			$repo['json'] = $data['data'];
//		}
//
//		// set container pwd
//		$this->pwd_container = __GOOSE_PWD__.$this->skinPath.'view_remove.html';
//
//		require_once($this->layout->getUrl());
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