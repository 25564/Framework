<?php
require_once $_SERVER['DOCUMENT_ROOT']  . "/core/init.php"; //Initialize

$user = new User();
$user->logout();
Session::flash('homeMessage', 'You were logged out successfully.');
Redirect::to("home");
?>