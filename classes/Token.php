<?php 
class Token {
	
	public static function generate() {
		//Generates the code which is unique every time the page is loaded
		return Session::put(Config::get('session/token_name'), md5(uniqid()))	;
	}
	
	public static function check($token){
		//Checks that the code given in the parameter is equal to the one set in the session
		$tokenName = Config::get("session/token_name");
		
		if(Session::exists($tokenName) && $token === Session::get($tokenName)){
			Session::delete($tokenName);
			return true;
		}
		
		return false;
	}
}
?>
