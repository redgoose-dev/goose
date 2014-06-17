<?
if(!defined("GOOSE")){exit();}

switch($paramAction)
{
	// upload
	case 'upload':
		$files = ($_FILES['file']) ? $_FILES['file'] : $_FILES['Filedata'];
		if (!$files)
		{
			echo '$_FILES error';
			exit;
		}

		// 변수정의
		$dir_absolute = PWD.'/data/original/';
		$dir_relative = ROOT.'/data/original/';
		$originalFileName = $files['name'];
		$regdate = date("YmdHis");
		$month = Date(Ym);
		$files['type'] = strtolower($files['type']);

		// 날짜 디렉토리 만들기
		if (!is_dir($dir_absolute.$month))
		{
			$umask = umask();
			umask(000);
			mkdir($dir_absolute.$month, 0777);
			umask($umask);
		}

		// 파일이름 정의
		$filename = ($files['type'] == 'image/png'
			|| $files['type'] == 'image/x-png'
			|| $files['type'] == 'image/jpg'
			|| $files['type'] == 'image/gif'
			|| $files['type'] == 'image/jpeg'
			|| $files['type'] == 'image/pjpeg') ?
				md5(date('YmdHis').'-'.rand()) . '.jpg' : $files['name']
		;

		// 절대경로 파일주소 정의
		$fileAbsoluteDir = $dir_absolute.$month.'/' . $filename;

		// copy file
		if ($files['tmp_name'])
		{
			move_uploaded_file($files['tmp_name'], $fileAbsoluteDir);
		}
		else
		{
			echo 'no file';
			exit;
		}

		// 상대경로 파일주소 정의
		$fileRelativeDir = $month.'/'.$filename;

		// insert db
		$spawn->insert(array(
			table => $tablesName[tempFiles],
			data => array(
				srl => null,
				loc => $fileRelativeDir,
				name => $originalFileName,
				date => $regdate
			)
		));

		$sess_srl = $spawn->conn->lastInsertId();

		// result print
		$result = array(
			filelink => $dir_relative.$fileRelativeDir,
			loc => $fileRelativeDir,
			filename => $originalFileName,
			sess_srl => $sess_srl
		);
		echo stripslashes(json_encode($result));
		break;


	// remove
	case 'remove':
		$out = '';
		foreach (explode(',', $_POST[data]) as $k=>$v)
		{
			$item = explode(':', $v);
			switch ($item[0])
			{
				case 'modify':
					$tableName = $tablesName[files];
					break;
				case 'session':
					$tableName = $tablesName[tempFiles];
					break;
			}
			$files = $spawn->getItem(array(
				field => 'loc,name',
				table => $tableName,
				where => 'srl='.$item[1]
			));
			$loc = PWD.'/data/original/'.$files[loc];
			if (file_exists($loc))
			{
				unlink($loc);
			}
			$spawn->delete(array(
				table => $tableName,
				where => 'srl='.$item[1]
			));
		}
		echo json_encode(array(
			status => 'success',
		));
		break;
}

$util->out();
exit;
?>