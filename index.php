<?php 
require_once $_SERVER['DOCUMENT_ROOT']  . "/Blog/app/core/init.php"; //Initialize

\Connection::setIP("1.12.1"); //Spoof IP for testing purposes

$User = false;

try {
	if(Session::exists(Config::get("session/session_name"))){
		$User = new User(Session::get(Config::get("session/session_name")));
	}
} catch (Exception $e) {
	echo 'Caught exception: ',  $e->getMessage(), "\n";
	$User = false;
}

//Should make these routes external

Router::map('/login', array(
	'_controller' => 'controllers\authenticate\login::create',
));

Router::map('/logout', array(
	'_controller' => 'controllers\authenticate\logout::create',
));

Router::map('/regster', array(
	'_controller' => 'controllers\authenticate\register::create',
));

Router::map('(/)', array(
	'_controller' => 'controllers\index::create',
));

Router::dispatch();
?>