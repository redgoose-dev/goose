<?php
if (!defined('__GOOSE__')) exit();

class View extends Article {

	private $parent;

	/**
	 * construct
	 *
	 * @param Article $parent
	 */
	public function __construct($parent)
	{
		$this->name = 'view';
		$this->parent = $parent;

		$this->param = $this->parent->param;
		$this->path = $this->parent->path;
		$this->set = $this->parent->set;
		$this->skinPath = $this->parent->skinPath;
	}

	/**
	 * index
	 */
	protected function render()
	{
		// create layout module
		$this->layout = Module::load('layout');

		switch($this->param['action'])
		{
			case 'read':
				$this->view_read();
				break;
			case 'create':
				$this->view_create();
				break;
			case 'modify':
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
	 * check admin
	 */
	private function checkAdmin()
	{
		if (!$this->parent->isAdmin)
		{
			Util::back('권한이 없습니다.');
			Goose::end();
		}
	}

	/**
	 * view - index
	 */
	private function view_index()
	{
		// set srl
		$nest_srl = ($this->param['params'][0]) ? (int)$this->param['params'][0] : null;
		$category_srl = ($this->param['params'][1]) ? (int)$this->param['params'][1] : null;

		// set repo
		$repo = array('article' => null, 'category' => null, 'nest' => null);

		// load modules
		$nest = Module::load('nest');
		$category = Module::load('category');

		// get nest data
		if ($nest_srl)
		{
			$data = $nest->getItem( array( 'where' => 'srl='.$nest_srl ) );
			$repo['nest'] = ($data['state'] == 'success') ? $data['data'] : null;

			// get category data
			if ($repo['nest']['json']['useCategory'])
			{
				$param = ($repo['nest']['srl']) ? 'nest_srl='.(int)$repo['nest']['srl'] : null;
				$data = $category->getItems( array('where' => $param) );
				$repo['category'] = ($data['state'] == 'success') ? $data['data'] : null;
			}
		}

		// set article params
		$param = '';
		$param .= ($nest_srl) ? ' nest_srl='.$nest_srl : '';
		$param .= ($nest_srl && $category_srl) ? ' and' : '';
		$param .= ($category_srl) ? ' category_srl='.$category_srl : '';
		$param .= ($category_srl && $_GET['keyword']) ? ' and' : '';
		$param .= ($_GET['keyword']) ? ' and (title LIKE \'%'.$_GET['keyword'].'%\' or content LIKE \'%'.$_GET['keyword'].'%\')' : '';

		// get article count
		$count = $this->parent->getCount( array('where' => $param) );
		$count = $count['data'];

		// get article data
		//if ($count)
		if (true)
		{
			// set listCount
			$pagePerCount = ($repo['nest']['json']['listCount']) ? $repo['nest']['json']['listCount'] : $this->set['pagePerCount'];

			// set paginate
			require_once(__GOOSE_PWD__.'core/classes/Paginate.class.php');
			$paginateParameter = array('keyword'=>(isset($_GET['keyword']))?$_GET['keyword']:'');
			$_GET['page'] = ((isset($_GET['page'])) && $_GET['page'] > 1) ? $_GET['page'] : 1;
			$paginate = new Paginate($count, $_GET['page'], $paginateParameter, (int)$pagePerCount, 5);

			// get article data
			$data = $this->parent->getItems(array(
				'where' => $param,
				'limit' => array($paginate->offset, $paginate->size)
			));
			$repo['article'] = ($data['state'] == 'success') ? $data['data'] : null;

			foreach($repo['article'] as $k=>$v)
			{
				// get category name
				if ($v['category_srl'])
				{
					$data = $category->getItem( array('field' => 'name', 'where' => 'srl='.$v['category_srl']) );
					$repo['article'][$k]['categoryName'] = ($data['state'] == 'success') ? $data['data']['name'] : null;
				}
			}
		}

		// set pwd_container
		$this->pwd_container = Util::isFile(array(
			__GOOSE_PWD__.$this->path.'skin/'.$repo['nest']['json']['articleSkin'].'/view_index.html',
			__GOOSE_PWD__.$this->skinPath.'view_index.html'
		));

		// set skin path
		$this->skinPath = Util::isDir($this->path.'skin/{dir}/', $repo['nest']['json']['articleSkin'], $this->set['skin'], __GOOSE_PWD__);

		require_once($this->layout->getUrl());
	}

	/**
	 * view - read
	 */
	private function view_read()
	{
		$article_srl = null;

		// set srl
		if ($this->param['params'][1])
		{
			$category_srl = (int)$this->param['params'][0];
			$article_srl = (int)$this->param['params'][1];
		}
		else if ($this->param['params'][0])
		{
			$article_srl = (int)$this->param['params'][0];
		}

		// check article_srl
		if (!$article_srl)
		{
			Util::back('article_srl값이 없습니다.');
			Goose::end();
		}

		// update hit
		if ($this->set['enableUpdateHit'] && $article_srl)
		{
			$this->parent->updateHit($article_srl, 1, __GOOSE_ROOT__.'/');
		}

		// get article data
		$data = $this->parent->getItem( array('where' => 'srl='.$article_srl) );
		$repo['article'] = ($data['state'] == 'success') ? $data['data'] : null;

		// get file data
		$file = Module::load('file');
		if ($file->name)
		{
			$data = $file->getItems(array(
				'where' => 'article_srl='.$article_srl,
				'sort' => 'asc'
			));
			$repo['file'] = ($data['state'] == 'success') ? $data['data'] : null;
		}

		// get nest data
		if ($repo['article']['nest_srl'])
		{
			$nest = Module::load('nest');
			if ($nest->name)
			{
				$data = $nest->getItem(array('where' => 'srl='.(int)$repo['article']['nest_srl']));
				$repo['nest'] = ($data['state'] == 'success') ? $data['data'] : null;
			}
		}

		// get category data
		if ($repo['nest']['json']['useCategory'] && $repo['article']['category_srl'])
		{
			$category = Module::load('category');
			if ($category->name)
			{
				$data = $category->getItem(array('where' => 'srl=' . (int)$repo['article']['category_srl']));
				$repo['category'] = ($data['state'] == 'success') ? $data['data'] : null;
			}
		}

		// set pwd_container
		$this->pwd_container = Util::isFile(array(
			__GOOSE_PWD__.$this->path.'skin/'.$repo['nest']['json']['articleSkin'].'/view_read.html',
			__GOOSE_PWD__.$this->skinPath.'view_read.html'
		));

		// set skin path
		$this->skinPath = Util::isDir($this->path.'skin/{dir}/', $repo['nest']['json']['articleSkin'], $this->set['skin'], __GOOSE_PWD__);

		require_once($this->layout->getUrl());
	}

	/**
	 * view - create
	 */
	private function view_create()
	{
		// check admin
		$this->checkAdmin();

		$nest_srl = ($this->param['params'][0]) ? (int)$this->param['params'][0] : null;
		$category_srl = ($this->param['params'][1]) ? (int)$this->param['params'][1] : null;

		// set repo
		$repo = array();

		// get nest data
		if ($nest_srl)
		{
			$nest = Module::load('nest');
			if ($nest->name)
			{
				$data = $nest->getItem(array('where' => 'srl=' . (int)$nest_srl));
				$repo['nest'] = ($data['state'] == 'success') ? $data['data'] : null;
			}
		}

		if ($repo['nest']['json']['useCategory'])
		{
			$category = Module::load('category');
			if ($category->name)
			{
				$data = $category->getItems(array(
					'where' => 'nest_srl=' . $repo['nest']['srl'],
					'order' => 'turn',
					'sort' => 'asc'
				));
				$repo['category'] = ($data['state'] == 'success') ? $data['data'] : null;
			}
		}

		// set pwd_container
		$this->pwd_container = Util::isFile(array(
			__GOOSE_PWD__.$this->path.'skin/'.$repo['nest']['json']['articleSkin'].'/view_form.html',
			__GOOSE_PWD__.$this->skinPath.'view_form.html'
		));

		// set skin path
		$this->skinPath = Util::isDir($this->path.'skin/{dir}/', $repo['nest']['json']['articleSkin'], $this->set['skin'], __GOOSE_PWD__);

		require_once($this->layout->getUrl());
	}

	/**
	 * view - modify
	 */
	private function view_modify()
	{
		// check admin
		$this->checkAdmin();

		// set srl
		if ($this->param['params'][1])
		{
			$category_srl = (int)$this->param['params'][0];
			$article_srl = (int)$this->param['params'][1];
		}
		else if ($this->param['params'][0])
		{
			$article_srl = (int)$this->param['params'][0];
		}

		// check article_srl
		if (!$article_srl)
		{
			Util::back('article_srl값이 없습니다.');
			Goose::end();
		}

		// set repo
		$repo = array();

		// get article data
		$data = $this->parent->getItem( array('where' => 'srl='.$article_srl) );
		$repo['article'] = ($data['state'] == 'success') ? $data['data'] : null;

		// get file data
		$file = Module::load('file');
		$data = $file->getItems( array('where' => 'article_srl='.$article_srl) );
		$repo['file'] = ($data['state'] == 'success') ? $data['data'] : null;

		// get nest data
		$nest = Module::load('nest');
		$data = $nest->getItem( array('where' => 'srl='.$repo['article']['nest_srl']) );
		$repo['nest'] = ($data['state'] == 'success') ? $data['data'] : null;

		// get category data
		if ($repo['nest']['json']['useCategory'])
		{
			$category = Module::load('category');
			$data = $category->getItems(array(
				'where' => 'nest_srl='.$repo['nest']['srl'],
				'order' => 'turn',
				'sort' => 'asc'
			));
			$repo['category'] = ($data['state'] == 'success') ? $data['data'] : null;
		}

		// set pwd_container
		$this->pwd_container = Util::isFile(array(
			__GOOSE_PWD__.$this->path.'skin/'.$repo['nest']['json']['articleSkin'].'/view_form.html',
			__GOOSE_PWD__.$this->skinPath.'view_index.html'
		));

		// set skin path
		$this->skinPath = Util::isDir($this->path.'skin/{dir}/', $repo['nest']['json']['articleSkin'], $this->set['skin'], __GOOSE_PWD__);

		require_once($this->layout->getUrl());
	}

	/**
	 * view - remove
	 */
	private function view_remove()
	{
		// check admin
		$this->checkAdmin();

		// set srl
		if ($this->param['params'][1])
		{
			$category_srl = (int)$this->param['params'][0];
			$article_srl = (int)$this->param['params'][1];
		}
		else if ($this->param['params'][0])
		{
			$article_srl = (int)$this->param['params'][0];
		}

		// check article_srl
		if (!$article_srl)
		{
			Util::back('article_srl값이 없습니다.');
			Goose::end();
		}

		// set repo
		$repo = array();

		// get article data
		$data = $this->parent->getItem(array('where' => 'srl='.$article_srl));
		$repo['article'] = ($data['state'] == 'success') ? $data['data'] : null;

		// get nest data
		$nest = Module::load('nest');
		if ($nest->name)
		{
			$data = $nest->getItem(array('where' => 'srl='.$repo['article']['nest_srl']));
			$repo['nest'] = ($data['state'] == 'success') ? $data['data'] : null;
		}

		// set pwd_container
		$this->pwd_container = Util::isFile(array(
			__GOOSE_PWD__.$this->path.'skin/'.$repo['nest']['json']['articleSkin'].'/view_remove.html',
			__GOOSE_PWD__.$this->skinPath.'view_remove.html'
		));

		// set skin path
		$this->skinPath = Util::isDir($this->path.'skin/{dir}/', $repo['nest']['json']['articleSkin'], $this->set['skin'], __GOOSE_PWD__);

		require_once($this->layout->getUrl());
	}
}