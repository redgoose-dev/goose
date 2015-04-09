<?php
if(!defined("GOOSE")){exit();}


// list count
$listCount = ($_POST['listCount']) ? $_POST['listCount'] : 12;


switch($paramAction)
{
	// create
	case 'create':
		$regdate = date("YmdHis");

		// post값 확인
		$errorValue = $goose->util->checkExistValue($_POST, array('name', 'id'));
		if ($errorValue)
		{
			$goose->util->back("[$errorValue]값이 없습니다.");
			$goose->out();
		}

		// 중복 아이디값 확인
		$cnt = $goose->spawn->getCount(array(
			'table' => 'nests',
			'where' => "id='$_POST[id]'"
		));
		if ($cnt > 0)
		{
			$goose->util->back('id가 이미 존재합니다.');
			$goose->out();
		}

		// insert data
		$dd = $goose->spawn->insert(array(
			'table' => 'nests',
			'data' => array(
				'srl' => null,
				'group_srl' => (int)$_POST['group_srl'],
				'id' => $_POST['id'],
				'name' => $_POST['name'],
				'listCount' => (int)$listCount,
				'useCategory' => (int)$_POST['useCategory'],
				'json' => $_POST['json'],
				'regdate' => $regdate
			)
		));

		$params = ($_POST['group_srl']) ? $_POST['group_srl'].'/' : '';
		$params = ($_SESSION['group_srl'] && $_POST['group_srl'] && ($_POST['group_srl'] != $_SESSION['group_srl'])) ? $_POST['group_srl'].'/' : $params;
		$redirectUrl = GOOSE_ROOT.'/nest/index/'.$params;
		break;


	// modify
	case 'modify':
		// check post
		$errorValue = $goose->util->checkExistValue($_POST, array('name', 'srl'));
		if ($errorValue)
		{
			$goose->util->back("[$errorValue]값이 없습니다.");
			$goose->out();
		}

		// update group_srl
		$result = $goose->spawn->update(array(
			'table' => 'articles',
			'where' => "nest_srl=".(int)$_POST['srl'],
			'data' => array(
				"group_srl=".(int)$_POST['group_srl']
			)
		));

		// update data
		$result = $goose->spawn->update(array(
			'table' => 'nests',
			'where' => 'srl='.(int)$_POST['srl'],
			'data' => array(
				"group_srl=".(int)$_POST['group_srl'],
				"name='$_POST[name]'",
				"listCount='$listCount'",
				"useCategory='$_POST[useCategory]'",
				"json='$_POST[json]'"
			)
		));

		$params = ($_SESSION['group_srl']) ? $_SESSION['group_srl'].'/' : '';
		$params = ($_SESSION['group_srl'] && $_POST['group_srl'] && ($_POST['group_srl'] != $_SESSION['group_srl'])) ? $_POST['group_srl'].'/' : $params;
		$redirectUrl = GOOSE_ROOT.'/nest/index/'.$params;
		break;


	// delete
	case 'delete':
		// post값 확인
		$errorValue = $goose->util->checkExistValue($_POST, array('srl'));
		if ($errorValue)
		{
			$goose->util->back("[$errorValue]값이 없습니다.");
			$goose->out();
		}

		// get articles
		$articles = $goose->spawn->getItems(array(
			'table' => 'articles',
			'where' => 'nest_srl='.(int)$_POST['srl']
		));

		// delete data
		$goose->spawn->delete(array(
			'table' => 'categories',
			'where' => 'nest_srl='.(int)$_POST['srl']
		));
		$goose->spawn->delete(array(
			'table' => 'articles',
			'where' => 'nest_srl='.(int)$_POST['srl']
		));
		$goose->spawn->delete(array(
			'table' => 'nests',
			'where' => 'srl='.(int)$_POST['srl']
		));

		$params = ($_SESSION['group_srl']) ? $_SESSION['group_srl'].'/' : '';
		$params = ($_SESSION['group_srl'] && $_POST['group_srl'] && ($_POST['group_srl'] != $_SESSION['group_srl'])) ? $_POST['group_srl'].'/' : $params;
		$redirectUrl = GOOSE_ROOT.'/nest/index/'.$params;
		break;
}
