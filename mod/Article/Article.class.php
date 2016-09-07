<?php
namespace mod\Article;
use core;
if (!defined('__GOOSE__')) exit();


class Article {

	public $name, $params, $set, $isAdmin;
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

			core\Goose::end();
		}
		else
		{
			require_once(__GOOSE_PWD__.$this->path.'view.class.php');
			$view = new View($this);
			//$view->render();
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
		if ($this->name != 'article') return [ 'state' => 'error', 'action' => 'back', 'message' => '잘못된 객체로 접근했습니다.' ];

		if ($post['nest_srl'])
		{
			$nest = core\Spawn::item([
				'table' => core\Spawn::getTableName('nest'),
				'where' => 'srl='.$post['nest_srl']
			]);
			$nest['json'] = (isset($nest['json'])) ? core\Util::jsonToArray($nest['json'], false, true) : $nest['json'];
		}
		$permission = (isset($nest['json']['permission2'])) ? $nest['json']['permission2'] : $this->set['adminPermission'];

		// check permission
		if (!$this->isAdmin && ((int)$permission > $_SESSION['goose_level']))
		{
			return [ 'state' => 'error', 'action' => 'back', 'message' => '권한이 없습니다.' ];
		}

		$loc = core\Util::isFile([
			__GOOSE_PWD__.$this->path.'skin/'.$post['skin'].'/transaction_'.$method.'.php',
			__GOOSE_PWD__.$this->path.'skin/'.$this->set['skin'].'/transaction_'.$method.'.php'
		]);

		if ($loc)
		{
			return (require_once($loc));
		}
		else
		{
			return [ 'state' => 'error', 'action' => 'back', 'message' => '처리파일이 없습니다.' ];
		}
	}

	/**
	 * api - update hit
	 *
	 * @param number $srl
	 * @param number $count
	 * @param string $cookieLoc
	 * @return array
	 */
	public function updateHit($srl, $count, $cookieLoc)
	{
		$article = $this->getItem([
			'field' => 'hit',
			'where' => 'srl='.$srl
		]);
		$articleCount = (isset($article['data'])) ? $article['data']['hit'] : null;
		$articleCount += $count;

		if (!isset($_COOKIE['hit-'.$srl]))
		{
			// set cookie
			setcookie('hit-'.$srl, 1, time()+3600*24, __GOOSE_ROOT__.'/');

			// update hit
			$result = core\Spawn::update([
				'table' => core\Spawn::getTableName('article'),
				'where' => 'srl='.$srl,
				'data' => [ 'hit='.$articleCount ]
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
				'message' => 'cookie error'
			]);
		}
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