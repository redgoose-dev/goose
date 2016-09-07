<?php
namespace mod\JSON;
use core;
if (!defined('__GOOSE__')) exit();


class JSON {

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
			switch($this->params['action'])
			{
				case 'create':
					$result = $this->transaction('create', $_POST);
					core\Module::afterAction($result);
					break;
				case 'modify':
					$result = $this->transaction('modify', $_POST);
					core\Module::afterAction($result);
					break;
				case 'remove':
					$result = $this->transaction('remove', $_POST);
					core\Module::afterAction($result);
					break;
			}
			core\Goose::end();
		}
		else
		{
			require_once(__GOOSE_PWD__.$this->path.'view.class.php');
			$view = new View($this);
			$view->render();
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
	public function transaction($method, $post=array())
	{
		if (!$method) return array('state' => 'error', 'action' => 'back', 'message' => 'method값이 없습니다.');
		if ($this->name != 'json') return array('state' => 'error', 'action' => 'back', 'message' => '잘못된 객체로 접근했습니다.');
		if (!$this->isAdmin) return array('state' => 'error', 'action' => 'back', 'message' => '권한이 없습니다.');

		$loc = __GOOSE_PWD__.$this->path.'skin/'.$this->set['skin'].'/transaction_'.$method.'.php';

		if (file_exists($loc))
		{
			return (require_once($loc));
		}
		else
		{
			return array(
				'state' => 'error',
				'action' => 'back',
				'message' => '처리파일이 없습니다.'
			);
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
		// create table
		$query = core\Spawn::arrayToCreateTableQuery(array(
			'tableName' => core\Spawn::getTableName($this->name)
			,'fields' => $installData
		));

		return core\Spawn::action($query);
	}
}