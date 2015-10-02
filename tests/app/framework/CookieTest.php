<?php
class CookieTest extends PHPUnit_Framework_TestCase {

	public function testCookieSet(){
		\Cookie::put("Test", "Hello World", 5);

		$this->assertEquals(
			\Cookie::get("Test"),
			"Hello World"
		);
	}

	/**
     * @depends testCookieSet
     */
	public function testCookieExist(){
		\Cookie::put("Test", "Hello World", 5);
		$this->assertEquals(
			\Cookie::exists("Test"),
			true
		);
	}

	/**
     * @depends testCookieSet
     * @depends testCookieExist
     */
	public function testSessionUnset(){
		\Cookie::put("Test", "Hello World", 5);
		\Cookie::delete("Test");
		$this->assertEquals(
			\Cookie::exists("Test"),
			false
		);
	}
}