<?php
class Template
{
    private $template;
    function __construct($template = null){
		//The only parameter in the construct is a link to the template
        if (isset($template))
        {
            $this->load($template);
        }
    }
	
	private function load($file){
		//Private helper function
		if (isset($file) && file_exists($file))
		{
			$this->template = file_get_contents($file);
		}
		else {
			echo '404: Template not found';	
		}
	}
	
	public function arrayset($array, $prefix = ''){
		/*Mass set from an array*/	
		//Sets the key of the array to the value so array("five" => 5, "six" => 6) would set {six} to 6 and {five} to 5
		foreach($array as $key => $value) {
			$this->set($prefix . $key, $value);
		}
	}
	
	public function set($var, $content) {
		//Replaces {$var} with the content in the template
		$this->template = str_replace("{" . "$var" . "}", escape($content), $this->template);
	}
	
	private function removeEmpty(){
		//Removes any place holders that were not set before exporting
		$this->template = preg_replace('^{.*}^', "", $this->template);
	}
	
	public function universalTags(){
		//Placeholders that are always set
		$this->set('DATE', date("d-m-Y"));
		$this->set('HTTP_HOST', $_SERVER['HTTP_HOST']);
	}
	
	public function parse(){ //Exports and returns the modified template
		$this->universalTags(); //Sets universal tags
		$this->removeEmpty(); //Removes any unset tags
		return $this->template; //Return template to be echoed
	}
}
?>