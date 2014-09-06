<?php
/*
Singeton DB Class
Created 24/7/14 - Cian
*/

class DB implements Countable, arrayaccess {
	private static $_instance = null;
	private static $_pastQueries = [];

	private $_pdo, 
			$_query, 
			$_error = false, 
			$_start = 0,
			$_results, 
			$_columns = "*",
			$_table = null,
			$_count = 0;

	private function __construct() {
		try {
			$this->_pdo = new PDO('mysql:host='.Config::get('mysql/host').';dbname='.Config::get('mysql/db').'', Config::get('mysql/username'), Config::get('mysql/password'));
		} catch(PDOException $e){
			die($e->getMessage());	
		}
	}
	
	public static function getInstance() {
		//Calling this static function initiates the class 
		// this class can be called by:
		//DB::getInstance()->get('users', array('username', '=', '25564'))
		if(!isset(self::$_instance)){
			self::$_instance = new DB();
		}
		return self::$_instance;
	}
	
	public function query($sql, $params = array()){
		//Raw Query

		//Uses binding so the sql send should be use ? as place holders then parameters in a seperatea array. An example of this is:
		//DB::getInstance()->query('SELECT * FROM `user` WHERE `username`=? AND `group`= ?', array('25564', 2)) This prevents SQL injection
		//The above query would be processed as SELECT * FROM `user` WHERE `username`='25564' AND `group`= 2
		$this->_error = false;
		
		if($this->_query = $this->_pdo->prepare($sql)){
			$x = 1;
			if(count($params)){
				foreach($params as $param){
					$this->_query->bindValue($x, $param);	
					$x++;
				}
			}
			
			if($this->_query->execute()){
				$this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
				$this->_query = "";
			}
			else {
				$this->_error = true;	
			}
		}
	}
	
	public function table($table) {
		$this->_table = $table;
		return $this;
	}
	
	public function orderBy($column, $type){
		$this->_query .= "ORDER BY $column $type";
		return $this;
	}

	public function groupBy($column){
		$this->_query .= "GROUP BY $column";
		return $this;
	}
	
	public function skip($amount){
		//Start from a certain point in the records. Example: Get 5 records but skip the first 7 
		$this->_start = $amount;	
		return $this;
	}
	
	public function pluck(array $Columns = array()){
		//Pluck Certain rows
		if($this->_columns === "*" && count($Columns) > 0){
			$this->_columns = "";
		}
		foreach($Columns as $column){
			$this->_columns .= "'{$column}',";
		}
		$this->_columns = substr($this->_columns, 0, -1);
		return $this;
	}
	
	public function count(){
		$Query = $this->_pdo->query("SELECT ".$this->_columns." FROM ".$this->_table." ".$this->_query);
		$this->clearQuery();
		return $Query->rowCount();
	}
	
	public function get($number = null) {
		if(!$number){
			$sql = "SELECT ".$this->_columns." FROM ".$this->_table." ".$this->_query;
		} else {
			$sql = "SELECT ".$this->_columns." FROM ".$this->_table." ".$this->_query." LIMIT " . $this->_start . ",".$number;
		}
		$Query = $this->_pdo->query($sql);
		$this->_results = $Query->fetchAll(PDO::FETCH_OBJ);
		$this->clearQuery();
		return $this->_results;
	}
	
	public function update(array $data){
		$update = "";
		foreach($data as $column => $value){
			$update .= $column."='".$value."',";
		}

		$update = substr($update, 0, -1);

		$this->_results = $this->_pdo->query("UPDATE ".$this->_table." SET ".$update." ".$this->_query);
		$this->clearQuery();
		return $this->_results;
	}

	public function delete() {
		$this->_results = $this->_pdo->query("DELETE FROM ".$this->_table." ".$this->_query);
		$this->_query = "";
		return $this->_results;
	}
	
	public function insert(array $data, $Multiple = false){
		//Second paramter dictates if more than one row is being inserted
		if($Multiple === false){
			$WorkData = array($data);
		} else {
			$WorkData = $data;
		}
		$columns = array();
		$values = array();
		$row = 0;
		$columnCount = 0;
		
		do {
			$rowValues = array();
			foreach($WorkData[$row] as $column => $value)
			{
				$columns[] = $column;
				$rowValues[] = $value;
			}
			$columnCount = count($rowValues);
			$values[] = "(" . implode(",", $rowValues) . ")";
			$row++;
		} while ($Multiple === true && $row < count($data));

		$this->_results = $this->_pdo->query("INSERT INTO " . $this->_table . " (" . implode(",", array_slice($columns, 0, $columnCount)) . ") VALUES " . implode(",", $values));
		return $this->_results;
	}
	
	public function insertGetId(array $data){
		$this->insert($data);
		return $this->_pdo->lastInsertId();
	}
	
	public function where($column, $operator, $row = null) {
		if(!$row){
			$row = $operator;
			$operator = "=";
		}
		$this->_query = !$this->hasQuery() ? "WHERE $column {$operator} '$row'" : $this->_query .= " AND $column {$operator} '$row'";
		return $this;
	}
	
	public function orwhere($column, $row){
		if($this->hasQuery()){
			$this->_query .= " OR $column = '$row'";
		}
		return $this;
	}

	public function like($column, $row) {
		$this->_query = !$this->hasQuery() ? "WHERE $column LIKE '.$row'" : $this->_query." AND $column LIKE '$row'";
		return this;
	}

	public function orlike($column, $row){
		if($this->hasQuery()){
			$this->_query .= " OR $column LIKE '$row' ";
		}
		return $this;
	}
	
	public function getQuery() {
		if($this->hasQuery()) {
			return $this->_query;
		}
	}
	
	public function results(){
		return $this->_results;
	}
	
	private function hasQuery() {
		if(isset($this->_query) and $this->_query != "")
		{
			return true;
		} else {
			return false;
		}
	}

	private function clearQuery(){
		$this->tagQuery();
		$this->_query = "";
	}

	public function loadQuery($id=0){
		if(isset(self::$_pastQueries[$id])){
		 	$this->_query = self::$_pastQueries[$id]["Query"];
			$this->_columns = self::$_pastQueries[$id]["Columns"];
			$this->_start = self::$_pastQueries[$id]["Start"];
			$this->_table = self::$_pastQueries[$id]["Table"];
			return $this;
		}
		return false;
	}

	public function setQuery(array $Query){
		$this->_query   = $Query["Query"];
		$this->_columns = $Query["Columns"];
		$this->_start   = $Query["Start"];
		$this->_table   = $Query["Table"];
	}

	public function tagQuery($name="", $value = null){
		if($value == null){
			self::$_pastQueries[$name] = array(
				"Query" 	=> $this->_query,
				"Columns"	=> $this->_columns,
				"Start"		=> $this->_start,
				"Table"		=> $this->_table
			);
		} else {
			self::$_pastQueries[$name] = $value;
		}
	}

	public function dumpPrevious(){
		return self::$_pastQueries;
	}

    public function offsetSet($offset, $value) {
	    if (is_null($offset)) {
	        array_push($this->$_results, $value);
	    } else {
	        $this->_results[$offset] = $value;
	    }
    }

    public function offsetExists($offset) {
        return isset($this->_results[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->_results[$offset]);
    }

    public function offsetGet($offset) {
    	return $this->skip($offset - 1)->get(1);
    }
}
?>