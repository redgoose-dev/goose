<?php
if(!defined("GOOSE")){exit();}

switch($paramAction)
{
	// create
	case 'create':
		if (!$_POST[name])
		{
			$util->back('[제목]항목이 비었습니다.');
			exit;
		}
		$regdate = date("YmdHis");
		$spawn->insert(array(
			table => $tablesName[moduleGroups],
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
		if (!$_POST[name])
		{
			$util->back('[제목]항목이 비었습니다.');
			exit;
		}
		$spawn->update(array(
			table => $tablesName[moduleGroups],
			where => 'srl='.$_POST[group_srl],
			data => array("name='$_POST[name]'")
		));
		$util->redirect(ROOT.'/group/index/');
		break;


	// delete
	case 'delete':
		$spawn->delete(array(
			table => $tablesName[moduleGroups],
			where => 'srl='.$_POST[group_srl]
		));
		$spawn->update(array(
			table => $tablesName[modules],
			where => 'group_srl='.$_POST[group_srl],
			data => array("group_srl=NULL")
		));
		$util->redirect(ROOT.'/group/index/');
		break;
}
?>