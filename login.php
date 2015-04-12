<?php 
require_once "/includes/header.php"; //Initialize

if(Input::exists()){
	$validater = new Validation();
	if(Token::check(Input::get("token"))){
		$valid = $validater->Validate($_POST, array(
			'Username' => array(
				'required' => true,   	//Is required
				'min' 	   => 3,	  	//Minium length
				'max' 	   => 35,	  	//Maximum Length
				'exists'   => array(	//Must exists
					"Value" => 'Users',
					'CustomError'=> "{Value} is not a registered User"
				)
			),
			'Password' => array(
				'required' => true,
				'min' 	   => 5,
				'differs'   => 'Username' //Cannot be the same as username
			),		
		));
		if($valid === true){
			//Attempt to Authenticate
			$user = new User();
			try {
				$Authenticated = $user->Authenticate(escape(Input::get("Username")), escape(Input::get("Password")), Input::get("remember"));
				if($Authenticated !== false){
					Redirect::to("home");
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
	<input type="text" placeholder="username" name="Username" value="<?php echo escape(Input::get("username"));?>">
    <input type="password" placeholder="password" name="Password">
    <br>
    <span>Remember me:</span><input type="checkbox" name="remember">
    <br>
    <input type="hidden" name="token" value="<?php echo Token::generate();?>">
    
    <input type="submit">
</form>
<?php 
require_once "/includes/footer.php"; //Initialize
?>