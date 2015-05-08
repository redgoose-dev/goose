<?php
if (!defined('__GOOSE__')) exit();

/**
 * Module - json
 */
class JSON {

	public $name, $goose, $param, $method, $repo, $layout, $isAdmin;
	public $path, $skinPath, $pwd_container;

	/**
	 * construct
	 *
	 * @param array $getter
	 */
	public function __construct($getter=array())
	{
		$this->name = $getter['name'];
		$this->goose = $getter['goose'];
		$this->isAdmin = $getter['isAdmin'];
		$this->param = $getter['param'];
		$this->path = $getter['path'];
		$this->set = $getter['set'];

		$this->skinPath = $this->path.'skin/'.$this->set['skin'].'/';
	}

	/**
	 * index
	 */
	public function index()
	{
		if ($this->param['method'] == 'POST')
		{
			switch($this->param['action'])
			{
				case 'create':
					$result = $this->transaction('create', $_POST);
					Module::afterAction($result);
					break;
				case 'modify':
					$result = $this->transaction('modify', $_POST);
					Module::afterAction($result);
					break;
				case 'remove':
					$result = $this->transaction('remove', $_POST);
					Module::afterAction($result);
					break;
			}
			Goose::end();
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
	 * api - get count
	 *
	 * @param array $getParams
	 * @return array
	 */
	public function getCount($getParams=null)
	{
		if ($this->name != 'nest') return array( 'state' => 'error', 'message' => '잘못된 객체로 접근했습니다.' );

		// set original parameter
		$originalParam = array('table' => Spawn::getTableName($this->name));

		// get data
		$data = Spawn::count(Util::extendArray($originalParam, $getParams));

		// return data
		return array( 'state' => 'success', 'data' => (int)$data );
	}

	/**
	 * api - get items
	 *
	 * @param array $getParams
	 * @return array|null
	 */
	public function getItems($getParams=null)
	{
		if ($this->name != 'json') return array( 'state' => 'error', 'message' => '잘못된 객체로 접근했습니다.' );

		// set original parameter
		$originalParam = array(
			'table' => Spawn::getTableName($this->name),
			'order' => 'srl',
			'sort' => 'desc'
		);

		// get data
		$data = Spawn::items(Util::extendArray($originalParam, $getParams));
		if (!count($data)) return array( 'state' => 'error', 'message' => '데이터가 없습니다.' );

		// return data
		return array( 'state' => 'success', 'data' => $data );
	}

	/**
	 * api - get item
	 *
	 * @param array $getParam
	 * @return array|null
	 */
	public function getItem($getParam=array())
	{
		if ($this->name != 'json') return array( 'state' => 'error', 'message' => '잘못된 객체로 접근했습니다.' );

		// set original parameter
		$originalParam = array( 'table' => Spawn::getTableName($this->name) );

		// get data
		$data = Spawn::item(Util::extendArray($originalParam, $getParam));

		// check data
		if (!$data) return array( 'state' => 'error', 'message' => '데이터가 없습니다.' );

		// return data
		return array( 'state' => 'success', 'data' => $data );
	}

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
		$query = Spawn::arrayToCreateTableQuery(array(
			'tableName' => Spawn::getTableName($this->name)
			,'fields' => $installData
		));

		return Spawn::action($query);
	}
}