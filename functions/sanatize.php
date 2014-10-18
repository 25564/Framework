<?php 
function escape($string) { //Deprecated - Use Secure::escape()
	return htmlentities($string, ENT_QUOTES, 'UTF-8');
}
?>
