<?php
namespace controllers\authenticate;

class register extends \Core\Controller {
	public function create(array $Params = array()){
		//Load Everything
		$this->loadModel();
		$this->loadView();

		if(\Input::exists()){
			$this->Model->validateInput($_POST);
			if($this->Model->registeredUser() == true){
				echo $this->Model->getUser()->Data->UserID;
				\Session::put(\Config::get("session/session_name"), $this->Model->getUser()->Data->UserID);
				$this->setAlert('You were registered successfully.', "index");
				\Redirect::to("home");
			}
		}

		//Testing everything is loaded
		$this->Model->create();
		$this->View->create($this->Model);
	} 
}

?>