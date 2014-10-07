<?php
class Secure {
	public static function encrypt($string, $key = ""){
		return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
	}

	public static function decrypt($string, $key=""){
		return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($string), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
	}

	public static function escape($string) {
		return htmlentities($string, ENT_QUOTES, 'UTF-8');
	}

	public static function user_Encrypt($string){
		if($key = self::SecureUserReady()){
			return self::encrypt($string, $key);
		}
		return false;
	}

	public static function user_Decrypt($string){
		if($key = self::SecureUserReady()){
			return self::decrypt($string, $key);
		}
		return false;
	}

	private static function SecureUserReady(){ //Can the 
		if(Session::exists("__user")){
			return Session::get("__user");
		}
		return false;
	}
}
?>