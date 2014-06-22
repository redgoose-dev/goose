<?php
if(!defined("GOOSE")){exit();}

switch($paramAction)
{
	// create
	case 'create':
		// post값 확인
		$errorValue = $util->checkExistValue($_POST, array('name'));
		if ($errorValue)
		{
			$util->back("[$errorValue]값이 없습니다.");
			$util->out();
		}

		$regdate = date("YmdHis");

		$spawn->insert(array(
			table => $tablesName[nestGroups],
			data => array(
				srl => null,
				name => $_POST[name],
				regdate => $regdate
			)
		));
		$util->redirect(ROOT.'/group/index/');
		break;


	// modify
	case 'modify':
		// post값 확인
		$errorValue = $util->checkExistValue($_POST, array('name'));
		if ($errorValue)
		{
			$util->back("[$errorValue]값이 없습니다.");
			$util->out();
		}

		$spawn->update(array(
			table => $tablesName[nestGroups],
			where => 'srl='.$_POST[group_srl],
			data => array("name='$_POST[name]'")
		));
		$util->redirect(ROOT.'/group/index/');
		break;


	// delete
	case 'delete':
		$spawn->delete(array(
			table => $tablesName[nestGroups],
			where => 'srl='.$_POST[group_srl]
		));
		$spawn->update(array(
			table => $tablesName[nests],
			where => 'group_srl='.$_POST[group_srl],
			data => array("group_srl=NULL")
		));
		$util->redirect(ROOT.'/group/index/');
		break;
}
?>