<?php
if (!defined('__GOOSE__')) exit();

/**
 * Module - intro
 *
 */

class Page {

	public $name, $goose, $layout, $param, $set;
	public $path, $pwd_container, $viewPath;

	/**
	 * construct
	 *
	 * @param array $getter
	 */
	public function __construct($getter=array())
	{
		$this->name = $getter['name'];
		$this->goose = $getter['goose'];
		$this->isAdmin = $getter['isAdmin'];
		$this->param = $getter['param'];
		$this->path = $getter['path'];
		$this->set = $getter['set'];

		$this->viewPath = $this->path.'skin/'.$this->set['skin'].'/';
	}

	/**
	 * index
	 */
	public function index()
	{
		// create layout module
		$this->layout = Module::load('layout');

		if ($this->param['action'])
		{
			$this->render($this->param['action']);
		}
		else if (!$this->param['action'] || ($this->param['action'] == 'index'))
		{
			$this->view_index();
		}
	}

	/**
	 * render
	 *
	 * @param string $action
	 */
	public function render($action)
	{
		$this->pwd_container = __GOOSE_PWD__.$this->path.'pages/'.$action.'.html';
		if (!file_exists($this->pwd_container))
		{
			Goose::error(404);
		}

		require_once($this->layout->getUrl());
	}

	/**
	 * view - index
	 */
	private function view_index()
	{
		// set repo
		$repo = Array();

		// get data
		$data = $this->getFileIndex();
		$repo['pages'] = ($data['state'] == 'success') ? $data['data'] : array();

		// set pwd_container
		$this->pwd_container = __GOOSE_PWD__.$this->viewPath.'view_index.html';

		require_once($this->layout->getUrl());
	}



	/**********************************************
	 * API AREA
	 *********************************************/

	/**
	 * api - get pages
	 *
	 * @return array
	 */
	public function getFileIndex()
	{
		if ($this->name != 'page') return array( 'state' => 'error', 'message' => '잘못된 객체로 접근했습니다.' );

		// check user
		if (!$this->isAdmin)
		{
			return array(
				'state' => 'error',
				'action' => 'back',
				'message' => '권한이 없습니다.'
			);
		}

		// get datas
		$result = Util::getFiles(__GOOSE_PWD__.$this->path.'pages/');

		// return
		if ($result)
		{
			return array( 'state' => 'success', 'data' => $result );
		}
		else
		{
			return array( 'state' => 'error', 'message' => 'no data' );
		}
	}
}