<?php
namespace models;

class tag extends \Core\Model {

	public $BlogPosts = array();
	public $Page = 1;
	public $PostPerPage = 12;
	public $Tag = false;

	public function Create(array $Params = array()){} 

	public function loadPosts($Amount = 12, $Start = 0){
		if($this->Tag != false){
			$PostsWithTag = \DB::getInstance()->table("blogtags")->where("tag", $this->Tag)->get();
			
			$Posts = array();
			foreach($PostsWithTag as $Post){
				$Posts[] = $Post->id;
			}

			$sql = "SELECT T1.*, GROUP_CONCAT(T2.tag SEPARATOR ':') AS tags
					FROM  `blogposts` AS T1
					INNER JOIN `blogtags` AS T2 ON T1.id = T2.id
					WHERE T1.id in (" . implode(',', array_map('intval', $Posts)) . ")
					GROUP BY T1.id";

			$this->BlogPosts = \DB::getInstance($sql)->raw($sql);
		}
	}
}

?>