<?php 
class Cookie {
	public static function exists($name){
		//Checks the existance of cookie and returns true if it exists
		return (isset($_COOKIE[$name])) ? true : false;	
	}
	
	public static function get($name){
		//Abstract return cookie value
		if(self::exists($name)){
			return $_COOKIE[$name];
		}
	}
	
	public static function put($name, $value, $expiry){
		//Creates a cookie
		if(setcookie($name, $value, time() + $expiry, '/'))	{
			$_COOKIE[$name] = $value;
			return true;	
		}
		return false;
	}
	
	public static function delete($name) {
		//Deletes the cookie by setting its expiry time to a point that has already passed
		self::put($name, '', time()-1);
		unset($_COOKIE[$name]);
	}
}
?>
