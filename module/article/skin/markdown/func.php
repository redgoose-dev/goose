<?php
if (!defined('__GOOSE__')) exit();

/**
 * get article json
 * article의 json필드의 내용을 가져온다.
 *
 * @param int $srl
 * @return array
 */
function getArticleJSON($srl)
{
	$data = Spawn::items(array(
		'table' => Spawn::getTableName('article'),
		'field' => 'json',
		'where' => 'srl='.(int)$srl
	));
	return (count($data)) ? Util::jsonToArray($data[0]['json'], null, true) : null;
}


/**
 * upload file db update
 * tempFiles 테이블에 있는 임시파일들 목록을 files 테이블에 옮기고, 썸네일으로 사용하는 첨부파일 번호를 리턴한다.
 *
 * @param array $post $_POST
 * @param int $art_srl 글을 등록하고 바로 가져온 srl번호
 * @param int $thum_srl 썸네일 srl번호
 * @return int 바뀐 썸네일 srl번호
 */
function fileUpload($post, $art_srl, $thum_srl)
{
	$thumnail_srl = null;
	if ($_POST['addQueue'])
	{
		$queue = explode(',', $post['addQueue']);
		foreach($queue as $k=>$v)
		{
			if (!$v) continue;
			$tmpFile = Spawn::item(array(
				'table' => Spawn::getTableName('file_tmp'),
				'where' => 'srl='.(int)$v
			));
			if ($tmpFile['state'] == 'success' && count($tmpFile['data']))
			{
				// insert file
				$result = Spawn::insert(array(
					'table' => Spawn::getTableName('file'),
					'data' => array(
						'srl' => null,
						'article_srl' => $art_srl,
						'name' => $tmpFile['data']['name'],
						'loc' => $tmpFile['data']['loc'],
						'type' => $tmpFile['data']['type'],
						'size' => $tmpFile['data']['size'],
						'regdate' => date("YmdHis")
					)
				));
				// set thumnail srl
				if ($tmpFile['srl'] == $thum_srl)
				{
					$thumnail_srl = Spawn::getLastIdx();
				}
				// remove tmp file
				Spawn::delete(array(
					'table' => Spawn::getTableName('file_tmp'),
					'where' => 'srl='.(int)$v
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
 * @param string $imgData base64형식의 이미지 데이터
 * @return string 서버에 업로드한 썸네일 이미지 경로
 */
function uploadThumnail($imgData=null)
{
	$file = Module::load('file');
	$uploadDir = '';
	$result = 0;
	if ($imgData)
	{
		$fileName = uniqid().".jpg";
		$month = Date('Ym');
		$uploadDir = $file->set['upPath_make'].'/'.$month.'/';
		Util::createDirectory(__GOOSE_PWD__.$uploadDir, 0777);
		$uploadDir .= $fileName;
		$imgData = str_replace('data:image/jpeg;base64,', '', $imgData);
		$imgData = str_replace(' ', '+', $imgData);
		$result = file_put_contents($uploadDir, base64_decode($imgData));
	}
	return ($result) ? $uploadDir : null;
}