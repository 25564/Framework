<?php
class Validation {
	public $Errors = array();
	public $Data;


	//Element Name Correlates to a private function prefixed with an underscore
	private $StandardRules = array(
		"required", "min", "max", "unique", "exists", "matches", "differs"
	);

	public function Validate($Data, $Rules){
		$this->Data = $Data;
		foreach($Rules as $InputKey => $KeyRules){
			foreach($KeyRules as $RuleName => $RuleValue){
				$CustomError = false;
				if(is_array($RuleValue)){
					if(isset($RuleValue["CustomError"])){
						$CustomError = $RuleValue["CustomError"];
					}
				}
				if(isset($this->Data[$InputKey])){
					$this->executeRule($InputKey, $RuleName, $RuleValue, $CustomError);
				} elseif(strtolower($RuleName) == "required") {
					if(!is_array($RuleValue)){
						$Settings = array("Value" => $RuleValue, "ValueName" => $InputKey);
					} else {
						$Settings = $RuleValue;
						$Settings["ValueName"] = $InputKey;
					}

					$this->checkError($this->_required("", $Settings), $InputKey, $CustomError);
				}
			}
		}
		if(empty($this->Errors)){
			return true;	
		}
		return $this->Errors;
	}
	private function executeRule($valueName, $RuleName, $ruleSettings, $customerror = false){
		if(in_array($RuleName, $this->StandardRules)){
			$Settings = array();
			if(!is_array($ruleSettings)){
				$Settings = array("Value" => $ruleSettings, "ValueName" => $valueName);
			} else {
				$Settings = $ruleSettings;
				$Settings["ValueName"] = $valueName;
			}
			$FunctionName = "_".strtolower($RuleName);
			
			$this->checkError($this->$FunctionName($this->Data[$valueName], $Settings), $valueName, $customerror);
		} else {
			$this->throwError("Unkown Rule '{$RuleName}'");
		}
	}
	private function checkError($State, $valueName, $CustomError = false){
		if($State !== true){
			if(!$CustomError){
				$this->throwError($State);
			} else {
				$this->ThrowCustomError($CustomError, $this->Data[$valueName]);
			}
		}
	}
	private function ThrowCustomError($Message, $Value){
		$Message = str_replace("{Value}", $Value, $Message);
		$this->throwError($Message);
	}
	private function throwError($string){
		array_push($this->Errors, escape($string));
	}
	/*************************************
				Standard Rules
	*************************************/
	private function _min($Value, $Settings){
		if(strlen($Value) >= $Settings["Value"]){
			return true;
		}
		return $Settings["ValueName"] . " must be at least " . $Settings["Value"] . " characters long";
	}
	private function _max($Value, $Settings){
		if(strlen($Value) <= $Settings["Value"]){
			return true;
		}
		return $Settings["ValueName"] . " can't be over " . $Settings["Value"] . " characters long";
	}
	private function _required($Value, $Settings){
		if(empty($Value) && $Settings["Value"] == true){
			return $Settings["ValueName"] . " is required";
		}	
		return true;
	}
	private function _unique($Value, $Settings){
		$exists = DB::getInstance()->table($Settings["Value"])->exists($Settings["ValueName"], $Value);
		if(!$exists){
			return true;
		}
		return "'{$Value}' already exists in table `{$Settings['Value']}`";
	}
	private function _exists($Value, $Settings){
		$exists = DB::getInstance()->table($Settings["Value"])->exists($Settings["ValueName"], $Value);
		if($exists === true){
			return true;
		}
		return "'{$Value}' does not exist in table `{$Settings['Value']}`";
	}
	private function _matches($Value, $Settings){
		if($Value == $this->Data[$Settings['Value']]){
			return true;
		}	

		return "{$Settings['ValueName']} does not match {$Settings['Value']}";
	}

	private function _differs($Value, $Settings){
		if($Value != $this->Data[$Settings['Value']]){
			return true;
		}	
		return "{$Settings['ValueName']} matches {$Settings['Value']}";
	}
}
?>