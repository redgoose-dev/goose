<?
if(!defined("GOOSE")){exit();}

/**
 * check file
 *
 * @param string $dir
 * @param string $file
 * @param number $n
 * @return string
 */
function checkFile($dir, $file, $n)
{
	if (is_null($n))
	{
		$n = 0;
		$newFilename = $file;
	}
	else
	{
		$n = $n + 1;
		$newFilename = basename($file, strrchr($file, '.')).'-'.$n.'.'.substr(strrchr($file, '.'), 1);
	}

	if (file_exists($dir.$newFilename))
	{
		return checkFile($dir, $file, $n);
	}
	else
	{
		return $newFilename;
	}
}


switch($paramAction)
{
	// upload
	case 'upload':
		$files = ($_FILES['file']) ? $_FILES['file'] : $_FILES['Filedata'];
		if (!$files)
		{
			echo '{"status":"error","message":"SERVER ERROR"}';
			$goose->out();
		}

		// check file size
		if ($files['size'] > $goose->user['limitFileSize'])
		{
			echo '{"status":"error","message":"The attachment size exceeds the allowable limit."}';
			$goose->out();
		}

		// 변수정의
		$dir_absolute = PWD.'/data/original/';
		$dir_relative = GOOSE_URL.'/data/original/';
		$originalFileName = $files['name'];
		$regdate = date("YmdHis");
		$month = Date('Ym');
		$files['type'] = strtolower($files['type']);

		// 날짜 디렉토리 만들기
		if (!is_dir($dir_absolute.$month))
		{
			$umask = umask();
			umask(000);
			mkdir($dir_absolute.$month, 0777);
			umask($umask);
		}

		// 이미지 파일이름 변경
		$filename = null;
		if (preg_match('/^image/', $files['type']) >= 1)
		{
			$filename = md5(date('YmdHis').'-'.rand()).'.'.substr(strrchr($files['name'], '.'), 1);
		}
		else
		{
			$filename = str_replace(' ', '_', $files['name']);
		}

		// check exist file
		$filename = checkFile($dir_absolute.$month.'/', $filename, null);

		// 절대경로 파일주소 정의
		$fileAbsoluteDir = $dir_absolute.$month.'/' . $filename;

		// copy file
		if ($files['tmp_name'])
		{
			move_uploaded_file($files['tmp_name'], $fileAbsoluteDir);
		}
		else
		{
			echo '{"status":"error","message":"no file"}';
			$goose->out();
		}

		// 상대경로 파일주소 정의
		$fileRelativeDir = $month.'/'.$filename;

		// insert db
		$goose->spawn->insert(array(
			'table' => $goose->tablesName['tempFiles'],
			'data' => array(
				'srl' => null,
				'loc' => $fileRelativeDir,
				'name' => $filename,
				'type' => $files['type'],
				'size' => $files['size'],
				'date' => $regdate
			)
		));

		$sess_srl = $goose->spawn->conn->lastInsertId();

		// result print
		$result = array(
			'filelink' => $dir_relative.$fileRelativeDir,
			'loc' => $fileRelativeDir,
			'filename' => $filename,
			'filetype' => $files['type'],
			'filesize' => $files['size'],
			'sess_srl' => $sess_srl
		);
		echo stripslashes(json_encode($result));
		break;


	// remove
	case 'remove':
		foreach (explode(',', $_POST['data']) as $k=>$v)
		{
			$item = explode(':', $v);
			switch ($item[0])
			{
				case 'modify':
					$tableName = 'files';
					break;
				case 'session':
					$tableName = 'tempFiles';
					break;
			}
			$files = $goose->spawn->getItem(array(
				'field' => 'loc,name',
				'table' => $tableName,
				'where' => 'srl='.$item[1]
			));
			$loc = PWD.'/data/original/'.$files['loc'];
			if (file_exists($loc))
			{
				unlink($loc);
			}
			$goose->spawn->delete(array(
				'table' => $tableName,
				'where' => 'srl='.$item[1]
			));
		}
		echo json_encode(array(
			'status' => 'success'
		));
		break;
}

$goose->out();
