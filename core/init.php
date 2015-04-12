<?php 
//Framework began at 1303 lines

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
	'session' => array(
		'session_name' => 'user',
		'token_name' => 'token'
	),
	'root' => '/Framework'
);

spl_autoload_register(function($class) { //Auto load classes as they are needed
	$file = $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['config']['root'] . '/classes/' . str_replace(array('\\', '_'), '/', $class).'.php';
    if (file_exists($file)) {
        require_once $file;
        return;
    }
});

//Start functions require
require_once $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['config']['root'] . "/functions/sanatize.php";
//End function require

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
?>
