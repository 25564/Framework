<?php
namespace Router;

class RouteController {
	private $_routes = array();
    private $_requestMethod = "GET";

	public function map(Route $NewRoute){
        $this->_routes[] = $NewRoute;
	}

    public function requestMethod($requestMethod){
        $this->_requestMethod = $requestMethod;
    }

    public function dispatch(){
        $Route = $this->match($_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"]);
        if($Route !== false){
            $Route->dispatch();
        }
    }

	public function match($requestUrl){
        $requestMethod = $this->_requestMethod;
        foreach ($this->_routes as $routes) {
        	$matches;
            $params = array();
            $regex = $_SERVER['HTTP_HOST'] . \Config::get("root") .str_replace(array(')','/*'), array(')?','(/?|/.*?)'), $routes->pattern);
            $ids = array();
            $regex = preg_replace_callback(
                '#@([\w]+)(:([^/\(\)]*))?#',
                function($matches) use (&$ids) {
                    $ids[$matches[1]] = null;
                    if (isset($matches[3])) {
                        return '(?P<'.$matches[1].'>'.$matches[3].')';
                    }
                    return '(?P<'.$matches[1].'>[^/\?]+)';
                },
                $regex
            );

            if (preg_match('#^'.$regex.'(?:\?.*)?$#i', $requestUrl, $matches)) {
                foreach ($ids as $key => $value) {
                    $params[$key] = (array_key_exists($key, $matches)) ? urldecode($matches[$key]) : null;
                    $routes->addParam($key, $params[$key]);
                }
                $this->regex = $regex;
                return $routes;
            }
        }
        return false;
    }
}
?>