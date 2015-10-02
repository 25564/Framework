<?php
namespace views\authenticate;

class login extends \Core\View {

	private $Model;

	public function create($Model){
		$this->Model = $Model;
		require($_SERVER['DOCUMENT_ROOT'] . \Config::get("root") . "/app/views/structure/header.php");
		echo '<p style="color:#FF0F13;">' . $this->errors($this->Model->getErrors()) . '</p>';

		echo 
			'<form action="" method="post">
				<input type="text" placeholder="username" name="Username" value="' . escape(\Input::get("username")) . '">
				<input type="password" placeholder="password" name="Password">
				<br>
				<span>Remember me:</span><input type="checkbox" name="remember">
				<br>
				<input type="hidden" name="token" value="' . \Token::generate() . '">

				<input type="submit">
			</form>';

		require($_SERVER['DOCUMENT_ROOT'] . \Config::get("root") . "/app/views/structure/footer.php");

	} 

	private function errors(){
		$String = '';
		if(!empty($errors)){
			foreach($errors as $error){
				$String .= $error . '<br>';	
			}
		}
		return $String;
	}
}

?>