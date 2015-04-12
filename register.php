<?php 
require_once "/includes/header.php"; //Initialize

if(Input::exists()){
	$validater = new Validate();
	if(Token::check(Input::get("token"))){
		$valid = $validater->check($_POST, array(
			'username' => array(
				'required' => true, //Is required
				'min' 	   => 3,	//Minium length
				'max' 	   => 35,	//Maximum Length
				'unique'   => 'user'//Must be only one in DB
			),
			'password' => array(
				'required' => true,
				'min' 	   => 5,
				'differ'   => 'username' //Cannot be the same as username
			),
			'email' => array(
				'required' => false,	//This may have to change but I am concerned people will be scared off by having to give such personal info
				'email'    => true		//Must be a valid email
			),
			'password2' => array(
				'required' => true,
				'matches'  => 'password'//Must have the same value as password
			)
		));
		if($validater->passed()){
			//Register the User
			$salt = Hash::salt();
			$hashed = Hash::make(Input::get("password"), $salt);
			$user = new User();
			try {
				$newUser = $user->create(array(
					'username' => escape(Input::get("username")),
					'password' => $hashed,
					'salt' 	   => $salt,
					'email'	   => escape(Input::get("email"))
				));
				if($newUser == true){
					Session::flash('homeMessage', 'You were registered successfully.');
					Redirect::to("home");
				}
			} catch (Exception $e) {
				$errors = array($e->getMessage());
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
	<input type="text" name="username" value="<?php echo escape(Input::get("username"));?>">
    <input type="password" name="password">
    <input type="password" name="password2">
    <input type="text" name="email" value="<?php echo escape(Input::get("email"));?>">
    <input type="hidden" name="token" value="<?php echo Token::generate();?>">
    
    <input type="submit">
</form>
<?php 
require_once "/includes/footer.php"; //Initialize
?>