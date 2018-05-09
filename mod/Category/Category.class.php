<?php
namespace mod\Category;
use core;
if (!defined('__GOOSE__')) exit();


class Category {

	public $name, $params, $set, $isAdmin;
	public $path, $skinPath, $skinAddr;

	public function __construct($params=[])
	{
		core\Module::initModule($this, $params);
	}

	/**
	 * index method
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
				case 'sort':
					$result = $this->transaction('sort', $_POST);
					break;
			}
			if ($result) core\Module::afterAction($result);
		}
		else
		{
			$view = new View($this);

			switch ($this->params['action']) {
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
	 * @return array
	 */
	public function transaction($method, $post=[])
	{
		if (!$method) return [ 'state' => 'error', 'action' => 'back', 'message' => 'method값이 없습니다.' ];
		if ($this->name != 'Category') return [ 'state' => 'error', 'action' => 'back', 'message' => '잘못된 객체로 접근했습니다.' ];

		// check permission
		if ($post['nest_srl'])
		{
			$nest = core\Spawn::item([
				'table' => core\Spawn::getTableName('Nest'),
				'field' => 'json',
				'where' => 'srl='.$post['nest_srl'],
				'jsonField' => ['json']
			]);
		}
		$permission = (isset($nest['json']['permission2'])) ? $nest['json']['permission2'] : $this->set['adminPermission'];
		if (!$this->isAdmin && ((int)$permission > $_SESSION['goose_level']))
		{
			return [
				'state' => 'error',
				'action' => 'back',
				'message' => '권한이 없습니다.'
			];
		}

		$loc = __GOOSE_PWD__ . $this->skinPath . 'transaction-' . $method . '.php';

		if (file_exists($loc))
		{
			return (require_once($loc));
		}
		else
		{
			return [
				'state' => 'error',
				'action' => 'back',
				'message' => '처리파일이 없습니다.'
			];
		}
	}

	/**
	 * make search
	 *
	 * @param string $search
	 * @return string
	 */
	public function makeSearch($search='')
	{
		if ($nest_srl = core\Util::getParameter('nest'))
		{
			$search .= ' and nest_srl='.$nest_srl;
		}
		if ($name = core\Util::getParameter('name'))
		{
			$search .= ' and name LIKE \''.$name.'\'';
		}

		$search = preg_replace("/^ and/", "", $search);
		return trim($search);
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