<?php
if(!defined("GOOSE")){exit();}

$nest_srl = $category_srl = $article_srl = $group_srl = null;
$goose->util->checkArray($_GET, array('page', 'm'));

switch ($paramAction)
{
	case 'index':
		$nest_srl = (int)$routePapameters['param0'];
		$category_srl = (int)$routePapameters['param1'];
		break;
	case 'view':
		if ($routePapameters['param1'])
		{
			$category_srl = (int)$routePapameters['param0'];
			$article_srl = (int)$routePapameters['param1'];
		}
		else
		{
			$article_srl = (int)$routePapameters['param0'];
		}
		if (!$article_srl)
		{
			$goose->util->back('article_srl값이 없습니다.');
			$goose->out();
		}
		break;
	case 'create':
		$nest_srl = (int)$routePapameters['param0'];
		$category_srl = (int)$routePapameters['param1'];
		if (!$nest_srl)
		{
			$goose->util->back('nest값이 없습니다.');
			$goose->out();
		}
		break;
	case 'modify':
		if ($routePapameters['param1'])
		{
			$category_srl = (int)$routePapameters['param0'];
			$article_srl = (int)$routePapameters['param1'];
		}
		else
		{
			$article_srl = (int)$routePapameters['param0'];
		}
		if (!$article_srl)
		{
			$goose->util->back('article_srl값이 없습니다.');
			$goose->out();
		}
		break;
	case 'delete':
		if ($routePapameters['param1'])
		{
			$category_srl = (int)$routePapameters['param0'];
			$article_srl = (int)$routePapameters['param1'];
		}
		else
		{
			$article_srl = (int)$routePapameters['param0'];
		}
		if (!$article_srl)
		{
			$goose->util->back('article_srl값이 없습니다.');
			$goose->out();
		}
		break;
}

// get article data
if ($article_srl)
{
	$article = $goose->spawn->getItem(array(
		'table' => 'articles',
		'where' => 'srl='.$article_srl
	));
	try {
		$article['json'] = json_decode(urldecode($article['json']), true);
	} catch(Exception $e) {}

	$category = $goose->spawn->getItem(array(
		'table' => 'categories',
		'where' => 'srl='.$article['category_srl']
	));
}

// get nest data
if ($nest_srl || $article['nest_srl'])
{
	$nest = $goose->spawn->getItem(array(
		'table' => 'nests',
		'where' => 'srl='.(($nest_srl) ? $nest_srl : $article['nest_srl'])
	));
	try {
		$nest['json'] = json_decode(urldecode($nest['json']), true);
	} catch(Exception $e) {}
}

// load skin file
$skin = (isset($nest['json']['articleSkin'])) ? $nest['json']['articleSkin'] : 'basic';
$actionFile = ($paramAction == 'create' || $paramAction == 'modify') ? 'post' : $paramAction;
if (is_file(PWD.'/plugins/article/'.$skin.'/'.$actionFile.'.php'))
{
	$path_skin = '/plugins/article/'.$skin;
	require_once(PWD.$path_skin.'/'.$actionFile.'.php');
}
else if (is_file(PWD.'/plugins/article/basic/'.$actionFile.'.php'))
{
	$path_skin = '/plugins/article/basic';
	require_once(PWD.$path_skin.'/'.$actionFile.'.php');
}
else
{
	$goose->util->alert('불러올 페이지가 없습니다.');
	$goose->out();
}
?>