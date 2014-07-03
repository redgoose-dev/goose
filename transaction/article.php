<?php
if(!defined("GOOSE")){exit();}

$ipAddress = $_SERVER['REMOTE_ADDR'];
$regdate = date("YmdHis");
$_POST['title'] = htmlspecialchars($_POST['title']);

/**
 * upload file db update
 * tempFiles 테이블에 있는 임시파일들 목록을 files 테이블에 옮기고, 썸네일으로 사용하는 첨부파일 번호를 리턴한다.
 * 
 * @param Number $art_srl : 글을 등록하고 바로 가져온 srl번호
 * @param Number $thum_srl : 썸네일 srl번호
 * @return Number $thumnail_srl : 바뀐 썸네일 srl번호
 */
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

/**
 * upload thumnail
 * 썸네일 이미지 데이터를 받아서 서버에 올리고, 이미지 경로를 리턴한다.
 * 
 * @param String $imgData : base64형식의 이미지 데이터
 * @return String $thumnailDir : 서버에 업로드한 썸네일 이미지 경로
 */
function uploadThumnail($imgData=null)
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


// act
switch($paramAction)
{
	// create
	case 'create':
		// post값 확인
		$errorValue = $util->checkExistValue($_POST, array('title', 'content'));
		if ($errorValue)
		{
			$util->back("[$errorValue]값이 없습니다.");
			$util->out();
		}

		$result = $spawn->insert(array(
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
			$thumnailUrl = uploadThumnail($_POST[thumnail_image]);
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
		if ($_POST['useExtraVar'])
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
				if ($value)
				{
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
		}

		$n = ($_POST['category_srl']) ? $_POST['category_srl'].'/' : '';
		$util->redirect(ROOT.'/article/index/'.$_POST[nest_srl].'/'.$n);
		break;

	// modify
	case 'modify':
		// post값 확인
		$errorValue = $util->checkExistValue($_POST, array('title', 'content'));
		if ($errorValue)
		{
			$util->back("[$errorValue]값이 없습니다.");
			$util->out();
		}

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
			$thumnailUrl = uploadThumnail($_POST['thumnail_image']);
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
		$result = $spawn->update(array(
			table => $tablesName[articles],
			where => 'srl='.(int)$_POST[article_srl],
			data => array(
				"category_srl='$_POST[category_srl]'",
				"title='$_POST[title]'",
				"content='$_POST[content]'",
				"thumnail_coords='$_POST[thumnail_coords]'",
				"modate='$regdate'",
				"ipAddress='$ipAddress'"
			)
		));

		// 썸네일 이미지는 있고, 첨부파일이 하나도 없을때 썸네일 이미지 삭제
		if ($article[thumnail_srl])
		{
			// get article item data
			$filesCount = $spawn->getCount(array(
				table => $tablesName[files],
				where => 'article_srl='.(int)$_POST[article_srl].' and srl='.(int)$article['thumnail_srl']
			));
			if (!$filesCount)
			{
				// delete thumnail file
				if (file_exists($absoluteThumnailDir.$article['thumnail_url']))
				{
					unlink($absoluteThumnailDir.$article['thumnail_url']);
				}
				// update article db
				$result = $spawn->update(array(
					table => $tablesName[articles],
					where => 'srl='.(int)$_POST[article_srl],
					data => array(
						"thumnail_srl='0'",
						"thumnail_url=''",
						"thumnail_coords=''"
					)
				));
			}
		}

		// update extra value
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