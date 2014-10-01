<?php
/*
Meta tables are basically normal arrays, however they have extra functionality suchs as Meta Methods which is methods called when certain global events

Meta Methods are defined with __ before the name and watchers are defined by __#watch_ValueName_WatchType

#Current Meta Methods
	-Index
		- Value returned from callback is default value for undefined values
	-newIndex
		- Run when a new index is added to the Table (Not when changed only when an entire new key is added)
	-toString
		- Value returned from this is used when the Table is used as a string
	-Count
		- Run when the count() function is run on the Table
		- Count(Table) == 7 even though it could have any amount of elements because the count function could be: 

		  "__count" = function () {
				return 1 + (2*3);
		  }
		- Defaults to counting the Normal Data
	-Unset
		- Run when a element is unset
	-Dump
		- Ran when Table->Dump() is run. Dump() is for bebugging the table or in 4.6 and further when var_dump() is called on the table 
	-Change
		- Ran every time a value is changed, set or unset
		- Occurs after value is changed and passes two oaraneters offset (The offset changed) and the new value

	-Save
		- Occurs when the save function is ran. This overides the default function meta method is returned further.

#Current Watchers
	- Change
		- When the element value is called. The first parameter is the new value
		- Function must return for the function to proceed otherwise the operation is cancelled
		- Called when unset if there is no unset watcher with the first parameter being null
		- An example of this functionality would be if a value is not allowed to drop below a set amount
		    '__#watch_Duck_change' = function(newValue){ //Example function
				if(newValue < 30){ 
					return false; //Don't let the value get larger than 30
				}
				return true;
		   	}
	- Unset
		- Called when the element is unset
		- Function must return for the function to proceed otherwise the operation is cancelled
	- Get
		- Called when the elements value is being retrieved.
		- Function must return for the function to proceed otherwise the operation is cancelled


#Functions
	- create
		- The basic function to create a meta table from a series of normal arrays such as the one below.
			
			$Table = MetaTable::create(array(
				"__index" => function() {
					return 7;
				}
			), array(
				1,2,3,4
			));

	- load
		- Allows for raw import of the table in the format of 3 sub arrays inside a wrapper array.
	- save
		- Saves the current table in the format that can be loaded by the load function
		- If the Meta method is present it will be overriden

#Extra Notes
	- Unsetting a Data value deletes all watchers attached to that element
	- Change is called after the change occurs so it will not be called if the action is blocked by a watcher
	- Save method could cause incosistancies with loading data.
*/
class MetaTable implements Countable, arrayaccess, Iterator {
	private $_MetaMethods,
			$_Data,
			$_Watchers = array(),
			$_Position = 0;

	private function __construct($MetaMethods, $Data, $Watchers = array()) {
		$this->_Watchers = $Watchers;
		foreach($MetaMethods as $Offset => $Method){
			$this->setMeta($Offset, $Method);
		}
		$this->_Data = $Data;
	}
	
	public static function create($MetaMethods = array(), $Data = array()) {
		return new MetaTable($MetaMethods, $Data);
	}

	public static function load($Data) {
		return new MetaTable($Data['MetaMethods'], $Data['Data'], $Data['Watchers']);
	}

	public function save(){
		if($this->hasMeta("save")){
			return $this->callMeta("save");
		} else {
			$MetaMethods = array();
			foreach($this->_MetaMethods as $Method => $value){
				$MetaMethods["__" . $Method] = $value;
			}
			return array(
				'MetaMethods' => $MetaMethods,
				'Data' => $this->_Data,
				'Watchers' => $this->_Watchers
			);
		}
	}

//Helpers
	//Meta Method
		private function isMetaMethod($offset){
			return (substr($offset, 0, 2) == "__") ? true : false;
		}

		private function setMeta($offset, $value){
			if(!$this->isWatcher($offset)){
				$this->_MetaMethods[strtolower(substr($offset, 2))] = $value;
			} else {
				$WatcherData = $this->isWatcher($offset);
				$this->setWatcher($WatcherData[1], $WatcherData[2], $value);
			}
		}

		private function hasMeta($offset){
			return isset($this->_MetaMethods[strtolower($offset)]);
		}

		private function callMeta($offset, $Parameter = array()){
			if($this->hasMeta($offset)){
				return call_user_func_array($this->_MetaMethods[strtolower($offset)], $Parameter);
			}
		}


