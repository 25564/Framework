<?php
class Report {
	private $_db,
			$_data,
			$_user = null,
			$_exists = false;
	
	public function __construct($report = null){
		$this->_db = DB::getInstance();	//Set local variable to the DB for convience 	
		$this->_user = new User(); //Sets local variable for convience
		if($report){ //Checks that something was set
			$data = $this->_db->table('reports')->where('id', $report)->where('visible', '0')->get(); //Query DB
			if(count($data) > 0){ //If report exists and is visible
				$this->_data = $data; //Sets up Local variable
				$this->_exists = true; //Sets up Local variable
			}
		}
	}
	
	public function create($fields){
		//Creates new report in DB
		if($this->spamFree($_SERVER['REMOTE_ADDR'], $fields['text'])){ //Confirm that its not spam
			if(!$this->_db->table('reports')->insert($fields)){ //Insert into DB
				throw new Exception('there was a problem creating the report.');
			} else {
				return true;	
			}
		}
	}
	
	private function inString($needle, $haystack, $delimeter = ','){
		//Helper Function for dealing with Views
		$Array = explode($delimeter, $haystack);
		if(in_array($needle, $Array)){
			return true;	
		}
		else {
			return false;	
		}
	}
	
	private function addToString($needle, $haystack, $delimeter = ','){
		//Helper Function for dealing with Views
		return $haystack . $delimeter . $needle;
	}
	
	private function removeFromString($needle, $haystack, $delimeter = ',') {
		//Helper Function for dealing with Views
		$Array = explode($delimeter, $haystack);
		while(($i = array_search($needle, $Array)) !== false) {
			unset($Array[$i]);
		}
		return implode($delimeter, $Array);
	}
	
	public function view(){
		//Increments the view counter and adds the user to the viewed list
		if($this->_user->isLoggedIn()){
			if($this->_user->data()->username != $this->data()->author && !$this->inString($this->_user->data()->username, $this->data()->view_users)){
				$this->_db->update('reports', array('id', '=', $this->data()->id), array(
					'view_count' => $this->data()->view_count + 1, 
					'view_users' => $this->addToString($this->_user->data()->username, $this->data()->view_users)
				));
			}
		} else {
			$this->_db->update('reports', array('id', '=', $this->data()->id), array('view_count' => $this->data()->view_count+1));
		}
	}
	
	private function spamFree($ip, $text=""){
		//Returns true if not spam
		if($this->_user->isLoggedIn()){
			$AccountSpam = $this->_db->table('reports')->where('author', $this->_user->data()->username)->where('time_posted','>',Time::get()-604800)->count();
			if($AccountSpam >= Config::get("reports/spam_filter_count")){
				throw new Exception('You can only post two reports a week in order to prevent spam');
				return false;
			}
		}		
		
		$IdenticalCheck = $this->_db->table('reports')->where('LOWER(`text`)', strtolower($text))->count();
		if($IdenticalCheck != 0){
			throw new Exception('Identical post was detected');
			return false;
		} 
		
		$IPSpam = $this->_db->table('reports')->where('ip',  $ip)->where('time_posted', '>', Time::get() - 604800)->count();
		if($IPSpam >= Config::get("reports/spam_filter_count")){
			throw new Exception('This IP address has hit its maximum reports for this week');
			return false;
		}
		
		return true;
	}
	
	public function getPage($Page=1) {
		$PostCount = Config::get("reports/reports_displayed_per_page");
		return $this->_db->table("reports")->where('visible', 0)->skip(($Page-1)*$PostCount)->orderBy('id', 'DESC')->get($PostCount);	
	}
	
	public function getPagesNeeded(){
		$PostCount = Config::get("reports/reports_displayed_per_page");
		$Query = $this->_db->table("reports")->where('visible', 0);
		return ceil($Query->count()/2);
	}
	
	public function hide($id){
		//Hides a report making it borderline impossible to access outside of phpmyadmin - Basically deletes it
		//Once this function has been called on a report it can no longer be acted upon by this class essentially freezing the report in its current state.
		return $this->_db->table("reports")->where('id', $id)->update(array('visible' => 1));
	}
	
	public function exists(){
		//Basic get function returns a bool does the report exist
		return $this->_exists;
	}
	
	public function data(){
		//Basic get function returns the Table row relating to the report in object form
		return $this->_data;	
	}
}
?>