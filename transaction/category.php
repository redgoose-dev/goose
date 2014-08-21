<?php
if(!defined("GOOSE")){exit();}

switch($paramAction)
{
	// create
	case 'create':
		// post값 확인
		$errorValue = $goose->util->checkExistValue($_POST, array('name', 'nest_srl'));
		if ($errorValue)
		{
			$goose->util->back("[$errorValue]값이 없습니다.");
			$goose->out();
		}

		$regdate = date("YmdHis");
		$turn = $goose->spawn->getCount(array(
			'table' => 'categories',
			'where' => 'nest_srl='.$_POST['nest_srl']
		));

		$goose->spawn->insert(array(
			'table' => 'categories',
			'data' => array(
				'srl' => null,
				'nest_srl' => $_POST['nest_srl'],
				'turn' => $turn,
				'name' => $_POST['name'],
				'regdate' => $regdate
			)
		));
		$goose->util->redirect(GOOSE_ROOT.'/category/index/'.$_POST['nest_srl'].'/');
		break;


	// modify
	case 'modify':
		// post값 확인
		$errorValue = $goose->util->checkExistValue($_POST, array('name', 'nest_srl'));
		if ($errorValue)
		{
			$goose->util->back("[$errorValue]값이 없습니다.");
			$goose->out();
		}

		$goose->spawn->update(array(
			'table' => 'categories',
			'where' => 'srl='.$_POST['category_srl'],
			'data' => array("name='$_POST[name]'")
		));
		$goose->util->redirect(GOOSE_ROOT.'/category/index/'.$_POST['nest_srl'].'/');
		break;


	// delete
	case 'delete':
		$goose->spawn->delete(array(
			'table' => 'categories',
			'where' => 'srl='.$_POST['category_srl']
		));
		$goose->spawn->update(array(
			'table' => 'articles',
			'where' => 'category_srl='.(int)$_POST['category_srl'],
			'data' => array('category_srl=NULL')
		));
		$category = $goose->spawn->getItems(array(
			'field' => 'srl,turn',
			'table' => 'categories',
			'where' => 'nest_srl='.$_POST['nest_srl'],
			'order' => 'turn',
			'sort' => 'asc'
		));
		$n = 0;
		foreach ($category as $k=>$v)
		{
			$goose->spawn->update(array(
				'table' => 'categories',
				'where' => 'srl='.$v[srl],
				'data' => array('turn='.$n)
			));
			$n++;
		}
		$goose->util->redirect(GOOSE_ROOT.'/category/index/'.$_POST['nest_srl'].'/');
		break;


	// sort
	case 'sort':
		// post값 확인
		$errorValue = $goose->util->checkExistValue($_POST, array('srls'));
		if ($errorValue)
		{
			$goose->util->back("[$errorValue]값이 없습니다.");
			$goose->out();
		}

		$srls = explode(',', $_POST['srls']);
		for ($i=0; $i<count($srls); $i++)
		{
			$goose->spawn->update(array(
				'table' => 'categories',
				'where' => 'srl='.(int)$srls[$i],
				'data' => array('turn='.$i)
			));
		}
		$goose->util->redirect(GOOSE_ROOT.'/category/index/'.$_POST['nest_srl'].'/');
		break;
}
?>
