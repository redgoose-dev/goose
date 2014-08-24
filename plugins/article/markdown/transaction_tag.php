<?php
if(!defined("GOOSE")){exit();}

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
	return ($json['tag']) ? $json['tag'] : array();
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
						array_push($resultTags[$pos]['srl'], $lastSrl);
					}
					else
					{
						array_push($resultTags, array(
							'name' => $v
							,'srl' => array($lastSrl)
						));
					}
				}
			}

			break;

		case 'modify':

			$oldTags = getTags($oldArticle['json']);
			$newTags = getTags($_POST['json']);

			foreach ($oldTags as $k=>$v)
			{
				$pos = array_search($v, $originalKeywords);
				if (is_numeric($pos))
				{
					$pos2 = array_search($article['srl'], $resultTags[$pos]['srl']);
					if (is_numeric($pos2))
					{
						unset($resultTags[$pos]['srl'][$pos2]);
					}
				}
			}

			foreach ($newTags as $k=>$v)
			{
				$pos = array_search($v, $originalKeywords);
				if (is_numeric($pos))
				{
					$pos2 = array_search($article['srl'], $resultTags[$pos]['srl']);
					if (is_bool($pos2))
					{
						array_push($resultTags[$pos]['srl'], $article['srl']);
					}
				}
				else
				{
					array_push($resultTags, array(
						'name' => $v
						,'srl' => array($article['srl'])
					));
				}
			}

			break;

		case 'delete':

			$oldTags= getTags($article['json']);

			foreach ($oldTags as $k=>$v)
			{
				$pos = array_search($v, $originalKeywords);
				if (is_numeric($pos))
				{
					$pos2 = array_search($article['srl'], $resultTags[$pos]['srl']);
					if (is_numeric($pos2))
					{
						unset($resultTags[$pos]['srl'][$pos2]);
					}
				}
			}

			break;
	}
}


if (is_array($resultTags))
{
	foreach ($resultTags as $k=>$v)
	{
		if (count($v['srl']))
		{
			$resultTags[$k]['srl'] = array_values($v['srl']);
		}
		else
		{
			unset($resultTags[$k]);
		}
	}
}

$fw = fopen($tagsFile, "w") or die('open error');
fwrite($fw, json_encode($resultTags));
fclose($fw);
?>