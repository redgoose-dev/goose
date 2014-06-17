<?php
if(!defined("GOOSE")){exit();}

if ($paramAction == 'create' or $paramAction == 'modify')
{
	if (!$_POST[keyName])
	{
		$util->back('[확장변수이름]항목이 비었습니다.');
		exit;
	}
	if (!$_POST[name])
	{
		$util->back('[입력항목이름]항목이 비었습니다.');
		exit;
	}
}

if ($paramAction == 'modify' or $type == 'delete')
{
	if (!$_POST[extra_srl])
	{
		$util->back('extrakey값이 없습니다.');
		exit;
	}
}

switch($paramAction)
{
	// create
	case 'create':
		$turn = $spawn->getCount(array(
			'table' => $tablesName[extraKey],
			'where' => 'module_srl='.(int)$_POST[module_srl]
		));
		
		$spawn->insert(array(
			'table' => $tablesName[extraKey],
			'data' => array(
				'srl' => null,
				'module_srl' => $_POST[module_srl],
				'turn' => $turn,
				'keyName' => $_POST[keyName],
				'name' => $_POST[name],
				'info' => $_POST[info],
				'formType' => $_POST[formType],
				'defaultValue' => $_POST[defaultValue]
			)
		));

		$util->redirect(ROOT.'/extrakey/index/'.$_POST[module_srl].'/', '등록완료');
		break;


	// modify
	case 'modify':
		$spawn->update(array(
			'table' => $tablesName[extraKey],
			'where' => 'srl='.(int)$_POST[extra_srl],
			'data' => array(
				"keyName='$_POST[keyName]'",
				"name='$_POST[name]'",
				"info='$_POST[info]'",
				"formType='$_POST[formType]'",
				"defaultValue='$_POST[defaultValue]'"
			)
		));

		$util->redirect(ROOT.'/extrakey/index/'.$_POST[module_srl].'/', '수정완료');
		break;


	// delete
	case 'delete':
		$spawn->delete(array(
			'table' => $tablesName[extraKey],
			'where' => 'srl='.(int)$_POST[extra_srl]
		));
		$spawn->delete(array(
			'table' => $tablesName[extraVar],
			'where' => 'key_srl='.(int)$_POST[extra_srl]
		));

		// reset turn
		$n = 0;
		$extraKey = $spawn->getItems(array(
			'field' => 'srl,turn',
			'table' => $tablesName[extraKey],
			'where' => 'module_srl='.(int)$_POST[module_srl],
			'order' => 'turn',
			'sort' => 'asc'
		));
		foreach ($extraKey as $k=>$v)
		{
			$spawn->update(array(
				'table' => $tablesName[extraKey],
				'where' => 'srl='.(int)$v[srl],
				'data' => array('turn='.$n)
			));
			$n++;
		}

		$util->redirect(ROOT.'/extrakey/index/'.$_POST[module_srl].'/', '삭제완료');
		break;


	// sort
	case 'sort':
		if ($_POST[srls])
		{
			$srls = explode(",", $_POST[srls]);
			for ($i=0; $i<count($srls); $i++)
			{
				$spawn->update(array(
					'table' => $tablesName[extraKey],
					'where' => 'srl='.(int)$srls[$i],
					'data' => array('turn='.$i)
				));
			}

			$util->redirect(ROOT.'/extrakey/index/'.$_POST[module_srl].'/');
		}
		break;
}
?>
