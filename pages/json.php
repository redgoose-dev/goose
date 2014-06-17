<?php
if(!defined("GOOSE")){exit();}

//echo "json page";
switch ($paramAction)
{
	case 'index':
		require(PWD.'/pages/json_index.php');
		break;
	case 'view':
		require(PWD.'/pages/json_view.php');
		break;
	case 'create':
		require(PWD.'/pages/json_post.php');
		break;
	case 'modify':
		require(PWD.'/pages/json_post.php');
		break;
	case 'delete':
		require(PWD.'/pages/json_post.php');
		break;
}
?>