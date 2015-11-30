<?php
if (!defined('__GOOSE__')) exit();

/**
 * Module - resource
 */
class Resource {

	public $goose, $param, $set, $name, $layout, $isAdmin, $method;
	public $path, $skinPath, $pwd_container;

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

		$this->viewPath = $this->path.'pages/';
		$this->url = __GOOSE_ROOT__.'/'.$this->name;
	}

	/**
	 * index
	 */
	public function index()
	{
		// set layout module
		$this->layout = Module::load('layout');

		if ($this->param['method'] == 'POST')
		{
			switch($this->param['action'])
			{
				case 'updateFTP':
					var_dump('update ftp');
					break;
				case 'testFTP':
					var_dump('test ftp');
					//var_dump($_POST);
					break;
			}
		}
		else
		{
			switch($this->param['action'])
			{
				case 'nest':
					break;
				case 'setting':
					$this->view_index('view_setting');
					break;
				default:
					$this->view_index('view_index');
					break;
			}
		}
	}

	/**
	 * view - index
	 *
	 * @param string $fileName
	 */
	private function view_index($fileName)
	{
		// set pwd_container
		$this->pwd_container = __GOOSE_PWD__.$this->viewPath.$fileName.'.html';

		require_once($this->layout->getUrl());
	}

}