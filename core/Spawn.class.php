<?php
namespace core;
use PDO;

/**
 * Spawn (database)
 *
 * @property PDO $this->db
 */
class Spawn {

	public $db;

	/**
	 * init and db connect
	 */
	public function __construct()
	{

	}

	/**
	 * connect database
	 *
	 * @param array $config
	 */
	public function connect($config)
	{
		try {
			$this->db = new PDO($config[0], $config[1], $config[2]);
			self::action("set names utf8");
		} catch (\PDOException $e) {
			echo 'Connection failed: '.$e->getMessage();
			die();
		}
	}

	/**
	 * disconnect database
	 */
	public function disconnect()
	{
		$this->db = null;
	}

	/**
	 * Array to sql query
	 *
	 * @param array $getArray 매개변수 배열. 배열 매개변수는 table, art값이 필수로 들어가야한다.
	 * @return String $str sql 쿼리내용
	 **/
	private function arrayToSelectQuery($getArray)
	{
		if (!$getArray['act'] || !$getArray['table'])
		{
			return null;
		}

		$getArray['where'] = (isset($getArray['where'])) ? preg_replace("/^and|and$/", "", $getArray['where']) : '';

		$str = $getArray['act'];
		$str .= ($getArray['field']) ? ' ' . $getArray['field'] : ' *';
		$str .= ' from ' . $getArray['table'];
		$str .= ($getArray['where']) ? ' where ' . $getArray['where'] : '';
		$str .= (isset($getArray['order'])) ? ' order by ' . $getArray['order'] : '';
		$str .= (isset($getArray['sort'])) ? ' ' . $getArray['sort'] : '';
		if (isset($getArray['limit']))
		{
			if (is_array($getArray['limit']))
			{
				$getArray['limit'][0] = ($getArray['limit'][0]) ? $getArray['limit'][0] : 0;
				$getArray['limit'][1] = ($getArray['limit'][1]) ? $getArray['limit'][1] : 0;
				$str .= ' limit ' . implode(',', $getArray['limit']);
			}
			else
			{
				$str .= ' limit ' . $getArray['limit'];
			}
		}
		return $str;
	}

	/**
	 * get table name
	 *
	 * @param string $moduleName
	 * @return string
	 */
	public static function getTableName($moduleName)
	{
		return __dbPrefix__.$moduleName;
	}

	/**
	 * Query action
	 *
	 * @param string $query 쿼리문
	 * @return string 처리 결과값
	 */
	public static function action($query)
	{
		global $goose;

		if (!$goose->spawn->db->query($query))
		{
			$error = $goose->spawn->db->errorInfo();
			return "DB ERROR : ".$error[2];
		}
		else
		{
			return "success";
		}
	}

	/**
	 * Get query
	 *
	 * @param array $data db데이터를 요청하는 매배변수 배열
	 * @return string $str sql 쿼리문자
	 **/
	public static function getQuery($data)
	{
		$str = self::arrayToSelectQuery($data);
		return $str;
	}

