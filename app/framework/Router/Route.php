<?php
namespace Router;
class Route { 
	public $pattern;
	public $_config = array();
	private $_methods;
	private $_params = array();

	public function __construct($resource, array $config = array()){
		$this->_config = $config;
		$this->pattern = $resource;
		$this->_methods = isset($config['methods']) ? $config['methods'] : array('GET', 'POST', 'PUT', 'DELETE');
		$this->_params = isset($config['params']) ? $config['params'] : array();
	}

	public function setParams($newParams){
		$this->_params = $newParams;
	}

	public function addParam($key, $value){
		$this->_params[$key]= $value;
	}

	public function dispatch(){
		$action = explode('::', $this->_config['_controller']);
		$file = $_SERVER['DOCUMENT_ROOT'] . \Config::get("root") . "/". str_replace(array('\\', '_'), '/', $action[0]).'.php';
		if (file_exists($file)) {
			require_once $file;
		}

		$instance = new $action[0];
		call_user_func_array(array($instance, $action[1]), array($this->_params));
	}

	public function getMethods(){
		return $this->_methods;
	}

	public function getConfig(){
		return $this->_config;
	}
}
?>