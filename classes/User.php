<?php 
class User implements Observable {
	private $_db, 
			$_data, 
			$_sessionName,
			$_cookieName,
			$_isLoggedIn = false,
			$_observers = array(),
			$_blocked = false, //Block creation and authentication
			$_error = "Password or Username is incorrect",
			$_UserID;

	public function __construct($user = null){
		$this->_db = DB::getInstance();	
		$this->_sessionName = Config::get("session/session_name"); //Store locally for efficiency
		$this->_cookieName = Config::get("remember/cookie_name"); //Store locally for efficiency		
		if(!$user){ //Was a custom user set
			if(Session::exists($this->_sessionName)){
				$user = Session::get($this->_sessionName); 
				if($this->find($user)){
					$this->_isLoggedIn = true;
				} else {
					$this->logout();	
				}
			}
		}
		else {
			$this->find($user);
		}
	}

	public function update($fields = array(), $id = null) {
		//Update user table -- Simple abstract function
		
		if(!$id && $this->isLoggedIn()){
			$i = $this->data()->id;
		}
		
		if(!$this->_db->table('user')->where('user_id', $id)->update($fields)) {
			throw new Exception("There was an issue updating");
		}
	}

	public function create($fields){
		//Creates new user in DB
		$Exists = $this->find($fields['username'], true);
		if(isset($fields['username']) && !$this->exists($fields['username'])){
			$this->notify();
			if(!$this->_blocked){
				if(!$this->_db->insert('user', $fields)){
					throw new Exception('There was a problem creating the account.');
				} else {
					return true;	
				}
			}
		}
	}

	public function hasPermission($key){
		//Checks group of users then checks if that group has permission in question
		$group = Group::getPermissions($this->data()->group);
		if($group){
			if(isset($permissions[$key])){
				return true;
			} elseif(isset($permissions['admin'])){
				return true;
			}
		}
		return false;
	}
	
	public function find($user = null, $username = false){
		//Check if user exists and find data
		//Finds user by ID by default, however the second parameter changes this to username
		if($user){
			$field = ($username == false ? 'user_id' : 'username');
			$data = $this->_db->table("user")->where($field , $user)->get();
			if(count($data)){
				$this->_data = $data[0];
				return true;
			}
		} elseif($this->_isLoggedIn === true) {
			$field = ($username == false ? 'id' : 'username');
			$data = $this->_db->table("user")->where('user_id', Session::get($this->_sessionName))->get();
			if(count($data) > 0){
				$this->_data = $data[0];
				return true;
			}	
		}
		return false;
	}
	
	public function exists() {
		return (!empty($this->_data)) ? true : false;
	}
	
	public function login($username = null, $password = null, $remember = false){
		//Logs in the user
		if($this->find($username, true)){ //Check user exists
			if(!$username && !$password && $this->exists()) {
				Session::put($this->_sessionName, $this->data()->user_id);
			} else {
				if($username){
					if($this->data()->password === Hash::make($password, $this->data()->salt)){ //Check password submitted
						$this->notify();
						if(!$this->_blocked){
							Session::put($this->_sessionName, $this->data()->user_id); //Creates session
							if($remember == 'on'){ //If they want to be remembered
								$hashCheck = $this->_db->table("user_sessions")->where('user_id', $this->data()->user_id)->get(); //Check for existing session
								if(count($hashCheck) == 0){ //If there is not an existing hash
									$hash = Hash::unique();
									
									$this->_db->table('user_sessions')->insert(array(
										'user_id' => $this->data()->user_id,
										'hash' => $hash
									));
								} else { //use existing hash if found
									$hash = $hashCheck[0]->hash;	
								}
								
								$Cookie = Cookie::put($this->_cookieName, $hash, Config::get("remember/cookie_expiry")); //Set cookie
							}
							return true;
						}
					}
				}
			}
		}
		return false;
	}

	public function attachObserver(Observer $object) {
		$this->_observers[] = $object;
	}

	public function detachObserver(Observer $object){
		foreach ($this->_observers as $index => $observer) {
			if ($object == $observer) {
				unset($this->_observers[$index]);
			}
		}
	}

	public function notify() {
		foreach ($this->_observers as $observer) {
			$observer->update($this);
		}
	}

	public function block($type = true){
		$this->_blocked = $type;
	}

	public function data() {
		return $this->_data; //Abstract get function
	}
	
	public function logout(){
		//Destroys Session, Cookie and removes session from DB
		Session::delete($this->_sessionName);	
		Cookie::delete($this->_cookieName);
		$this->_db->table('user_session')->where("user_id", $this->data()->user_id)->delete();
	}
	
	public function isLoggedIn() {
		return $this->_isLoggedIn; //Abstract get function	
	}	

	public function error() {
		return $this->_error;
	}

	public function setError($error){
		$this->_error = $error;
	}
}
?>