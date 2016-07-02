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
		$permission = (isset($permission)) ? $permission : $this->set['adminPermission'];
		if (!$this->parent->isAdmin)
		{
			if ($_SESSION['goose_level'] < $permission)
			{
				Util::back('권한이 없습니다.');
				Goose::end();
			}
		}
	}

	/**
	 * Get skin names
	 * if the form file brings the skin
	 *
	 * @param array $skins
	 * @return array
	 */
	private function getSkinNames($skins)
	{
		$return = [];
		foreach ($skins as $item) {
			if (file_exists(__GOOSE_PWD__.$this->path.'skin/'.$item.'/view_form.html'))
			{
				$return[] = $item;
			}
		}
		return $return;
	}

	/**
	 * view - index
	 */
	private function view_index()
	{
		// set app srl
		$app_srl = ($this->param['params'][0]) ? (int)$this->param['params'][0] : null;

		// set session
		if ($app_srl)
		{
			$_SESSION['app_srl'] = $app_srl;
		}
		else
		{
			unset($_SESSION['app_srl']);
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
		// load modules
		$app = Module::load('app');

		// get skin index (If only it contains the "view_form.html" file.)
		$repo['skin'] = $this->getSkinNames(Util::getDir(__GOOSE_PWD__.$this->path.'skin/'));

		// get article skin index
		$repo['articleSkin'] = Util::getDir(__GOOSE_PWD__.'module/article/skin/');

		// get app data
		$data = $app->getItems( array('order' => 'srl', 'sort' => 'asc') );
		$repo['app'] = ($data['state'] == 'success') ? $data['data'] : array();

		// set skinPath
		$this->skinPath = ($_GET['skin']) ? $this->path.'skin/'.$_GET['skin'].'/' : $this->skinPath;

		// check permission
		$this->checkAdmin();

		// set pwd_container
		$this->pwd_container = Util::isFile(array(
			__GOOSE_PWD__.$this->path.'skin/'.$_GET['skin'].'/view_form.html',
			__GOOSE_PWD__.$this->path.'skin/'.$this->set['skin'].'/view_form.html',
			__GOOSE_PWD__.$this->path.'skin/default/view_form.html'
		));

		require_once($this->layout->getUrl());
	}

	/**
	 * view - modify
	 */
	private function view_modify()
	{
		// set nest srl
		$nest_srl = ($this->param['params'][0]) ? (int)$this->param['params'][0] : null;

		// load modules
		$app = Module::load('app');

		// get skin index (If only it contains the "view_form.html" file.)
		$repo['skin'] = $this->getSkinNames(Util::getDir(__GOOSE_PWD__.$this->path.'skin/'));

		// get article skin index
		$repo['articleSkin'] = Util::getDir(__GOOSE_PWD__.'module/article/skin/');

		// get app data
		$data = $app->getItems( array('order' => 'srl', 'sort' => 'asc') );
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

		// check permission
		$this->checkAdmin($repo['nest']['json']['permission2']);

		// set pwd_container
		$this->pwd_container = Util::isFile(array(
			__GOOSE_PWD__.$this->path.'skin/'.$_GET['skin'].'/view_form.html',
			__GOOSE_PWD__.$this->path.'skin/'.$repo['nest']['json']['nestSkin'].'/view_form.html',
			__GOOSE_PWD__.$this->path.'skin/'.$this->set['skin'].'/view_form.html',
			__GOOSE_PWD__.$this->path.'skin/default/view_form.html',
		));

		require_once($this->layout->getUrl());
	}

	/**
	 * view - remove
	 */
	private function view_remove()
	{
		// set nest srl
		$nest_srl = ($this->param['params'][0]) ? (int)$this->param['params'][0] : null;

		// get nest data
		$data = $this->parent->getItem([ 'where' => 'srl='.$nest_srl ]);
		if ($data['state'] == 'error')
		{
			Util::back($data['message']);
			Goose::end();
		}
		$repo['nest'] = ($data['state'] == 'success') ? $data['data'] : [];

		// check permission
		$this->checkAdmin($repo['nest']['json']['permission2']);

		// set pwd_container
		$this->pwd_container = Util::isFile([
			__GOOSE_PWD__.$this->path.'skin/'.$repo['nest']['json']['nestSkin'].'/view_remove.html',
			__GOOSE_PWD__.$this->path.'skin/'.$this->set['skin'].'/view_remove.html',
			__GOOSE_PWD__.$this->path.'skin/default/view_remove.html'
		]);

		require_once($this->layout->getUrl());
	}
}
