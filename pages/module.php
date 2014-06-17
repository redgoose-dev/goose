<?php
if(!defined("GOOSE")){exit();}

if ($paramAction == 'index')
{
	require(PWD.'/pages/module_index.php');
}
else
{
	require(PWD.'/pages/module_post.php');
}
?>

