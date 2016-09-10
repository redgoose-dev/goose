<?php
namespace mod\Modules;
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
	 * check admin
	 */
	private function checkAdmin()
	{
		if (!$this->parent->isAdmin)
		{
			core\Util::back('권한이 없습니다.');
			core\Goose::end();
		}
	}

	/**
	 * view - index
	 */
	public function view_index()
	{
		// get module names
		$modulesName = $this->parent->getModuleIndex('name');

		// make repo
		$repo = new stdClass();
		$repo->modules = ($modulesName['state'] == 'success') ? $modulesName['data'] : [];

		foreach ($repo->modules as $k=>$v)
		{
			$repo->modules[$k]['set'] = core\Module::getSetting($v['name']);
		}

		// set skin path
		$this->setSkinPath('index');

		// render page
		$this->blade->render($this->parent->skinAddr . '.index', [
			'mod' => $this->parent,
			'repo' => $repo,
			'installModules' => core\Module::getInstallModule(),
			'systemModules' => [ 'User' ]
		]);
	}

	/**
	 * view - edit setting
	 *
	 * @param string $_module
	 */
	public function view_editSetting($_module)
	{
		global $goose;

		if (core\Module::existModule($_module)['state'] != 'success')
		{
			core\Util::back('not found module');
		}

		// make repo
		$repo = new stdClass();
		$repo->setting = core\Module::getSetting($_module);

		// check permission
		if (!$goose->isAdmin && !($_SESSION['goose_level'] >= $repo->setting['adminPermission']))
		{
			core\Util::back('You do not have permission.');
		}

		// set skin path
		$this->setSkinPath('editSetting');

		// render page
		$this->blade->render($this->parent->skinAddr . '.editSetting', [
			'mod' => $this->parent,
			'repo' => $repo,
			'action' => $this->parent->params['action']
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