<?php
if (!defined('__GOOSE__')) exit();

/**
 * Module - API
 *
 */

class API {

	public $name, $param, $set, $layout;
	public $path;
	public $method;

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
	}

	/**
	 * index
	 */
	public function index()
	{
		$this->method = Util::getMethod();

		switch($this->param['action'])
		{
			case 'get':
				$result = $this->api_get($this->param['params'][0], $this->method);
				$this->render($result, $this->method['type']);
				break;
			default:
				$this->view_index();
				break;
		}
	}

	/**
	 * auth
	 * api를 사용할 수 있는지 자격을 확인한다.
	 *
	 * @param string $apiKey
	 * @return boolean
	 */
	private function auth($apiKey)
	{
		return ($apiKey == __apiKey__) ? true : false;
		return true;
	}

	/**
	 * render
	 * 결과물을 출력한다.
	 *
	 * @param array $getResult
	 * @param string $type
	 */
	private function render($getResult, $type)
	{
		$result = '';
		switch($type)
		{
			case "xml":
				$xml = new SimpleXMLElement("<?xml version=\"1.0\"?><root></root>");
				$this->arrayToXml($getResult, $xml);
				$result = $xml->asXML();
				$header = "Content-Type: text/xml; charset=utf-8";
				break;

			case "json":
				$result = json_encode($getResult, true);
				$header = "Content-Type: text/plain; charset=utf-8";
				break;

			case "text":
				$result = $getResult;
				$header = "Content-Type: text/plain; charset=utf-8";
				break;

			default:
				// to html
				$result = $getResult;
				$header = "content-type:text/html; charset=utf-8";
				break;
		}

		header($header);
		print_r($result);
		Goose::end(false);
	}

	/**
	 * parameter to array
	 * 얻어온 파라메터를 가공해서 array형식으로 변환해서 반환한다.
	 *
	 * @param array $getParam
	 * @param array|string $allowFields
	 * @return array
	 */
	private function parameterToArray($getParam=array(), $allowFields=null)
	{
		// check where
		if ($getParam['where'])
		{
			$str = '';
			foreach(explode('|', $getParam['where']) as $k=>$v)
			{
				$val = explode('::', $v);
				$val[1] = (is_numeric($val[1])) ? $val[1] : "'$val[1]'";
				$val[2] = ($val[2]) ? $val[2] : 'and';
				$str .= ($k == 0) ? '' : ' '.$val[2].' ';
				$str .= $val[0].'='.$val[1];
			}
			$getParam['where'] = $str;
		}

		// check fields
		if ($getParam['field'])
		{
			if ($allowFields == '*')
			{
				$getParam['field'] = ($getParam['field']) ? $getParam['field'] : '*';
			}
			else if (is_array($allowFields))
			{
				$fields = explode(',', $getParam['field']);
				$fields = array_intersect($allowFields, $fields);
				$getParam['field'] = implode(',', $fields);
				$getParam['field'] = (!$getParam['field']) ? implode(',', $allowFields) : $getParam['field'];
			}
		}
		else
		{
			if ($allowFields == '*')
			{
				$getParam['field'] = '*';
			}
			else if (is_array($allowFields))
			{
				$getParam['field'] = implode(',', $allowFields);
			}
		}

		return $getParam;
	}

	/**
	 * array to xml
	 *
	 * @param array $student_info
	 * @param SimpleXMLElement $xml_student_info
	 * @return array
	 */
	private function arrayToXml($student_info, &$xml_student_info)
	{
		foreach($student_info as $key => $value)
		{
			if (is_array($value))
			{
				if(!is_numeric($key))
				{
					$subnode = $xml_student_info->addChild("$key");
					$this->arrayToXml($value, $subnode);
				}
				else
				{
					$subnode = $xml_student_info->addChild("item$key");
					$this->arrayToXml($value, $subnode);
				}
			}
			else
			{
				$xml_student_info->addChild("$key", htmlspecialchars("$value"));
			}
		}
	}


	/**********************************************
	 * VIEW AREA
	 *********************************************/

	/**
	 * view - index
	 */
	private function view_index()
	{
		// layout 모듈을 불러와서 출력
		//var_dump($this->goose);
		//echo "view index";
	}


	/**********************************************
	 * API AREA
	 *********************************************/

	/**
	 * api - get data
	 * 데이터를 얻어오는 역할을 하는 메서드
	 *
	 * @param string $method
	 * @param array $get parameter
	 * @return array
	 */
	private function api_get($method, $get)
	{
		if (!$this->auth($get['api_key'])) return Array( 'state' => 'error', 'message' => '올바른 api_key값이 아닙니다.' );

		// check mod value
		if (!$get['mod']) return array('state' => 'error', 'message' => 'mod값이 없습니다.');

		// set table
		$get['table'] = ($get['table']) ? $get['table'] : $get['mod'];

		// get module
		$activeMod = Module::load($get['mod']);

		// get allow field
		if (!count($activeMod->set['allowApi']['read'])) return array('state' => 'error', 'message' => '해당모듈에 허용하는 필드에 접근할 수 없습니다.');

		// set parameters
		$params = $this->parameterToArray($get, $activeMod->set['allowApi']['read'][$get['table']]);

		switch($method)
		{
			// get count
			case 'count':
				$result = Spawn::count(array(
					'table' => Spawn::getTableName($params['table']),
					'where' => ($params['where']) ? $params['where'] : null
				));

				return array('state' => 'success', 'data' => $result);
				break;

			// get single item
			case 'single':
				$result = Spawn::item(array(
					'table' => Spawn::getTableName($params['table']),
					'field' => $params['field'],
					'where' => $params['where']
					,'debug' => false
				));
				if (!$result) $result = array();

				return array('state' => 'success', 'data' => $result);
				break;

			// get multiple items
			case 'multi':
				$total = Spawn::count(array(
					'table' => Spawn::getTableName($params['table']),
					'where' => $params['where']
				));
				if ($total > 0)
				{
					require_once(__GOOSE_PWD__.'core/classes/Paginate.class.php');
					$params['page'] = ($params['page'] > 1) ? $params['page'] : 1;
					$params['limit'] = ($params['limit']) ? $params['limit'] : $this->set['defaultPagePerCount'];
					$params['sort'] = ($params['sort']) ? $params['sort'] : ($params['order']) ? "desc" : "";
					$paginate = new Paginate($total, $params['page'], array(), $params['limit'], 1);

					$result = Spawn::items(array(
						'table' => Spawn::getTableName($params['table']),
						'field' => $params['field'],
						'where' => $params['where'],
						'order' => $params['order'],
						'sort' => $params['sort'],
						'limit' => array($paginate->offset, $paginate->size)
						,'debug' => false
					));
				}
				else
				{
					$result = array();
				}

				return array('state' => 'success', 'data' => $result);
				break;

			// no method
			default:
				return array( 'state' => 'error', 'message' => 'method값이 없습니다.' );
				break;
		}
	}

	/**
	 * api - insert data
	 * 데이터를 삽입하는 메서드
	 *
	 * @param array $get parameter
	 * @return array
	 */
	private function api_insert($get)
	{
		if (!$this->auth($get['api_key'])) return Array( 'state' => 'error', '올바른 api_key값이 아닙니다.' );
	}
}
