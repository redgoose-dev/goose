<?php
namespace mod\Article;
use core, mod;
if (!defined('__GOOSE__')) exit();


class Article {

	public $name, $set, $params, $isAdmin;
	public $path, $skinPath, $skinAddr;

	public function __construct($params=[])
	{
		core\Module::initModule($this, $params);
	}


	/**
	 * index
	 */
	public function index()
	{
		if ($this->params['method'] == 'POST')
		{
			$result = null;
			switch($this->params['action'])
			{
				case 'create':
					$result = $this->transaction('create', $_POST, $_FILES);
					break;
				case 'modify':
					$result = $this->transaction('modify', $_POST, $_FILES);
					break;
				case 'remove':
					$result = $this->transaction('remove', $_POST, $_FILES);
					break;
			}
			if ($result) core\Module::afterAction($result);
		}
		else
		{
			$view = new View($this);

			switch ($this->params['action'])
			{
				case 'read':
					$view->view_read();
					break;
				case 'create':
					$view->view_create();
					break;
				case 'modify':
					$view->view_modify();
					break;
				case 'remove':
					$view->view_remove();
					break;
				default:
					$view->view_index();
					break;
			}
		}
	}

	/**********************************************
	 * API AREA
	 *********************************************/

	/**
	 * api - transaction
	 *
	 * @param string $method
	 * @param array $post
	 * @param array $files
	 * @return array
	 */
	public function transaction($method, $post=[], $files=[])
	{
		if (!$method) return [ 'state' => 'error', 'action' => 'back', 'message' => 'method값이 없습니다.' ];
		if ($this->name != 'Article') return [ 'state' => 'error', 'action' => 'back', 'message' => '잘못된 객체로 접근했습니다.' ];

		if ($post['nest_srl'])
		{
			$nest = core\Spawn::item([
				'table' => core\Spawn::getTableName('Nest'),
				'where' => 'srl='.$post['nest_srl'],
				'jsonField' => ['json']
			]);
		}
		$permission = (isset($nest['json']['permission2'])) ? $nest['json']['permission2'] : $this->set['adminPermission'];

		// check permission
		if (!$this->isAdmin && ((int)$permission > $_SESSION['goose_level']))
		{
			return [ 'state' => 'error', 'action' => 'back', 'message' => '권한이 없습니다.' ];
		}

		$path = core\Util::isFile([
			__GOOSE_PWD__ . $this->path . 'skin/' . $post['skin'] . '/transaction-' . $method . '.php',
			__GOOSE_PWD__ . $this->path . 'skin/' . $this->set['skin'] . '/transaction-' . $method . '.php'
		]);

		if ($path)
		{
			return (require_once($path));
		}
		else
		{
			return [ 'state' => 'error', 'action' => 'back', 'message' => '처리파일이 없습니다.' ];
		}
	}

	/**
	 * api - update hit
	 *
	 * @param int $srl
	 * @param int $count
	 * @param string $cookieLoc
	 * @return string
	 */
	public function updateHit($srl=null, $count=1, $cookieLoc='/')
	{
		if (!isset($_COOKIE['hit-' . $srl]) && $srl)
		{
			// get article
			$article = core\Spawn::item([
				'table' => core\Spawn::getTableName($this->name),
				'field' => 'hit',
				'where' => 'srl=' . $srl
			]);

			// update count
			$articleCount = (int)$article['hit'] + $count;

			// set cookie
			setcookie('hit-' . $srl, 1, time()+3600*24, __GOOSE_ROOT__ . '/');

			// update hit
			$result = core\Spawn::update([
				'table' => core\Spawn::getTableName('article'),
				'where' => 'srl=' . $srl,
				'data' => [ 'hit=' . $articleCount ]
			]);
			if ($result == 'success')
			{
				return core\Util::arrayToJson([
					'state' => 'success',
					'message' => 'success update hit.'
				]);
			}
			else
			{
				return core\Util::arrayToJson([
					'state' => 'error',
					'message' => 'db error'
				]);
			}
		}
		else
		{
			return core\Util::arrayToJson([
				'state' => 'error',
				'message' => 'update hit error'
			]);
		}
	}

	/**
	 * create link url in read page
	 *
	 * @param array $src
	 * @return string
	 */
	public function createLinkUrlInReadPage($src=[])
	{
		$url = __GOOSE_ROOT__ . '/';

		if ($src['type'] == 'index' && $src['main']) return $url;

		$url .= $this->name . '/';
		$url .= ($src['type']) ? $src['type'] . '/' : '';
		$url .= ($src['nest_srl']) ? $src['nest_srl'] . '/' : '';
		$url .= ($src['category_srl']) ? $src['category_srl'] . '/' : '';
		$url .= ($src['article_srl']) ? $src['article_srl'] . '/' : '';

		$param = ($src['page']) ? '&page=' . $src['page'] : '';
		$param .= ($src['main']) ? '&m=' . $src['main'] : '';
		$param = preg_replace('/^&/', '?', $param);

		return $url . $param;
	}

	/**
	 * make search
	 *
	 * @param string $search
	 * @return string
	 */
	public function makeSearch($search='')
	{
		if ($app_srl = core\Util::getParameter('app'))
		{
			$search .= ' and app_srl='.$app_srl;
		}
		if ($nest_srl = core\Util::getParameter('nest'))
		{
			$search .= ' and nest_srl='.$nest_srl;
		}
		if ($category_srl = core\Util::getParameter('category'))
		{
			$search .= ' and category_srl='.$category_srl;
		}
		if ($user_srl = core\Util::getParameter('user'))
		{
			$search .= ' and user_srl='.$user_srl;
		}
		if ($title = core\Util::getParameter('title'))
		{
			$search .= ' and title LIKE \'%'.$title.'%\'';
		}
		if ($content = core\Util::getParameter('content'))
		{
			$search .= ' and title LIKE \'%'.$content.'%\'';
		}
		if ($ip = core\Util::getParameter('ip'))
		{
			$search .= ' and ip LIKE \'%'.$ip.'%\'';
		}
		if ($q = core\Util::getParameter('q'))
		{
			$search .= ' and (title LIKE \'%'.$q.'%\' or content LIKE \'%'.$q.'%\')';
		}
		return $search;
	}

	/**********************************************
	 * INSTALL AREA
	 *********************************************/

	/**
	 * install
	 *
	 * @param array $installData install.json 데이터
	 * @return string 문제없으면 "success" 출력한다.
	 */
	public function install($installData)
	{
		$query = core\Spawn::arrayToCreateTableQuery([
			'tableName' => core\Spawn::getTableName($this->name),
			'fields' => $installData
		]);

		return core\Spawn::action($query);
	}
}