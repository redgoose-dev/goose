<?php
namespace mod\File;
use core, mod, stdClass;
if (!defined('__GOOSE__')) exit();


class View {

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
		var_dump('view index');


//		// set repo
//		$repo = [];
//
//		// get file count
//		$count = $this->parent->getCount();
//		$count = $count['data'];
//
//		if ($count)
//		{
//			// set paginate
//			require_once(__GOOSE_PWD__.'core/classes/Paginate.class.php');
//			$_GET['page'] = ((isset($_GET['page'])) && $_GET['page'] > 1) ? $_GET['page'] : 1;
//			$paginate = new Paginate($count, $_GET['page'], [], (int)$this->set['pagePerCount'], 5);
//
//			// get article data
//			$data = $this->parent->getItems([
//				'limit' => [ $paginate->offset, $paginate->size ]
//			]);
//			$repo['file'] = ($data['state'] == 'success') ? $data['data'] : null;
//		}
//
//		// set pwd_container
//		$this->pwd_container = __GOOSE_PWD__.$this->skinPath.'view_index.html';
//
//		require_once($this->layout->getUrl());
	}
}