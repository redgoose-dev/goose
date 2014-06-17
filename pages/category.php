<?php
if(!defined("GOOSE")){exit();}

// index
if ($paramAction == 'index')
{
	require(PWD.'/pages/category_index.php');
}
// create, modify, delete
else
{
	require(PWD.'/pages/category_post.php');
}
?>