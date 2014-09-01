<?php 
session_start();

$GLOBALS['config'] = array( //Config
	'mysql' => array(
		'host' => 'localhost',
		'username' => 'root',
		'password' => '',
		'db' => 'krypto',
	),
	'remember' => array(
		'cookie_name' => 'hash',
		'cookie_expiry' => '604800' //Seconds
	),
	'session' => array(
		'session_name' => 'user',
		'token_name' => 'token'
	)
);

spl_autoload_register(function($class) { //Auto load classes as they are needed
	require_once $_SERVER['DOCUMENT_ROOT'] .'/classes/' . $class . '.php';	
});
//Start functions require
require_once $_SERVER['DOCUMENT_ROOT'] . "/functions/sanatize.php";
//End function require

//Start Remember me
if(Cookie::exists(Config::get("remember/cookie_name")) && !Session::exists(Config::get("session/session_name"))){ //Checks users is not logged in and cookie exists
	
	$hash = Cookie::get(Config::get("remember/cookie_name")); //Get local cookie
	$hashCheck = DB::getInstance()->get('user_sessions', array('hash', '=', $hash)); //Get hash from DB for comparision
	
	if($hashCheck->count() != 0){
		
		if($hash == $hashCheck->first()->hash){ //Check they match
			Session::put(Config::get("session/session_name"), $hashCheck->first()->user_id); //Create Session
		}
		else {
			Cookie::delete("remember/cookie_name"); //If they don't match may aswell delete it so this check does not keep getting ran
		}
	}
}
//End remember me
?>