<?php
if(!defined("GOOSE")){exit();}

/**
 * Spawn Class
 * Created on 2014
 * 
 * @author Redgoose (http://redgoose.me)
 */
class Spawn extends Database {
	public $conn;
	private $tablesName;

	/**
	 * init method
	 * 
	 * @param Array $config : 데이터베이스 접속정보 배열값
	 * @return Spawn
	**/
	public function Spawn($config, $tablesName=null)
	{
		$this->tablesName = $tablesName;
		$this->conn = parent::Database($config);
		$this->action("set names utf8");
	}

	/**
	 * Adjustment table name
	 * 
	 * @param 
	 * 
	 */
	private function adjustTableName($str)
	{
		if (isset($this->tablesName) && $str)
		{
			return (isset($this->tablesName[$str])) ? $this->tablesName[$str] : $str;
		}
		else
		{
			return $str;
		}
	}

	/**
	 * Array to sql query
	 * 
	 * @param Array $getArray : 매개변수 배열. 배열 매개변수는 table, art값이 필수로 들어가야한다.
	 * @return String $str : sql 쿼리문자
	**/
	private function arrayToQuery($getArray)
	{
		$getArray['table'] = self::adjustTableName($getArray['table']);

		if (!$getArray['act'] || !$getArray['table'])
		{
			return null;
		}

		$getArray['where'] = (isset($getArray['where'])) ? preg_replace("/^and|and$/", "", $getArray['where']) : '';
		$getArray['limit'] = (isset($getArray['limit'])) ? $getArray['limit'] : false;

		$str = $getArray['act'];
		$str .= ($getArray['field']) ? ' '.$getArray['field'] : ' *';
		$str .= ' from '.$getArray['table'];
		$str .= ($getArray['where']) ? ' where '.$getArray['where'] : '';
		$str .= (isset($getArray['order'])) ? ' order by '.$getArray['order'] : '';
		$str .= (isset($getArray['sort'])) ? ' '.$getArray['sort'] : '';
		$str .= (is_array($getArray['limit'])) ? ' limit '.$getArray['limit'][0].', '.$getArray['limit'][1] : '';
		return $str;
	}

	/**
	 * Disconnect database
	**/
	public function disconnect()
	{
		parent::disconnect();
	}

	/**
	 * Get query
	 * 
	 * @param Array $data : db데이터를 요청하는 매배변수 배열
	 * @return String $str : sql 쿼리문자
	**/
	public function getQuery($data)
	{
		$str = $this->arrayToQuery($data);
		return $str;
	}

	/**
	 * Item insert
	 * 
	 * @param Array $get : 요청 파라메터 배열
	 * @return String : 결과값
	**/
	// insert
	public function insert($get)
	{
		$get['table'] = self::adjustTableName($get['table']);
		if ($get['table'] and $get['data'])
		{
			$result = "insert into $get[table] (";
			$sw = true;
			foreach ($get['data'] as $k=>$v)
			{
				if ($sw == true)
				{
					$sw = false;
					$result .= $k;
				}
				else
				{
					$result .= ','.$k;
				}
			}
			$result .= ') values (';
			$sw = true;
			foreach ($get['data'] as $k=>$v)
			{
				//$v = ($v) ? '\''.$v.'\'' : 'null';
				$v = '\''.$v.'\'';
				if ($sw == true)
				{
					$sw = false;
					$result .= $v;
				}
				else
				{
					$result .= ','.$v;
				}
			}
			$result .= ')';
		}

		if (isset($get['debug']) && $get['debug'] == true)
		{
			return $result;
		}
		else
		{
			return parent::action($result);;
		}
	}

	/**
	 * Item update
	 * 
	 * @param Array $get : 요청 파라메터 배열
	 * @return String : 결과값
	**/
	public function update($get)
	{
		$get['table'] = self::adjustTableName($get['table']);
		if ($get['table'] and $get['data'] and $get['where'])
		{
			$result = "update $get[table] set ";
			$sw = true;
			foreach ($get['data'] as $k=>$v)
			{
				if ($sw)
				{
					$sw = false;
					$result .= $v;
				}
				else
				{
					$result .= ($v) ? ','.$v : '';
				}
			}
			$result .= " where $get[where]";
		}
		if (isset($get['debug']) && $get['debug'] == true)
		{
			return $result;
		}
		else
		{
			return parent::action($result);
		}
	}

	/**
	 * Item delete
	 * 
	 * @param Array $get : 요청 파라메터 배열
	 * @return String : 결과값
	**/
	public function delete($get)
	{
		$get['table'] = self::adjustTableName($get['table']);
		if ($get['table'] and $get['where'])
		{
			$result = "delete from $get[table] where $get[where]";
		}

		if (isset($get['debug']) && $get['debug'] == true)
		{
			return $result;
		}
		else
		{
			return parent::action($result);
		}
	}

	/**
	 * Query action
	 * 
	 * @param String $query : 쿼리문
	 * @return String : 처리 결과값
	 */
	public function action($query)
	{
		return parent::action($query);
	}

	/**
	 * Get items index
	 * 
	 * @param Array $data : 요청 파라메터 배열
	 * @return Array : 결과값
	**/
	public function getItems($data)
	{
		$data['act'] = 'select';
		$data['field'] = (isset($data['field'])) ? $data['field'] : '*';
		$query = $this->arrayToQuery($data);
		if (isset($data['debug']) && $data['debug'] == true)
		{
			var_dump($query);
			return array();
		}
		else
		{
			return parent::getMultiData($query);
		}
	}
	
	/**
	 * Get items data
	 * 
	 * @param Array $data : 요청 파라메터 배열
	 * @return Array : 결과값
	**/
	public function getItem($data)
	{
		$data['act'] = 'select';
		$data['field'] = (isset($data['field'])) ? $data['field'] : '*';
		$query = $this->arrayToQuery($data);
		if (isset($data['debug']) && $data['debug'] == true)
		{
			var_dump($query);
			return null;
		}
		else
		{
			return parent::getSingleData($query);
		}
	}

	/**
	 * Get item count
	 * 
	 * @param Array $data : 요청 파라메터 배열
	 * @return Array : 결과값
	**/
	public function getCount($data)
	{
		$data['act'] = 'select';
		$data['field'] = 'count(*)';
		$query = $this->arrayToQuery($data);
		if (isset($data['debug']) && $data['debug'] == true)
		{
			var_dump($query);
			return null;
		}
		else
		{
			return (int)parent::count($query);
		}
	}
}
?>