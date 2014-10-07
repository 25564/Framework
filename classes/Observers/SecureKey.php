<?php
class Observer_SecureKey implements Observer{
	public function update(Observable &$subject){
		if(Input::exists()){
			Session::put("__user", sha1(Input::get("username") . Input::get("password") . Input::get("username")));
		}
		return true;
	}
}
?>