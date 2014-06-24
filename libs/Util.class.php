<?
if(!defined("GOOSE")){exit();}

class Util {
	var $start_time;

	public function Util()
	{
		$this->start_time = array_sum(explode(' ', microtime()));
	}

	private function typeCheck($obj)
	{
		return (is_array($obj)) ? json_encode($obj) : "'" . $obj . "'";
	}

	// error
	public function error($code=404)
	{
		switch($code)
		{
			case 404:
				echo "page not found : 404";
				break;

			default:
				echo $code;
				break;
		}
	}

	// console.log
	public function console($obj)
	{
		echo "<script type='text/javascript'>console.log(" . $this->typeCheck($obj) . ");</script>";
	}

	// alert message
	public function alert($str)
	{
		echo "<script type='text/javascript'>if('$str'){alert('$str');}</script>";
	}

	// go to back
	public function back($str)
	{
		echo ("<script type='text/javascript'>if('$str'){alert('$str');};history.back();</script>");
	}

	// redirect url
	public function redirect($loc=null, $msg=null)
	{
		echo "<script type='text/javascript'>";
		echo ($msg) ? "alert('$msg');" : "";
		echo ($loc) ? "location.href='$loc';" : "";
		echo "</script>";
	}

	/**
	 * Convert date
	 * db날짜값을 날짜형태로 변환시켜준다.
	 * 
	 * @param String $str : db의 날짜데이터
	 * @return String $result : 날짜값 리턴 (0000-00-00)
	 */
	public function convertDate($str="00000000000000")
	{
		$o = substr($str, 0, 8);
		$result .= substr($o, 0, 4)."-";
		$result .= substr($o, 4, 2)."-";
		$result .= substr($o, 6, 8);
		return $result;
	}

	/**
	 * Convert time
	 * db날짜값을 시간형태로 변환시켜준다.
	 * 
	 * @param String $str : db의 날짜데이터
	 * @return String $result : 시간값 리턴 (00:00)
	 */
	public function convertTime($str="00000000000000")
	{
		$o = substr($str, 8);
		$result = substr($o, 0, 2).":";
		$result .= substr($o, 2, 2);
		return $result;
	}

	/**
	 * Create directory
	 * 
	 * @param String $loc : 위치주소
	 * @param Number $permission : 권한번호
	 * @return void
	 */
	public function createDirectory($loc=null, $permission)
	{
		if (!is_dir($loc))
		{
			$umask = umask();
			umask(000);
			mkdir($loc, $permission);
			umask($umask);
		}
	}

	/**
	 * EXIT
	 * 페이지를 종료한다. db연결을 끊어주는 역할을 하고, 처리시간을 확인할 수 있는 옵션이 있다.
	 * 
	 * @param Boolean $timeCheck : true값으로 넣으면 처리시간 로그가 출력된다.
	 * @return void
	*/
	public function out($timeCheck=false)
	{
		global $spawn;
		if ($spawn)
		{
			$spawn->disconnect();
		}
		if ($timeCheck)
		{
			$end_time = array_sum(explode(' ', microtime()));
			echo "<hr/>\n\nTIME : ".($end_time - $this->start_time);
		}
		exit;
	}

	/**
	 * File open
	 * 파일을 읽거나 생성하거나 내용추가, 내용을 덮어버리는 기능을 한다.
	 * 
	 * @param String $dir : 파일경로
	 * @param String $method : 조작방식(a:기존 데이터 추가, w:새로작성, r:내용읽기)
	 * @param String $str : 추가하거나 수정할 내용
	 * @param Number $permission : 퍼미션. ex)777
	 * @return String : $method값이 'r'이면 파일내용이 출력되고, 'w'나 'a'이면 처리 결과값이 출력된다.
	 */
	public function fop($dir=null, $method=null, $str=null, $permission=null)
	{
		$file = fopen($dir, $method) or die('file open fail');
		if ($method == 'r')
		{
			$result = fread($file, 1000000);
			fclose($file);
			return $result;
		}
		else
		{
			fwrite($file, $str);
			if ($permission)
			{
				chmod($dir, $permission);
			}
			fclose($file);
			return "success";
		}
	}

	// 디렉토리 목록 가져오기
	public function readDir($str=null)
	{
		$result = array();
		if (is_dir($str))
		{
			$dir = opendir($str);
			
			while($item = readdir($dir))
			{
				if ($item != "." && $item != ".." && is_dir($str.$item)) {
					array_push($result, $item);
				}
			}
		}
		return $result;
	}

	// 배열속에 필수값이 들어있는지 확인
	public function checkExistValue($target, $required=null)
	{
		if ($required)
		{
			foreach ($required as $k=>$v)
			{
				if (array_key_exists($v, $target) && !$target[$v])
				{
					return $v;
				}
			}
		}
		return false;
	}

	/**
	 * Create user file value
	 * 
	 * @return String : user.php 내용
	 */
	public function createUserFile($post, $dir)
	{
		global $root, $url;
	
		$str = "<?php\n";
		$str .= "if(!defined(\"GOOSE\")){exit();}\n";
		$str .= "\n";
		$str .= "define('ROOT', '".$post['root']."');\n";
		$str .= "define('URL', '".$post['url']."');\n";
		$str .= "\n";
		$str .= "\$dbConfig = array('mysql:dbname=".$post['dbName'].";host=".$post['dbHost']."', '".$post['dbId']."', '".$post['dbPassword']."');\n";
		$str .= "\$tablesName = array(\n";
		$str .= "\t'articles' => '".$post['dbPrefix']."articles',\n";
		$str .= "\t'categories' => '".$post['dbPrefix']."categories',\n";
		$str .= "\t'extraKey' => '".$post['dbPrefix']."extraKey',\n";
		$str .= "\t'extraVar' => '".$post['dbPrefix']."extraVar',\n";
		$str .= "\t'files' => '".$post['dbPrefix']."files',\n";
		$str .= "\t'users' => '".$post['dbPrefix']."users',\n";
		$str .= "\t'nestGroups' => '".$post['dbPrefix']."nestGroups',\n";
		$str .= "\t'nests' => '".$post['dbPrefix']."nests',\n";
		$str .= "\t'tempFiles' => '".$post['dbPrefix']."tempFiles',\n";
		$str .= "\t'jsons' => '".$post['dbPrefix']."jsons'\n";
		$str .= ");\n";
		$str .= "\$api_key = \"".$post['apiPrefix']."\";\n";
		$str .= "\$adminLevel = \"".$post['adminLevel']."\";\n";
		$str .= "?>";

		return $this->fop($dir, 'w', $str);
	}
}
?>