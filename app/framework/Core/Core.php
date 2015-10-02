<?php
namespace Core;

abstract class Core {
	private $PageName;

	public function setPageName($Name = ""){
		$this->PageName = $Name;
	}
	
	public function getPageName(){
		return $this->PageName;
	}

	public function assumePageName(){
		$Name = (explode("\\", get_class($this)));
		if(count($Name) > 0){
			array_shift($Name);
		}
		$this->setPageName(implode("\\", $Name));
	}
}