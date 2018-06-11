<?php
namespace mod\api;
use core, mod;
if (!defined('__GOOSE__')) exit();


// TODO: 컴포넌트별로 요구 파라메터를 만들어야함 (Nest의 makeSearch 메서드 참고)


class api {

	public $name, $set, $params, $isAdmin;
	public $path, $skinPath, $skinAddr;

	public function __construct($params=[])
	{
		core\Module::initModule($this, $params);
	}

	public function index()
	{
		global $goose;

		if (!$this->params['action'])
		{
			$view = new View($this);
			$view->view_index();
			return null;
		}

		// check token
		$getToken = '';
		if (core\Util::getParameter('token'))
		{
			$getToken = core\Util::getParameter('token');
		}
		else if (getallheaders()['token'])
		{
			$getToken = getallheaders()['token'];
		}
		if ($goose->token !== $getToken)
		{
			$this->error('Token error', 403);
		}

		try
		{
			// get module
			$module = $this->getModule($this->params['action']);

			// check is module
			if (!($module && $module->set && $module->set['api']))
			{
				throw new \Exception('not-found-module');
				return null;
			}

			// check using api
			if (!$module->set['api']['use'])
			{
				throw new \Exception('not-allow-module');
				return null;
			}

			switch ($_SERVER['REQUEST_METHOD'])
			{
				case 'GET':
				default:
					$this->get($module);
					break;
			}
		}
		catch(\Exception $e)
		{
			$this->error($e->getMessage());
		}
	}

	/**
	 * check using api and module
	 *
	 * @param string $action
	 * @return object
	 */
	private function getModule($action)
	{
		if (!$action) return null;

		// check module name
		$module = $this->set['modules'][$action];
		if (!$module) return null;

		// check module setting
		return core\Module::load($module);
	}

	/**
	 * print error
	 *
	 * @param string $message
	 * @param int $code
	 */
	private function error($message, $code=500)
	{
		switch($message)
		{
			case 'not-found-module':
				$message = 'Not found module.';
				break;
			case 'not-allow-module':
				$message = 'Not allowed by the module.';
				break;
		}

		$this->output((object)[
			'message' => $message,
			'code' => $code
		]);
	}

	private function get($mod)
	{
		// set config on module
		$config = (object)$mod->set['api'];

		// set srl
		$srl = $this->params['params'][0];
		$srl = str_replace('/', '', $srl);

		// set where
		$where = '';
		if ($srl)
		{
			$where .= 'srl='.$srl;
		}
		if (method_exists($mod, 'makeSearch'))
		{
			$where = $mod->makeSearch($where);
		}

		// set field
		$field = null;
		if (isset($_GET['field']))
		{
			$field = $config->field;
			$getField = explode(',', $_GET['field']);
			for ($i=0; $i<count($getField); $i++)
			{
				$k = array_search($getField[$i], $config->field);
				if (gettype($k) === 'boolean') unset($getField[$i]);
			}
			$field = $getField ? implode(',', $getField) : 'NONE';
		}
		else
		{
			$field = implode(',', $config->field);
		}

		// set start and size
		$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
		$page = $page > 1 ? $page : 1;
		$size = isset($_GET['size']) ? (int)$_GET['size'] : $config->size;
		$start = ($page - 1) * $size;

		// set options
		$options = [
			'table' => core\Spawn::getTableName($mod->name),
			'field' => $field,
			'where' => $where,
			'jsonField' => isset($config->jsonField) ? $config->jsonField : null,
			'sort' => isset($_GET['sort']) ? $_GET['sort'] : $config->sort,
			'order' => isset($_GET['order']) ? $_GET['order'] : $config->order,
			'limit' => [ $start, $size ],
			'debug' => false
		];

		// get item
		$result = (isset($srl)) ? core\Spawn::item($options) : core\Spawn::items($options);

		$this->output((object)[
			'data' => $result,
			'code' => (!$result || !count($result)) ? 404 : 200
		], true, true);
	}

	/**
	 * output
	 *
	 * @param object $result
	 * @param boolean $useJson
	 * @param boolean $min
	 * @return null
	 */
	private function output($result=null, $useJson=true, $min=true)
	{
		global $goose;

		if (!$result)
		{
			$result = (object)[
				'message' => 'Invalid error',
				'code' => 500
			];
		}

		if ($useJson)
		{
			header('Content-type: application/json');
			echo json_encode($result, $min ? JSON_PRETTY_PRINT : null);
		}
		else
		{
			header("Content-Type: tgext/plain");
			print_r($result);
		}

		$goose->end(false);
	}

}