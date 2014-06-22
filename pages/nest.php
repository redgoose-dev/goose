<?php
if(!defined("GOOSE")){exit();}

if ($paramAction == 'index')
{
	require(PWD.'/pages/nest_index.php');
}
else
{
	require(PWD.'/pages/nest_post.php');
}
?>

