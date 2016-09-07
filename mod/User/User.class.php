<?php
namespace mod\User;
use core;
if (!defined('__GOOSE__')) exit();


class User {

	public $name, $params, $set, $repo, $isAdmin;
	public $path;

	public function __construct($params=[])
	{
		core\Module::initModule($this, $params, $params['install']);
	}

	/**
	 * index
	 */
	// TODO : 주석풀기
//	public function index()
//	{
//		if ($this->param['method'] == 'POST')
//		{
//			switch($this->param['action'])
//			{
//				case 'create':
//					$result = $this->transaction('create', $_POST);
//					core\Module::afterAction($result);
//					break;
//				case 'modify':
//					$result = $this->transaction('modify', $_POST);
//					core\Module::afterAction($result);
//					break;
//				case 'remove':
//					$result = $this->transaction('remove', $_POST);
//					core\Module::afterAction($result);
//					break;
//			}
//			core\Goose::end();
//		}
//		else
//		{
//			require_once(__GOOSE_PWD__.$this->path.'View.class.php');
//			$view = new View($this);
//			$view->render();
//		}
//	}



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
		if ($this->name != 'user') return [ 'state' => 'error', 'action' => 'back', 'message' => '잘못된 객체로 접근했습니다.' ];
		if (($post['email'] !== $_SESSION['goose_email']) && !$this->isAdmin)
		{
			return [
				'state' => 'error',
				'action' => 'back',
				'message' => '권한이 없습니다.'
			];
		}

		$loc = __GOOSE_PWD__.$this->path.'skin/'.$this->set['skin'].'/transaction_'.$method.'.php';

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
		// create table
		$query = core\Spawn::arrayToCreateTableQuery([
			'tableName' => __dbPrefix__.$this->name,
			'fields' => $installData
		]);

		return core\Spawn::action($query);
	}
}