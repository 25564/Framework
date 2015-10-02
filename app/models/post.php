<?php
namespace models;

class post extends \Core\Model {

	public $Post = false;
	public $Tags = [];

	public function Create(array $Params = array()){} 

	public function getPost($ID = 1){
		if($this->Post == false){
			$this->Post = \DB::getInstance()->table("blogposts")->where("id", $ID)->get()[0];
		}
		return $this->Post;
	}

	public function getPostTags($ID = 1){
		if($this->Tags == false){
			$Tags = \DB::getInstance()->table("blogtags")->where("id", $ID)->get();
			foreach($Tags as $Tag){
				$this->Tags[] = $Tag->tag;
			}
		}
		return $this->Tags;
	}


}

?>