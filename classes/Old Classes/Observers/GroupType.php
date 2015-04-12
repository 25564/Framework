<?php
class Observer_GroupType implements Observer{
	public function update(Observable &$subject){
		if($subject->data()->group == 0){
			$subject->block();
			$subject->setError("Currently Alpha testers only");	
		}
	}
}
?>