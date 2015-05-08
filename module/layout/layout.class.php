<?php
if (!defined('__GOOSE__')) exit();

/**
 * Module - layout
 */

class layout {

	public $set;
	public $path, $viewPath;

	/**
	 * construct
	 *
	 * @param array $getter
	 */
	public function __construct($getter=array())
	{
		$this->name = $getter['name'];
		$this->path = $getter['path'];
		$this->set = $getter['set'];

		$this->viewPath = $this->path.'skin/'.$this->set['skin'].'/';
	}

	/**
	 * get url layout
	 * 레이아웃 페이지의 주소를 반환한다.
	 *
	 */
	public function getUrl()
	{
		return __GOOSE_PWD__.$this->viewPath.'view_layout.html';
	}

}