<?php
namespace mod\Script;
use core;
if (!defined('__GOOSE__')) exit();


class View extends Script
{
	private $parent;

	/**
	 * construct
	 *
	 * @param Script $parent
	 */
	public function __construct($parent)
	{
		$this->name = 'view';
		$this->parent = $parent;

		$this->param = $this->parent->param;
		$this->path = $this->parent->path;
		$this->set = $this->parent->set;
		$this->skinPath = $this->parent->skinPath;
		$this->runPath = $this->parent->runPath;
	}

	/**
	 * index
	 */
	protected function render()
	{
		// create layout module
		$this->layout = core\Module::load('layout');

		// act view index
		$this->view_index();
	}

	/**
	 * view - index
	 */
	private function view_index()
	{
		// set repo
		$repo = Array('run');

		// get data
		$repo['run'] = core\Util::getDir(__GOOSE_PWD__.$this->runPath, 'name');

		// set data
		foreach($repo['run'] as $k=>$v)
		{
			$meta = core\Util::jsonToArray(core\Util::openFile(__GOOSE_PWD__.$this->runPath.$v['name'].'/meta.json'), null, true);
			$repo['run'][$k]['meta'] = $meta;
		}

		// set pwd_container
		$this->pwd_container = __GOOSE_PWD__.$this->skinPath.'view_index.html';

		require_once($this->layout->getUrl());
	}

}