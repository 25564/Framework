<?php 
require_once "/includes/header.php"; //Initialize

if(Input::exists()){
	$validater = new Validation();
	if(Token::check(Input::get("token"))){
		$valid = $validater->Validate($_POST, array(
			'Username' => array(
				'required' => true, //Is required
				'min' 	   => 3,	//Minium length
				'max' 	   => 35,	//Maximum Length
				'unique'   => 'Users'//Must be only one in DB
			),
			'Password' => array(
				'required' => true,
				'min' 	   => 5,
				'differs'   => 'Username' //Cannot be the same as username
			),
			'Password2' => array(
				'required' => true,
				'matches'  => 'Password'//Must have the same value as password
			)
		));
		if($valid === true){
			//Register the User
			$salt = Hash::salt();
			$hashed = Hash::make(Input::get("password"), $salt);
			$user = new User();
			try {
				$newUser = $user->Create(array(
					'Username' => escape(Input::get("Username")),
					'Password' => $hashed,
					'Salt' 	   => $salt,
				));
				if($newUser == true){
					Session::put("UserID", $user->Data->UserID);
					Session::flash('homeMessage', 'You were registered successfully.');
					//Redirect::to("home");
				}
			} catch (Exception $e) {
				$errors = array($e->getMessage());
			}
		} else {
			$errors = $valid;
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
	<input type="text" name="Username" value="<?php echo escape(Input::get("username"));?>"><br>
    <input type="password" name="Password"><br>
    <input type="password" name="Password2"><br>
    <input type="hidden" name="token" value="<?php echo Token::generate();?>">
    
    <input type="submit">
</form>
<?php 
require_once "/includes/footer.php"; //Initialize
?>