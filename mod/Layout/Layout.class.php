<?php
namespace mod\Layout;
use core;
if (!defined('__GOOSE__')) exit();


class Layout {

	public $set, $path, $params, $skinPath;

	public function __construct($params=null)
	{
		core\Module::initModule($this, $params);

		// set navigation
		$this->navigation = self::getNavigation();
	}

	/**
	 * get navigation
	 *
	 * @return array
	 */
	private function getNavigation()
	{
		$result = [];

		$navigationTree = core\Spawn::item([
			'table' => core\Spawn::getTableName('JSON'),
			'where' => 'srl=1'
		]);
		$navigationTree = core\Util::jsonToArray($navigationTree['json'], null, true);

		foreach($navigationTree as $k=>$v)
		{
			if ($v['permission'] <= $_SESSION['goose_level'])
			{
				$result[] = [
					'url' => str_replace('{GOOSE_ROOT}', __GOOSE_ROOT__, $v['url']),
					'name' => $v['name'],
					'target' => $v['target']
				];
			}
		}

		if ($_SESSION['goose_email'])
		{
			$result[] = [
				'url' => __GOOSE_ROOT__ . '/auth/logout/',
				'name' => 'Logout',
				'target' => null
			];
		}
		else
		{
			$result[] = [
				'url' => __GOOSE_ROOT__ . '/auth/login/',
				'name' => 'Login',
				'target' => null
			];
		}

		return $result;
	}

	/**
	 * get url layout
	 * 레이아웃 페이지의 주소를 반환한다.
	 *
	 * return string
	 */
	public function getUrl()
	{
		return __GOOSE_PWD__.$this->skinPath.'view_layout.html';
	}

}