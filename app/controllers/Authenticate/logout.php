<?php
namespace controllers\authenticate;

class logout extends \Core\Controller {
	public function create(array $Params = array()){
		if(\Session::exists("UserID")){
			\Cookie::delete(\Config::get("remember/cookie_name"));
			\DB::getInstance()->table("Users")->where("user_id", \Session::get("UserID"));
			\Session::delete("UserID");
			\Session::flash('homeMessage', 'You were logged out successfully.');
		}
		\Redirect::to("home");
	}
}