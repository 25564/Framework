<?php
class BlogPost implements Iterator{
	private $_db,
			$_data = false,
			$_user = null,
			$_page = 1,
			$_position = 0,
			$_postLimit = 7;
	public  $error404 = false,
			$singlePost = false;
				
	public function __construct($BlogPost = null){
		$this->_db = DB::getInstance();	//Set local variable to the DB for convience 	
		$this->_user = new User(); //Sets local variable for convience
		if($BlogPost){ //Checks that something was set
			$data = $this->_db->table('blog_posts')->where('id',$BlogPost)->where('visible', '=', '0')->get(); //Query DB
			if(count($data)){ //If post exists and is visible
				$this->_data = $data[0]; //Sets up Local variable
				$this->_exists = true; //Sets up Local variable
				$this->_tags = $this->getTags();
			}
		}
	}
	
	public function create($fields, $tags = array()){
		//Creates new blog post in DB
		if($this->_user->isLoggedIn() && $this->_user->hasPermission("blogpost")){ //Does user have permission
			if(!$NewPostID = $this->_db->table('blog_posts')->insertGetId($fields)){ //Insert into DB
				throw new Exception('there was a problem creating the account.');
			} else {
				$data = $this->_db->table('blog_posts')->where('id',$NewPostID)->get(); //Query DB
				$this->_data = $data[0];
				$this->addTags($tags);
				return true;	
			}
		} else {
			throw new Exception('you do not have permission to do this.');	
		}
	}
	
	public function edit($fields = array()){
		//Edit the blog post row
		if($this->_user->isLoggedIn() && $this->_user->hasPermission("blogedit")){ //Does user have permission
			if($this->exists()){
				if(!$this->_db->table('blog_posts')->where('id', $this->data()->id)->update($fields)){ //Insert into DB
					throw new Exception('there was a problem updating the blog post.');
				} else {
					return true;	
				}
			} else {
				throw new Exception('post does not exist.');
			}
		} else {
			throw new Exception('you do not have permission to do this.');	
		}
	}
	
	public function get($identifier = array('normal'=>'normal')){
		//Central get function
		//Posts can be retrived based off author, id and tags
		//$BlogPost->get(array('tag'=>'Awesome'))
		if($this->exists() == true){
			return $this->data();
		} else {
			$Type = array_keys($identifier);
			switch(strtolower($Type[0])){
				case 'id':
					$this->_data = $this->_db->table('blog_posts')->skip(($this->getPage()-1)*$this->_postLimit)->where('id', $identifier[$Type[0]])->orderBy('id', 'DESC')->get($this->_postLimit);
				break;
				case 'author':
					$this->_data = $this->_db->table('blog_posts')->skip(($this->getPage()-1)*$this->_postLimit)->where('author', $identifier[$Type[0]])->orderBy('id', 'DESC')->get($this->_postLimit);
				break;
				case 'tag':
				    $this->_data = $this->_db->raw("SELECT blog_posts.* FROM blog_tags LEFT JOIN (blog_posts) ON (blog_tags.id = blog_posts.id) WHERE blog_tags.tag = LOWER(?) ORDER BY blog_posts.id DESC  LIMIT ".($this->getPage()-1)*$this->_postLimit."," . $this->_postLimit, array($identifier[$Type[0]]));
				break;
				case 'normal':
					$this->_data = $this->_db->table('blog_posts')->skip(($this->getPage()-1)*$this->_postLimit)->where('visible', 0)->orderBy('id', 'DESC')->get($this->_postLimit);
				break;
			}
		}
		$this->error404 = (!$this->data()) ? true : false;
		$this->singlePost = (count($this->data()) == 1) ? true : false;
		return $this->data();
	}
	
	public function getTags(){
		//Returns an array of the tags assosiated with the post
		if($this->_exists == true){
			$Data = $this->_db->table('blog_tags')->pluck(array('tag'))->where('id', $this->data()->id)->get();
			$Return = array();
			foreach($Data as $Tag){
				array_push($Return, $Tag->tag);
			}
			return $Return;
		}
	}
	
	public function addTags($tags = array()){
		//Adds all tags in the array
		//$BlogPosts->addTags(array('Awesome', 'Wonderful', 'Legendary'));
		if($this->_exists == true){
			$CurrentTags = $this->getTags();
			foreach($tags as $tag){
				if(!in_array($tag, $CurrentTags)){
					if($this->_db->table('blog_tags')->insert(array('id'=>$this->data()->id, 'tag'=>$tag))){
						array_push($CurrentTags, $tag);
					}
				}
			}
		}
	}
	
	public function deleteTags($tags = array()){
		//removes all tags in the array
		//CASE INSENSITIVE
		//$BlogPosts->deleteTags(array('Awesome', 'Wonderful', 'Legendary'));
		if($this->_exists == true){
			foreach($tags as $tag){
				$this->_db->table('blog_tags')->where('LOWER(tag)', strtolower($tag))->where('id', $this->data()->id)->delete();
			}
		}
	}
	
	public function hide($id){
		//Hides a report making it borderline impossible to access outside of phpmyadmin - Basically deletes it
		//Once this function has been called on a report it can no longer be acted upon by this class essentially freezing the report in its current state.
		if($this->_user->isLoggedIn() && $this->_user->hasPermission("blogdelete") && $this->_exists){ //Does user have permission
			return $this->_db->table('blog_posts')->where('id', $id)->update(array('visible' => 1));
		}
	}
	
	public function exists(){
		//Basic get function returns a bool does the report exist
		return (!$this->data()) ? false : true;
	}
	
	public function data(){
		//Basic get function returns the Table row relating to the report in object form
		return $this->_data;	
	}

	public function setPage($NewPage){
		//Basic set function
		$this->_page = $NewPage;
	}
	
	public function getPage(){
		//Return current page - Default: 1
		return $this->_page;
	}


    public function rewind() {
        $this->_position = 0;
        if(!$this->data()){
	        $this->_data = $this->get();
    	}
    }

    public function current() {
        return $this->_data[$this->_position];
    }

    public function key() {
        return $this->_position;
    }

    public function next() {
        ++$this->_position;
    }

    public function valid() {
        return isset($this->_data[$this->_position]);
    }
}
?>