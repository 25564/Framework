<?php
namespace controllers\authenticate;

class login extends \Core\Controller {
	public function create(array $Params = array()){
		//Load Everything
		$this->loadModel();
		$this->loadView();

		if(\Input::exists()){
			$this->Model->validateInput($_POST);
			var_dump($this->Model->isAuthenticated());
			if($this->Model->isAuthenticated() == true){
				$this->setAlert('You were logged in successfully.', "index");
				\Redirect::to("home");
			}
		}

		//Lets Go
		$this->Model->create();
		$this->View->create($this->Model);
	} 
}

?>