<?php
if (!defined('__GOOSE__')) exit();

// set versions
define( '__GOOSE_MIN_PHP_VERSION__', '5.6.0' );
define( '__GOOSE_RECOMMEND_PHP_VERSION__', '7.0.0' );


// check php version
if(version_compare(PHP_VERSION, __GOOSE_MIN_PHP_VERSION__) <= 0)
{
	echo "The current php version ".PHP_VERSION.". Please upgrade to ".__GOOSE_RECOMMEND_PHP_VERSION__." or later.";
	exit;
}


// set error reporting
if(version_compare(PHP_VERSION, '5.4.0', '<'))
{
	@error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);
}
else
{
	@error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING ^ E_STRICT);
}