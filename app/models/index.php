<?php
namespace models;

class index extends \Core\Model {

	public $BlogPosts = array();
	public $Page = 1;
	public $PostPerPage = 12;

	public function Create(array $Params = array()){} 

	public function loadPosts($Amount = 12, $Start = 0){
		$sql = "SELECT T1.*, GROUP_CONCAT(T2.tag SEPARATOR ':') AS tags
				FROM  blogposts AS T1
				LEFT JOIN blogtags AS T2 ON T1.id = T2.id
				GROUP BY T1.id
				LIMIT 0,12";

		$this->BlogPosts = \DB::getInstance($sql)->raw($sql);
	}
}

?>