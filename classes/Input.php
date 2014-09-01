<?php
class Input {
	public static function exists($type = 'post') {
		//Checks to see if there is input 
		switch($type){
			case 'post':
				return (!empty($_POST))? true : false;
			break;
			case 'get':
				return (!empty($_GET))? true : false;
			break;
			default:
				return false;
			break;
				
		}
	}
	
	public static function get($item) {
		//returns abstract function that returns an input. Prioritizes $_POST  by will check $_GET secondary
		if(isset($_POST[$item])){
			return $_POST[$item];
		} elseif(isset($_GET[$item])){
			return $_GET[$item];
		}
		return ''; 
	}
}
?>