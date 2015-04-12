<?php 
function escape($string) { //Not recommended. Use Secure::escape() if possible
	return htmlentities($string, ENT_QUOTES, 'UTF-8');
}
?>
