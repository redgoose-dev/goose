<?php
if (!defined('__GOOSE__')) exit();

class View extends File
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

		// act index
		$this->view_index();
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
		$repo = [];

		// get file count
		$count = $this->parent->getCount();
		$count = $count['data'];

		if ($count)
		{
			// set paginate
			require_once(__GOOSE_PWD__.'core/classes/Paginate.class.php');
			$_GET['page'] = ((isset($_GET['page'])) && $_GET['page'] > 1) ? $_GET['page'] : 1;
			$paginate = new Paginate($count, $_GET['page'], [], (int)$this->set['pagePerCount'], 5);

			// get article data
			$data = $this->parent->getItems([
				'limit' => [ $paginate->offset, $paginate->size ]
			]);
			$repo['file'] = ($data['state'] == 'success') ? $data['data'] : null;
		}

		// set pwd_container
		$this->pwd_container = __GOOSE_PWD__.$this->skinPath.'view_index.html';

		require_once($this->layout->getUrl());
	}

}