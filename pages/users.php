<?
if(!defined("GOOSE")){exit();}

// index
if ($paramAction == 'index')
{
	require(PWD.'/pages/users_index.php');
}
// create, modify, delete
else
{
	require(PWD.'/pages/users_post.php');
}
?>
