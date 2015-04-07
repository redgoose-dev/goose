<?php
if(!defined("GOOSE")){exit();}

// check user level
if (!$goose->isAdmin)
{
	$goose->util->back("적용할 권한이 없습니다.");
	$goose->out();
}


// action
switch($paramAction)
{
	// create
	case 'create':
		// post값 확인
		$errorValue = $goose->util->checkExistValue($_POST, array('name'));
		if ($errorValue)
		{
			$goose->util->back("[$errorValue]값이 없습니다.");
			$goose->out();
		}

		$regdate = date("YmdHis");

		$goose->spawn->insert(array(
			'table' => 'nestGroups',
			'data' => array(
				'srl' => null,
				'name' => $_POST['name'],
				'regdate' => $regdate
			)
		));
		$goose->util->redirect(GOOSE_ROOT.'/group/index/');
		break;


	// modify
	case 'modify':
		// post값 확인
		$errorValue = $goose->util->checkExistValue($_POST, array('name'));
		if ($errorValue)
		{
			$goose->util->back("[$errorValue]값이 없습니다.");
			$goose->out();
		}

		$goose->spawn->update(array(
			'table' => 'nestGroups',
			'where' => 'srl='.$_POST['group_srl'],
			'data' => array("name='$_POST[name]'")
		));
		$goose->util->redirect(GOOSE_ROOT.'/group/index/');
		break;


	// delete
	case 'delete':
		$goose->spawn->delete(array(
			'table' => 'nestGroups',
			'where' => 'srl='.$_POST['group_srl']
		));
		$goose->spawn->update(array(
			'table' => 'nests',
			'where' => 'group_srl='.$_POST['group_srl'],
			'data' => array("group_srl=NULL")
		));
		$goose->util->redirect(GOOSE_ROOT.'/group/index/');
		break;
}
?>