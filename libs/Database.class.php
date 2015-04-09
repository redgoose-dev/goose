<?
if(!defined("GOOSE")){exit();}

class Database {
	var $conn;

	// init and connect
	public function Database($config)
	{
		try {
			$this->conn = new PDO($config[0], $config[1], $config[2]);
			return $this->conn;
		} catch (PDOException $e) {
			echo 'Connection failed: '.$e->getMessage();
			die();
		}
	}

	// disconnect
	public function disconnect()
	{
		$this->conn = null;
	}

	// get item count
	public function count($query)
	{
		$result = $this->conn->prepare($query);
		$result->execute();
		return $result->fetchColumn();
	}

	// query action
	public function action($query)
	{
		if (!$this->conn->query($query))
		{
			$error = $this->conn->errorInfo();
			return "DB ERROR : ".$error[2];
		}
		else
		{
			return "success";
		}
	}

	// get multi index
	public function getMultiData($query)
	{
		$qry = $this->conn->query($query);
		if ($qry)
		{
			return $qry->fetchAll(PDO::FETCH_ASSOC);
		}
		else
		{
			return false;
		}
	}

	// get single data
	public function getSingleData($query)
	{
		$qry = $this->conn->query($query);
		if ($qry)
		{
			return $qry->fetch(PDO::FETCH_ASSOC);
		}
		else
		{
			return false;
		}
	}
}