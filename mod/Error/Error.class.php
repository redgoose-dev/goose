<?php
namespace mod\Error;
use core, mod, stdClass;
if (!defined('__GOOSE__')) exit();


class Error {

	public $name, $set, $path, $params;
	public $skinPath, $skinAddr, $message;

	public function __construct($params=[])
	{
		core\Module::initModule($this, $params);

		// set blade class
		$this->blade = new core\Blade();
	}

	/**
	 * index
	 */
	public function index()
	{
		echo "[error] index page";
	}


	/**********************************************
	 * VIEW AREA
	 *********************************************/

	/**
	 * full render
	 * 전체로 출력되는 에러페이지
	 *
	 * @param int $code
	 * @param string $message
	 * @param string $homeUrl
	 */
	public function render($code, $message, $homeUrl=null)
	{
		// set skin path
		$this->setSkinPath('render');

		// play render page
		$this->blade->render($this->skinAddr . '.render', [
			'mod' => $this,
			'homeUrl' => ($homeUrl) ? $homeUrl : __GOOSE_ROOT__,
			'code' => $code,
			'message' => $message
		]);
	}

	/**
	 * box
	 * 페이지에서 호출되는 부분에 출력하는 에러 메시지
	 *
	 * @param int $code
	 * @param string $message
	 */
	public function box($code, $message)
	{
		// set skin path
		$this->setSkinPath('box');

		// play render page
		$this->blade->render($this->skinAddr . '.box', [
			'mod' => $this,
			'code' => $code,
			'message' => $message
		]);
	}

	/**
	 * set skin path
	 *
	 * @param string $type
	 * @param string $userSkin
	 */
	private function setSkinPath($type, $userSkin=null)
	{
		// check blade file
		$bladeResult = core\Blade::isFile(__GOOSE_PWD__ . 'mod', $type, [
			$this->name . '.skin.' . $_GET['skin'],
			$this->name . '.skin.' . $userSkin,
			$this->name . '.skin.' . $this->set['skin'],
			$this->name . '.skin.default'
		]);

		// set blade and file path
		$this->skinAddr = $bladeResult['address'];
		$this->skinPath = 'mod/' . $bladeResult['path'] . '/';
	}
}