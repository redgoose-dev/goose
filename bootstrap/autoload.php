<?php
if (!defined('__GOOSE__')) exit();


/**
 * set auto loader
 *
 * @param string $className
 */
function rgAutoload($className)
{
	// set filename
	$fileName = str_replace('\\', DIRECTORY_SEPARATOR, $className);

	if (__GOOSE_DEBUG__ && false)
	{
		echo "<script>";
		echo "console.log('" . __GOOSE_PWD__ . $fileName . "')";
		echo "</script>";
	}

	// check class files
	if (file_exists(__GOOSE_PWD__ . $fileName . '.user.class.php'))
	{
		require __GOOSE_PWD__ . $fileName . '.user.class.php';
	}
	else if (file_exists(__GOOSE_PWD__ . $fileName . '.class.php'))
	{
		require __GOOSE_PWD__ . $fileName . '.class.php';
	}
	else if (file_exists(__GOOSE_PWD__ . $fileName . '.php'))
	{
		require __GOOSE_PWD__ . $fileName . '.php';
	}
}

spl_autoload_register('rgAutoload');
