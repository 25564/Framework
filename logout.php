<?php
require_once "/core/init.php"; //Initialize

if(Session::exists("UserID")){
	Cookie::delete(Config::get("remember/cookie_name"));
	DB::getInstance()->table("Users")->where("user_id", Session::get("UserID"));
	Session::delete("UserID");
	Session::flash('homeMessage', 'You were logged out successfully.');
}
Redirect::to("home");
?>