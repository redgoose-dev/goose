<?php
if (!defined('__GOOSE__')) exit();

/**
 * Module - Article
 *
 */
class Article {

	public $name, $goose, $layout, $param, $isAdmin, $set, $repo;
	public $path, $skinPath;

	/**
	 * construct
	 *
	 * @param array $getter
	 */
	public function __construct($getter=[])
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
					$result = $this->transaction('create', $_POST, $_FILES);
					break;
				case 'modify':
					$result = $this->transaction('modify', $_POST, $_FILES);
					break;
				case 'remove':
					$result = $this->transaction('remove', $_POST, $_FILES);
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
		if ($this->name != 'article') return [ 'state' => 'error', 'message' => '잘못된 객체로 접근했습니다.' ];

		// set original parameter
		$originalParam = [ 'table' => Spawn::getTableName($this->name) ];

		// get data
		$data = Spawn::count(Util::extendArray($originalParam, $getParams));

		// return data
		return [ 'state' => 'success', 'data' => $data ];
	}

	/**
	 * api - get items
	 *
	 * @param array $getParams
	 * @return array|null
	 */
	public function getItems($getParams=null)
	{
		if ($this->name != 'article') return [ 'state' => 'error', 'message' => '잘못된 객체로 접근했습니다.' ];

		// set original parameter
		$originalParam = [
			'table' => Spawn::getTableName($this->name),
			'order' => 'srl',
			'sort' => 'desc'
		];

		// get data
		$data = Spawn::items(Util::extendArray($originalParam, $getParams));
		if (!count($data)) return [ 'state' => 'error', 'message' => '데이터가 없습니다.' ];

		// convert json data
		foreach ($data as $k=>$v)
		{
			if ($data[$k]['json'])
			{
				$data[$k]['json'] = Util::jsonToArray($v['json'], null, true);
			}
		}

		// return data
		return [ 'state' => 'success', 'data' => $data ];
	}

	/**
	 * api - get item
	 *
	 * @param array $getParam
	 * @return array|null
	 */
	public function getItem($getParam=[])
	{
		if ($this->name != 'article') return [ 'state' => 'error', 'message' => '잘못된 객체로 접근했습니다.' ];

		// set original parameter
		$originalParam = [ 'table' => Spawn::getTableName($this->name) ];

		// get data
		$data = Spawn::item(Util::extendArray($originalParam, $getParam));

		// check data
		if (!$data) return [ 'state' => 'error', 'message' => '데이터가 없습니다.' ];

		// convert json data
		if ($data['json'])
		{
			$data['json'] = Util::jsonToArray($data['json'], null, true);
		}

		// return data
		return [ 'state' => 'success', 'data' => $data ];
	}

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
			$nest = Spawn::item([
				'table' => Spawn::getTableName('nest'),
				'where' => 'srl='.$post['nest_srl']
			]);
			$nest['json'] = (isset($nest['json'])) ? Util::jsonToArray($nest['json'], false, true) : $nest['json'];
		}
		$permission = (isset($nest['json']['permission2'])) ? $nest['json']['permission2'] : $this->set['adminPermission'];

		// check permission
		if (!$this->isAdmin && ((int)$permission > $_SESSION['goose_level']))
		{
			return [ 'state' => 'error', 'action' => 'back', 'message' => '권한이 없습니다.' ];
		}

		$loc = Util::isFile([
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
			$result = Spawn::update([
				'table' => Spawn::getTableName('article')
				,'where' => 'srl='.$srl
				,'data' => [ 'hit='.$articleCount ]
			]);
			if ($result == 'success')
			{
				return Util::arrayToJson([
					'state' => 'success',
					'message' => 'success update hit.'
				]);
			}
			else
			{
				return Util::arrayToJson([
					'state' => 'error',
					'message' => 'db error'
				]);
			}
		}
		else
		{
			return Util::arrayToJson([
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
		$query = Spawn::arrayToCreateTableQuery([
			'tableName' => Spawn::getTableName($this->name),
			'fields' => $installData
		]);

		return Spawn::action($query);
	}
}