<?php
/*   --  Meta Methods  --
NewIndex  - When a new index is added
Modify    - When an element is changed
Isset     - When isset() is ran on a value
Unset     - When unset() is ran
Index     - When an element is requested that does not exist
Count     - When count() is ran. This will exclude MetaMethods when counting elements
*/

class MetaTable implements arrayaccess, Countable, IteratorAggregate {
	public $_data = array();
	private $_Config = array(
		"MetaPrefix" => "__" //Changing may cause compatibility issues
	);

	public function __construct(array $Data = array()) {
		$this->_data = $Data;
	}

	public function AddMeta($Offset, $Value){ //Metas should not be able to intervene when dealing with other Metas
		$this->_data[$this->_Config["MetaPrefix"] . $Offset] = $Value;
	}

	public function RemoveMeta($Offset){ //Metas should not be able to intervene when dealing with other Metas
		unset($this->_data[$this->_Config["MetaPrefix"] . $Offset]);
	}

	public function GetMeta($Offset){ //Metas should not be able to intervene when dealing with other Metas
		return $this->_data[$this->_Config["MetaPrefix"] . $Offset];
	}

	/** ------------------- Helpers ------------------- **/

	private function HasMeta($Value){
		return (isset($this->_data[$this->_Config["MetaPrefix"] . $Value])) ? true : false;
	}

	private function CallMeta($Value, $Params = array()){
		if(is_callable($this->_data[$this->_Config["MetaPrefix"] . $Value])){
			return $this->_data[$this->_Config["MetaPrefix"] . $Value]($this, $Params);
		} else {
			return $this->_data[$this->_Config["MetaPrefix"] . $Value];
		}
	}

	private function IsolateElements(){ //Returns an array that excludes all Meta Methods
		$ElementsArray = array();
		foreach ($this->_data as $key => $value) {
			$StringPos = strpos($key, $this->_Config["MetaPrefix"]);
			if ($StringPos === false || $StringPos != 0) {
				$ElementsArray[$key] = $value;
			}
		}
		return $ElementsArray;
	}

	/** ------------------- Array Access ------------------- **/

	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
			if($this->HasMeta("NewIndex") === false){
				$this->_data[] = $value;
			} else {
				$this->_data = $this->CallMeta("NewIndex", array("Value" => $value, "Offset" => $offset));
			}
		} else {
			if($this->offsetExists($offset) == false){
				if($this->HasMeta("NewIndex") === false){
					$this->_data[$offset] = $value;
				} else {
					$this->_data = $this->CallMeta("NewIndex", array("Value" => $value, "Offset" => $offset));
				}
			} else {
				if($this->HasMeta("Modify") === false){
					$this->_data[$offset] = $value;
				} else {
					$this->_data = $this->CallMeta("Modify", array("CurrentValue" => $this->_data[$offset], "NewValue" => $value, "Offset" => $offset));
				}
			}
		}
	}

	public function offsetExists($offset) {
		if($this->HasMeta("Isset") === false){
			return isset($this->_data[$offset]);
		} else {
			return $this->CallMeta("Isset", array("Offset" => $offset));
		}
	}

	public function offsetUnset($offset) {
		if($this->HasMeta("Unset") === false){
			unset($this->_data[$offset]);
			return true;
		} else {
			$this->_data = $this->CallMeta("Unset", array("Offset" => $offset));
		}
	}

	public function offsetGet($offset) {
		if(isset($this->_data[$offset]) === false){
			if($this->HasMeta("Index") === false){
				return null;
			} else {
				return $this->CallMeta("Index", array("Offset" => $offset));
			}
		} else {
			return $this->_data[$offset];
		}
	}

	/** ------------------- Object Notation ------------------- **/

	public function __set($offset, $value){
		$this->offsetSet($offset, $value);
	}

	public function __get($offset){
		return $this->offsetGet($offset);
	}

	public function __isset($offset){
		return $this->offsetExists($offset);
	}

	public function __unset($offset){
		return $this->offsetUnset($offset);
	}

	/** ------------------- Iterator ------------------- **/

	public function getIterator() {
		return new ArrayIterator($this->IsolateElements());
	}

	/** ------------------- Count ------------------- **/

	public function count(){ //Does not count MetaMethods
		if($this->HasMeta("Count") === false){
			return count($this->IsolateElements());
		} else {
			return $this->CallMeta("Count");
		}
	}
}