<?php
if(!defined("GOOSE")){exit();}

// thumnail size
$thumnailSize = ($_POST[thumWidth] and $_POST[thumHeight]) ? $_POST[thumWidth].'*'.$_POST[thumHeight] : '100*100';

// list count
$listCount = ($_POST[listCount]) ? $_POST[listCount] : 12;

switch($paramAction)
{
	// create
	case 'create':
		$regdate = date("YmdHis");

		// post값 확인
		$errorValue = $util->checkExistValue($_POST, array('name', 'id'));
		if ($errorValue)
		{
			$util->back("[$errorValue]값이 없습니다.");
			$util->out();
		}

		// 중복 아이디값 확인
		$cnt = $spawn->getCount(array(
			table => $tablesName['nests'],
			where => "id='$_POST[id]'"
		));
		if ($cnt > 0)
		{
			$util->back('id가 이미 존재합니다.');
			$util->out();
		}
		
		// insert data
		$dd = $spawn->insert(array(
			table => $tablesName['nests'],
			data => array(
				srl => null,
				group_srl => (int)$_POST['group_srl'],
				id => $_POST['id'],
				name => $_POST['name'],
				thumnailSize => $thumnailSize,
				thumnailType => $_POST['thumType'],
				listCount => (int)$listCount,
				useCategory => (int)$_POST['useCategory'],
				useExtraVar => (int)$_POST['useExtraVar'],
				editor => $_POST['editor'],
				regdate => $regdate
			)
		));

		$util->redirect(ROOT.'/nest/index/');
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

		$result = $spawn->update(array(
			table => $tablesName['articles'],
			where => 'nest_srl='.(int)$_POST[nest_srl],
			data => array(
				"group_srl=".(int)$_POST[group_srl]
			)
		));

		$result = $spawn->update(array(
			table => $tablesName[nests],
			where => 'srl='.(int)$_POST[nest_srl],
			data => array(
				"group_srl=$_POST[group_srl]",
				"name='$_POST[name]'",
				"thumnailSize='$thumnailSize'",
				"thumnailType='$_POST[thumType]'",
				"listCount=$listCount",
				"useCategory=$_POST[useCategory]",
				"useExtraVar=$_POST[useExtraVar]",
				"editor='$_POST[editor]'"
			)
		));

		$util->redirect(ROOT.'/nest/index/'.$_POST[group_srl].'/');
		break;


	// delete
	case 'delete':
		// post값 확인
		$errorValue = $util->checkExistValue($_POST, array('nest_srl'));
		if ($errorValue)
		{
			$util->back("[$errorValue]값이 없습니다.");
			$util->out();
		}

		$articles = $spawn->getItems(array(
			table => $tablesName[articles],
			where => 'nest_srl='.(int)$_POST[nest_srl]
		));

		foreach ($articles as $k=>$v)
		{
			// get file index
			$files = $spawn->getItems(array(
				field => 'loc',
				table => $tablesName[files],
				where => 'article_srl='.(int)$v[srl]
			));

			// delete original files
			if (count($files))
			{
				foreach ($files as $k2=>$v2)
				{
					if (file_exists(PWD.$dataOriginalDirectory.$v2[loc]))
					{
						unlink(PWD.$dataOriginalDirectory.$v2[loc]);
					}
				}
			}

			// delete thumnail image
			if ($v[thumnail_url] and file_exists(PWD.$dataThumnailDirectory.$v[thumnail_url]))
			{
				unlink(PWD.$dataThumnailDirectory.$v[thumnail_url]);
			}

			// delete db files
			$spawn->delete(array(
				table => $tablesName[files],
				where => 'article_srl='.(int)$v[srl]
			));
			// delete db extravar
			$spawn->delete(array(
				table => $tablesName[extraVars],
				where => 'article_srl='.(int)$v[srl]
			));
		}
		$spawn->delete(array(
			table => $tablesName[categories],
			where => 'nest_srl='.(int)$_POST[nest_srl]
		));
		$spawn->delete(array(
			table => $tablesName[extraKeys],
			where => 'nest_srl='.(int)$_POST[nest_srl]
		));
		$spawn->delete(array(
			table => $tablesName[articles],
			where => 'nest_srl='.(int)$_POST[nest_srl]
		));
		$spawn->delete(array(
			table => $tablesName[nests],
			where => 'srl='.(int)$_POST[nest_srl]
		));

		$util->redirect(ROOT.'/nest/index/');
		break;
}
