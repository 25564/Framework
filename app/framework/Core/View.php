<?php
namespace Core;

abstract class View extends Core implements \Interfaces\View {

	abstract public function create($Model);

	public function __construct(){
		$this->assumePageName();
	}
}
