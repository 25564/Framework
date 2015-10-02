<?php
namespace models\authenticate;

class register extends \Core\Model {

	private $Errors = array();
	private $Registered = false;
	private $User = false;

	public function Create(array $Params = array()){
	} 

	public function validateInput($Input = array()){
		$validater = new \Validation();
		if(\Token::check($Input["token"])){
			$valid = $validater->Validate($_POST, array(
				'Username' => array(
					'required' => true, //Is required
					'min' 	   => 3,	//Minium length
					'max' 	   => 35,	//Maximum Length
					'unique'   => 'Users'//Must be only one in DB
				),
				'Password' => array(
					'required' => true,
					'min' 	   => 5,
					'differs'   => 'Username' //Cannot be the same as username
				),
				'Password2' => array(
					'required' => true,
					'matches'  => 'Password'//Must have the same value as password
				)
			));
			if($valid === true){
				//Register the User
				$salt = \Hash::salt();
				$hashed = \Hash::make($Input["Password"], $salt);
				$this->User = new \User();
				try {
					$newUser = $this->User->Create(array(
						'Username' => escape($Input["Username"]),
						'Password' => $hashed,
						'Salt' 	   => $salt,
					));
					$this->Registered = $newUser;
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

	public function registeredUser() {
		return $this->Registered;
	}

	public function getUser(){
		return $this->User;
	}
}

?>