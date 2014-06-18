<?php
if(!defined("GOOSE")){exit();}

//header("Content-Type:text/plain");

if ($paramAction != 'delete')
{
	if (!$_POST['title'])
	{
		$util->back('[제목] 항목이 비었습니다.');
		exit;
	}
	if (!$_POST['content'])
	{
		$util->back('[내용] 항목이 비었습니다.');
		exit;
	}
}

$ipAddress = $_SERVER['REMOTE_ADDR'];
$regdate = date("YmdHis");
$_POST['title'] = htmlspecialchars($_POST['title']);


// upload file db update
function fileUpload($art_srl, $thum_srl)
{
	global $spawn, $util, $tablesName;
	if ($_POST[addQueue])
	{
		$thumnail_srl = null;
		$queue = explode(',', $_POST[addQueue]);
		foreach($queue as $k=>$v)
		{
			if ($v)
			{
				$tempFile = $spawn->getItem(array(
					table => $tablesName[tempFiles],
					where => 'srl='.(int)$v
				));
				$spawn->insert(array(
					table => $tablesName[files],
					data => array(
						srl => null,
						article_srl => $art_srl,
						name => $tempFile[name],
						loc => $tempFile[loc]
					)
				));

				if ($tempFile[srl] == $thum_srl)
				{
					$thumnail_srl = $spawn->conn->lastInsertId();
				}
				$spawn->delete(array(
					table => $tablesName[tempFiles],
					where => 'srl='.(int)$v
				));
			}
		}
	}
	else
	{
		$thumnail_srl = $thum_srl;
	}
	return $thumnail_srl;
}

// upload thumnail
function uploadThumnail($srl, $imgData)
{
	global $dir, $util, $dataThumnailDirectory;
	if ($imgData)
	{
		$fileName = uniqid() . ".jpg";
		$month = Date(Ym);
		$thumnailDir = $month.'/'.$fileName;
		$uploadDir = PWD.$dataThumnailDirectory.$month.'/';
		$util->createDirectory($uploadDir, 0777);
		$uploadDir .= $fileName;
		$imgData = str_replace('data:image/jpeg;base64,', '', $imgData);
		$imgData = str_replace(' ', '+', $imgData);
		$success = file_put_contents($uploadDir, base64_decode($imgData));
	}
	return $thumnailDir;
}

