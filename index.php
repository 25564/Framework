<?php 
require_once "/includes/header.php"; //Initialize
if(Session::exists("homeMessage")){
	echo Session::flash('homeMessage');
}

//echo "<b>Current State:</b></br><pre>", var_dump($Data->_data), "</pre><br>";

/*$Data["__Modify"] = function($self, $Params){
	$StringPos = strpos($Params["Offset"], "#");
	if($StringPos === 0) {} else { //It is easier this way.
		$self->_data[$Params["Offset"]] = $Params["Value"];
	}
	return $self->_data;
};*/

$User = new User(1);

$User->Data->Account->Points = 6;

echo "<pre>", var_dump($User->Data->Account), "</pre>";

require_once "/includes/footer.php"; //Initialize
?>