	/**
	 * insert item
	 *
	 * @param array $get 요청 파라메터 값
	 * @return string 결과값
	 **/
	public static function insert($get)
	{
		$result = '';
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
				$v = (!is_null($v)) ? '\''.$v.'\'' : 'null';
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
			return self::action($result);
		}
	}

	/**
	 * Item update
	 *
	 * @param array $get : 요청 파라메터 배열
	 * @return String : 결과값
	 **/
	public static function update($get)
	{
		$result = '';
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
			return self::action($result);
		}
	}

	/**
	 * delete item
	 *
	 * @param array $get 요청 파라메터 배열
	 * @return string 결과값
	 **/
	public static function delete($get)
	{
		$result = null;

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
			return self::action($result);
		}
	}

	/**
	 * Get items index
	 *
	 * @param array $data 요청 파라메터 배열
	 * @return array
	 */
	public static function items($data)
	{
		global $goose;

		$data['act'] = 'select';
		$data['field'] = (isset($data['field'])) ? $data['field'] : '*';
		$query = self::arrayToSelectQuery($data);

		if (isset($data['debug']) && $data['debug'] == true)
		{
			return $query;
		}
		else
		{
			$qry = $goose->spawn->db->query($query);
			if ($qry)
			{
				$result = $qry->fetchAll(PDO::FETCH_ASSOC);
				if ($data['jsonField'] && count($data['jsonField']))
				{
					foreach ($result as $k=>$v)
					{
						$result[$k] = self::convertJsonToArray($v, $data['jsonField']);
					}
				}
				return $result;
			}
			else
			{
				return [];
			}
		}
	}

	/**
	 * Get item data
	 *
	 * @param array $data 요청 파라메터 배열
	 * @return array
	 */
	public static function item($data)
	{
		global $goose;

		$data['act'] = 'select';
		$data['field'] = (isset($data['field'])) ? $data['field'] : '*';
		$query = self::arrayToSelectQuery($data);

		if (isset($data['debug']) && $data['debug'] == true)
		{
			return $query;
		}
		else
		{
			$qry = $goose->spawn->db->query($query);
			if ($qry)
			{
				$result = $qry->fetch(PDO::FETCH_ASSOC);
				if ($data['jsonField'] && count($data['jsonField']))
				{
					$result = self::convertJsonToArray($result, $data['jsonField']);
				}
				return $result;
			}
			else
			{
				return [];
			}
		}
	}

	/**
	 * Get item count
	 *
	 * @param array $data 요청 파라메터 배열
	 * @return string|array
	 */
	public static function count($data)
	{
		global $goose;

		$data['act'] = 'select';
		$data['field'] = 'count(*)';
		$query = self::arrayToSelectQuery($data);

		if (isset($data['debug']) && $data['debug'] == true)
		{
			return $query;
		}
		else
		{
			$result = $goose->spawn->db->prepare($query);
			$result->execute();
			return (int)$result->fetchColumn();
		}
	}

	/**
	 * array to create table query
	 *
	 * @param array $data source
	 * @return string
	 */
	public static function arrayToCreateTableQuery($data=array())
	{
		$fields = array();
		$fields2 = array();

		foreach($data['fields'] as $k=>$v)
		{
			$str = "`$v[name]` $v[type]";
			$str .= ($v['length']) ? "($v[length])" : "";
			$str .= " $v[etc]";
			$str .= ($v['auto']) ? " auto_increment" : "";
			$fields[] = $str;
			if ($v['key'])
			{
				$fields2[] = "primary key (`$v[name]`)";
			}
			if ($v['unique'])
			{
				$fields2[] = "unique (`$v[name]`)";
			}
			if ($v['index'])
			{
				$fields2[] = "index (`$v[name]`)";
			}
		}

		$str = "create table `$data[tableName]` (";
		$str .= implode(',', $fields);
		$str .= (count($fields2)) ? ', '.implode(',', $fields2) : "";
		$str .= ") engine=InnoDB default charset=utf8";

		return $str;
	}

	/**
	 * drop table
	 *
	 * @param string $tableName
	 * @return string
	 */
	public static function dropTable($tableName=null)
	{
		$qry = 'drop table ' . $tableName;
		return Spawn::action($qry);
	}

	/**
	 * get last idx
	 * db에 입력된 마지막 번호를 가져온다.
	 *
	 * @return int
	 */
	public static function getLastIdx()
	{
		global $goose;
		return $goose->spawn->db->lastInsertId();
	}

	/**
	 * convert json to array
	 *
	 * @param array $item
	 * @param array $fields
	 * @return array
	 */
	private static function convertJsonToArray($item, $fields)
	{
		foreach ($fields as $k=>$v)
		{
			$item[$v] = Util::jsonToArray($item[$v], null, true);
		}

		return $item;
	}
}