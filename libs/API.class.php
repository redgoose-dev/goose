<?php
class API {
	var
		$result = array(),
		$util,
		$spawn,
		$tablesName,
		$apikey
	;

	// init
	public function API($opt)
	{
		$this->util = $opt[util];
		$this->spawn = $opt[spawn];
		$this->tablesName = $opt[tablesName];
		$this->apikey = $opt[apikey];
	}

	// api key auth
	public function auth($key)
	{
		function encryption($str)
		{
			return md5($str);
		}
		return (encryption($this->apikey) == $key) ? false : "APIKEY ERROR";
	}

	// get index data
	public function getIndexItem($params)
	{
		// table check
		$error = $this->checkTableName($params);
		if ($error)
		{
			$this->result[error] = $error;
			return $this->result;
		}

		$tableName = $this->tablesName[$params[table]];

		$where = ($params[module]) ? "and id='".$params[module]."'" : "";
		$where .= ($params[group]) ? " and group_srl='".$params[group]."'" : "";
		$where .= ($params[category]) ? " and category_srl='".$params[category]."'" : "";
		$where .= ($params[search][0] && $params[search][1]) ? " and ".$params[search][0]."='".$params[search][1]."'" : "";

		// get item count
		$itemCount = $this->spawn->getCount(array(
			'table' => $tableName
			,'where' => $where
		));

		if ($itemCount > 0)
		{
			require_once(PWD.'/libs/Paginate.class.php');

			$params[page] = ($params[page] > 1) ? $params[page] : 1;
			$params[limit] = ($params[limit]) ? $params[limit] : 15;
			$params[field] = ($params[field]) ? $params[field] : "*";
			$params[order] = ($params[order]) ? $params[order] : "srl";
			$params[sort] = ($params[sort]) ? $params[sort] : "desc";

			$paginate = new Paginate(
				$itemCount,
				$params[page],
				array(),
				$params[limit],
				1
			);

			$this->result = $this->spawn->getItems(array(
				field => $params[field],
				table => $tableName,
				where => $where,
				order => $params[order],
				sort => $params[sort],
				limit => array($paginate->offset, $paginate->size)
			));
		}

		return $this->result;
	}

	// get article data
	public function getSingleItem($params)
	{
		// table check
		$error = $this->checkTableName($params);
		if ($error)
		{
			$this->result[error] = $error;
			return $this->result;
		}

		if (!$params[key])
		{
			$this->result[error] = "\"key\" parameter does not exist.";
			return $this->result;
		}

		if (!$params[value])
		{
			$this->result[error] = "\"value\" parameter does not exist.";
			return $this->result;
		}

		$this->result = $this->spawn->getItem(array(
			table => $this->tablesName[$params[table]],
			where => $params[key].'='.$params[value]
		));

		return $this->result;
	}

	// print data
	public function out($data, $type)
	{
		switch($type)
		{
			case "xml":
				$xml = new SimpleXMLElement("<?xml version=\"1.0\"?><root></root>");
				$this->array_to_xml($data,$xml);
				$result = $xml->asXML();
				$header = "Content-Type: text/xml; charset=utf-8";
				break;

			case "json":
				$result = json_encode($data);
				$header = "Content-Type: text/plain; charset=utf-8";
				break;

			default:
				// to html
				$result = $data;
				$header = "content-type:text/html; charset=utf-8";
				break;
		}

		header($header);
		print_r($result);
	}

	// check table name
	private function checkTableName($params)
	{
		if (!$params[table])
		{
			return "\"table\" value does not exist.";
		}
		if (!$this->tablesName[$params[table]])
		{
			return "\"table\" match error";
		}
		return false;
	}

	// array to xml
	private function array_to_xml($student_info, &$xml_student_info)
	{
		foreach($student_info as $key => $value)
		{
			if (is_array($value))
			{
				if(!is_numeric($key))
				{
					$subnode = $xml_student_info->addChild("$key");
					$this->array_to_xml($value, $subnode);
				}
				else
				{
					$subnode = $xml_student_info->addChild("item$key");
					$this->array_to_xml($value, $subnode);
				}
			}
			else
			{
				$xml_student_info->addChild("$key", htmlspecialchars("$value"));
			}
		}
	}
}
?>