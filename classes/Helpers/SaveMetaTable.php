<?php
namespace Helpers;

class SaveMetaTable extends \MetaTable{
	private $_Table;
	private $_UserID;

	public function __construct($Data, $Table, $UserID){
		$this->_Table = $Table;
		$this->_UserID = $UserID;
		foreach ($Data[0] as $key => $value) {
			if (is_int($key)) { //Remove all Numerical keys so no Duplicates
				unset($Data[0][$key]);
			}
		}
		
		parent::__construct($Data[0]);

		$this->AddMeta("Modify", function($self, $Params){ //Update DB when values change
			\DB::getInstance()->table($this->_Table)->where("UserID", $this->_UserID)->update(array(
				$Params["Offset"] => $Params["NewValue"]
			));
			$self->_data[$Params["Offset"]] = $Params["NewValue"];
			return $self->_data;
		});

		$this->AddMeta("NewIndex", function($self, $Params){
			return $self->_data; //Block new elements from being created. Can only modify existing Elements
		});
	}
}