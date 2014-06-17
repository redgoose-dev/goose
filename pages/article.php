<?php
if(!defined("GOOSE")){exit();}

switch ($paramAction)
{
	// index
	case 'index':
		require(PWD.'/pages/article_index.php');
		break;
	// view
	case 'view':
		require(PWD.'/pages/article_view.php');
		break;
	// delete
	case 'delete':
		require(PWD.'/pages/article_delete.php');
		break;
}

// create or modify
if ($paramAction == 'create' or $paramAction == 'modify')
{
	if ($paramAction=='create')
	{
		$module_srl = $routePapameters['param0'];
		$category_srl = $routePapameters['param1'];
	}
	else
	{
		$article_srl = $routePapameters['param0'];
	}
	require(PWD.'/pages/article_post.php');
}

?>