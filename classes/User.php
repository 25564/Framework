<?php

class User {
	public $Data = false;
	private $Config = array(
		"Modules" => array(//Modules under the User folder which can be utilized
			"Account" //What can the User do
		)
	);
	

	public function __construct($UserID = 0){
		if($UserID != 0){
			if(count(DB::getInstance()->table("Users")->where("UserID", $UserID)) > 0){
				$this->form($UserID);
			} else {
				throw new Exception('Account ID Invalid');
			}
		}
	}

	private function form($UserID) {
		$this->Data = new MetaTable(array( //Base Structure
			"UserID" => $UserID
		));

		//Load in sections as they are needed
		$this->Data->AddMeta("Index", function($self, $Params){ //Use AddMeta where possible for compatibility reasons
			$Offset = $Params["Offset"];
			if(in_array($Offset, $this->Config["Modules"])){
				$ClassName = "User\\" . $Offset;
				$NewClass = new $ClassName();
				$this->Data[$Offset] = $NewClass->form($this->Data["UserID"]);
				return $this->Data[$Offset];
			}
			return null;
		});

		return $this->Data; //Should this return $this and not just the Meta Table?
	}

	public function Create($Data){
		$UserID = DB::getInstance()->table("Users")->insertGetId($Data);
		if(!is_null($UserID)){
			try {
			    $UserData = $this->form($UserID);
			    if($UserData->Account->Username == $Data["Username"]){
			    	return true;
			    } else {
			    	throw new Exception('Error Creating User');
			    }
			} catch (Exception $e) {
			    echo 'Caught exception: ',  $e->getMessage(), "\n";
			    $User = false;
			}
		}
	}

	public function Authenticate($Username = false, $Password = false, $Remember = false){
		if($Username !== false && $Password !== false){ //Check something was passed
			$UserData = DB::getInstance()->table("Users")->where("Username", $Username)->get(1)[0];
			$HashedPassAttempt = Hash::make(Input::get("Password"), $UserData->Salt); //Hash the password attempt

			if($HashedPassAttempt == $UserData->Password){
				Session::put("UserID", $UserData->UserID);
				if($Remember == 'on'){ //If they want to be remembered
					$hashCheck = DB::getInstance()->table("user_sessions")->where('user_id', $UserData->UserID)->get(); //Check for existing session
					if(count($hashCheck) == 0){ //If there is not an existing hash
						$hash = Hash::unique();
						
						DB::getInstance()->table('user_sessions')->insert(array( //Insert Data
							'user_id' => $UserData->UserID,
							'hash' => $hash
						));
					} else { //use existing hash if found
						$hash = $hashCheck[0]->hash;	
					}
					
					$Cookie = Cookie::put(Config::get("remember/cookie_name"), $hash, Config::get("remember/cookie_expiry")); //Set cookie
				}
				return $this->form($UserData->UserID); //Return User Data
			} else {
				throw new Exception('Invalid Username or Password');
			}
		} else {
			throw new Exception('Invalid Username or Password');
		}
		return false;
	}
}

?>