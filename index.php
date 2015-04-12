<?php 
require_once "/includes/header.php"; //Initialize
if(Session::exists("homeMessage")){
	echo Session::flash('homeMessage');
}

$User = false;

try {
	if(Session::exists(Config::get("session/session_name"))){
    	$User = new User(Session::get(Config::get("session/session_name")));
	}
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
    $User = false;
}

if($User !== false){
	echo $User->Data->Account->Points; //Will automagically load Account since not currently present
	echo "<br><br><br><pre>", var_dump($User), "</pre>";
}
require_once "/includes/footer.php"; //Initialize
?>