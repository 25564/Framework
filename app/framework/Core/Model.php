<?php
namespace Core;

abstract class Model extends Core implements \Interfaces\Model {

	abstract public function create(array $Params = array());

	public function __construct(){
		$this->assumePageName();
	}

	public function getAlert(){
		if($this->hasAlert() == true){
			return \Session::flash($this->getPageName() . 'Alert');
		}
		return "";
	}

	public function hasAlert(){
		if(\Session::exists($this->getPageName() . 'Alert')){
			return true;
		}
		return false;	
	}

	public function setAlert($Alert = "", $Page = ""){
		if($Page == ""){//Ties to the current Page by Default
			\Session::flash($this->getPageName() . 'Alert', $Alert);
		} else {
			\Session::flash($Page . 'Alert', $Alert);
		}
	}
}
