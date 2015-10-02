<?php
namespace views;

class index extends \Core\View {

	public function create($Model){
		require($_SERVER['DOCUMENT_ROOT'] . \Config::get("root") . "/app/views/structure/header.php");
		if($Model->hasAlert()){
			echo $Model->getAlert();
		}
		
		echo "Hello World!";
			
		require($_SERVER['DOCUMENT_ROOT'] . \Config::get("root") . "/app/views/structure/footer.php");
	} 
}

?>