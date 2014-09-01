<?php 
class Session {
	public static function exists($name){
		//Checks to see if a session is set
		return (isset($_SESSION[$name])) ? true : false;	
	}

	public static function put($name, $value){
		//Creates a session
		return $_SESSION[$name] = $value;	
	}
	
	public static function get($name) {
		//Returns a session value
		return $_SESSION[$name];	
	}
	
	public static function delete($name) {
		//Deletes the session
		if(self::exists($name)){
			unset($_SESSION[$name]);	
		}
	}
	
	public static function flash($name, $string = ''){
		//Stores a value in a session then deletes it next time that function is called for example if I call Session:flash("forum", "Thread Updated")
		//The first time it will set that session however next time I call it will Session:flash("forum") it will return "Thread Updated". This can be 
		//useful when data and alerts need to be displayed once such as when a user is registered successfully
		if(self::exists($name)){
			$session = self::get($name);
			self::delete($name);
			return $session;
		} else {
			self::put($name, $string);
		}
		return '';
	}
}
?>