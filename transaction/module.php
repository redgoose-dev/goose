<?php
if(!defined("GOOSE")){exit();}

if (!$_POST[name] and $paramAction!='delete')
{
	$util->back('[모듈이름]항목이 비었습니다.');
	exit;
}

if (!$_POST[module_srl] and $paramAction!='create')
{
	$util->back('module_srl값이 없습니다.');
	exit;
}

// thumnail size
$thumnailSize = ($_POST[thumWidth] and $_POST[thumHeight]) ? $_POST[thumWidth].'*'.$_POST[thumHeight] : '100*100';

// list count
$listCount = ($_POST[listCount]) ? $_POST[listCount] : 12;

switch($paramAction)
{
	// create
	case 'create':
		$regdate = date("YmdHis");
		
		// module id check
		if (!$_POST[id])
		{
			$util->back('id값이 없습니다.');
			exit;
		}
		$cnt = $spawn->getCount(array(
			table => $tablesName[modules],
			where => "id='$_POST[id]'"
		));
		if ($cnt > 0)
		{
			$util->back('id가 이미 존재합니다.');
			exit;
		}
		
		// insert data
		$dd = $spawn->insert(array(
			table => $tablesName['modules'],
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

		$util->redirect(ROOT.'/module/index/', '등록완료');
		break;


	// modify
	case 'modify':
		$spawn->update(array(
			table => $tablesName[articles],
			where => 'module_srl='.(int)$_POST[module_srl],
			data => array(
				"group_srl=$_POST[module_srl]"
			)
		));

		$dd = $spawn->update(array(
			table => $tablesName[modules],
			where => 'srl='.(int)$_POST[module_srl],
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

		$util->redirect(ROOT.'/module/index/'.$_POST[group_srl].'/', '수정완료');
		break;


	// delete
	case 'delete':
		$articles = $spawn->getItems(array(
			table => $tablesName[articles],
			where => 'module_srl='.(int)$_POST[module_srl]
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
			where => 'module_srl='.(int)$_POST[module_srl]
		));
		$spawn->delete(array(
			table => $tablesName[extraKeys],
			where => 'module_srl='.(int)$_POST[module_srl]
		));
		$spawn->delete(array(
			table => $tablesName[articles],
			where => 'module_srl='.(int)$_POST[module_srl]
		));
		$spawn->delete(array(
			table => $tablesName[modules],
			where => 'srl='.(int)$_POST[module_srl]
		));

		$util->redirect(ROOT.'/module/index/', '삭제완료');
		break;
}
