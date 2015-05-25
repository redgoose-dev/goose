<?php
if (!defined('__GOOSE__')) exit();

/**
 * Module - nest
 */
class Nest {

	public $name, $goose, $layout, $param, $set, $isAdmin;
	public $repo;
	public $path, $pwd_container, $skinPath;
	public $app_srl, $nest_srl;

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
			$result = null;
			switch($this->param['action'])
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
			if ($result) Module::afterAction($result);

			Goose::end();
		}
		else
		{
			require_once(__GOOSE_PWD__.$this->path.'view.class.php');
			$view = new View($this);
			$view->render();
		}
	}

	/**
	 * check admin
	 */
	private function checkAdmin()
	{
		if (!$this->isAdmin)
		{
			Util::back('권한이 없습니다.');
			Goose::end();
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
	 * get data items
	 *
	 * @param array $getParams
	 * @return array|null
	 */
	public function getItems($getParams=null)
	{
		if ($this->name != 'nest') return array( 'state' => 'error', 'message' => '잘못된 객체로 접근했습니다.' );

		// set original parameter
		$originalParam = array(
			'table' => Spawn::getTableName($this->name),
			'order' => 'srl',
			'sort' => 'desc'
		);

		// get data
		$data = Spawn::items(Util::extendArray($originalParam, $getParams));
		if (!count($data)) return array( 'state' => 'error', 'message' => '데이터가 없습니다.' );

		// convert json data
		foreach ($data as $k=>$v)
		{
			if ($data[$k]['json'])
			{
				$data[$k]['json'] = Util::jsonToArray($v['json'], null, true);
			}
		}

		// return data
		return array( 'state' => 'success', 'data' => $data );
	}

	/**
	 * get data item
	 *
	 * @param array $getParam
	 * @return array|null
	 */
	public function getItem($getParam=array())
	{
		if ($this->name != 'nest') return array( 'state' => 'error', 'message' => '잘못된 객체로 접근했습니다.' );

		// set original parameter
		$originalParam = array( 'table' => Spawn::getTableName($this->name) );

		// get data
		$data = Spawn::item(Util::extendArray($originalParam, $getParam));

		// check data
		if (!$data) return array( 'state' => 'error', 'message' => '데이터가 없습니다.' );

		// convert json data
		if (is_array($data) && $data['json'])
		{
			$data['json'] = Util::jsonToArray($data['json'], null, true);
		}

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
		if ($this->name != 'nest') return array('state' => 'error', 'action' => 'back', 'message' => '잘못된 객체로 접근했습니다.');
		if (!$this->isAdmin) return array('state' => 'error', 'action' => 'back', 'message' => '권한이 없습니다.');

		$json = Util::jsonToArray($post['json'], null, true);

		$loc = Util::isFile(array(
			__GOOSE_PWD__.$this->path.'skin/'.$post['nestSkin'].'/transaction_'.$method.'.php',
			__GOOSE_PWD__.$this->path.'skin/'.$this->set['skin'].'/transaction_'.$method.'.php'
		));

		if ($loc)
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
		$query = Spawn::arrayToCreateTableQuery(array(
			'tableName' => __dbPrefix__.$this->name
			,'fields' => $installData
		));

		return Spawn::action($query);
	}
}