<?php
namespace mod\Intro;
use mod, core, stdClass;
if (!defined('__GOOSE__')) exit();


class View {

	public $parent;

	public function __construct()
	{
		$this->name = 'view';
	}

	/**
	 * get article
	 *
	 * @param object $parent
	 */
	public function render($parent)
	{
		$this->parent = $parent;

		// create layout module
		$layout = core\Module::load('Layout');

		// render page
		echo $parent->goose->blade->run(
			$parent->skinAddr . '.index', [
				'root' => __GOOSE_ROOT__,
				'layout' => $layout,
				'mod' => $parent,
				'repo' => new stdClass(),
				'util' => new core\Util()
			]
		);
	}
}