switch($paramAction)
{
	// create
	case 'create':
		$dd = $spawn->insert(array(
			table => $tablesName[articles],
			data => array(
				srl => null,
				group_srl => $_POST[group_srl],
				nest_srl => $_POST[nest_srl],
				category_srl => $_POST[category_srl],
				thumnail_srl => null,
				title => $_POST[title],
				content => $_POST[content],
				thumnail_url => null,
				thumnail_coords => $_POST[thumnail_coords],
				regdate => $regdate,
				modate => $regdate,
				ipAddress => $ipAddress
			)
		));

		// get last id number
		$article_srl = $spawn->conn->lastInsertId();

		// files upload
		$thumnail_srl = fileUpload($article_srl, $_POST[thumnail_srl]);

		// thumnail image upload
		if ($thumnail_srl)
		{
			$thumnailUrl = uploadThumnail($thumnail_srl, $_POST[thumnail_image]);
			$spawn->update(array(
				table => $tablesName[articles],
				where => 'srl='.$article_srl,
				data => array(
					"thumnail_srl='$thumnail_srl'",
					"thumnail_url='$thumnailUrl'"
				)
			));
		}

		// 확장변수 데이터 입력
		if ($_POST[useExtraVar])
		{
			$extraKey = $spawn->getItems(array(
				table => $tablesName[extraKey],
				where => 'nest_srl='.(int)$_POST[nest_srl],
				order => 'turn',
				sort => 'asc'
			));
			foreach ($extraKey as $k=>$v)
			{
				$keyName = $v[keyName];
				$value = $_POST['ext_'.$keyName];
				$spawn->insert(array(
					table => $tablesName[extraVar],
					data => array(
						article_srl => $article_srl,
						key_srl => $v[srl],
						value => $value
					)
				));
			}
		}

		$n = ($_POST['category_srl']) ? $_POST['category_srl'].'/' : '';
		$util->redirect(ROOT.'/article/index/'.$_POST[nest_srl].'/'.$n);
		break;

	// modify
	case 'modify':
		$absoluteThumnailDir = PWD.$dataThumnailDirectory;

		// get article item data
		$article = $spawn->getItem(array(
			table => $tablesName[articles],
			where => 'srl='.(int)$_POST[article_srl]
		));

		// upload files
		$thumnail_srl = fileUpload($_POST['article_srl'], $_POST['thumnail_srl']);

		// upload thumnail image
		if ($thumnail_srl or $_POST['thumnail_srl'])
		{
			if (file_exists($absoluteThumnailDir.$article['thumnail_url']))
			{
				unlink($absoluteThumnailDir.$article['thumnail_url']);
			}
			$thumnailUrl = uploadThumnail($thumnail_srl, $_POST['thumnail_image']);
			$spawn->update(array(
				'table' => $tablesName['articles'],
				'where' => 'srl='.(int)$_POST['article_srl'],
				'data' => array(
					"thumnail_srl=$thumnail_srl",
					"thumnail_url='$thumnailUrl'"
				)
			));
		}

		// update article
		$dd = $spawn->update(array(
			table => $tablesName[articles],
			where => 'srl='.(int)$_POST[article_srl],
			data => array(
				"category_srl='$_POST[category_srl]'",
				"title='".$_POST['title']."'",
				"content='".$_POST['content']."'",
				"thumnail_coords='$_POST[thumnail_coords]'",
				"modate='$regdate'",
				"ipAddress='$ipAddress'"
			)
		));

		// update extravar
		$extraKey = $spawn->getItems(array(
			field => 'srl,keyName',
			table => $tablesName['extraKey'],
			where => 'nest_srl='.(int)$_POST[nest_srl],
			order => 'turn',
			sort => 'asc'
		));

		foreach ($extraKey as $k=>$v)
		{
			$extraVarCount = $spawn->getCount(array(
				table => $tablesName[extraVar],
				where => 'article_srl='.(int)$_POST[article_srl].' and key_srl='.$v[srl]
			));
			$keyName = $v['keyName'];
			$value = $_POST['ext_'.$keyName];

			if ($extraVarCount==0 and $value)
			{
				$spawn->insert(array(
					table => $tablesName['extraVar'],
					data => array(
						article_srl => $_POST['article_srl'],
						key_srl => $v['srl'],
						value => $value
					)
				));
			}
			else
			{
				if ($value)
				{
					$spawn->update(array(
						table => $tablesName[extraVar],
						where => 'key_srl='.$v[srl],
						data => array("value='$value'")
					));
				}
				else
				{
					$spawn->delete(array(
						table => $tablesName[extraVar],
						where => 'article_srl='.$_POST[article_srl].' and key_srl='.$v[srl]
					));
				}
			}
		}

		$util->redirect(ROOT.'/article/view/'.$_POST[article_srl].'/');
		break;

	// delete
	case 'delete':
		// get article item data
		$article = $spawn->getItem(array(
			table => $tablesName[articles],
			where => 'srl='.(int)$_POST[article_srl]
		));

		// delete thumnail image
		if ($article[thumnail_url] and file_exists(PWD.$dataThumnailDirectory.$article[thumnail_url]))
		{
			unlink(PWD.$dataThumnailDirectory.$article[thumnail_url]);
		}

		// delete original files
		$files = $spawn->getItems(array(
			table => $tablesName[files],
			where => 'article_srl='.$article[srl]
		));
		if (count($files))
		{
			foreach ($files as $k=>$v)
			{
				if (file_exists(PWD.$dataOriginalDirectory.$v[loc]))
				{
					unlink(PWD.$dataOriginalDirectory.$v[loc]);
					$spawn->delete(array(
						table => $tablesName[files],
						where => 'srl='.$v[srl]
					));
				}
			}
		}

		// delete article
		$spawn->delete(array(
			table => $tablesName[articles],
			where => 'srl='.(int)$_POST[article_srl]
		));
		// delete extravar item
		$spawn->delete(array(
			table => $tablesName[extraVar],
			where => 'article_srl='.(int)$_POST[article_srl]
		));

		$url = ROOT.'/article/index/';
		$url .= ($_POST[nest_srl]) ? $_POST[nest_srl].'/' : '';
		$url .= ($_POST[category_srl]) ? $_POST[category_srl].'/' : '';
		$util->redirect($url);
		break;
}
?>