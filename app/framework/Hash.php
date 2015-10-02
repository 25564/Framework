<?php 
class Hash {
	
	public static function make($string, $salt = ''){
		return hash("whirlpool", $salt . $string . $salt); //Hashs the string after double salting it
	}
	
	public static function salt($length=16){
		$randomValues = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',1,2,3,4,5,6,7,8,9,0); //Array of possible characters in salt
	
		if($length > 0) { 
			$rand_id=""; //String to be returned
			for($i=1; $i <= $length; $i++) {
				$num = mt_rand(0,count($randomValues) - 1);
				$rand_id .= $randomValues[$num];
			}
		}
		return $rand_id;
	}
	
	public static function unique(){
		return self::make(uniqid());		
	}
}
?>