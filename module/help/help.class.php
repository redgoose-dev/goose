<?php
if (!defined('__GOOSE__')) exit();

class Help {

	public $goose, $param, $set, $name, $layout, $method;
	public $path, $skinPath, $moduleName, $page, $pwd_page, $pwd_container, $page_modSet;

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

		$this->skinPath = $this->path.'skin/'.$this->set['skin'].'/';
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
			$this->view_read($this->param['action']);
		}
		else
		{
			$this->view_index();
		}
	}

	/**
	 * view - index
	 */
	private function view_index()
	{
		// set repo
		$repo = array();

		// get modules name
		$dirIndex = Util::getDir(__GOOSE_PWD__.'module/');

		// search help file and set data
		$repo['help'] = array();
		foreach ($dirIndex as $k=>$v)
		{
			$dir_helpFile = __GOOSE_PWD__.'module/'.$v.'/help/index';
			if (file_exists($dir_helpFile.'.html') || file_exists($dir_helpFile.'.md'))
			{
				$getSetting = Util::jsonToArray(Util::openFile(__GOOSE_PWD__.'module/'.$v.'/setting.json'), true);
				array_push($repo['help'], array(
					'name' => $v,
					'title' => $getSetting['title'],
					'description' => $getSetting['description'],
					'url_index' => __GOOSE_ROOT__.'/'.$v.'/'
				));
			}
		}

		// set pwd_container
		$this->pwd_container = __GOOSE_PWD__.$this->skinPath.'view_index.html';

		require_once($this->layout->getUrl());
	}

	/**
	 * view - read
	 */
	private function view_read($module)
	{
		$this->moduleName = $module;
		$this->page = ($this->param['params'][0]) ? $this->param['params'][0] : 'index';

		// check doc file
		$pwd_page = __GOOSE_PWD__.'module/'.$this->moduleName.'/help/'.$this->page;
		$this->pwd_page = Util::isFile(array($pwd_page.'.md', $pwd_page.'.html'));
		if (file_exists($this->pwd_page))
		{
			// get module setting
			$this->page_modSet = Util::jsonToArray(Util::openFile(__GOOSE_PWD__.'module/'.$this->moduleName.'/setting.json'));

			// set pwd_container
			$this->pwd_container = __GOOSE_PWD__.$this->skinPath.'view_page.html';
			require_once($this->layout->getUrl());
		}
		else
		{
			Goose::error(404);
			Goose::end();
		}
	}
}