	//Watchers
		private function isWatcher($offset){
			if(substr($offset, 0, 3) == "__#"){
				$Exploded = explode("_", substr($offset, 3));
				if($Exploded[0] = "watch"){
					return $Exploded;
				}
			}
			return false;
		}

		public function setWatcher($offset, $type, $callback){
			if(!isset($this->_Watchers[$offset])){
				$this->_Watchers[$offset] = array();
			}
			$this->_Watchers[$offset][strtolower($type)] = $callback;
		}

		private function hasWatcher($offset, $type){
			return isset($this->_Watchers[$offset][$type]);
		}

		private function getWatcher($offset, $type){
			return $this->_Watchers[$offset][$type];
		}

		private function callWatcher($offset, $type, $Parameter = array()){
			if($this->hasWatcher($offset, $type)){
				return call_user_func_array($this->_Watchers[$offset][strtolower($type)], $Parameter);
			}
		}

//Magic Methods
	public function __toString() {
	    if($this->hasMeta("tostring")){
			return $this->callMeta("tostring");
		} else {
			return "";
		}
	}


//Countable Interface
	public function count(){
		if($this->hasMeta("count")){
			return $this->callMeta("count");
		} else {
			return count($this->_Data);
		}
	}

//Array Access interface

    public function offsetSet($offset, $value) {
    	$Proceed = true;
 	    if($this->hasWatcher($offset, "change")){
	    	$Proceed = $this->callWatcher($offset, "change", array($value));
	    }
	    if($Proceed){
		    if (is_null($offset)) {
		    	if($this->hasMeta("newindex")){
					return $this->callMeta("newindex", array($offset));
				} else {
			        array_push($this->$_Data, $value);
				}
		    } else {
		    	if($this->isMetaMethod($offset)){
					$this->setMeta($offset, $value);
				} else {
		        	$this->_Data[$offset] = $value;
		    	}
		    }
		    if($this->hasMeta("change")){
				return $this->callMeta("change", array($offset, $this->_Data[$offset]));
			}
		}
    }

    public function offsetExists($offset) {
        return isset($this->_Data[$offset]);
    }

    public function offsetUnset($offset) {
    	if(!$this->isMetaMethod($offset)){
	    	$Proceed = true;
	 	    if($this->hasWatcher($offset, "unset")){
		    	$Proceed = $this->callWatcher($offset, "unset");
		    } elseif($this->hasWatcher($offset, "change")){
			    $Proceed = $this->callWatcher($offset, "change", array(null));
		    }

		    if($Proceed){
		    	if($this->hasMeta("unset")){
					return $this->callMeta("unset", array($offset));
				} else {
		        	unset($this->_Data[$offset]);
		        	unset($this->_Watchers[$offset]);
		    	}
		    }

		    if($this->hasMeta("change")){
				return $this->callMeta("change", array($offset, null));
			}
		} else {
			if($this->isWatcher($offset)){
				unset($this->_Watchers[$offset]);
			} else {
				unset($this->_MetaMethods[$offset]);
			}
		}
    }

    public function offsetGet($offset) {
    	$Proceed = true;
 	    if($this->hasWatcher($offset, "get")){
	    	$Proceed = $this->callWatcher($offset, "get");
	    }

	    if($Proceed){
	    	if(!isset($this->_Data[$offset])){
		    	if($this->hasMeta("index")){
					return $this->callMeta("index", array($offset));
				}
			} else {

				return $this->_Data[$offset];
			}
		}
    }

//Array Iterator interface

    public function rewind() {
        $this->_position = 0;
    }

    public function current() {
        return $this->_Data[$this->_position];
    }

    public function key() {
        return $this->_position;
    }

    public function next() {
        ++$this->_position;
    }

    public function valid() {
        return isset($this->_Data[$this->_position]);
    }


////////////// Debug /////////////////

	public function Dump(){
		if($this->hasMeta("dump")){
			return $this->callMeta("dump");
		} else {
			echo "<b>Meta Methods:</b>", var_dump($this->_MetaMethods), "<br><br><b>Normal Data:</b>", var_dump($this->_Data), "<br><br><b>Watchers:</b>", var_dump($this->_Watchers);
		}
	}

	public function __debugInfo() {
		if($this->hasMeta("dump")){
			return $this->callMeta("dump");
		} else {
	        return [
	            'MetaMethods' => $this->_MetaMethods,
	            'Data' => $this->_Data,
	            'Watchers' => $this->_Watchers
	        ];
    	}
    }

/////////////////////////////////////
}
?>