<?php
namespace views\authenticate;

class register extends \Core\View {

	private $Model;

	public function create($Model){
		$this->Model = $Model;
		if($this->Model->hasErrors()){
			echo '<p style="color:#FF0F13;">' . $this->errors($this->Model->getErrors()) . '</p>';
		}
		echo 
			'<form action="" method="post">
				<input type="text" placeholder="Username" name="Username" value="' . escape(\Input::get("username")) . '"><br>
				<input type="password" placeholder="Password" name="Password"><br>
				<input type="password" placeholder="Repeat Password" name="Password2"><br>
				<br>
				<input type="hidden" name="token" value="' . \Token::generate() . '">

				<input type="submit">
			</form>';
	} 

	private function errors($errors){
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