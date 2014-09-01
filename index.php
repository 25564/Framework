<?php 
require_once $_SERVER['DOCUMENT_ROOT']  . "/includes/header.php"; //Initialize
if(Session::exists("homeMessage")){
	echo Session::flash('homeMessage');
}

$CurrentUser = new User();
if($CurrentUser->isLoggedIn()){
	echo 'Welcome back ', $CurrentUser->data()->username, '<br>';
}
?>

<?php 
require_once $_SERVER['DOCUMENT_ROOT']  . "/includes/footer.php"; //Initialize
?>