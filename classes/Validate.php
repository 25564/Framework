<?php
class Validate {
	private $_passed = false, 
			$_errors = array(),
			$_db = null;	
	
	public function __construct() {
		$this->_db = DB::getInstance(); //Initiates the DB	
	}
	
	public function check($source, $items = array()) {
		//Form checking function
		//This function can be called by 
		/*
		$validater = new Validate();
		if(Input::exists()){
			$valid = $validater->check('$_POST', array(
				'username' => array(
					'required' => true, //Is required
					'min' 	   => 2,	//Minium length
					'max' 	   => 40	//Maximum Length
				),
				'password' => array(
					'required' => true,
					'min' 	   => 6,
					'differ'   => 'username' //Cannot be the same as username
				),
				'email' => array(
					'required' => true,
					'email'    => true		//Must be a valid email
				),
				'password2' => array(
					'required' => true,
					'matches'  => 'password'//Must have the same value as password
				)
			));
			if($valid->passed()){
				//Register
			} else {
				var_dump($valid->errors);
			}
		}
		//This simple registration form validator is a prime example of how this class can be used
		*/
		foreach($items as $item => $rules){
			foreach($rules as $rule => $rule_value){
				
				$value = trim($source[$item]);
				if($rule === 'required' && empty($value) && $rule_value == true){
					$this->addError("{$item} is required");
				} elseif(!empty($value) && !empty($value)){
					switch($rule){
						case 'min': //Minimum Length - INT
							if(strlen($value) < $rule_value){
								$this->addError("{$item} must be a minium of {$rule_value} characters");
							}
						break;
						case 'numeric': //Only a number - BOOL
							if($rule === 'numeric' && !is_numeric($value) && $rule_value == true){
								$this->addError("{$item} must be a number");
							}
						break;
						case 'email':  //Is valid email - BOOL
							if($rule_value == true && !filter_var($value, FILTER_VALIDATE_EMAIL)){
								$this->addError("{$item} is not a valid email");
							}
						break;
						case 'max': //Maximum Length - INT
							if(strlen($value) > $rule_value){
								$this->addError("{$item} has a maximum of {$rule_value} characters");
							}
						break;
						case 'matches':  //Equal to other input - STRING
							if($value != $source[$rule_value]){
								$this->addError("{$item} does not match {$rule_value}");
							}
						break;
						case 'differ':  //Different to other input - STRING
							if($value == $source[$rule_value]){
								$this->addError("{$item} is the same as {$rule_value}");
							}
						break;
						case 'unique':   //Form name must be equal to Table Column - Table Name - STRING 
							$check = $this->_db->table($rule_value)->where('LOWER(' . $item . ')', strtolower($value))->count();
							if($check > 0){
								$this->addError("{$item} already exists");
							}
						break;
						case 'exists':   //Form name must be equal to Table Column - Table Name - STRING
							$check = $this->_db->table($rule_value)->where('LOWER(' . $item . ')', strtolower($value))->count();
							if($check == 0){
								$this->addError("{$item} does not exist");
							}
						break;
					}
				}
				
			}
		}
		
		if(empty($this->_errors)){
			$this->_passed = true;
			return true;
		}
		return false;
	}

	public function errors() {
		//Get Method returns array of errors
		return $this->_errors;	
	}
	
	public function passed(){
		//returns bool if passed
		return $this->_passed;	
	}
	
	private function addError($error) {
		//private helper function. Adds an error to the error array
		$this->_errors[] = $error;	
	}
}
?>