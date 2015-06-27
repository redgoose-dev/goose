<?php
if (!defined('__GOOSE__')) exit();

/**
 * Util class
 *
 */

class Util {

	const tagScriptHead = "<script>";
	const tagScriptFoot = "</script>";

	/**
	 * open file
	 *
	 * @param string $pwd
	 * @return string
	 */
	public static function openFile($pwd)
	{
		return (file_exists($pwd)) ? file_get_contents($pwd) : null;
	}

	/**
	 * Alert message
	 *
	 * @param string $msg
	 */
	public static function alert($msg)
	{
		echo self::tagScriptHead."alert('$msg');".self::tagScriptFoot;
	}

	/**
	 * go to back
	 *
	 * @param string $msg alert message
	 */
	public static function back($msg=null)
	{
		echo self::tagScriptHead;
		echo ($msg) ? "alert('$msg');" : "";
		echo "history.back();";
		echo self::tagScriptFoot;
	}

	/**
	 * redirect url
	 *
	 * @param string $loc 이동할 페이지 주소
	 * @param string $msg 페이지 이동할때 alert으로 나올 메세지
	 */
	public static function redirect($loc=null, $msg=null)
	{
		echo self::tagScriptHead;
		echo ($msg) ? "alert('$msg');" : "";
		echo ($loc) ? "location.href='$loc';" : "";
		echo self::tagScriptFoot;
	}

	/**
	 * reload page
	 */
	public static function reload()
	{
		echo self::tagScriptHead;
		echo "location.reload();";
		echo self::tagScriptFoot;
	}

	/**
	 * array to json
	 *
	 * @param array $array
	 * @param boolean $urlEncode
	 * @return string
	 */
	public static function arrayToJson($array, $urlEncode=false)
	{
		try {
			if ($urlEncode)
			{
				return urlencode(json_encode($array));
			}
			else
			{
				return json_encode($array);
			}
		} catch(Exception $e) {
			return null;
		}
	}

	/**
	 * json to array
	 *
	 * @param string $json
	 * @param boolean $type
	 * @param boolean $urlDecode
	 * @return array
	 */
	public static function jsonToArray($json, $type=null, $urlDecode=false)
	{
		try {
			if ($urlDecode)
			{
				return json_decode(urldecode($json), ($type) ? $type : true);
			}
			else
			{
				return json_decode($json, ($type) ? $type : true);
			}
		} catch(Exception $e) {
			return null;
		}
	}

	/**
	 * check type (배열이나 객체를 javascript object나 배열 형태로 변환)
	 *
	 * @param * $obj 데이터
	 * @return string javascript 형태의 데이터로 리턴
	 */
	private function typeCheck($obj)
	{
		if (is_object($obj))
		{
			return Util::arrayToJson(get_object_vars($obj));
		}
		else if (is_array($obj))
		{
			return Util::arrayToJson($obj);
		}
		else
		{
			return "'" . $obj . "'";
		}
	}

	/**
	 * console.log (javascript console.log)
	 *
	 * @param string $obj console.log에 표시할 데이터. 문자,번호,배열... 등등 값을 확인할 수 있다.
	 */
	public static function console($obj)
	{
		echo self::tagScriptHead;
		echo "console.log(" . self::typeCheck($obj) . ");";
		echo self::tagScriptFoot;
	}

	/**
	 * 랜덤 문자열이나 숫자 만들기
	 *
	 * @param number $length 문자길이
	 * @param string $type 출력타입(text, number, special). 값이 없으면 모든타입 출력
	 * @return string
	 */
	public static function generateRandomString($length, $type=null)
	{
		$str = '';
		$str .= ($type != 'text' && $type != 'special') ? '0123456789' : '';
		$str .= ($type != 'number' && $type != 'special') ? 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' : '';
		$str .= ($type != 'text' && $type != 'number') ? '-=~!@#$%^&*_+,./<>?;:|' : '';
		$result = '';
		$n = $length;
		while ($n--)
		{
			$result .= $str[mt_rand(0, strlen($str))];
		}
		return $result;
	}

