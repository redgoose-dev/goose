<?php
namespace mod\File;
use core, mod;
if (!defined('__GOOSE__')) exit();


class File {

	public $name, $set, $params, $isAdmin;
	public $path, $skinPath, $skinAddr;

	public function __construct($params=[])
	{
		core\Module::initModule($this, $params);
	}

	/**
	 * index method
	 */
	public function index()
	{
		if ($this->params['method'] == 'POST')
		{
			$post = core\Util::getMethod();

			switch($this->params['action'])
			{
				case 'upload':
					$result = $this->actUploadFiles(
						$_FILES['file'],
						($post['uploadPath']) ? $post['uploadPath'] : null,
						($post['article_srl']) ? $post['article_srl'] : null,
						($post['ready']) ? $post['ready'] : 0
					);
					echo core\Util::arrayToJson($result);
					break;

				case 'remove':
					$data = core\Util::jsonToArray($post['data'], true);
					$fileSrls = $fileTmpSrls = [];

					foreach($data as $k=>$v)
					{
						$fileSrls[] = $v['srl'];
					}

					$result = $this->actRemoveFile($fileSrls);
					echo json_encode($result);
					break;
			}
		}
		else
		{
			$view = new View($this);

			switch ($this->params['action'])
			{
				default:
					$view->view_index();
					break;
			}
		}
	}

	/**
	 * check exist file
	 * 파일이름이 같은것이 있다면 이름뒤에 "-{x}"키워드를 붙입니다.
	 * 중복되는 이름이 있다면 x값을 올려서 붙입니다.
	 *
	 * @param string $dir
	 * @param string $file
	 * @param number|null $n
	 * @return string
	 */
	private function checkExistFile($dir='', $file='', $n=null)
	{
		if (!$file) return null;

		if (is_null($n))
		{
			$n = 0;
			$newFilename = $file;
		}
		else
		{
			$n = $n + 1;
			$newFilename = basename($file, strrchr($file, '.')) . '-' . $n . '.' . substr(strrchr($file, '.'), 1);
		}

		if (file_exists($dir . $newFilename))
		{
			return $this->checkExistFile($dir, $file, $n);
		}
		else
		{
			return $newFilename;
		}
	}

	/**
	 * check file name
	 * 파일이름에 특수문자나 공백이 들어가있으면 제거하고, 허용하는 파일타입만 이름을 반환한다.
	 *
	 * @param string $name
	 * @param boolean $is_random 파일 이름을 랜덤값으로 대입해서 반환
	 * @return string
	 */
	private function checkFilename($name=null, $is_random=false)
	{
		if (!$name) return null;

		// set source
		$src = [
			basename($name, strrchr($name, '.')),
			strtolower(substr(strrchr($name, '.'), 1))
		];

		// check file type
		if (!in_array($src[1], $this->set['allowFileType'])) return null;

		// remove special characters
		$src[0] = core\Util::removeSpecialChar($src[0]);

		// make random name
		if (!$src[0] || $is_random)
		{
			$src[0] = md5(date('YmdHis') . '-' . rand());
		}

		return $src[0] . '.' . $src[1];
	}


	/**********************************************
	 * API AREA
	 *********************************************/

