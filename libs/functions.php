<?php
if(!defined("GOOSE")){exit();}

function convertParameter()
{
	function getParameter($get)
	{
		if ($_POST[$get])
		{
			return $_POST[$get];
		}
		else if ($_GET[$get])
		{
			return $_GET[$get];
		}
		else
		{
			return null;
		}
	}
	$output = array(
		'group' => getParameter('group'),
		'module' => getParameter('module'),
		'category' => getParameter('category'),
		'article' => getParameter('article'),
		'page' => getParameter('page')
	);
	return $output;
}

// get action type
function getActionType($act)
{
	$result = ($act == 'create') ? '만들기' : '';
	$result = ($act == 'modify') ? '수정' : $result;
	$result = ($act == 'delete') ? '삭제' : $result;
	return $result;
}
?>
