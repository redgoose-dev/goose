<?php
namespace core;


class Blade {

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

}