<?php 
require_once $_SERVER['DOCUMENT_ROOT']  . "/includes/header.php"; //Initialize

if(Input::exists()){
	$validater = new Validate();
	if(Token::check(Input::get("token"))){
		$valid = $validater->check($_POST, array(
			'username' => array(
				'required' => true, //Is required
				'min' 	   => 3,	//Minium length
				'max' 	   => 35,	//Maximum Length
				'exists'   => 'user'
			),
			'password' => array(
				'required' => true,
				'min' 	   => 5,
				'differ'   => 'username' //Cannot be the same as username
			),
		));
		if($validater->passed()){
			$user = new User();
			$authenticated = $user->login(Input::get("username"), Input::get("password"), Input::get("remember"));
			if($authenticated){
				Session::flash('homeMessage', 'You were logged in successfully.');
				Redirect::to("home");
			} else {
				$errors = array('Password or Username is incorrect');	
			}
		} else {
			$errors = $validater->errors();	
		}
	}
}
?>
<p style="color:#FF0F13;"><?php
	if(!empty($errors)){
		foreach($errors as $error){
			echo $error, '<br>';	
		}
	}
?></p>
<form action="" method="post">
	<input type="text" placeholder="username" name="username" value="<?php echo escape(Input::get("username"));?>">
    <input type="password" placeholder="password" name="password">
    <br>
    <span>Remember me:</span><input type="checkbox" name="remember">
    <br>
    <input type="hidden" name="token" value="<?php echo Token::generate();?>">
    
    <input type="submit">
</form>
<?php 
require_once $_SERVER['DOCUMENT_ROOT']  . "/includes/footer.php"; //Initialize
?>