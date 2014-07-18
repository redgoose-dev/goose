<?php
if(!defined("GOOSE")){exit();}

switch($paramAction)
{
	// create
	case 'create':
		// post값 확인
		$errorValue = $util->checkExistValue($_POST, array('name', 'nest_srl'));
		if ($errorValue)
		{
			$util->back("[$errorValue]값이 없습니다.");
			$util->out();
		}

		$regdate = date("YmdHis");
		$turn = $spawn->getCount(array(
			'table' => $tablesName['categories'],
			'where' => 'nest_srl='.$_POST['nest_srl']
		));

		$spawn->insert(array(
			'table' => $tablesName['categories'],
			'data' => array(
				'srl' => null,
				'nest_srl' => $_POST['nest_srl'],
				'turn' => $turn,
				'name' => $_POST['name'],
				'regdate' => $regdate
			)
		));
		$util->redirect(ROOT.'/category/index/'.$_POST['nest_srl'].'/');
		break;


	// modify
	case 'modify':
		// post값 확인
		$errorValue = $util->checkExistValue($_POST, array('name', 'nest_srl'));
		if ($errorValue)
		{
			$util->back("[$errorValue]값이 없습니다.");
			$util->out();
		}

		$spawn->update(array(
			'table' => $tablesName['categories'],
			'where' => 'srl='.$_POST['category_srl'],
			'data' => array("name='$_POST[name]'")
		));
		$util->redirect(ROOT.'/category/index/'.$_POST['nest_srl'].'/');
		break;


	// delete
	case 'delete':
		$spawn->delete(array(
			'table' => $tablesName['categories'],
			'where' => 'srl='.$_POST['category_srl']
		));
		$spawn->update(array(
			'table' => $tablesName['articles'],
			'where' => 'category_srl='.(int)$_POST['category_srl'],
			'data' => array('category_srl=NULL')
		));
		$category = $spawn->getItems(array(
			'field' => 'srl,turn',
			'table' => $tablesName['categories'],
			'where' => 'nest_srl='.$_POST['nest_srl'],
			'order' => 'turn',
			'sort' => 'asc'
		));
		$n = 0;
		foreach ($category as $k=>$v)
		{
			$spawn->update(array(
				'table' => $tablesName['categories'],
				'where' => 'srl='.$v[srl],
				'data' => array('turn='.$n)
			));
			$n++;
		}
		$util->redirect(ROOT.'/category/index/'.$_POST['nest_srl'].'/');
		break;


	// sort
	case 'sort':
		// post값 확인
		$errorValue = $util->checkExistValue($_POST, array('srls'));
		if ($errorValue)
		{
			$util->back("[$errorValue]값이 없습니다.");
			$util->out();
		}

		$srls = explode(',', $_POST['srls']);
		for ($i=0; $i<count($srls); $i++)
		{
			$spawn->update(array(
				'table' => $tablesName['categories'],
				'where' => 'srl='.(int)$srls[$i],
				'data' => array('turn='.$i)
			));
		}
		$util->redirect(ROOT.'/category/index/'.$_POST['nest_srl'].'/');
		break;
}
?>