	/**
	 * api - action upload files
	 * 다수의 파일을 업로드한다. 데이터페이스에 있는 정보도 추가한다.
	 *
	 * @param array $file 파일목록($_FILES['name'])
	 * @param string|null $dir 업로드 디렉토리
	 * @param int $article_srl 마지막 article번호
	 * @param int $ready 대기상태(0:사용하기, 1:대기)
	 * @return array
	 */
	public function actUploadFiles($file=[], $dir=null, $article_srl=null, $ready=0)
	{
		if ($this->name != 'File') return [ 'state' => 'error', 'message' => '잘못된 객체로 접근했습니다.' ];

		// check upload file
		if (!$file['name'] || (is_array($file['name']) && !$file['name'][0]))
		{
			return [ 'state' => 'error', 'action' => 'back', 'message' => 'not found file' ];
		}

		// string to array
		if (!is_array($file['name']))
		{
			$file['error'] = [ $file['error'] ];
			$file['name'] = [ $file['name'] ];
			$file['size'] = [ $file['size'] ];
			$file['tmp_name'] = [ $file['tmp_name'] ];
			$file['type'] = [ $file['type'] ];
		}

		// set variable
		$result = [];
		$month = date('Ym');

		// set path
		$path = ($dir) ? $dir : $this->set['upPath_original'] . '/';
		$path_absolute = __GOOSE_PWD__ . $path;

		// make directory
		if (!is_dir($path_absolute . $month))
		{
			core\Util::createDirectory($path_absolute . $month, 0777);
		}

		// action upload
		for ($i=0; $i<count($file['name']); $i++)
		{
			if ($file['error'][$i])
			{
				$result[] = [ 'state' => 'error', 'message' => $file['error'][$i] ];
				continue;
			}

			if ($file['size'][$i] > $this->set['limitFileSize'])
			{
				$result[] = [
					'state' => 'error',
					'message' => 'The attachment size exceeds the allowable limit.'
				];
				continue;
			}

			// check filename
			$file['name'][$i] = $this->checkFilename($file['name'][$i], false);
			if (!$file['name'][$i])
			{
				$result[] = [
					'state' => 'error',
					'message' => 'This file is a format that is not allowed.'
				];
				continue;
			}

			// check exist file
			$file['name'][$i] = $this->checkExistFile($path_absolute . $month . '/', $file['name'][$i], null);

			// copy file
			if ($file['tmp_name'][$i] && is_dir($path_absolute . $month . '/'))
			{
				move_uploaded_file($file['tmp_name'][$i], $path_absolute . $month . '/' . $file['name'][$i]);
			}
			else
			{
				$result[] = [ 'state' => 'error', 'message' => 'upload error' ];
				continue;
			}

			// insert data
			$db_result = core\Spawn::insert([
				'table' => core\Spawn::getTableName($this->name),
				'data' => [
					'srl' => null,
					'article_srl' => $article_srl,
					'name' => $file['name'][$i],
					'loc' => $path . $month . '/' . $file['name'][$i],
					'type' => $file['type'][$i],
					'size' => (int)$file['size'][$i],
					'regdate' => date("YmdHis"),
					'ready' => (int)$ready
				]
			]);
			if ($db_result != 'success')
			{
				// remove file
				if (file_exists($path_absolute . $month . '/' . $file['name'][$i]))
				{
					@unlink($path_absolute . $month . '/' . $file['name'][$i]);
				}
				$result[] = [
					'state' => 'error',
					'message' => 'Fail execution database'
				];
			}
			else
			{
				$result[] = [
					'state' => 'success',
					'loc' => $path . $month . '/' . $file['name'][$i],
					'name' => $file['name'][$i],
					'size' => $file['size'][$i],
					'type' => $file['type'][$i],
					'srl' => (int)core\Spawn::getLastIdx(),
					'ready' => (int)$ready
				];
			}
		}

		return $result;
	}

	/**
	 * api - action remove files
	 * 파일삭제, 데이터베이스에 있는 정보도 삭제한다.
	 *
	 * @param array $srls file srl
	 * @return array
	 */
	public function actRemoveFile($srls=[])
	{
		if ($this->name != 'File') return [ 'state' => 'error', 'message' => '잘못된 객체로 접근했습니다.' ];

		// set result
		$error = false;
		$errorMessage = '';

		// action
		for ($i=0; count($srls)>$i; $i++)
		{
			// get data
			$data = core\Spawn::item([
				'table' => core\Spawn::getTableName($this->name),
				'where' => 'srl=' . $srls[$i]
			]);

			if (file_exists(__GOOSE_PWD__ . $data['loc']))
			{
				@unlink(__GOOSE_PWD__ . $data['loc']);
			}

			// remove data
			$result_db = core\Spawn::delete([
				'table' => core\Spawn::getTableName($this->name),
				'where' => 'srl=' . $data['srl']
			]);
			if ($result_db != 'success')
			{
				$error = true;
				$errorMessage = 'Fail execution database';
			}
		}

		if ($error)
		{
			return [ 'state' => 'error', 'message' => $errorMessage ];
		}
		else
		{
			return [ 'state' => 'success' ];
		}
	}


	/**********************************************
	 * INSTALL AREA
	 *********************************************/

	/**
	 * install
	 *
	 * @param array $installData install.json 데이터
	 * @return string 문제없으면 "success" 출력한다.
	 */
	public function install($installData)
	{
		core\Util::createDirectory(__GOOSE_PWD__.$this->set['upPath_upload'], 0777);
		core\Util::createDirectory(__GOOSE_PWD__.$this->set['upPath_original'], 0777);
		core\Util::createDirectory(__GOOSE_PWD__.$this->set['upPath_make'], 0777);

		$query = core\Spawn::arrayToCreateTableQuery([
			'tableName' => core\Spawn::getTableName($this->name),
			'fields' => $installData
		]);
		$queryResult = core\Spawn::action($query);

		if ($queryResult == 'success')
		{
			return 'success';
		}
		else
		{
			return $queryResult;
		}
	}
}