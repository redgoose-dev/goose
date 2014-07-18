<?php
if(!defined("GOOSE")){exit();}

switch($paramAction)
{
	// create
	case 'create':
		// post값 확인
		$errorValue = $util->checkExistValue($_POST, array('keyName', 'name'));
		if ($errorValue)
		{
			$util->back("[$errorValue]값이 없습니다.");
			$util->out();
		}

		$turn = $spawn->getCount(array(
			'table' => $tablesName['extraKey'],
			'where' => 'nest_srl='.(int)$_POST['nest_srl']
		));
		
		$spawn->insert(array(
			'table' => $tablesName['extraKey'],
			'data' => array(
				'srl' => null,
				'nest_srl' => $_POST['nest_srl'],
				'turn' => $turn,
				'keyName' => $_POST['keyName'],
				'name' => $_POST['name'],
				'info' => $_POST['info'],
				'formType' => $_POST['formType'],
				'defaultValue' => $_POST['defaultValue'],
				'required' => $_POST['required']
			)
		));

		$util->redirect(ROOT.'/extrakey/index/'.$_POST['nest_srl'].'/');
		break;


	// modify
	case 'modify':
		// post값 확인
		$errorValue = $util->checkExistValue($_POST, array('extra_srl', 'keyName', 'name'));
		if ($errorValue)
		{
			$util->back("[$errorValue]값이 없습니다.");
			$util->out();
		}

		$spawn->update(array(
			'table' => $tablesName['extraKey'],
			'where' => 'srl='.(int)$_POST['extra_srl'],
			'data' => array(
				"keyName='$_POST[keyName]'",
				"name='$_POST[name]'",
				"info='$_POST[info]'",
				"formType='$_POST[formType]'",
				"defaultValue='$_POST[defaultValue]'",
				"required='$_POST[required]'"
			)
		));

		$util->redirect(ROOT.'/extrakey/index/'.$_POST['nest_srl'].'/');
		break;


	// delete
	case 'delete':
		// post값 확인
		$errorValue = $util->checkExistValue($_POST, array('extra_srl'));
		if ($errorValue)
		{
			$util->back("[$errorValue]값이 없습니다.");
			$util->out();
		}

		$spawn->delete(array(
			'table' => $tablesName['extraKey'],
			'where' => 'srl='.(int)$_POST['extra_srl']
		));

		$spawn->delete(array(
			'table' => $tablesName['extraVar'],
			'where' => 'key_srl='.(int)$_POST['extra_srl']
		));

		// reset turn
		$n = 0;
		$extraKey = $spawn->getItems(array(
			'field' => 'srl,turn',
			'table' => $tablesName['extraKey'],
			'where' => 'nest_srl='.(int)$_POST['nest_srl'],
			'order' => 'turn',
			'sort' => 'asc'
		));
		foreach ($extraKey as $k=>$v)
		{
			$spawn->update(array(
				'table' => $tablesName['extraKey'],
				'where' => 'srl='.(int)$v['srl'],
				'data' => array('turn='.$n)
			));
			$n++;
		}

		$util->redirect(ROOT.'/extrakey/index/'.$_POST['nest_srl'].'/');
		break;


	// sort
	case 'sort':
		if ($_POST['srls'])
		{
			$srls = explode(",", $_POST['srls']);
			for ($i=0; $i<count($srls); $i++)
			{
				$spawn->update(array(
					'table' => $tablesName['extraKey'],
					'where' => 'srl='.(int)$srls[$i],
					'data' => array('turn='.$i)
				));
			}

			$util->redirect(ROOT.'/extrakey/index/'.$_POST['nest_srl'].'/');
		}
		break;
}
?>
