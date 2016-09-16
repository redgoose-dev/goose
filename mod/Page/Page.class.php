<?php
namespace mod\Page;
use core, mod;
if (!defined('__GOOSE__')) exit();


class Page {

	public $name, $set, $params, $isAdmin;
	public $path, $skinPath, $skinAddr;

	public function __construct($params=[])
	{
		core\Module::initModule($this, $params);
	}


	/**
	 * index
	 */
	public function index()
	{
		$view = new View($this);

		if ($this->params['action'])
		{
			$view->view_read($this->params['action']);
		}
		else
		{
			$view->view_index();
		}
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
		if ($this->name != 'Page') return [ 'state' => 'error', 'message' => '잘못된 객체로 접근했습니다.' ];
		if (!$this->isAdmin) return [ 'state' => 'error', 'action' => 'back', 'message' => '권한이 없습니다.' ];

		// get datas
		return core\Util::getFiles(__GOOSE_PWD__ . $this->path . 'pages/', 'html');
	}
}