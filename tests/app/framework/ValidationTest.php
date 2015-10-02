<?php

class ValidationTest extends PHPUnit_Framework_TestCase {

	private $Data = array(
		"Compulsary" => "Present",
		"PresentOptional" => "Present",
		"OptionalShort" => "12345",
		'Empty' => '',
		'OptionalEmpty' => '',
		'CorrectCustomError' => '12345',
		'IncorrectCustomError' => '123',
		'ValueA' => '123',
		'ValueB' => '1234',
		'ValueC' => '123',
	);

	public function testValidationRequired(){
		$Test = new Validation();
		$TestValid = $Test->Validate($this->Data, array(
			'Compulsary' => array(
				'required' => true
			)
		));

		$this->assertEquals($TestValid, true);


		$Test = new Validation();
		$TestValid = $Test->Validate($this->Data, array(
			'Missing' => array(
				'required' => false
			)
		));

		$this->assertEquals($TestValid, true);

		$Test = new Validation();
		$TestValid = $Test->Validate($this->Data, array(
			'Empty' => array(
				'required' => true
			)
		));

		$this->assertEquals($TestValid, array("Empty is required"));
	}

	public function testValidationNonRequired(){
		$Test = new Validation();
		$TestValid = $Test->Validate($this->Data, array(
			'MissingOptional' => array(
				'required' => false,
				'max' => 4
			)
		));

		$this->assertEquals($TestValid, true);

		$Test = new Validation();
		$TestValid = $Test->Validate($this->Data, array(
			'OptionalShort' => array(
				'required' => false,
				'max' => 4
			)
		));

		$this->assertEquals($TestValid, array(escape("OptionalShort can't be over 4 characters long")));

		$Test = new Validation();
		$TestValid = $Test->Validate($this->Data, array(
			'PresentOptional' => array(
				'required' => false,
			)
		));

		$this->assertEquals($TestValid, true);

		$Test = new Validation();
		$TestValid = $Test->Validate($this->Data, array(
			'OptionalEmpty' => array(
				'required' => false,
			)
		));

		$this->assertEquals($TestValid, true);
	}

	public function testValidationCustomErrors(){
		$Test = new Validation();
		$TestValid = $Test->Validate($this->Data, array(
			'CorrectCustomError' => array(
				'min' => array(
					"Value" => 4,
					"CustomError" => "{Value} is too short"
				)
			)
		));

		$this->assertEquals($TestValid, true);

		$Test = new Validation();
		$TestValid = $Test->Validate($this->Data, array(
			'IncorrectCustomError' => array(
				'min' => array(
					"Value" => 4,
					"CustomError" => "{Value} is too short"
				)
			)
		));

		$this->assertEquals($TestValid, array("123 is too short"));

	}

	public function testValidationComparrision(){
		$Test = new Validation();
		$TestValid = $Test->Validate($this->Data, array(
			'ValueA' => array(
				"required" => true
			),

			'ValueB' => array(
				"differs" => "ValueA"
			),

			'ValueC' => array(
				"matches" => "ValueA"
			)
		));

		$this->assertEquals($TestValid, true);
	}
}