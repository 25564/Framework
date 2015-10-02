<?php
if(session_id() == '') { //Just incase a session is already set
	session_start(); //Set session if not set already
}

class TokenTest extends PHPUnit_Framework_TestCase {
	public function testToken(){
		$Token = \Token::generate();
		$this->assertEquals(
			\Token::check($Token),
			true
		);
	}
}