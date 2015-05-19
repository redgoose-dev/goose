<?php
if (!defined('__GOOSE__')) exit();

class View extends Nest
{

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
		$this->isAdmin = $this->parent->isAdmin;
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
		// set app srl
		$app_srl = ($this->param['params'][0]) ? (int)$this->param['params'][0] : null;

		// set repo
		$repo = array();

		// set session
		if ($app_srl)
		{
			$_SESSION['app_srl'] = $app_srl;
		}
		else
		{
			unset($_SESSION['app_srl']);
		}

		// load modules
		$app = Module::load('app');
		$article = Module::load('article');
		$category = Module::load('category');

		// get app items
		$data = $app->getItems(array('field' => 'srl,name'));
		$repo['app'] = ($data['state'] == 'success') ? $data['data'] : array();
		foreach($repo['app'] as $k=>$v)
		{
			$data = $this->parent->getCount( array('where' => 'app_srl='.(int)$v['srl']) );
			$repo['app'][$k]['countNest'] = ($data['state'] == 'success') ? $data['data'] : 0;
		}

		// get total nest
		$data = $this->parent->getCount();
		$repo['totalNest'] = ($data['state'] == 'success') ? $data['data'] : 0;

		// get nest items
		$params = ($app_srl) ? 'app_srl='.$app_srl : '';
		$data = $this->parent->getItems(array( 'where' => $params ));
		$repo['nest'] = ($data['state'] == 'success') ? $data['data'] : array();
		foreach($repo['nest'] as $k=>$v)
		{
			// get count of article
			$data = $article->getCount( array('where' => 'nest_srl='.(int)$v['srl']) );
			$repo['nest'][$k]['countArticle'] = ($data['state'] == 'success') ? $data['data'] : 0;
			// get app name
			$data = $app->getItem( array('field' => 'name', 'where' => 'srl='.(int)$v['app_srl']) );
			$repo['nest'][$k]['appName'] = ($data['state'] == 'success') ? $data['data']['name'] : '';
			// get category count
			if ($v['json']['useCategory'] == 1)
			{
				$data = $category->getCount( array('where' => 'nest_srl='.(int)$v['srl']) );
				$repo['nest'][$k]['countCategory'] = ($data['state'] == 'success') ? $data['data'] : 0;
			}
		}

		// set pwd_container
		$this->pwd_container = __GOOSE_PWD__.$this->skinPath.'view_index.html';

		require_once($this->layout->getUrl());
	}

	/**
	 * view - create
	 */
	private function view_create()
	{
		// check admin
		$this->checkAdmin();

		// load modules
		$app = Module::load('app');
		$article = Module::load('article');

		// get skin index
		$repo['skin'] = Util::getDir(__GOOSE_PWD__.$this->path.'skin/');

		// get article skin index
		$repo['articleSkin'] = Util::getDir(__GOOSE_PWD__.'module/article/skin/');

		// get app data
		$data = $app->getItems( array('order' => 'srl', 'sort' => 'desc') );
		$repo['app'] = ($data['state'] == 'success') ? $data['data'] : array();

		// set skinPath
		$this->skinPath = ($_GET['skin']) ? $this->path.'skin/'.$_GET['skin'].'/' : $this->skinPath;

		// set pwd_container
		$this->pwd_container = Util::isFile(array(
			__GOOSE_PWD__.$this->path.'skin/'.$_GET['skin'].'/view_form.html',
			__GOOSE_PWD__.$this->path.'skin/'.$this->set['skin'].'/view_form.html'
		));

		require_once($this->layout->getUrl());
	}

	/**
	 * view - modify
	 */
	private function view_modify()
	{
		// check admin
		$this->checkAdmin();

		// set nest srl
		$nest_srl = ($this->param['params'][0]) ? (int)$this->param['params'][0] : null;

		// load modules
		$app = Module::load('app');

		// get skin index
		$repo['skin'] = Util::getDir(__GOOSE_PWD__.$this->path.'skin/');

		// get article skin index
		$repo['articleSkin'] = Util::getDir(__GOOSE_PWD__.'module/article/skin/');

		// get app data
		$data = $app->getItems( array('order' => 'srl', 'sort' => 'desc') );
		$repo['app'] = ($data['state'] == 'success') ? $data['data'] : array();

		// get nest data
		$data = $this->parent->getItem( array('where' => 'srl='.$nest_srl) );
		if ($data['state'] == 'error')
		{
			Util::back($data['message']);
			Goose::end();
		}
		else if ($data['state'] == 'success')
		{
			$repo['nest'] = $data['data'];
		}

		if (!file_exists(__GOOSE_PWD__.$this->path.'skin/'.$repo['nest']['json']['nestSkin'].'/view_form.html'))
		{
			$message['head'] = '['.$repo['nest']['json']['nestSkin'].']스킨의 페이지가 없으므로 ['.$this->set['skin'].']스킨으로 출력합니다.';
		}
		if ($_GET['skin'])
		{
			$message['head'] = '스킨이 변경되었습니다. 적용하면 설정값이 변할수도 있습니다.';
		}

		// set pwd_container
		$this->pwd_container = Util::isFile(array(
			__GOOSE_PWD__.$this->path.'skin/'.$_GET['skin'].'/view_form.html',
			__GOOSE_PWD__.$this->path.'skin/'.$repo['nest']['json']['nestSkin'].'/view_form.html',
			__GOOSE_PWD__.$this->path.'skin/'.$this->set['skin'].'/view_form.html'
		));

		require_once($this->layout->getUrl());
	}

	/**
	 * view - remove
	 */
	private function view_remove()
	{
		// check admin
		$this->checkAdmin();

		// set nest srl
		$nest_srl = ($this->param['params'][0]) ? (int)$this->param['params'][0] : null;

		// get nest data
		$data = $this->parent->getItem( array('where' => 'srl='.$nest_srl) );
		if ($data['state'] == 'error')
		{
			Util::back($data['message']);
			Goose::end();
		}
		$repo['nest'] = ($data['state'] == 'success') ? $data['data'] : array();

		// set pwd_container
		$this->pwd_container = Util::isFile(array(
			__GOOSE_PWD__.$this->path.'skin/'.$repo['nest']['json']['nestSkin'].'/view_remove.html',
			__GOOSE_PWD__.$this->path.'skin/'.$this->set['skin'].'/view_remove.html'
		));

		require_once($this->layout->getUrl());
	}
}
