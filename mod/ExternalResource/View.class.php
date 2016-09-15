<?php
namespace mod\ExternalResource;
use core, mod, stdClass;
if (!defined('__GOOSE__')) exit();


class View {

	/** @var Resource $parent */
	public $parent;

	public function __construct($parent)
	{
		$this->name = 'View';
		$this->parent = $parent;

		// set blade class
		$this->blade = new core\Blade();
	}


	/**
	 * view - index
	 */
	public function view_index()
	{
		//var_dump('view index');

		var_dump($this->parent->skinAddr . '.index');
		// render page
		$this->blade->render($this->parent->skinAddr . '.index', [
			'mod' => $this->parent,
			'repo' => null
		]);
	}


	/**
	 * view - setting
	 */
	public function view_setting()
	{
		var_dump('view setting');
	}
}