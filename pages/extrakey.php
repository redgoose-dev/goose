<?php
if(!defined("GOOSE")){exit();}

if ($paramAction == 'index')
{
	require(PWD.'/pages/extrakey_index.php');
}
else
{
	require(PWD.'/pages/extrakey_post.php');
}
?>