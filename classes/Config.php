<?php 
class Config {
	public static function get($path = null){
		//Simple URL like get function returns values from the main config located in init.php
		//some example code is: Config::get("session/session_name"); - This goes into session then within that session name
		if($path){
			$config = $GLOBALS['config'];
			$path = explode('/', $path);
			
			foreach($path as $bit){
				if(isset($config[$bit])){
					$config = $config[$bit];
				}
			}
			
			return $config;
		}
	}
}
?>