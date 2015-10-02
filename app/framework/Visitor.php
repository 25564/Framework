<?php 
class Visitor {
	public $Identity;
	public $reason="";

	public function initialize(){
		if($this->shouldTrack()){
			try {
				$this->Identity = new Visitor\Identity;
			} catch (Exception $e) {
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
		}
		Session::put("LastVisited", Time::get());
	}

	protected function shouldTrack(){
		if(Session::exists(Config::get("session/session_name"))){ //Are they logged in?
			$this->reason = "Authenticated";
			return false;
		}

		if(Session::exists("LastVisited")){
			/* 
			We don't want to track the user every time they open a page but rather every time they 
			open the site To achieve this we will only record the first page load every ten minutes
			*/

			//----------- Currently 1 for testing -----------//
			if(Time::get() - Session::get("LastVisited") < 1){ 
				$this->reason = "Last Visit";
				return false;
			}
		}

		return true;
	}
}