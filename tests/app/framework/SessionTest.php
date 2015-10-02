<?php
if(session_id() == '') { //Just incase a session is already set
	session_start(); //Set session if not set already
}

class SessionTest extends PHPUnit_Framework_TestCase {

	public function testSessionSet(){
		$this->assertEquals(
			\Session::put("Test", "Hello World"),
			\Session::get("Test")
		);
	}

	/**
     * @depends testSessionSet
     */
	public function testSessionExist(){
		\Session::put("Test", "Hello World");
		$this->assertEquals(
			\Session::exists("Test"),
			true
		);
	}

	/**
     * @depends testSessionSet
     * @depends testSessionExist
     */
	public function testSessionUnset(){
		\Session::put("Test", "Hello World");
		\Session::delete("Test");
		$this->assertEquals(
			\Session::exists("Test"),
			false
		);
	}

	/**
     * @depends testSessionSet
     * @depends testSessionUnset
     */
	public function testSessionFlash(){

		\Session::flash("TestNotice", "Hello");

		$this->assertEquals(
			\Session::get("TestNotice"),
			"Hello"
		);

		$this->assertEquals(
			\Session::flash("TestNotice"),
			"Hello"
		);

		$this->assertEquals(
			\Session::exists("Test"),
			false
		);
	}
}