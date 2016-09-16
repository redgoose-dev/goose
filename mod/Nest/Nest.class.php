<?php
namespace mod\Nest;
use core;
if (!defined('__GOOSE__')) exit();


class Nest {

	public $name, $params, $set, $isAdmin;
	public $path, $skinAddr, $skinPath;
	public $app_srl, $nest_srl;

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
					$result = $this->transaction('create', $_POST);
					break;
				case 'modify':
					$result = $this->transaction('modify', $_POST);
					break;
				case 'remove':
					$result = $this->transaction('remove', $_POST);
					break;
			}
			if ($result) core\Module::afterAction($result);
		}
		else
		{
			$view = new View($this);

			switch($this->params['action'])
			{
				case 'create':
					$view->view_create();
					break;
				case 'modify':
					$view->view_modify();
					break;
				case 'clone':
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
	 * @return array
	 */
	public function transaction($method, $post=[])
	{
		if (!$method) return [ 'state' => 'error', 'action' => 'back', 'message' => 'method값이 없습니다.' ];
		if ($this->name != 'Nest') return [ 'state' => 'error', 'action' => 'back', 'message' => '잘못된 객체로 접근했습니다.' ];

		if ($method == 'create')
		{
			// check user
			if (!$this->isAdmin) return [ 'state' => 'error', 'action' => 'back', 'message' => '권한이 없습니다.' ];
		}
		else if ($method == 'modify' || $method == 'remove')
		{
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
		}

		// check exist file
		$path = __GOOSE_PWD__ . $this->path . 'skin/';
		$loc = core\Util::isFile([
			$path . $post['nestSkin'] . '/transaction-' . $method . '.php',
			$path . $this->set['skin'] . '/transaction-' . $method . '.php',
			$path . 'default/transaction-' . $method . '.php'
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