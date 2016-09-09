<?php
namespace mod\App;
use core;
if (!defined('__GOOSE__')) exit();


class App {

	public $name, $set, $path, $params;
	public $skinPath, $skinAddr, $message;

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

			switch ($this->params['action'])
			{
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
		if ($this->name != 'App') return [ 'state' => 'error', 'action' => 'back', 'message' => '잘못된 객체로 접근했습니다.' ];
		if (!$this->isAdmin) return [ 'state' => 'error', 'action' => 'back', 'message' => '권한이 없습니다.' ];

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
		$result = core\Spawn::action($query);
		return $result;
	}
}