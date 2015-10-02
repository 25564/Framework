<?php
class Router {	
	private static $RouteController = null;

	// Don't allow object instantiation
	private function __construct() {}
	private function __destruct() {}
	private function __clone() {}

	public static function map($pattern, array $config = array()){
        if(!isset(self::$RouteController)){
            self::$RouteController = new Router\RouteController();
        }
    
        $Route = new router\route($pattern, $config);
        return self::$RouteController->map($Route);
    }

    public static function dispatch(){
        if(!isset(self::$RouteController)){
            self::$RouteController = new Router\RouteController();
        }
    
        return self::$RouteController->dispatch();
    }
}
?>