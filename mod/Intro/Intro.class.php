<?php
namespace mod\Intro;
use core, stdClass;
if (!defined('__GOOSE__')) exit();


class Intro {

	public $name, $param, $set, $layout, $path;

	public function __construct($params=[])
	{
		core\Module::initModule($this, $params);

		// set blade class
		$this->blade = new core\Blade();

		// set skin path
		$this->skinAddr = $this->name . '.skin.' . $this->set['skin'];
		$this->skinPath = $this->path.'skin/'.$this->set['skin'].'/';
	}

	/**
	 * index method
	 */
	public function index()
	{
		// play render page
		$this->blade->render($this->skinAddr . '.index', [
			'mod' => $this,
			'repo' => new stdClass()
		]);
	}

}