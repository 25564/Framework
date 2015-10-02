<?php
namespace Core;

abstract class Controller extends Core implements \Interfaces\Controller {
	public $Model = false;
	public $View = false;

	abstract public function create(array $Params = array());


	public function __construct(){
		$this->assumePageName();
	}

	public function loadModel($Model = false){
		if($this->Model == false){
			if($Model == false){
				$Model = "\\models\\" . $this->getPageName();
			}
			$this->Model = new $Model();
		}
	}

	public function loadView($View = false){
		if($this->View == false){
			if($View == false){
				$View = "\\views\\" . $this->getPageName();
			}
			$this->View = new $View();
		}
	}

	public function setAlert($Alert = "", $Page = ""){
		if($Page == ""){//Ties to the current Page by Default
			\Session::flash($this->getPageName() . 'Alert', $Alert);
		} else {
			\Session::put($Page . 'Alert', $Alert);
		}
	}
}
