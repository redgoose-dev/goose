<?php
namespace core;
use eftec\bladeone, mod;


class Blade {

	/**
	 * construct
	 *
	 * @param string $path_view
	 * @param string $path_cache
	 */
	function __construct($path_view=BLADE_VIEW, $path_cache=BLADE_CACHE)
	{
		// check blade class
		self::checkBladeOne();

		// make blade instance
		$this->blade = new bladeone\BladeOne($path_view, $path_cache);
	}

	/**
	 * check is blade file
	 *
	 * @param string $prefix
	 * @param string $filename
	 * @param array $paths
	 * @return array
	 */
	public static function isFile($prefix, $filename, $paths)
	{
		foreach ($paths as $k=>$v)
		{
			if (!$v) continue;

			$addrsPath = str_replace('.', '/', $v);
			$fullPath = $prefix . '/' . $addrsPath . '/' . $filename . '.blade.php';

			if (file_exists($fullPath))
			{
				return [ 'path' => $addrsPath, 'address' => $v ];
			}
		}
		return [ 'path' => null, 'address' => null ];
	}

	/**
	 * check BladeOne class
	 */
	private static function checkBladeOne()
	{
		if (!class_exists('eftec\bladeone\BladeOne'))
		{
			include __GOOSE_PWD__ . 'vendor/BladeOne/BladeOne.php';
		}
		if (!class_exists('eftec\bladeone\BladeOne'))
		{
			Goose::error(101, 'can not load blade class');
		}
	}

	/**
	 * render
	 *
	 * @param string $path
	 * @param array $data
	 */
	public function render($path, $data)
	{
		// set layout
		$layout = new mod\Layout\Layout();
		$data['layout'] = $layout;
		$data['root'] = __GOOSE_ROOT__;
		$data['pwd'] = __GOOSE_PWD__;
		$data['url'] = __GOOSE_URL__;

		// render page
		echo $this->blade->run($path, $data);
	}

}