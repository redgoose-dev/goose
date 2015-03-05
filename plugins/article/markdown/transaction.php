<?php
if(!defined("GOOSE")){exit();}

if ($paramAction != 'delete')
{
	// post값 확인
	$errorValue = $goose->util->checkExistValue($_POST, array('title', 'content'));
	if ($errorValue)
	{
		$goose->util->back("[$errorValue]값이 없습니다.");
		$goose->out();
	}
}


// action
switch($paramAction)
{
	// create
	case 'create':

		$result = $goose->spawn->insert(array(
			'table' => 'articles',
			'data' => array(
				'srl' => null,
				'group_srl' => $_POST['group_srl'],
				'nest_srl' => $_POST['nest_srl'],
				'category_srl' => $_POST['category_srl'],
				'title' => addslashes($_POST['title']),
				'content' => addslashes($_POST['content']),
				'regdate' => $regdate,
				'modate' => $regdate,
				'json' => $_POST['json'],
				'hit' => 0,
				'ipAddress' => $ipAddress
			)
		));

		$addUrl = ($_POST['category_srl']) ? $_POST['category_srl'].'/' : '';
		$redirectUrl = GOOSE_ROOT.'/article/index/'.$_POST['nest_srl'].'/'.$addUrl;
		break;

	// modify
	case 'modify':

		// update article
		$result = $goose->spawn->update(array(
			'table' => 'articles'
			,'where' => 'srl='.(int)$_POST['article_srl']
			,'data' => array(
				"category_srl='$_POST[category_srl]'"
				,"title='".addslashes($_POST['title'])."'"
				,"content='".addslashes($_POST['content'])."'"
				,"modate='$regdate'"
				,"json='$_POST[json]'"
				,"ipAddress='$ipAddress'"
			)
		));

		$redirectUrl = $_POST['url'];
		break;

	// delete
	case 'delete':

		// get article item data
		$article = $goose->spawn->getItem(array(
			'table' => 'articles',
			'where' => 'srl='.(int)$_POST['article_srl']
		));

		// delete article
		$goose->spawn->delete(array(
			'table' => 'articles',
			'where' => 'srl='.(int)$_POST['article_srl']
		));

		$addUrl = ($_POST['nest_srl']) ? $_POST['nest_srl'].'/' : '';
		$addUrl .= ($_POST['category_srl']) ? $_POST['category_srl'].'/' : '';
		$params = ($_POST['page']) ? "page=$_POST[page]&" : "";
		$redirectUrl = GOOSE_ROOT.'/article/index/'.$addUrl.(($params) ? '?'.$params : '');
		break;
}


// attach files
require_once('transaction_files.php');
?>