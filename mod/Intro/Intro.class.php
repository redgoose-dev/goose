<?php
namespace mod\Intro;
use core, mod, stdClass;
if (!defined('__GOOSE__')) exit();


class Intro {

	public $name, $param, $set, $layout;
	public $path, $skinPath, $skinAddr;

	public function __construct($params=[])
	{
		core\Module::initModule($this, $params);

		// set blade class
		$this->blade = new core\Blade();
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