<?php
namespace mod\Script;
use core, mod;
if (!defined('__GOOSE__')) exit();


class Script {

	public $name, $set, $params, $isAdmin;
	public $path, $skinPath, $skinAddr, $runPath;

	public function __construct($params=[])
	{
		core\Module::initModule($this, $params);

		// set run path
		$this->runPath = 'mod/' . $this->name . '/run/';
	}

	/**
	 * index method
	 */
	public function index()
	{
		if ($this->params['action'] == 'run')
		{
			$result = $this->run($this->params['params'][0]);
			if ($result)
			{
				core\Module::afterAction($result);
			}
		}
		else
		{
			$view = new View($this);

			switch ($this->params['action'])
			{
				default:
					$view->view_index();
					break;
			}
		}
	}


	/**
	 * run
	 *
	 * @param string $name
	 * @return array
	 */
	public function run($name)
	{
		if ($this->name != 'Script') return [ 'state' => 'error', 'message' => '잘못된 객체로 접근했습니다.' ];

		// set run path
		$path = $this->runPath . $name . '/';

		// check run file
		if (!file_exists(__GOOSE_PWD__ . $path . 'run.php'))
		{
			return [
				'state' => 'error',
				'message' => '실행파일이 없습니다.'
			];
		}

		// get meta data
		$meta = core\Util::jsonToArray(core\Util::openFile(__GOOSE_PWD__ . $path . 'meta.json'), null, true);

		return (require_once(__GOOSE_PWD__ . $path . 'run.php'));
	}
}