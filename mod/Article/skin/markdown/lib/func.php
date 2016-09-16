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
	$data = core\Spawn::item([
		'table' => core\Spawn::getTableName('Article'),
		'field' => 'json',
		'where' => 'srl=' . (int)$srl,
		'jsonField' => ['json']
	]);
	return $data['json'];
}


/**
 * translate file data
 * 파일 데이터에 article_srl값을 추가하고, ready값을 0으로 변경한다.
 *
 * @param array $post $_POST
 * @param int $art_srl 글을 등록하고 바로 가져온 srl번호
 * @param int $thumb_srl 썸네일 srl번호
 * @return int 바뀐 썸네일 srl번호
 */
function translateFileData($post, $art_srl, $thumb_srl)
{
	$thumbnail_srl = null;

	if ($post['addQueue'])
	{
		$queue = explode(',', $post['addQueue']);
		foreach($queue as $k=>$v)
		{
			if (!$v) continue;

			// update file
			$result = core\Spawn::update([
				'table' => core\Spawn::getTableName('File'),
				'where' => 'srl=' . (int)$v,
				'data' => [
					'article_srl=' . $art_srl,
					'ready=0'
				]
			]);

			// set thumbnail srl
			if ((int)$v == (int)$thumb_srl)
			{
				$thumbnail_srl = core\Spawn::getLastIdx();
			}
		}
	}

	return ($thumbnail_srl) ? $thumbnail_srl : $thumb_srl;
}


/**
 * upload thumbnail
 * 썸네일 이미지 데이터를 받아서 서버에 올리고, 이미지 경로를 리턴한다.
 *
 * @param string $imgData base64형식의 이미지 데이터
 * @return string 서버에 업로드한 썸네일 이미지 경로
 */
function uploadThumbnail($imgData=null)
{
	$file = new mod\File\File();
	$uploadDir = '';
	$result = 0;
	if ($imgData)
	{
		$fileName = uniqid().".jpg";
		$month = date('Ym');
		$uploadDir = $file->set['upPath_make'] . '/' . $month . '/';
		core\Util::createDirectory(__GOOSE_PWD__.$uploadDir, 0777);
		$uploadDir .= $fileName;
		$imgData = str_replace('data:image/jpeg;base64,', '', $imgData);
		$imgData = str_replace(' ', '+', $imgData);
		$result = file_put_contents($uploadDir, base64_decode($imgData));
	}
	return ($result) ? $uploadDir : null;
}