	/**
	 * Convert date
	 * db의 날짜값을 "YYYY-MM-DD"형태로 변환시켜준다.
	 *
	 * @param string $str
	 * @return string $result
	 */
	public static function convertDate($str="00000000000000")
	{
		$result = '';
		$o = substr($str, 0, 8);
		$result .= substr($o, 0, 4)."-";
		$result .= substr($o, 4, 2)."-";
		$result .= substr($o, 6, 8);

		return $result;
	}

	/**
	 * Convert time
	 * db의 날짜값을 "HH:MM"형태로 변환시켜준다.
	 *
	 * @param string $str
	 * @return string $result
	 */
	public static function convertTime($str="00000000000000")
	{
		$o = substr($str, 8);
		$result = substr($o, 0, 2).":";
		$result .= substr($o, 2, 2);

		return $result;
	}

	/**
	 * Create directory
	 *
	 * @param string $loc 위치주소
	 * @param int $permission 권한번호
	 */
	public static function createDirectory($loc=null, $permission)
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
	 * File open
	 * 파일을 읽거나 생성하거나 내용추가, 내용을 덮어버리는 기능을 한다.
	 *
	 * @param string $dir : 파일경로
	 * @param string $method : 조작방식(a:기존 데이터 추가, w:새로작성, r:내용읽기)
	 * @param string $str : 추가하거나 수정할 내용
	 * @param int $permission : 퍼미션. ex)0777
	 * @return string : $method값이 'r'이면 파일내용이 출력되고, 'w'나 'a'이면 처리 결과값이 출력된다.
	 */
	public static function fop($dir=null, $method=null, $str=null, $permission=null)
	{
		if (!file_exists($dir) && $method != 'w')
		{
			return false;
		}

		$file = fopen($dir, $method) or die('file open fail');

		if ($method == 'r')
		{
			$result = fread($file, (filesize($dir) > 1) ? filesize($dir) : 10);
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

	/**
	 * check user file
	 * "filename.user.php"형식으로 된 파일을 우선적으로 리턴해주는 메서드
	 *
	 * @param string $file
	 * @return string
	 */
	public static function checkUserFile($file=null)
	{
		$exet = strrchr($file,".");
		$loc = preg_replace('/'.$exet.'$/', '', $file);

		if (file_exists($loc.'.user'.$exet))
		{
			return $loc.'.user'.$exet;
		}
		else if (file_exists($file))
		{
			return $file;
		}
		else
		{
			return null;
		}
	}

	/**
	 * get directory
	 * 하위 디렉토리 목록을 가져온다.
	 *
	 * @param string $path
	 * @param string $keyName
	 * @return array
	 */
	public static function getDir($path=null, $keyName=null)
	{
		$arr = array();
		$dirs = dir($path);
		while(false !== ($entry = $dirs->read()))
		{
			if(($entry != '.') && ($entry != '..'))
			{
				if(is_dir($path.'/'.$entry))
				{
					if ($keyName)
					{
						$arr[][$keyName] = $entry;
					}
					else
					{
						$arr[] = $entry;
					}
				}
			}
		}
		$dirs->close();
		return $arr;
	}

	/**
	 * get directory
	 * 하위 파일목록을 가져온다.
	 *
	 * @param string $path
	 * @return array
	 */
	public static function getFiles($path=null)
	{
		$results = array();
		if(!($path = opendir($path)))
		{
			return null;
		}
		while ($file = readdir($path))
		{
			if (is_dir($file) != '1' && !preg_match('/^\./', $file)) {
				$results[] = array(
					'filename' => $file
				);
			}
		}
		return $results;
	}

	/**
	 * get parameter
	 * GET, POST 파라메터값이 존재하면 가져온다.
	 *
	 * @param string $get
	 * @return string
	 */
	public static function getParameter($get)
	{
		if ($_POST[$get])
		{
			return $_POST[$get];
		}
		else if ($_GET[$get])
		{
			return $_GET[$get];
		}
		else
		{
			return null;
		}
	}

	/**
	 * check exist value
	 * 배열속에 필수값이 들어있는지 확인
	 *
	 * @param array $target : 확인할 배열
	 * @param array $required : 키값이 들어있는 배열
	 * @return string $required에 있는 이름이 $target키에 없으면 $required이름 리턴. 모두 있으면 null
	 */
	public static function checkExistValue($target, $required=null)
	{
		if ($required)
		{
			foreach ($required as $k=>$v)
			{
				if (!array_key_exists($v, $target) || !$target[$v])
				{
					return $v;
				}
			}
		}
		return null;
	}

	/**
	 * extend array
	 * A에 있는 배열의 내용을 B에 합친다.
	 *
	 * @param array $original 합치는 대상
	 * @param array $source 합치는 내용
	 * @return array
	 */
	public static function extendArray($original=array(), $source=array())
	{
		return ($source && is_array($source)) ? $source + $original : $original;
	}

	/**
	 * is dir
	 * 디렉토리가 존재하는지 검사하고 있는 경로를 반환한다.
	 *
	 * @param string $addr '/dir/{dir}/'형식으로 입력할 수 있다. {dir} 키워드 위치에 다음 인자를 삽입해서 검사한다.
	 * @param string $dir1 first dir
	 * @param string $dir2 second dir
	 * @param string $prefix
	 * @return string
	 */
	public static function isDir($addr=null, $dir1=null, $dir2=null, $prefix=null)
	{
		if (!$addr) $addr = '{dir}';

		$dir1 = ($dir1) ? str_replace('{dir}', $dir1, $addr) : null;
		$dir2 = ($dir2) ? str_replace('{dir}', $dir2, $addr) : null;

		if ($dir1 && is_dir($prefix.$dir1))
		{
			return $dir1;
		}
		else if ($dir2 && is_dir($prefix.$dir2))
		{
			return $dir2;
		}
		else
		{
			return '';
		}
	}

	/**
	 * is file
	 * 파일이 존재하는지 확인한다.
	 *
	 * @param array $files 파일목록
	 * @return string|null
	 */
	public static function isFile($files=array())
	{
		foreach($files as $k=>$v)
		{
			if (file_exists($v))
			{
				return $v;
			}
		}
		return null;
	}

	/**
	 * remove special characters
	 * 특수문자를 제거한다.("-"와 "_"는 제외) 공백은 "_"로 변환한다.
	 *
	 * @param array $str
	 * @return string
	 */
	public static function removeSpecialChar($str=null)
	{
		if (!$str) return '';
		$str = preg_replace("/\s+/", "_", $str);
		$str = preg_replace ("/[#\&\+%@=\/\\\:;,\.'\"\^`~|\!\?\*$#<>()\[\]\{\}]/i", "", $str);
		return $str;
	}

	/**
	 * get file size
	 * 파일 사이즈 단위값을 가져온다.
	 *
	 * @param int $size
	 * @param int $float
	 * @return string
	 */
	public static function getFileSize($size, $float=0)
	{
		$unit = array('Byte', 'KB', 'MB', 'GB', 'TB');
		for ($L = 0; intval($size / 1024) > 0; $L++, $size/= 1024);
		if (($float === 0) && (intval($size) != $size)) $float = 2;
		return number_format($size, $float, '.', ',') .''. $unit[$L];
	}

	/**
	 * get method
	 * $_POST나 $_GET 중에 값이 값이 있으면 가져온다.
	 *
	 * @return array
	 */
	public static function getMethod()
	{
		return ($_SERVER['REQUEST_METHOD'] == 'POST') ? $_POST : $_GET;
	}

	/**
	 * get extension
	 * 파일 확장자를 가져온다.
	 *
	 * @param string $file
	 * @return string
	 */
	public static function getExtension($file)
	{
		return pathinfo($file)['extension'];
	}
}