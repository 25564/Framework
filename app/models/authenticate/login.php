<?php
namespace models\authenticate;

class login extends \Core\Model {

	private $Errors = array();
	private $User;
	private $Authenticated;

	public function Create(array $Params = array()){
	} 

	public function validateInput($Input = array()){
		$validater = new \Validation();
		if(\Token::check($Input["token"])){
			$valid = $validater->Validate($Input, array(
				'Username' => array(
					'required' => true,   	//Is required
					'min' 	   => 3,	  	//Minium length
					'max' 	   => 35,	  	//Maximum Length
					'exists'   => array(	//Must exists
						"Value" => 'Users',
						'CustomError'=> "{Value} is not a registered User"
					)
				),
				'Password' => array(
					'required' => true,
					'min' 	   => 5,
					'differs'   => 'Username' //Cannot be the same as username
				),		
			));
			if($valid === true){
				//Attempt to Authenticate
				$this->User = new \User();
				try {
					$this->Authenticated = $this->User->Authenticate(escape($Input["Username"]), escape($Input["Password"]), $Input["remember"]);
				} catch (\Exception $e) {
					$this->Errors = array($e->getMessage());
				}
			} else {
				$this->Errors = $valid;
			}
		}
	}

	public function getErrors(){
		return $this->Errors;
	}

	public function hasErrors(){
		if(count($this->Errors) > 0){
			return true;
		}
		return false;
	}

	public function getUser(){
		return $this->User;
	}

	public function isAuthenticated(){
		return $this->Authenticated;
	}
}

?>