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
		$nest_srl = $routePapameters['param0'];
		$category_srl = (isset($routePapameters['param1'])) ? $routePapameters['param1'] : null;
	}
	else
	{
		$article_srl = $routePapameters['param0'];
	}
	require(PWD.'/pages/article_post.php');
}

?>