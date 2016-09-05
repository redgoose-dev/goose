<?php
namespace mod\Error;
if (!defined('__GOOSE__')) exit();


class Error {

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

		$this->skinPath = $this->path.'skin/'.$this->set['skin'].'/';
	}

	/**
	 * index
	 */
	public function index()
	{
		echo "[error] index page";
	}


	/**********************************************
	 * VIEW AREA
	 *********************************************/

	/**
	 * full render
	 * 전체로 출력되는 에러페이지
	 *
	 * @param int $code error code
	 * @param string $message error message
	 */
	public function render($code, $message, $url_home)
	{
		$url_home = ($url_home) ? $url_home : __GOOSE_ROOT__;
		require_once(__GOOSE_PWD__.$this->skinPath.'view_render.html');
	}

	public function box($code, $message)
	{
		require_once(__GOOSE_PWD__.$this->skinPath.'view_box.html');
	}
}