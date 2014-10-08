<?php 
class Redirect {
	public static function to($location = null){
		//Redirects to a set page or to an error page
		//Redirect to an error page by Redirect::to(404) otherwise use Redirect::to("www.rodev.x10.mx")
		if($location != 'home') {
			if(is_numeric($location)){
				switch($location){
					case 404:
						header('HTTP/1.0 404 Not Found');
						include ($_SERVER['DOCUMENT_ROOT'] . "/includes/errors/404.php");
					break;
				}
				
			}
			header('Location: ' . $location);
			exit();
		} else {
			header('Location: http://' . $_SERVER['HTTP_HOST'] . Config::get("root"));
			exit();	
		}
	}
	
	public static function refresh() {
		//Refreshs the current page
		self::to($_SERVER['REQUEST_URI']);
	}
}
?>