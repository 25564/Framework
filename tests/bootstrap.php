<?php

ob_start();

if(session_id() == '') {
    session_start();
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
	'root' => '/Framework'
);

spl_autoload_register(function($class) { //Auto load classes as they are needed
	$Class = str_replace(array('\\', '_'), '/', $class);
	
	$SeperateDirectories = array("models", "controllers", "includes", "views");
	$Prefix = "/app/framework/";

	if(in_array(explode("/", $Class)[0], $SeperateDirectories)){
		$Prefix = "/app/";
	}

	$Root = explode("\\", dirname(__FILE__));
	array_pop($Root);
	$Root = implode("/",$Root);

	$file = $Root . $Prefix . $Class .'.php';

    if (file_exists($file)) {
        require_once $file;
        return;
    }
});

function escape($string) {
	return htmlentities($string, ENT_QUOTES, 'UTF-8');
}
