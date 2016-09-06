<?php
namespace mod\Nest;
use mod, core, stdClass;
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
	 * check permission
	 *
	 * @param int $permission
	 */
	private function checkAdmin($permission=null)
	{
		$permission = (isset($permission)) ? $permission : $this->parent->set['adminPermission'];
		if (!$this->parent->isAdmin)
		{
			if ($_SESSION['goose_level'] < $permission)
			{
				core\Util::back('권한이 없습니다.');
				core\Goose::end();
			}
		}
	}

	/**
	 * view - index
	 */
	public function view_index()
	{
		$app_srl = ($this->parent->params['params'][0]) ? (int)$this->parent->params['params'][0] : null;

		// set session
		if ($app_srl)
		{
			$_SESSION['app_srl'] = $app_srl;
		}
		else
		{
			unset($_SESSION['app_srl']);
		}

		// set skin path
		$this->setSkinPath('index');

		// play render page
		$this->blade->render($this->parent->skinAddr . '.index', [
			'mod' => $this->parent,
			'repo' => new stdClass(),
			'app_srl' => $app_srl
		]);
	}

	/**
	 * view - create
	 */
	public function view_create()
	{
		// check permission
		$this->checkAdmin();

		// set skin path
		$this->setSkinPath('form');

		// play render page
		$this->blade->render($this->parent->skinAddr . '.form', [
			'mod' => $this->parent,
			'repo' => new stdClass(),
			'action' => ($this->parent->params['action'] == 'clone') ? 'create' : $this->parent->params['action'],
			'titleType' => '만들기',
			'article' => core\Module::load('Article'),
			'nowSkin' => $_GET['skin']
		]);
	}

	/**
	 * view - modify
	 */
	public function view_modify()
	{
		// set nest srl
		if (!isset($this->parent->params['params'][0]))
		{
			core\Util::back('not found `$nest_srl`');
			core\Goose::end();
		}
		$this->parent->nest_srl = (int)$this->parent->params['params'][0];

		// set repo
		$repo = new stdClass();
		$repo->nest = core\Spawn::item([
			'table' => core\Spawn::getTableName('nest'),
			'where' => 'srl='.$this->parent->nest_srl
		]);

		// get article skins
		$repo->articleSkins = core\Util::getDir(__GOOSE_PWD__.'mod/Article/skin/');

		// convert json to array
		$repo->nest['json'] = core\Util::jsonToArray($repo->nest['json'], null, true);

		// check permission
		$this->checkAdmin($repo->nest['json']['permission2']);

		// set nest skin message
		$nestSkinMessage = null;
		if (!file_exists(__GOOSE_PWD__.$this->parent->path.'skin/'.$repo->nest['json']['nestSkin'].'/form.blade.php'))
		{
			$nestSkinMessage = ''.$repo->nest['json']['nestSkin'].']스킨 페이지가 없으므로 ['.$this->parent->set['skin'].']스킨으로 출력합니다.';
		}
		if ($_GET['skin'])
		{
			$nestSkinMessage = '스킨이 변경되었습니다. 적용하면 설정값이 변할수도 있습니다.';
		}

		// set now skin
		$nowSkin = ($repo->nest['json']['nestSkin']) ? $repo->nest['json']['nestSkin'] : $this->parent->set['skin'];
		$nowSkin = ($_GET['skin']) ? $_GET['skin'] : $nowSkin;

		// set use category
		$useCategory = [
			'yes' => ($repo->nest['json']['useCategory']) ? 'checked' : '',
			'no' => ($repo->nest['json']['useCategory']) ? '' : 'checked'
		];

		// set skin path
		$this->setSkinPath('form', $repo->nest['json']['nestSkin']);

		// play render page
		$this->blade->render($this->parent->skinAddr . '.form', [
			'mod' => $this->parent,
			'repo' => $repo,
			'action' => ($this->parent->params['action'] == 'clone') ? 'create' : $this->parent->params['action'],
			'titleType' => '수정',
			'article' => core\Module::load('Article'),
			'nowSkin' => $nowSkin,
			'nestSkinMessage' => $nestSkinMessage,
			'useCategory' => $useCategory
		]);
	}

	/**
	 * view - remove
	 */
	public function view_remove()
	{
		// set nest srl
		if (!isset($this->parent->params['params'][0]))
		{
			core\Util::back('not found `$nest_srl`');
			core\Goose::end();
		}
		$this->parent->nest_srl = (int)$this->parent->params['params'][0];

		// set repo
		$repo = new stdClass();
		$repo->nest = core\Spawn::item([
			'table' => core\Spawn::getTableName('nest'),
			'where' => 'srl='.$this->parent->nest_srl
		]);

		// convert json to array
		$repo->nest['json'] = core\Util::jsonToArray($repo->nest['json'], null, true);

		// check nest data
		if (!isset($repo->nest['srl']))
		{
			core\Util::back('not found nest data');
			core\Goose::end();
		}

		// check permission
		$this->checkAdmin($repo->nest['json']['permission2']);

		// play render page
		$this->blade->render($this->parent->skinAddr . '.remove', [
			'mod' => $this->parent,
			'repo' => $repo,
			'action' => $this->parent->params['action'],
			'titleType' => '삭제',
			'article' => core\Module::load('Article')
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
