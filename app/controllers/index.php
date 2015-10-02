<?php
namespace controllers;

class index extends \Core\Controller {


	public function create(array $Params = array()){
		$this->loadModel();
		$this->loadView();

		$this->Model->create();
		$this->View->create($this->Model);
	} 
}

?>