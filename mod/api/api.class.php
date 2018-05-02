<?php
namespace mod\api;
use core, mod;
if (!defined('__GOOSE__')) exit();


class api {

	public $name, $set, $params, $isAdmin;
	public $path, $skinPath, $skinAddr;

	public function __construct($params=[])
	{
		core\Module::initModule($this, $params);
	}


	public function index()
	{
		// method: GET, POST, PUT, PATCH

		// * GET
		// `/goose/API/`: 문서
		// `/goose/API/nest/`: nest index
		// `/goose/API/nest/1/`: nest srl:1 article
		// `/goose/API/nest/?`: param

		if ($this->params['action'])
		{
			$asd = 'qwe';
		}
		else
		{
			// 문서나 설정
			$aa = '123';
		}

		core\Util::console($this->params['action']);

		echo "api module";
	}

}