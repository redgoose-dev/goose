<?php
namespace mod\Nest;
use mod, core, stdClass;
if (!defined('__GOOSE__')) exit();


class View
{
	public $name, $parent;

	const FILE_INDEX = 'index';
	const FILE_FORM = 'form';
	const FILE_REMOVE = 'remove';

	public function __construct($parent)
	{
		$this->name = 'View';
		$this->parent = $parent;
	}

	/**
	 * index
	 */
	public function index()
	{
		switch($this->parent->param['action'])
		{
			case 'create':
				$this->view_create();
				break;
			case 'modify':
				$this->view_modify();
				break;
			case 'clone':
				$this->view_modify();
				break;
			case 'remove':
				$this->view_remove();
				break;
			default:
				$this->view_index();
				break;
		}
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
	private function view_index()
	{
		$app_srl = ($this->parent->param['params'][0]) ? (int)$this->parent->param['params'][0] : null;

		// set session
		if ($app_srl)
		{
			$_SESSION['app_srl'] = $app_srl;
		}
		else
		{
			unset($_SESSION['app_srl']);
		}

		// act render
		$this->render(self::FILE_INDEX, null, [
			'root' => __GOOSE_ROOT__,
			'mod' => $this->parent,
			'repo' => new stdClass(),
			'app_srl' => $app_srl
		]);
	}

	/**
	 * view - create
	 */
	private function view_create()
	{
		// check permission
		$this->checkAdmin();

		// act render
		$this->render(self::FILE_FORM, null, [
			'root' => __GOOSE_ROOT__,
			'mod' => $this->parent,
			'repo' => new stdClass(),
			'action' => ($this->parent->param['action'] == 'clone') ? 'create' : $this->parent->param['action'],
			'titleType' => '만들기',
			'article' => core\Module::load('Article'),
			'nowSkin' => $_GET['skin']
		]);
	}

	/**
	 * view - modify
	 */
	private function view_modify()
	{
		// set nest srl
		if (!isset($this->parent->param['params'][0]))
		{
			core\Util::back('not found `$nest_srl`');
			core\Goose::end();
		}
		$this->parent->nest_srl = (int)$this->parent->param['params'][0];

		// set repo
		$repo = new stdClass();
		$repo->nest = core\Spawn::item([
			'table' => core\Spawn::getTableName('nest'),
			'where' => 'srl='.$this->parent->nest_srl
		]);

		$repo->articleSkins = core\Util::getDir(__GOOSE_PWD__.'mod/Article/skin/');

		// convert json to array
		$repo->nest['json'] = core\Util::jsonToArray($repo->nest['json'], null, true);

		// check permission
		$this->checkAdmin($repo->nest['json']['permission2']);

		// set nest skin message
		$nestSkinMessage = null;
		if (!file_exists(__GOOSE_PWD__.$this->parent->path.'skin/'.$repo->nest['json']['nestSkin'].'/'.self::FILE_FORM.'.blade.php'))
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

		// act render
		$this->render(self::FILE_FORM, $repo->nest['json']['nestSkin'], [
			'root' => __GOOSE_ROOT__,
			'mod' => $this->parent,
			'repo' => $repo,
			'action' => ($this->parent->param['action'] == 'clone') ? 'create' : $this->parent->param['action'],
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
	private function view_remove()
	{
		// set nest srl
		if (!isset($this->parent->param['params'][0]))
		{
			core\Util::back('not found `$nest_srl`');
			core\Goose::end();
		}
		$this->parent->nest_srl = (int)$this->parent->param['params'][0];

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

		// act render
		$this->render(self::FILE_REMOVE, $repo->nest['json']['nestSkin'], [
			'root' => __GOOSE_ROOT__,
			'mod' => $this->parent,
			'repo' => $repo,
			'action' => $this->parent->param['action'],
			'titleType' => '삭제',
			'article' => core\Module::load('Article')
		]);
	}

	/**
	 * render
	 *
	 * @param string $type
	 * @param string $skin
	 * @param array $data
	 */
	private function render($type, $skin=null, $data)
	{
		// set layout
		$layout = core\Module::load('Layout');
		$data['layout'] = $layout;

		// check blade file
		$bladeResult = core\Blade::isFile(__GOOSE_PWD__ . 'mod', $type, [
			$this->parent->name . '.skin.' . $_GET['skin'],
			$this->parent->name . '.skin.' . $skin,
			$this->parent->name . '.skin.' . $this->parent->set['skin'],
			$this->parent->name . '.skin.default'
		]);

		// set blade and file path
		$this->parent->skinAddr = $bladeResult['address'];
		$this->parent->skinPath = 'mod/' . $bladeResult['path'] . '/';

		// render page
		echo $this->parent->goose->blade->run(
			$this->parent->skinAddr . '.' . $type,
			$data
		);
	}
}
