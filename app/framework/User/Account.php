<?php
namespace User;

class Account implements \Interfaces\SubTable {

	private $_Account;
	public $_UserID;

	public function __construct(){
	}

	public function form($Params){
		$Data = \DB::getInstance()->table("Users")->FetchArray(true)->where("UserID", $Params)->get(1); //Get User Data
		$this->_UserID = $Params;

		$this->_Account = new \Helpers\SaveMetaTable($Data, "Users", $Params);

		return $this->_Account;
	}

}

?>