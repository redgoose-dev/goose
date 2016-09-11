<?php
namespace mod\Page;
use core, mod, stdClass;
if (!defined('__GOOSE__')) exit();


class View {

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
		// make repo
		$repo = new stdClass();
		$repo->pages = $this->parent->getFileIndex();

		foreach ($repo->pages as $k=>$v)
		{
			$name = substr($v['filename'], 0, strpos($v['filename'], '.'));
			$repo->pages[$k]['url'] = __GOOSE_ROOT__ . '/' . $this->parent->name . '/' . $name . '/';
		}

		// set skin path
		$this->setSkinPath('index');

		// render page
		$this->blade->render($this->parent->skinAddr . '.index', [
			'mod' => $this->parent,
			'repo' => $repo
		]);
	}

	/**
	 * view - read
	 *
	 * @param string $action
	 */
	public function view_read($action=null)
	{
		// set skin path
		$this->setSkinPath('read');

		// render page
		$this->blade->render($this->parent->skinAddr . '.read', [
			'mod' => $this->parent,
			'action' => $action
		]);
	}

	/**
	 * set skin path
	 *
	 * @param string $type
	 * @param string $userSkin
	 */
	private function setSkinPath($type, $userSkin=null)
	{
		// check blade file
		$bladeResult = core\Blade::isFile(__GOOSE_PWD__ . 'mod', $type, [
			$this->parent->name . '.skin.' . $_GET['skin'],
			$this->parent->name . '.skin.' . $userSkin,
			$this->parent->name . '.skin.' . $this->parent->set['skin'],
			$this->parent->name . '.skin.default'
		]);

		// set blade and file path
		$this->parent->skinAddr = $bladeResult['address'];
		$this->parent->skinPath = 'mod/' . $bladeResult['path'] . '/';
	}
}
