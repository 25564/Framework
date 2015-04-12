<?php
/*
Singeton DB Class
Created 24/7/14 - Cian
*/

class DB implements Countable, arrayaccess, Iterator {
	private static $_instance = null;
	private static $_pastQueries = [];

	private $_pdo, //PDO connection
			$_query, //The query
			$_error = false, //Has an error occured
			$_start = 0, //How many rows should be skiped. This is set by skip(Amount)
			$_results, //Result of the query
			$_columns = "*", //Columns being returned. This can be altered by pluck()
			$_table = null, //Table query acts upon
			$_position = 0, //Current Array iterator increment
			$_Array = false;
	private function __construct() {
		try { //Try DB Connection with Config settings
			$this->_pdo = new PDO('mysql:host='.Config::get('mysql/host').';dbname='.Config::get('mysql/db').'', Config::get('mysql/username'), Config::get('mysql/password'));
		} catch(PDOException $e){
			die($e->getMessage());	
		}
	}
	
	public static function getInstance() {
		//Singleton getInstance()
		if(!isset(self::$_instance)){
			self::$_instance = new DB();
		}
		return self::$_instance;
	}
	
//Base Query functions

	private function query($sql, $params = array()){
		//Query DB
		$this->_error = false;
		$this->tagQuery();
		if($this->_query = $this->_pdo->prepare($sql)){
			$x = 1;
			if(!empty($params)){
				foreach($params as $param){
					$this->_query->bindValue($x, $param);	
					$x++;
				}
			}
			if($this->_query->execute()){
				return true;				
			}
			else {
				$this->_error = true;	
			}		
		}
		return false;
	}
	
	public function raw($sql,  $params = array()){
		//Raw query
		$this->query($sql,  $params);
		if($this->_Array === false){
			$this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
		} else {
			$this->_results = $this->_query->fetchAll();
		}
		$this->clearQuery();
		return $this->results();
	}

//Operation Types

	public function count(){
		$this->query("SELECT ".$this->_columns." FROM ".$this->_table." ".$this->_query);
		$this->_results = $this->_query->rowCount();
		$this->clearQuery();
		return $this->results();
	}
	
	public function get($number = null) {//Select row data
		if(!$number){
			$sql = "SELECT ".$this->_columns." FROM ".$this->_table." ".$this->_query;
		} else {
			$sql = "SELECT ".$this->_columns." FROM ".$this->_table." ".$this->_query." LIMIT " . $this->_start . ",".$number;
		}
		$Query = $this->query($sql);
		if($this->_Array === false){
			$this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
		} else {
			$this->_results = $this->_query->fetchAll();
		}
		$this->clearQuery();
		return $this->_results;
	}

	public function FetchArray($Mode){
		$this->_Array = $Mode;
		return $this;
	}
	
	public function update(array $data){//Update row data
		$update = array();
		foreach($data as $column => $value){
			$update[] = $column."='".$value."'";
		}
		$this->_results = $this->query("UPDATE ".$this->_table." SET ".implode(",", $update)." ".$this->_query);
		$this->clearQuery();
		return $this->_results;
	}

	public function delete() {//Delete rows
		$this->_results = $this->query("DELETE FROM ".$this->_table." ".$this->_query);
		$this->clearQuery();
		return $this->_results;
	}
	
	public function insert(array $data, $MultipleRows = false){ //DB Insert function
		//Second paramter dictates if more than one row is being inserted
		if($MultipleRows === false){
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
			foreach((array)$WorkData[$row] as $column => $value)
			{
				$columns[] = "`" . $column . "`";
				$rowValues[] = "'" . $value . "'";
			}
			$columnCount = count($rowValues);
			$values[] = "(" . implode(",", $rowValues) . ")";
			$row++;
		} while ($MultipleRows === true && $row < count($data));

		$this->_results = $this->query("INSERT INTO " . $this->_table . " (" . implode(",", array_slice($columns, 0, $columnCount)) . ") VALUES " . implode(",", $values));
		$this->clearQuery();
		return $this->_results;
	}
	
	public function insertGetId(array $data){
		//Inserts data then returns the ID
		$this->insert($data);
		return $this->_pdo->lastInsertId();
	}
	
//Query Builder functions

	public function table($table) {//Set the table that is being acted upon
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

//Get functions	
	
	public function results(){
		return $this->_results;
	}

	public function hasError(){
		return $this->_error;
	}

//Query Managment 	
	public function getQuery() { //Returns the current query
		if($this->hasQuery()) {
			return $this->_query;
		}
		return false;
	}

	private function hasQuery() { //Is a query set
		if(isset($this->_query) and $this->_query != "")
		{
			return true;
		} else {
			return false;
		}
	}

	private function clearQuery(){ //Set query to blank
		$this->_query = "";
	}

//Previous Queries Managment

	public function loadQuery($id=0){//Load a previously stored query
		if(isset(self::$_pastQueries[$id])){
		 	$this->_query = self::$_pastQueries[$id]["Query"];
			$this->_columns = self::$_pastQueries[$id]["Columns"];
			$this->_start = self::$_pastQueries[$id]["Start"];
			$this->_table = self::$_pastQueries[$id]["Table"];
			return $this;
		}
		return false;
	}

	public function setQuery(array $Query){//Manually enter a query to be loaded
		$this->_query   = $Query["Query"];
		$this->_columns = $Query["Columns"];
		$this->_start   = $Query["Start"];
		$this->_table   = $Query["Table"];
	}

	public function tagQuery($name="", $value = null){//Store the current query in its current state
		if($value == null){
			if($name != ""){
				self::$_pastQueries[$name] = array(
					"Query" 	=> $this->_query,
					"Columns"	=> $this->_columns,
					"Start"		=> $this->_start,
					"Table"		=> $this->_table
				);
				return $name;
			} else {
				self::$_pastQueries[] = array(
					"Query" 	=> $this->_query,
					"Columns"	=> $this->_columns,
					"Start"		=> $this->_start,
					"Table"		=> $this->_table
				);
				end(self::$_pastQueries);
				return key(self::$_pastQueries);//Return index of last query stored
			}
		} else {
			self::$_pastQueries[$name] = $value;
		}
	}

//Array Access interface

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

//Array Iterator interface

    public function rewind() {
        $this->_position = 0;
        $this->get();
    }

    public function current() {
        return $this->_results[$this->_position];
    }

    public function key() {
        return $this->_position;
    }

    public function next() {
        ++$this->_position;
    }

    public function valid() {
        return isset($this->_results[$this->_position]);
    }
}
?>