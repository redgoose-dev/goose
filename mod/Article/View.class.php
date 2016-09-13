<?php
namespace mod\Article;
use core, mod, stdClass;
if (!defined('__GOOSE__')) exit();


class View {

	/** @var  Article $parent */
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
		$permission = (isset($permission)) ? (int)$permission : (int)$this->parent->set['adminPermission'];
		if (!$this->parent->isAdmin && $_SESSION['goose_level'] < $permission)
		{
			core\Util::back('권한이 없습니다.');
		}
	}

	/**
	 * check nest permission
	 *
	 * @param int $permission
	 */
	private function checkNestPermission($permission=0)
	{
		if ($_SESSION['goose_level'] < $permission)
		{
			core\Util::redirect(__GOOSE_ROOT__ . '/Nest/index/', '둥지의 권한이 없습니다.');
		}
	}

	/**
	 * view - index
	 */
	public function view_index()
	{
		// set srl
		$nest_srl = ($this->parent->params['params'][0]) ? (int)$this->parent->params['params'][0] : null;
		$category_srl = ($this->parent->params['params'][1]) ? (int)$this->parent->params['params'][1] : null;

		// make repo
		$repo = new stdClass();
		$repo->nest = null;
		$repo->category = [];

		// get nest data
		if ($nest_srl)
		{
			$repo->nest = core\Spawn::item([
				'table' => core\Spawn::getTableName('Nest'),
				'field' => 'srl,name,json',
				'where' => 'srl=' . $nest_srl,
				'jsonField' => ['json']
			]);

			// get category data
			if ($repo->nest['json']['useCategory'])
			{
				$repo->category = core\Spawn::items([
					'table' => core\Spawn::getTableName('Category'),
					'field' => 'srl,name',
					'where' => 'nest_srl=' . $nest_srl,
					'order' => 'turn',
					'sort' => 'asc'
				]);
				foreach($repo->category as $k=>$v)
				{
					$repo->category[$k]['countArticle'] = core\Spawn::count([
						'table' => core\Spawn::getTableName($this->parent->name),
						'where' => 'category_srl=' . (int)$v['srl']
					]);
				}
			}
		}

		// check permission
		$permission = new stdClass();
		$permission->level1 = (isset($repo->nest['json']['permission'])) ? (int)$repo->nest['json']['permission'] : 0;
		$permission->level2 = (isset($repo->nest['json']['permission2'])) ? (int)$repo->nest['json']['permission2'] : 0;
		$this->checkNestPermission($permission->level1);
		if ($permission->level2 < $_SESSION['goose_level'])
		{
			$this->parent->isAdmin = true;
		}

		// set article params
		$param = ($nest_srl) ? ' nest_srl='.$nest_srl : '';
		$param .= ($nest_srl && $category_srl) ? ' and' : '';
		$param .= ($category_srl) ? ' category_srl='.$category_srl : '';
		$param .= (($nest_srl || $category_srl) && $_GET['keyword']) ? ' and' : '';
		$param .= ($_GET['keyword']) ? ' (title LIKE \'%' . $_GET['keyword'] . '%\' or content LIKE \'%' . $_GET['keyword'] . '%\')' : '';

		// get article count
		$articleCount = core\Spawn::count([
			'table' => core\Spawn::getTableName($this->parent->name),
			'where' => $param
		]);

		// get article data
		if ($articleCount)
		{
			// set listCount
			$pagePerCount = ($repo->nest['json']['listCount']) ? $repo->nest['json']['listCount'] : $this->parent->set['pagePerCount'];

			// set paginate
			$paginateParameter = [ 'keyword' => (isset($_GET['keyword'])) ? $_GET['keyword'] : '' ];
			$_GET['page'] = (isset($_GET['page']) && $_GET['page'] > 1) ? $_GET['page'] : 1;
			$repo->paginate = new core\Paginate($articleCount, $_GET['page'], $paginateParameter, (int)$pagePerCount, 5);

			// get article data
			$repo->article = core\Spawn::items([
				'table' => core\Spawn::getTableName($this->parent->name),
				'where' => $param,
				'order' => 'srl',
				'sort' => 'desc',
				'limit' => [ $repo->paginate->offset, $repo->paginate->size ],
				'jsonField' => [ 'json' ]
			]);

			foreach($repo->article as $k=>$v)
			{
				if ($v['category_srl'])
				{
					$data = core\Spawn::item([
						'table' => core\Spawn::getTableName('Category'),
						'field' => 'name',
						'where' => 'srl=' . (int)$v['category_srl']
					]);
					$repo->article[$k]['categoryName'] = (isset($data['name'])) ? $data['name'] : '';
				}
			}
		}

		// set skin path
		$this->setSkinPath('index', $repo->nest['json']['articleSkin']);

		// render page
		$this->blade->render($this->parent->skinAddr . '.index', [
			'mod' => $this->parent,
			'repo' => $repo,
			'nest_srl' => $nest_srl,
			'category_srl' => $category_srl,
			'totalArticleCount' => core\Spawn::count([
				'table' => core\Spawn::getTableName($this->parent->name),
				'where' => ($nest_srl) ? 'nest_srl=' . $nest_srl : null
			])
		]);
	}

	/**
	 * view - read
	 */
	public function view_read()
	{
		$article_srl = null;
		$category_srl = null;

		// set srl
		if ($this->parent->params['params'][1])
		{
			$category_srl = (int)$this->parent->params['params'][0];
			$article_srl = (int)$this->parent->params['params'][1];
		}
		else if ($this->parent->params['params'][0])
		{
			$article_srl = (int)$this->parent->params['params'][0];
		}

		// check article_srl
		if (!$article_srl)
		{
			core\Util::back('`article_srl`값이 없습니다.');
		}

		// make repo
		$repo = new stdClass();

		// update hit
		if ($this->parent->set['enableUpdateHit'] && $article_srl)
		{
			$repo->resultUpdateHit = core\Util::jsonToArray($this->parent->updateHit($article_srl, 1, __GOOSE_ROOT__ . '/'));
		}

		// get article data
		$repo->article = core\Spawn::item([
			'table' => core\Spawn::getTableName($this->parent->name),
			'where' => 'srl=' . $article_srl,
			'jsonField' => [ 'json' ]
		]);

		// get file data
		$repo->file = core\Spawn::items([
			'table' => core\Spawn::getTableName('File'),
			'where' => 'article_srl=' . $article_srl,
			'order' => 'srl',
			'sort' => 'asc'
		]);

		// get nest data
		if ($repo->article['nest_srl'])
		{
			$repo->nest = core\Spawn::item([
				'table' => core\Spawn::getTableName('Nest'),
				'field' => 'srl,name,json',
				'where' => 'srl=' . (int)$repo->article['nest_srl'],
				'jsonField' => [ 'json' ]
			]);
		}

		// get category data
		if ($repo->nest['json']['useCategory'] && $repo->article['category_srl'])
		{
			$repo->category = core\Spawn::item([
				'table' => core\Spawn::getTableName('Category'),
				'where' => 'srl=' . (int)$repo->article['category_srl']
			]);
		}

		// check permission
		$permission = new stdClass();
		$permission->level1 = (isset($repo->nest['json']['permission'])) ? (int)$repo->nest['json']['permission'] : 0;
		$permission->level2 = (isset($repo->nest['json']['permission2'])) ? (int)$repo->nest['json']['permission2'] : 0;
		$this->checkNestPermission($permission->level1);
		if ($permission->level2 < $_SESSION['goose_level'])
		{
			$this->parent->isAdmin = true;
		}

		// set skin path
		$this->setSkinPath('read', $repo->nest['json']['articleSkin']);

		// render page
		$this->blade->render($this->parent->skinAddr . '.read', [
			'mod' => $this->parent,
			'repo' => $repo,
			'article_srl' => $article_srl,
			'category_srl' => $category_srl
		]);
	}

	/**
	 * view - create
	 */
	public function view_create()
	{
		$nest_srl = ($this->parent->params['params'][0]) ? (int)$this->parent->params['params'][0] : null;
		$category_srl = ($this->parent->params['params'][1]) ? (int)$this->parent->params['params'][1] : null;

		// make repo
		$repo = new stdClass();
		$repo->nest = [];
		$repo->category = [];

		// get nest data
		if ($nest_srl)
		{
			$repo->nest = core\Spawn::item([
				'table' => core\Spawn::getTableName('Nest'),
				'field' => 'srl,app_srl,name,json',
				'where' => 'srl=' . $nest_srl,
				'jsonField' => ['json']
			]);

			// get category data
			if ($repo->nest['json']['useCategory'])
			{
				$repo->category = core\Spawn::items([
					'table' => core\Spawn::getTableName('Category'),
					'field' => 'srl,name',
					'where' => 'nest_srl=' . $nest_srl,
					'order' => 'turn',
					'sort' => 'asc'
				]);
			}

			// check permission
			$this->checkAdmin($repo->nest['json']['permission2']);
		}
		else
		{
			// check permission
			$this->checkAdmin($this->parent->set['adminPermission']);
		}

		// set skin path
		$this->setSkinPath('form');

		// play render page
		$this->blade->render($this->parent->skinAddr . '.form', [
			'mod' => $this->parent,
			'repo' => $repo,
			'nest_srl' => $nest_srl,
			'category_srl' => $category_srl,
			'action' => $this->parent->params['action'],
			'typeName' => '등록'
		]);
	}

	/**
	 * view - modify
	 */
	public function view_modify()
	{
		$article_srl = null;
		$category_srl = null;

		// set srl
		if ($this->parent->params['params'][1])
		{
			$category_srl = (int)$this->parent->params['params'][0];
			$article_srl = (int)$this->parent->params['params'][1];
		}
		else if ($this->parent->params['params'][0])
		{
			$article_srl = (int)$this->parent->params['params'][0];
		}

		// check article_srl
		if (!$article_srl)
		{
			core\Util::back('article_srl값이 없습니다.');
		}

		// make repo
		$repo = new stdClass();

		// get article data
		$repo->article = core\Spawn::item([
			'table' => core\Spawn::getTableName($this->parent->name),
			'where' => 'srl=' . $article_srl,
			'jsonField' => ['json']
		]);

		// get file data
		$repo->file = core\Spawn::items([
			'table' => core\Spawn::getTableName('File'),
			'where' => 'article_srl=' . $article_srl
		]);

		// get nest data
		$repo->nest = core\Spawn::item([
			'table' => core\Spawn::getTableName('Nest'),
			'where' => 'srl='.$repo->article['nest_srl'],
			'jsonField' => ['json']
		]);

		// get category data
		if ($repo->nest['json']['useCategory'])
		{
			$repo->category = core\Spawn::items([
				'table' => core\Spawn::getTableName('Category'),
				'where' => 'nest_srl=' . $repo->nest['srl'],
				'order' => 'turn',
				'sort' => 'asc'
			]);
		}

		// check permission
		$this->checkAdmin($repo->nest['json']['permission2']);

		// set skin path
		$this->setSkinPath('form');

		// play render page
		$this->blade->render($this->parent->skinAddr . '.form', [
			'mod' => $this->parent,
			'repo' => $repo,
			'article_srl' => $article_srl,
			'category_srl' => $category_srl,
			'action' => $this->parent->params['action'],
			'typeName' => '수정'
		]);
	}

	/**
	 * view - remove
	 */
	public function view_remove()
	{
		$article_srl = null;
		$category_srl = null;

		// set srl
		if ($this->parent->params['params'][1])
		{
			$category_srl = (int)$this->parent->params['params'][0];
			$article_srl = (int)$this->parent->params['params'][1];
		}
		else if ($this->parent->params['params'][0])
		{
			$article_srl = (int)$this->parent->params['params'][0];
		}

		// check article_srl
		if (!$article_srl)
		{
			core\Util::back('article_srl값이 없습니다.');
		}

		// make repo
		$repo = new stdClass();

		// get article data
		$repo->article = core\Spawn::item([
			'table' => core\Spawn::getTableName($this->parent->name),
			'where' => 'srl=' . $article_srl,
			'jsonField' => ['json']
		]);

		// get nest data
		$repo->nest = core\Spawn::item([
			'table' => core\Spawn::getTableName('Nest'),
			'where' => 'srl='.$repo->article['nest_srl'],
			'jsonField' => ['json']
		]);

		// check permission
		$this->checkAdmin($repo->nest['json']['permission2']);

		// set skin path
		$this->setSkinPath('remove');

		// play render page
		$this->blade->render($this->parent->skinAddr . '.remove', [
			'mod' => $this->parent,
			'repo' => $repo,
			'article_srl' => $article_srl,
			'category_srl' => $category_srl,
			'action' => $this->parent->params['action'],
			'typeName' => '삭제'
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