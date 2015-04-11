<?php
if(!defined("GOOSE")){exit();}

/**
 * upload file db update
 * tempFiles 테이블에 있는 임시파일들 목록을 files 테이블에 옮기고, 썸네일으로 사용하는 첨부파일 번호를 리턴한다.
 * 
 * @param {Number} $art_srl : 글을 등록하고 바로 가져온 srl번호
 * @param {Number} $thum_srl : 썸네일 srl번호
 * @return {Number} $thumnail_srl : 바뀐 썸네일 srl번호
 */
function fileUpload($art_srl, $thum_srl)
{
	global $goose;
	if ($_POST['addQueue'])
	{
		$thumnail_srl = null;
		$queue = explode(',', $_POST['addQueue']);
		foreach($queue as $k=>$v)
		{
			if ($v)
			{
				$tempFile = $goose->spawn->getItem(array(
					'table' => 'tempFiles',
					'where' => 'srl='.(int)$v
				));
				$goose->spawn->insert(array(
					'table' => 'files'
					,'data' => array(
						'srl' => null
						,'article_srl' => $art_srl
						,'name' => $tempFile['name']
						,'loc' => $tempFile['loc']
						,'type' => $tempFile['type']
						,'size' => $tempFile['size']
						,'date' => date("YmdHis")
					)
				));

				if ($tempFile['srl'] == $thum_srl)
				{
					$thumnail_srl = $goose->spawn->conn->lastInsertId();
				}
				$goose->spawn->delete(array(
					'table' => 'tempFiles'
					,'where' => 'srl='.(int)$v
				));
			}
		}
	}
	return ($thumnail_srl) ? $thumnail_srl : $thum_srl;
}

/**
 * upload thumnail
 * 썸네일 이미지 데이터를 받아서 서버에 올리고, 이미지 경로를 리턴한다.
 * 
 * @param {String} $imgData : base64형식의 이미지 데이터
 * @return {String} $thumnailDir : 서버에 업로드한 썸네일 이미지 경로
 */
function uploadThumnail($imgData=null)
{
	global $goose, $dataThumnailDirectory;
	if ($imgData)
	{
		$fileName = uniqid() . ".jpg";
		$month = Date(Ym);
		$thumnailDir = $month.'/'.$fileName;
		$uploadDir = PWD.$dataThumnailDirectory.$month.'/';
		$goose->util->createDirectory($uploadDir, 0777);
		$uploadDir .= $fileName;
		$imgData = str_replace('data:image/jpeg;base64,', '', $imgData);
		$imgData = str_replace(' ', '+', $imgData);
		$success = file_put_contents($uploadDir, base64_decode($imgData));
	}
	return $thumnailDir;
}


/**
 * get JSON
 * article의 json필드의 내용을 가져온다.
 * 
 * @param {Number} $srl
 * @return {Array}
 */
function getJSON($srl)
{
	global $goose;
	$data = $goose->spawn->getItem(array(
		'table' => 'articles'
		,'field' => 'json'
		,'where' => 'srl='.(int)$srl
	));
	return ($data['json']) ? json_decode(urldecode($data['json']), true) : array();
}


// action
switch($paramAction)
{
	// create
	case 'create':

		// get last id number
		$article_srl = $goose->spawn->conn->lastInsertId();;

		// get last item json
		$json = getJSON($article_srl);

		// files upload
		$thumnail_srl = fileUpload($article_srl, $json['thumnail']['srl']);

		// thumnail image upload
		if ($thumnail_srl)
		{
			$thumnailUrl = uploadThumnail($_POST['thumnail_image']);

			$json['thumnail']['srl'] = $thumnail_srl;
			$json['thumnail']['url'] = $thumnailUrl;
			$json_result = urlencode(json_encode($json));

			$result = $goose->spawn->update(array(
				'table' => 'articles',
				'where' => 'srl='.$article_srl,
				'data' => array(
					"json='$json_result'"
				)
			));
		}
		break;

	// modify
	case 'modify':
		// get article json
		$json = getJSON((int)$_POST['article_srl']);
		$json_new = $json;

		$absoluteThumnailDir = PWD.$dataThumnailDirectory;

		// upload files
		$thumnail_srl = fileUpload($_POST['article_srl'], $json['thumnail']['srl']);

		// upload thumnail image
		if ($_POST['thumnail_image'])
		{
			if (file_exists($absoluteThumnailDir.$json['thumnail']['url']))
			{
				unlink($absoluteThumnailDir.$json['thumnail']['url']);
			}
			$thumnailUrl = uploadThumnail($_POST['thumnail_image']);

			// set json
			$json_new['thumnail']['srl'] = $thumnail_srl;
			$json_new['thumnail']['url'] = $thumnailUrl;

			$thumnailUploaded = true;
		}

		// 썸네일 이미지는 있고, 썸네일 이미지가 새로 만들어지지 않을때
		if ($json['thumnail']['srl'] && !$thumnailUploaded)
		{
			// get article item data
			$filesCount = $goose->spawn->getCount(array(
				'table' => 'files',
				'where' => 'article_srl='.(int)$_POST['article_srl'].' and srl='.(int)$json['thumnail']['srl']
			));
			if (!$filesCount)
			{
				// delete thumnail file
				if (file_exists($absoluteThumnailDir.$json['thumnail']['url']))
				{
					unlink($absoluteThumnailDir.$json['thumnail']['url']);
				}
				// set json
				$json_new['thumnail'] = array(srl => '0', url => '', coords => '');
			}
		}

		// array to json
		$json_result = urlencode(json_encode($json_new));

		// update article
		$result = $goose->spawn->update(array(
			'table' => 'articles'
			,'where' => 'srl='.(int)$_POST['article_srl']
			,'data' => array(
				"json='$json_result'"
			)
		));
		break;

	// delete
	case 'delete':
		// convert string to json data
		$article['json'] = json_decode(urldecode($article['json']), true);

		// delete thumnail image
		if ($article['json']['thumnail']['url'] and file_exists(PWD.$dataThumnailDirectory.$article['json']['thumnail']['url']))
		{
			unlink(PWD.$dataThumnailDirectory.$article['json']['thumnail']['url']);
		}

		// delete original files
		$files = $goose->spawn->getItems(array(
			'table' => 'files',
			'where' => 'article_srl='.$article['srl']
		));
		if (count($files))
		{
			foreach ($files as $k=>$v)
			{
				if (file_exists(PWD.$dataOriginalDirectory.$v['loc']))
				{
					unlink(PWD.$dataOriginalDirectory.$v['loc']);
					$goose->spawn->delete(array(
						'table' => 'files',
						'where' => 'srl='.$v['srl']
					));
				}
			}
		}
		break;
}
?>