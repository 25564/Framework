<?php
namespace Interfaces;

interface Controller {
    public function create(array $Params = array());
    public function setPageName($Name);
	public function getPageName();
}