<?php
// 필드수정
$add_field_tempFiles = $goose->spawn->action("
	ALTER TABLE `".$goose->tablesName['tempFiles']."`
		ADD `type` VARCHAR(100) NULL DEFAULT NULL AFTER `name`,
		ADD `size` BIGINT(11) NULL DEFAULT NULL AFTER `type`
");

if($add_field_tempFiles)
{
	echo "<p>tempFiles - complete fix<p>";
  echo "<hr>";
}

$add_field_files = $goose->spawn->action("
	ALTER TABLE `".$goose->tablesName['files']."`
		ADD `type` VARCHAR(100) NULL DEFAULT NULL AFTER `loc`,
		ADD `size` BIGINT(11) NULL DEFAULT NULL AFTER `type`,
		ADD `date` VARCHAR(14) NULL DEFAULT NULL AFTER `size`
");

if($add_field_files)
{
	echo "<p>files - complete fix<p>";
  echo "<hr>";
}


// 기존 데이터들 새로운 필드의 데이터를 참고하여 데이터 수정하기
$files = $goose->spawn->getItems(array(
	'table' => $goose->tablesName['files']
	,'where' => 'type is NULL'
));

foreach($files as $k=>$v)
{
	$article = $goose->spawn->getItem(array(
		'table' => $goose->tablesName['articles']
		,'field' => 'regdate'
		,'where' => 'srl='.$v['article_srl']
	));

	$ext = strtolower(substr(strrchr($v['name'], '.'), 1));
	$type = null;
	if ($ext == 'jpg' || $ext == 'jpeg')
	{
		$type = 'image/jpeg';
	}
	else if ($ext == 'png')
	{
		$type = 'image/png';
	}
	else if ($ext == 'gif')
	{
		$type = 'image/gif';
	}
	else if ($ext == 'svg')
	{
		$type = 'image/svg+xml';
	}
	else if ($ext == 'txt')
	{
		$type = 'text/plain';
	}
	else if ($ext == 'doc')
	{
		$type = 'application/msword';
	}
	else if ($ext == 'pdf')
	{
		$type = 'application/pdf';
	}
	else if ($ext == 'json')
	{
		$type = 'application/json';
	}
	else if ($ext == 'js')
	{
		$type = 'text/javascript';
	}
	else if ($ext == 'css')
	{
		$type = 'text/css';
	}
	else if ($ext == 'htm' || $ext == 'html')
	{
		$type = 'text/html';
	}
	else if ($ext == 'exe')
	{
		$type = 'application/octet-stream';
	}
	else if ($ext == 'zip')
	{
		$type = 'application/zip';
	}
	else if ($ext == 'rar')
	{
		$type = 'application/x-rar-compressed';
	}
	else if ($ext == 'swf')
	{
		$type = 'application/x-shockwave-flash';
	}

	if (file_exists(PWD.'/data/original/'.$v['loc']))
	{
		$size = filesize(PWD.'/data/original/'.$v['loc']);
	}
	else
	{
		$size = 0;
	}

	$update = $goose->spawn->update(array(
		'table' => $goose->tablesName['files'],
		'where' => 'srl='.$v['srl'],
		'data' => array(
			"type='$type'"
			,"size=$size"
			,"date='".$article['regdate']."'"
		)
		,'debug' => false
	));
}
echo "update database";
echo "<hr>";