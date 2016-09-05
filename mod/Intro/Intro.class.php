<?php
namespace mod\Intro;
if (!defined('__GOOSE__')) exit();


class Intro {

	public $name, $param, $set, $layout;
	public $path, $view;

	/**
	 * construct
	 *
	 * @param array $getter
	 */
	public function __construct($getter=[])
	{
		$this->name = $getter['name'];
		$this->goose = $getter['goose'];
		$this->isAdmin = $getter['isAdmin'];
		$this->param = $getter['param'];
		$this->path = $getter['path'];
		$this->set = $getter['set'];

		$this->skinAddr = $this->name . '.skin.' . $this->set['skin'];
		$this->skinPath = $this->path.'skin/'.$this->set['skin'].'/';
	}

	/**
	 * index method
	 */
	public function index()
	{
		$this->view = new View();
		$this->view->render($this);
	}

}