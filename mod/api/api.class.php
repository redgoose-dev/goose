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
		// `/goose/api/mod/nest`
		// `/goose/api/mod/article

		echo "api module";
	}

}