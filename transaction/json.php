<?php
if(!defined("GOOSE")){exit();}

$regdate = date("YmdHis");

switch($paramAction)
{
	// create
	case 'create':
		// post값 확인
		$errorValue = $util->checkExistValue($_POST, array('name', 'json'));
		if ($errorValue)
		{
			$util->back("[$errorValue]값이 없습니다.");
			$util->out();
		}

		// insert data
		$spawn->insert(array(
			'table' => $tablesName['jsons'],
			'data' => array(
				'srl' => null,
				'name' => $_POST['name'],
				'json' => $_POST['json'],
				'regdate' => $regdate
			)
		));

		// go to index
		$util->redirect(GOOSE_ROOT.'/json/index/');
		break;

	// modify
	case 'modify':
		// post값 확인
		$errorValue = $util->checkExistValue($_POST, array('srl', 'name', 'json'));
		if ($errorValue)
		{
			$util->back("[$errorValue]값이 없습니다.");
			$util->out();
		}

		$spawn->update(array(
			'table' => $tablesName['jsons'],
			'where' => 'srl='.(int)$_POST['srl'],
			'data' => array(
				"name='$_POST[name]'",
				"json='$_POST[json]'",
				"regdate=$regdate",
			)
			,'debug' => false
		));
		$util->redirect(GOOSE_ROOT.'/json/view/'.$_POST['srl'].'/');
		break;

	// delete
	case 'delete':
		// post값 확인
		$errorValue = $util->checkExistValue($_POST, array('srl'));
		if ($errorValue)
		{
			$util->back("[$errorValue]값이 없습니다.");
			$util->out();
		}

		$spawn->delete(array(
			'table' => $tablesName['jsons'],
			'where' => 'srl='.(int)$_POST['srl']
		));
		$util->redirect(GOOSE_ROOT.'/json/index/');
		break;
}
?>
