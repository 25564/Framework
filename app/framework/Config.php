<?php 
class Config {
	public static function get($path = null){
		//Returns values from the multi dimensional array declared in init.php
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