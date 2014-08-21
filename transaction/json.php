<?php
if(!defined("GOOSE")){exit();}

$regdate = date("YmdHis");

switch($paramAction)
{
	// create
	case 'create':
		// post값 확인
		$errorValue = $goose->util->checkExistValue($_POST, array('name', 'json'));
		if ($errorValue)
		{
			$goose->util->back("[$errorValue]값이 없습니다.");
			$goose->out();
		}

		// insert data
		$goose->spawn->insert(array(
			'table' => 'jsons',
			'data' => array(
				'srl' => null,
				'name' => $_POST['name'],
				'json' => $_POST['json'],
				'regdate' => $regdate
			)
		));

		// go to index
		$goose->util->redirect(GOOSE_ROOT.'/json/index/');
		break;

	// modify
	case 'modify':
		// post값 확인
		$errorValue = $goose->util->checkExistValue($_POST, array('srl', 'name', 'json'));
		if ($errorValue)
		{
			$goose->util->back("[$errorValue]값이 없습니다.");
			$goose->out();
		}

		$goose->spawn->update(array(
			'table' => 'jsons',
			'where' => 'srl='.(int)$_POST['srl'],
			'data' => array(
				"name='$_POST[name]'",
				"json='$_POST[json]'",
				"regdate=$regdate",
			)
			,'debug' => false
		));
		$goose->util->redirect(GOOSE_ROOT.'/json/view/'.$_POST['srl'].'/');
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

		$goose->spawn->delete(array(
			'table' => 'jsons',
			'where' => 'srl='.(int)$_POST['srl']
		));
		$goose->util->redirect(GOOSE_ROOT.'/json/index/');
		break;
}
?>
