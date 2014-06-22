<?
if(!defined("GOOSE")){exit();}

// index
if ($paramAction == 'index')
{
	require(PWD.'/pages/user_index.php');
}
// create, modify, delete
else
{
	require(PWD.'/pages/user_post.php');
}
?>
