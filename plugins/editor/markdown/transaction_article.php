<?php
if(!defined("GOOSE")){exit();}

/*
	$editorDir : editor plugin location
*/

/**
 * check tag data
 * 
 * @Param {String} $tags
 * @Return {String}
*/
function checkTag($str)
{
	return preg_replace("/[^a-zA-Z0-9가-힣]|/", "", $str);;
}


// tag processing
$tagsFile = $editorDir.'/tags.user.txt';
if (file_exists($tagsFile))
{
	$fo = fopen($tagsFile, "r") or die('open error');
	$fr = fread($fo, filesize($tagsFile));
	$originalTagData = json_decode($fr);
	$originalKeywords = array();
	$resultTags = array();
	foreach ($originalTagData as $k=>$v)
	{
		array_push($originalKeywords, $v->name);
	}
	var_dump($originalKeywords);
	echo "<hr /><hr />";
	fclose($fo);

	if ($paramAction != 'delete')
	{
		$json = (get_magic_quotes_gpc()) ? stripslashes($_POST['json']) : $_POST['json'];
		$json = json_decode($json, true);
		$newTags = $json['tag'];
	}

	switch($paramAction)
	{
		case 'create':
			/*
				1. 태그 단어가 문제가 없는지 체크한다.(특수문자, 사이띄개 필터링)
				2. 새로운 태그데이터를 반복문으로 돌린다.
				3. 태그맵과 체크를 한다.
				3-1. 태그맵에 같은 키워드가 없으면 키워드를 추가하고, 카운터는 1으로 초기화
				3-2. 태그맵에 같은 키워드가 있으면 그 키워드쪽에서 카운터를 1올린다.
			*/
			foreach($newTags as $k=>$v)
			{
				$v = checkTag($v);
				if ($v)
				{
					$pos = array_search($v, $originalKeywords);
					if (is_numeric($pos))
					{
						$item = array(
							'name' => $originalTagData[$pos]->name
							,'count' => $originalTagData[$pos]->count + 1
						);
					}
					else
					{
						$item = array(
							'name' => $v
							,'count' => 1
						);
					}
					array_push($resultTags, $item);
				}
			}
			break;

		case 'modify':
			echo 'plugin - modify';
			/*
				1. 원본 태그데이터를 준비한다.
				2. 수정할 태그단어가 문제없는지 체크한다.(특수문자, 사이띄개 필터링)
				3. 원본 태그데이터를 반복문 돌린다.
				3-1. 원본 키워드를 찾아서 전부 -1 처리한다. 1이면 0으로...
				4. 새로운 태그데이터를 반복문 돌린다.
				4-1. 새 키워드를 +1 처리하거나 없으면 만든다.
				5. 태그맵의 보정작업을 한다.(카운터가 0인 키워드는 삭제)
			*/
			break;

		case 'delete':
			echo 'plugin - delete';
			/*
				1. 원본 태그데이터를 준비한다.
				2. 원본 태그데이터를 반복문으로 돌린다.
				3-1. 원본 키워드를 찾아서 전부 -1 처리한다. 1이면 0으로...
				4. 태그맵의 보정작업을 한다.(카운터가 0인 키워드는 삭제)
			*/
			break;
	}

	$fw = fopen($tagsFile, "w") or die('open error');
	fwrite($fw, json_encode($resultTags));
	fclose($fw);
}
?>