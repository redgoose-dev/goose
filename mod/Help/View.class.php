<?php
namespace mod\Help;
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
		$repo->help = [];

		// get modules name
		$dirIndex = core\Util::getDir(__GOOSE_PWD__.'mod/');

		// processing $repo->help[] data
		foreach ($dirIndex as $k=>$v)
		{
			$helpFileDir = __GOOSE_PWD__ . 'mod/' . $v . '/help/index';
			if (file_exists($helpFileDir . '.html') || file_exists($helpFileDir . '.md'))
			{
				$getSetting = core\Module::getSetting($v);
				$repo->help[] = [
					'name' => $v,
					'title' => $getSetting['title'],
					'description' => $getSetting['description'],
					'url_index' => __GOOSE_ROOT__ . '/' . $v . '/'
				];
			}
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
	 * view - index
	 *
	 * @param string $modName
	 * @param string $pageName
	 */
	public function view_read($modName, $pageName=null)
	{
		// set pageName
		$pageName = ($pageName) ? $pageName : 'index';

		// check doc file
		$pwd = __GOOSE_PWD__ . 'mod/' . $modName . '/help/' . $pageName;
		$pageDir = core\Util::isFile([ $pwd.'.md', $pwd.'.html' ]);

		// check page exists
		if (!file_exists($pageDir))
		{
			core\Goose::error(404);
		}

		// make repo
		$repo = new stdClass();
		$repo->setting = core\Module::getSetting($modName);

		// get file type
		$fileType = core\Util::getExtension($pageDir);

		// get content
		$content = core\Util::openFile($pageDir);

		if ($fileType == 'md')
		{
			// load parsedown class
			require_once(__GOOSE_PWD__ . 'vendor/Parsedown/Parsedown.class.php');

			// get instance parsedown
			$parsedown = new \Parsedown();

			// convert markdown
			$content = str_replace('](./', '](' . __GOOSE_ROOT__ . '/mod/' . $repo->setting['name'] . '/help/', $content);
			$content = '<div class="markdown-body">' . $parsedown->text($content) . '</div>';
		}

		// set skin path
		$this->setSkinPath('read');

		// render page
		$this->blade->render($this->parent->skinAddr . '.read', [
			'mod' => $this->parent,
			'repo' => $repo,
			'pageDir' => $pageDir,
			'fileType' => $fileType,
			'content' => $content
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
