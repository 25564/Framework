<?php
//Comma Seperated Values
class CSV {

	public static function parseCSV($filename, $delimiter=','){
	    if(!file_exists($filename) || !is_readable($filename)){
	        return false;
	    } else {
		    $header = null;
		    $data = array();
		    if (($handle = fopen($filename, 'r')) !== false) {
		        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
		            if(!$header){
		                $header = $row;
		            } else {
		                $data[] = array_combine($header, $row);
		        	}
		        }
		        fclose($handle);
		    }
		    return $data;
		}
	}

	public static function containsString($needle, $haystack, $delimeter = ','){
		$Array = explode($delimeter, $haystack);
		if(in_array($needle, $Array)){
			return true;	
		}
		else {
			return false;	
		}
	}
	
	public static function addString($needle, $haystack, $delimeter = ','){
		return $haystack . $delimeter . $needle;
	}
	
	private function load($file){
		if (isset($file) && file_exists($file))
		{
			$this->_rawFile = file_get_contents($file);
			return true;
		}
		else {
			return false;
		}
	}

	public static function removeString($needle, $haystack, $delimeter = ',') {
		$Array = explode($delimeter, $haystack);
		while(($i = array_search($needle, $Array)) !== false) {
			unset($Array[$i]);
		}
		return implode($delimeter, $Array);
	}
}
?>