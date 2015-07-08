<?php
if (!defined('__GOOSE__')) exit();

/**
 * Module - file
 *
 */
class File {

	public $name, $goose, $layout;

	/**
	 * construct
	 *
	 * @param array $getter
	 */
	public function __construct($getter=array())
	{
		$this->name = $getter['name'];
		$this->goose = $getter['goose'];
		$this->isAdmin = $getter['isAdmin'];
		$this->param = $getter['param'];
		$this->path = $getter['path'];
		$this->set = $getter['set'];

		$this->skinPath = $this->path.'skin/'.$this->set['skin'].'/';
	}

	/**
	 * index method
	 */
	public function index()
	{
		if ($this->param['method'] == 'POST')
		{
			$post = Util::getMethod();

			switch($this->param['action'])
			{
				case 'upload':
					$result = $this->actUploadFiles(
						$_FILES['file'],
						$post['upload_loc'],
						($post['table']) ? $post['table'] : $this->name
					);
					echo Util::arrayToJson($result);
					break;
				case 'remove':
					$data = Util::jsonToArray($post['data'], true);
					$fileSrls = $fileTmpSrls = array();

					foreach($data as $k=>$v)
					{
						if ($v['table'] == 'file_tmp')
						{
							$fileTmpSrls[] = $v['srl'];
						}
						else if ($v['table'] == 'file')
						{
							$fileSrls[] = $v['srl'];
						}
					}

					if (count($fileSrls))
					{
						$this->actRemoveFile($fileSrls, 'file');
					}
					if (count($fileTmpSrls))
					{
						$this->actRemoveFile($fileTmpSrls, 'file_tmp');
					}

					echo json_encode(array( 'state' => 'success' ));
					break;
			}
			Goose::end(false);
		}
		else
		{
			require_once(__GOOSE_PWD__.$this->path.'view.class.php');
			$view = new View($this);
			$view->render();
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
		if (!$file)
		{
			return null;
		}

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
		if (!$name)
		{
			return null;
		}

		// set source
		$src = Array(
			basename($name, strrchr($name, '.')),
			strtolower(substr(strrchr($name, '.'), 1))
		);

		// check file type
		if (!in_array($src[1], $this->set['allowFileType']))
		{
			return null;
		}

		// remove special characters
		$src[0] = Util::removeSpecialChar($src[0]);

		// make random name
		if (!$src[0] || $is_random)
		{
			$src[0] = md5(date('YmdHis').'-'.rand());
		}

		return $src[0].'.'.$src[1];
	}


	/**********************************************
	 * API AREA
	 *********************************************/

	/**
	 * api - get count
	 *
	 * @param array $getParams
	 * @return array
	 */
	public function getCount($getParams=null)
	{
		if ($this->name != 'file') return array( 'state' => 'error', 'message' => '잘못된 객체로 접근했습니다.' );

		// set original parameter
		$originalParam = array('table' => Spawn::getTableName($this->name));

		// get data
		$data = Spawn::count(Util::extendArray($originalParam, $getParams));

		// return data
		return array( 'state' => 'success', 'data' => (int)$data );
	}

	/**
	 * get data index
	 *
	 * @param array $getParams
	 * @return array|null
	 */
	public function getItems($getParams=null)
	{
		if ($this->name != 'file') return array( 'state' => 'error', 'message' => '잘못된 객체로 접근했습니다.' );

		// set original parameter
		$originalParam = array(
			'table' => Spawn::getTableName($this->name),
			'order' => 'srl',
			'sort' => 'desc'
		);

		// get data
		$data = Spawn::items(Util::extendArray($originalParam, $getParams));
		if (!count($data)) return array( 'state' => 'error', 'message' => '데이터가 없습니다.' );

		// return data
		return array( 'state' => 'success', 'data' => $data );
	}

	/**
	 * get data item
	 *
	 * @param array $getParam
	 * @return array|null
	 */
	public function getItem($getParam=array())
	{
		if ($this->name != 'file') return array( 'state' => 'error', 'message' => '잘못된 객체로 접근했습니다.' );

		// set original parameter
		$originalParam = array( 'table' => Spawn::getTableName($this->name) );

		// get data
		$data = Spawn::item(Util::extendArray($originalParam, $getParam));

		// check data
		if (!$data) return array( 'state' => 'error', 'message' => '데이터가 없습니다.' );

		// return data
		return array( 'state' => 'success', 'data' => $data );
	}

	/**
	 * api - action upload files
	 * 다수의 파일을 업로드한다. 데이터페이스에 있는 정보도 추가한다.
	 *
	 * @param array $file 파일목록($_FILES['name'])
	 * @param string|null $dir 업로드 디렉토리
	 * @param string $table 업로드 테이블 (file|file_tmp)
	 * @param int $article_srl 마지막 article번호. 테이블이 file_tmp라면 필요없음
	 * @return array
	 */
	public function actUploadFiles($file=Array(), $dir=null, $table='', $article_srl=null)
	{
		if ($this->name != 'file') return array( 'state' => 'error', 'message' => '잘못된 객체로 접근했습니다.' );
		if (!$this->isAdmin) return array('state' => 'error', 'message' => '권한이 없습니다.');

		// check table
		if ($table != 'file' && $table !='file_tmp')
		{
			return array('state' => 'error', 'message' => '$table값이 잘못되었습니다.');
		}

		// check upload file
		if (!$file['name'] || (is_array($file['name']) && !$file['name'][0]))
		{
			return array('state' => 'error', 'action' => 'back', 'message' => 'not found file');
		}

		// string to array
		if (!is_array($file['name']))
		{
			$file['error'] = Array($file['error']);
			$file['name'] = Array($file['name']);
			$file['size'] = Array($file['size']);
			$file['tmp_name'] = Array($file['tmp_name']);
			$file['type'] = Array($file['type']);
		}

		// set variable
		$result = Array();
		$month = Date('Ym');

		// set path
		$path = ($dir) ? $dir : $this->set['upPath_original'].'/';
		$path_absolute = __GOOSE_PWD__.$path;

		// make directory
		if (!is_dir($path_absolute.$month))
		{
			Util::createDirectory($path_absolute.$month, 0777);
		}

		// action upload
		for ($i=0; $i<count($file['name']); $i++)
		{
			if ($file['error'][$i])
			{
				$result[] = Array('state' => 'error', 'message' => $file['error'][$i]);
				continue;
			}

			if ($file['size'][$i] > $this->set['limitFileSize'])
			{
				$result[] = Array('state' => 'error', 'message' => 'The attachment size exceeds the allowable limit.');
				continue;
			}

			// check filename
			$file['name'][$i] = $this->checkFilename($file['name'][$i], false);
			if (!$file['name'][$i])
			{
				$result[] = Array('state' => 'error', 'message' => 'This file is a format that is not allowed.');
				continue;
			}

			// check exist file
			$file['name'][$i] = $this->checkExistFile($path_absolute.$month.'/', $file['name'][$i], null);

			// copy file
			if ($file['tmp_name'][$i] && is_dir($path_absolute.$month.'/'))
			{
				move_uploaded_file($file['tmp_name'][$i], $path_absolute.$month.'/'.$file['name'][$i]);
			}
			else
			{
				$result[] = Array('state' => 'error', 'message' => 'upload error');
				continue;
			}

			// insert data
			if ($table == 'file')
			{
				$db_result = Spawn::insert(array(
					'table' => Spawn::getTableName($table),
					'data' => array(
						'srl' => null,
						'article_srl' => $article_srl,
						'name' => $file['name'][$i],
						'loc' => $path.$month.'/'.$file['name'][$i],
						'type' => $file['type'][$i],
						'size' => (int)$file['size'][$i],
						'regdate' => date("YmdHis")
					)
				));
			}
			else if ($table == 'file_tmp')
			{
				$db_result = Spawn::insert(array(
					'table' => Spawn::getTableName($table),
					'data' => array(
						'srl' => null,
						'name' => $file['name'][$i],
						'loc' => $path.$month.'/'.$file['name'][$i],
						'type' => $file['type'][$i],
						'size' => (int)$file['size'][$i],
						'regdate' => date("YmdHis")
					)
				));
			}
			else
			{
				$db_result = null;
			}
			if ($db_result != 'success')
			{
				// remove file
				if (file_exists($path_absolute.$month.'/'.$file['name'][$i]))
				{
					@unlink($path_absolute.$month.'/'.$file['name'][$i]);
				}
				$result[] = Array(
					'state' => 'error',
					'message' => 'Fail execution database'
				);
			}
			else
			{
				$result[] = array(
					'state' => 'success',
					'loc' => $path.$month.'/'.$file['name'][$i],
					'name' => $file['name'][$i],
					'size' => $file['size'][$i],
					'type' => $file['type'][$i],
					'srl' => (int)Spawn::getLastIdx()
				);
			}
		}

		return $result;
	}

	/**
	 * api - action remove files
	 * 파일삭제, 데이터베이스에 있는 정보도 삭제한다.
	 *
	 * @param array $srls file srl
	 * @param string|null $table module name
	 * @return array
	 */
	public function actRemoveFile($srls=Array(), $table=null)
	{
		if ($this->name != 'file') return Array( 'state' => 'error', 'message' => '잘못된 객체로 접근했습니다.' );
		if (!$this->isAdmin) return Array('state' => 'error', 'message' => '권한이 없습니다.');
		if (!$table) return Array('state' => 'error', 'message' => '$table값이 없습니다.');

		// set result
		$result = Array();

		// action
		for ($i=0; count($srls)>$i; $i++)
		{
			// get data
			$data = $this->getItem(Array(
				'table' => Spawn::getTableName($table),
				'where' => 'srl='.$srls[$i]
			));
			$data = ($data['state'] == 'success') ? $data['data'] : null;

			if (file_exists(__GOOSE_PWD__.$data['loc']))
			{
				@unlink(__GOOSE_PWD__.$data['loc']);
			}

			// remove data
			$result_db = Spawn::delete(Array(
				'table' => Spawn::getTableName($table),
				'where' => 'srl='.$data['srl']
			));
			if ($result_db != 'success')
			{
				$result[] = Array(
					'state' => 'error',
					'message' => 'Fail execution database'
				);
			}
			else
			{
				$result[] = Array('state' => 'success');
			}
		}

		return $result;
	}

	/**
	 * api - action file_tmp to file
	 * file_tmp에 있는 db데이터를 file로 옮긴다.
	 *
	 * @param array $file_tmp_srls
	 * @param int|null $article_srl
	 * @return array
	 */
	public function actDBFiletmpToFile($file_tmp_srls=Array(), $article_srl=null)
	{
		if ($this->name != 'file') return Array( 'state' => 'error', 'message' => '잘못된 객체로 접근했습니다.' );
		if (!$this->isAdmin) return Array('state' => 'error', 'message' => '권한이 없습니다.');

		foreach ($file_tmp_srls as $k=>$v)
		{
			$data = $this->getItem(array(
				'table' => Spawn::getTableName('file_tmp'),
				'where' => 'srl='.(int)$v
			));
			$tmpData = ($data['state'] == 'success') ? $data['data'] : null;

			$db_result = Spawn::insert(array(
				'table' => Spawn::getTableName('file'),
				'data' => array(
					'srl' => null,
					'article_srl' => $article_srl,
					'name' => $tmpData['name'],
					'loc' => $tmpData['loc'],
					'type' => $tmpData['type'],
					'size' => (int)$tmpData['size'],
					'regdate' => $tmpData['regdate']
				)
			));
			if ($db_result != 'success') return Array('state' => 'error', 'message' => '[file] DB : Insert Error');

			$db_result2 = Spawn::delete(array(
				'table' => Spawn::getTableName('file_tmp'),
				'where' => 'srl='.(int)$v
			));
			if ($db_result2 != 'success') return Array('state' => 'error', 'message' => '[file_tmp] DB : remove Error');
		}

		return Array('state' => 'success', 'message' => 'complete');
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
		Util::createDirectory(__GOOSE_PWD__.$this->set['upPath_upload'], 0777);
		Util::createDirectory(__GOOSE_PWD__.$this->set['upPath_original'], 0777);
		Util::createDirectory(__GOOSE_PWD__.$this->set['upPath_make'], 0777);

		$query = Spawn::arrayToCreateTableQuery(array(
			'tableName' => __dbPrefix__.$this->name.'_tmp',
			'fields' => $installData['field_tmp']
		));
		$queryResult = Spawn::action($query);

		$query2 = Spawn::arrayToCreateTableQuery(array(
			'tableName' => __dbPrefix__.$this->name,
			'fields' => $installData['field']
		));
		$query2Result = Spawn::action($query2);

		if ($queryResult == 'success' && $query2Result == 'success')
		{
			return 'success';
		}
		else
		{
			return $queryResult.', '.$query2Result;
		}
	}

}
