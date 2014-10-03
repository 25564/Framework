<?php
class BlogComment implements Countable, Iterator{
	private $_db,
			$_data = false,
			$_user = null,
			$_position = 0;
				
	public function __construct($BlogPost = null){
		$this->_db = DB::getInstance();	//Set local variable to the DB for convience 	
		$this->_user = new User(); //Sets local variable for convience
		if($BlogPost){ //Checks that something was set
			$Data = $this->_db->table('blog_comments')->where('parent_id',$BlogPost)->where('visible', '0')->get();
			if(count($Data) > 0){ //If post exists and is visible
				$this->_data = $Data;
			}
		}
	}

	public function create($fields){
		if($this->_user->isLoggedIn()){ //Does user have permission
			if(!$this->_db->table('blog_comments')->insert($fields)){ //Insert into DB
				throw new Exception('Error posting comment');
			} else {
				return true;	
			}
		} else {
			throw new Exception('You need to be logged in to do this.');	
		}
	}

	public function count(){
		if($this->_data){
			return count($this->_data);
		}
		return 0;
	}

	public function data() {
		return $this->_data;
	}

	public function rewind() {
        $this->_position = 0;
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