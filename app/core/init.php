<?php 
ob_start();

if(session_id() == '') { //Just incase a session is already set
    session_start(); //Set session if not set already
}

$GLOBALS['config'] = array( //Config
	'mysql' => array(
		'host' => 'localhost',
		'username' => 'root',
		'password' => '',
		'db' => 'framework',
	),
	'remember' => array(
		'cookie_name' => 'hash',
		'cookie_expiry' => '604800' //Seconds
	),
	'tracking' => array(
		"cookie_expiry" => '31536000'
	),
	'session' => array(
		'session_name' => 'UserID',
		'token_name' => 'token'
	),
	'root' => '/framework'
);

spl_autoload_register(function($class) { //Auto load classes as they are needed
	$Class = str_replace(array('\\', '_'), '/', $class);
	
	$SeperateDirectories = array("models", "classes", "controllers", "includes", "views");
	$Prefix = "/app/framework/";

	if(in_array(explode("/", $Class)[0], $SeperateDirectories)){
		$Prefix = "/app/";
	}

	$file = $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['config']['root'] . $Prefix . $Class .'.php';
	if (file_exists($file)) {
		require_once $file;
		return;
	}
});


//Start Remember me
if(Cookie::exists(Config::get("remember/cookie_name")) && !Session::exists(Config::get("session/session_name"))){ //Checks users is not logged in and cookie exists
	$hash = Cookie::get(Config::get("remember/cookie_name")); //Get local cookie
	$hashCheck = DB::getInstance()->table('user_sessions')->where('hash', $hash)->get(); //Get hash from DB for comparision
	if(count($hashCheck) != 0){
		if($hash == $hashCheck[0]->hash){ //Check they match
			Session::put(Config::get("session/session_name"), $hashCheck[0]->user_id); //Create Session
		}
		else {
			Cookie::delete("remember/cookie_name"); //If they don't match may aswell delete it so this check does not keep getting ran
		}
	}
}
//End remember me

function escape($string) {
	return htmlentities($string, ENT_QUOTES, 'UTF-8');
}

function root(){
	return $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['config']['root'];
}
?>
