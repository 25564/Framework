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
		$this->Data = new MetaTable(array(
			"UserID" => $UserID
		));

		//Load in sections as they are needed
		$this->Data->AddMeta("Index", function($self, $Params){ //Use AddMeta where possible for compadibility reasons
			$Offset = $Params["Offset"];
			if(in_array($Offset, $this->Config["Modules"])){
				$ClassName = "User\\" . $Offset;
				$NewClass = new $ClassName();
				$this->Data[$Offset] = $NewClass->form($this->Data["UserID"]);
				return $this->Data[$Offset];
			}
			return null;
		});

		return $this->Data;
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
}

?>