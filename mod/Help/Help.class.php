<?php
namespace mod\Help;
use core, mod;
if (!defined('__GOOSE__')) exit();


class Help {

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
			$view->view_read($this->params['action'], $this->params['params'][0]);
		}
		else
		{
			$view->view_index();
		}
	}
}