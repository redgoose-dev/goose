<?php
if(!defined("GOOSE")){exit();}

/* value check */
if (!$_POST[srl] and $paramAction != 'create')
{
	$util->back('srl값이 없습니다.');
	$util->out();
}
if ($paramAction != 'delete')
{
	if (!$_POST[name])
	{
		$util->back('name값이 없습니다.');
		$util->out();
	}
	
	if (!$_POST[json])
	{
		$util->back('JSON 데이터가 없습니다.');
		$util->out();
	}
}

$regdate = date("YmdHis");

switch($paramAction)
{
	// create
	case 'create':
		// insert data
		$spawn->insert(array(
			table => $tablesName[jsons],
			data => array(
				srl => null,
				name => $_POST[name],
				json => $_POST[json],
				regdate => $regdate
			)
		));

		// go to index
		$util->redirect(ROOT.'/json/index/');
		break;

	// modify
	case 'modify':
		$spawn->update(array(
			table => $tablesName[jsons],
			where => 'srl='.(int)$_POST[srl],
			data => array(
				"name='$_POST[name]'",
				"json='$_POST[json]'",
				"regdate=$regdate",
			), debug => false
		));
		$util->redirect(ROOT.'/json/view/'.$_POST[srl].'/');
		break;

	// delete
	case 'delete':
		$spawn->delete(array(
			table => $tablesName[jsons],
			where => 'srl='.(int)$_POST[srl]
		));
		$util->redirect(ROOT.'/json/index/');
		break;
}
?>
