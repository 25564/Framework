<?php
//PHP has countless Libaries for manipulating, reading and saving XML documents. This class is simply for when a spoof document is needed for an API.
class XML implements ArrayAccess {
	private $_xml,
			$_data = array(),
			$_numKey;

	public function __construct($root = "root", $NumericKey = "item"){
		$this->_xml = new SimpleXMLElement("<?xml version=\"1.0\"?><{$root}></{$root}>");
 		$this->_numKey = $NumericKey;
 	}

	public function print(){
		header('Content-Type: text/xml');
		print $this->_xml->asXML();
	}
	 
	public function parse(){
		$this->subParse($this->_data, $this->_xml);
	}

	function subParse($info, &$xml) {
	    foreach($info as $key => $value) {
	        if(is_array($value)) {
	            $key = is_numeric($key) ? $this->_numKey : $key;
	            $subnode = $xml->addChild("$key");
	            $this->subParse($value, $subnode);
	        }
	        else {
	            $key = is_numeric($key) ? $this->_numKey : $key;
	            $xml->addChild("$key","$value");
	        }
	    }
	}
	
	public function setData($data){
		$this->_data = $data;
	}

	//Array Access
	public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->_data[] = $value;
        } else {
            $this->_data[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->_data[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->_data[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
    }
}
?>