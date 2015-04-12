<?php

class User {
	public $Data;
	private $Config = array(
		"Modules" => array(//Modules under the User folder which can be utilized
			"Account" //What can the User do
		)
	);
	

	public function __construct($ID){
		$this->Data = new MetaTable(array(
			"UserID" => $ID
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
	}

	public function form(){
		return $this->Data;	
	}
}

?>