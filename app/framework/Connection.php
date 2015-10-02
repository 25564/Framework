<?php
class Connection {
	//Primarily for Spoofing IP Address during testing
	private static $_overrideIP = false;


	public static function IP() {
		if(self::$_overrideIP != false){
			return self::$_overrideIP;
		}
		return $_SERVER['REMOTE_ADDR'];
	} 

	public static function setIP($IP) {
		self::$_overrideIP = $IP;
	}
}