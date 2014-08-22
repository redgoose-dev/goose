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
	return preg_replace("/[^a-zA-Z0-9가-힣ㄱ-ㅎ]|/", "", $str);;
}

/**
 * check tag data
 * 
 * @Param {String} $str
 * @Return 
 */ 
function getTags($str)
{
	$json = (get_magic_quotes_gpc()) ? stripslashes($str) : $str;
	$json = json_decode($json, true);
	return $json['tag'];
}


// tag processing
$tagsFile = PWD.'/data/config/tags.user.txt';

if (!file_exists($tagsFile))
{
	$goose->util->fop($tagsFile, 'w', '[]', 0777);
}

if (file_exists($tagsFile))
{
	$fr = $goose->util->fop($tagsFile, 'r');
	$originalTagData = json_decode($fr, true);
	$originalKeywords = $goose->util->arrayToArray($originalTagData, 'name');
	$resultTags = ($originalTagData) ? $originalTagData : array();

	switch($paramAction)
	{
		case 'create':
			$newTags = getTags($_POST['json']);
			foreach($newTags as $k=>$v)
			{
				$v = checkTag($v);
				if ($v)
				{
					$pos = array_search($v, $originalKeywords);
					if (is_numeric($pos))
					{
						$resultTags[$pos]['count']++;
					}
					else
					{
						array_push($resultTags, array(
							'name' => $v
							,'count' => 1
						));
					}
				}
			}
			break;

		case 'modify':
			$oldTags = getTags($article['json']);
			$newTags = getTags($_POST['json']);

			foreach ($oldTags as $k=>$v)
			{
				$pos = array_search($v, $originalKeywords);
				$resultTags[$pos]['count']--;
			}

			foreach ($newTags as $k=>$v)
			{
				$pos = array_search($v, $originalKeywords);
				if (is_numeric($pos))
				{
					$resultTags[$pos]['count']++;
				}
				else
				{
					array_push($resultTags, array('name' => $v, 'count' => 1));
				}
			}
			break;

		case 'delete':
			$oldTags = getTags($article['json']);

			foreach ($oldTags as $k=>$v)
			{
				$pos = array_search($v, $originalKeywords);
				$resultTags[$pos]['count']--;
			}
			break;
	}

	foreach ($resultTags as $k=>$v)
	{
		if ($v['count'] <= 0)
		{
			unset($resultTags[$k]);
		}
	}

	$fw = fopen($tagsFile, "w") or die('open error');
	fwrite($fw, json_encode($resultTags));
	fclose($fw);
}
?>