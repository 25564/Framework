<?php
	interface Observable {
		public function attachObserver(Observer $dependent);
		public function detachObserver(Observer $dependent);
		public function notify();
	}
?>