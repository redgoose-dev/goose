<?php
if(!defined("GOOSE")){exit();}

// thumnail size
$thumnailSize = ($_POST['thumWidth'] and $_POST['thumHeight']) ? $_POST['thumWidth'].'*'.$_POST['thumHeight'] : '100*100';

// list count
$listCount = ($_POST['listCount']) ? $_POST['listCount'] : 12;

switch($paramAction)
{
	// create
	case 'create':
		$regdate = date("YmdHis");

		// post값 확인
		$errorValue = $goose->util->checkExistValue($_POST, array('name', 'id'));
		if ($errorValue)
		{
			$goose->util->back("[$errorValue]값이 없습니다.");
			$goose->out();
		}

		// 중복 아이디값 확인
		$cnt = $goose->spawn->getCount(array(
			'table' => 'nests',
			'where' => "id='$_POST[id]'"
		));
		if ($cnt > 0)
		{
			$goose->util->back('id가 이미 존재합니다.');
			$goose->out();
		}
		
		// insert data
		$dd = $goose->spawn->insert(array(
			'table' => 'nests',
			'data' => array(
				'srl' => null,
				'group_srl' => (int)$_POST['group_srl'],
				'id' => $_POST['id'],
				'name' => $_POST['name'],
				'thumnailSize' => $thumnailSize,
				'thumnailType' => $_POST['thumType'],
				'listCount' => (int)$listCount,
				'useCategory' => (int)$_POST['useCategory'],
				'json' => $_POST['json'],
				'regdate' => $regdate
			)
		));

		$params = ($_POST['group_srl']) ? $_POST['group_srl'].'/' : '';
		$params = ($_SESSION['group_srl'] && $_POST['group_srl'] && ($_POST['group_srl'] != $_SESSION['group_srl'])) ? $_POST['group_srl'].'/' : $params;
		$redirectUrl = GOOSE_ROOT.'/nest/index/'.$params;
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

		$result = $goose->spawn->update(array(
			'table' => 'articles',
			'where' => "nest_srl='$_POST[nest_srl]'",
			'data' => array(
				"group_srl='$_POST[group_srl]'"
			)
		));

		$result = $goose->spawn->update(array(
			'table' => 'nests',
			'where' => 'srl='.(int)$_POST['nest_srl'],
			'data' => array(
				"group_srl='$_POST[group_srl]'",
				"name='$_POST[name]'",
				"thumnailSize='$thumnailSize'",
				"thumnailType='$_POST[thumType]'",
				"listCount='$listCount'",
				"useCategory='$_POST[useCategory]'",
				"json='$_POST[json]'"
			)
		));

		$params = ($_SESSION['group_srl']) ? $_SESSION['group_srl'].'/' : '';
		$params = ($_SESSION['group_srl'] && $_POST['group_srl'] && ($_POST['group_srl'] != $_SESSION['group_srl'])) ? $_POST['group_srl'].'/' : $params;
		$redirectUrl = GOOSE_ROOT.'/nest/index/'.$params;
		break;


	// delete
	case 'delete':
		// post값 확인
		$errorValue = $goose->util->checkExistValue($_POST, array('nest_srl'));
		if ($errorValue)
		{
			$goose->util->back("[$errorValue]값이 없습니다.");
			$goose->out();
		}

		$articles = $goose->spawn->getItems(array(
			'table' => 'articles',
			'where' => 'nest_srl='.(int)$_POST['nest_srl']
		));

		foreach ($articles as $k=>$v)
		{
			// get file index
			$files = $goose->spawn->getItems(array(
				'field' => 'loc',
				'table' => 'files',
				'where' => 'article_srl='.(int)$v['srl']
			));

			// delete original files
			if (count($files))
			{
				foreach ($files as $k2=>$v2)
				{
					if (file_exists(PWD.$dataOriginalDirectory.$v2['loc']))
					{
						unlink(PWD.$dataOriginalDirectory.$v2['loc']);
					}
				}
			}

			// delete thumnail image
			if ($v['thumnail_url'] and file_exists(PWD.$dataThumnailDirectory.$v['thumnail_url']))
			{
				unlink(PWD.$dataThumnailDirectory.$v['thumnail_url']);
			}

			// delete db files
			$goose->spawn->delete(array(
				'table' => 'files',
				'where' => 'article_srl='.(int)$v['srl']
			));
		}
		$goose->spawn->delete(array(
			'table' => 'categories',
			'where' => 'nest_srl='.(int)$_POST['nest_srl']
		));
		$goose->spawn->delete(array(
			'table' => 'articles',
			'where' => 'nest_srl='.(int)$_POST['nest_srl']
		));
		$goose->spawn->delete(array(
			'table' => 'nests',
			'where' => 'srl='.(int)$_POST['nest_srl']
		));

		$params = ($_SESSION['group_srl']) ? $_SESSION['group_srl'].'/' : '';
		$params = ($_SESSION['group_srl'] && $_POST['group_srl'] && ($_POST['group_srl'] != $_SESSION['group_srl'])) ? $_POST['group_srl'].'/' : $params;
		$redirectUrl = GOOSE_ROOT.'/nest/index/'.$params;
		break;
}


// redirect url
if ($redirectUrl)
{
	$goose->util->redirect($redirectUrl);
}