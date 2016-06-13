<?php

use Eximporter\Eximporter;
use PHPUnit\Framework\TestCase;
use Eximporter\Exceptions\BadFile;

class TestValidateFields extends TestCase
{

	public function testRequiredValidator()
	{

		// this file is not a valid spreadsheet
		$file     = "./tests/resources/test_required_03.xlsx";
		$importer = new Eximporter($file);

		$importer->setValidationRules([
			'description' => 'required',
		]);

		$importer->read();

		$this->assertEquals($importer->getGoodRowsCount(), 2);
		$this->assertEquals($importer->getBadRowsCount(), 1);

	}

	public function testRegexpValidator()
	{

		// this file is not a valid spreadsheet
		$file     = "./tests/resources/test_required_03.xlsx";
		$importer = new Eximporter($file);

		$importer->setValidationRules([
			'title'       => 'required|regexp:#^[a-zA-Z]{4}$#',
			'description' => 'required',
		]);

		$importer->read();

		$this->assertEquals($importer->getGoodRowsCount(), 1);
		$this->assertEquals($importer->getBadRowsCount(), 2);

	}

	public function testCustomClosureValidator()
	{

		// this file is not a valid spreadsheet
		$file     = "./tests/resources/test_required_03.xlsx";
		$importer = new Eximporter($file);

		$importer->setValidationRules([
			'title'       => [
				[
					'custom_rule' => function ($value) {
						return mb_substr($value, -1, 1) != 'e';
					},
				],
			],
			'description' => [
				[
					'custom_rule_2' => function ($value) {
						return mb_strlen($value) >= 6;
					},
				],
			],
		]);

		$importer->read();

		$this->assertEquals($importer->getGoodRowsCount(), 1);


	}


	public function testCombinedValidators()
	{

		// this file is not a valid spreadsheet
		$file     = "./tests/resources/test_05.xlsx";
		$importer = new Eximporter($file);

		$importer->setValidationRules([
			'title'       => 'required',
			'description' => 'required',
			'amount'      => 'regexp:#^[0-9]+$#',
		]);

		$importer->read();

		$this->assertEquals($importer->getGoodRowsCount(), 4);

	}

	public function testCombined2Validators()
	{

		// this file is not a valid spreadsheet
		$file     = "./tests/resources/test_05.xlsx";
		$importer = new Eximporter($file);

		$importer->setValidationRules([
			'title'       => 'required',
			'description' => 'required',
			'amount'      => ['regexp:#^[0-9]+$#', ['custom1' => function ($value) { return $value > 100; }]],
		]);

		$importer->read();


		$this->assertEquals($importer->getGoodRowsCount(), 2);


	}

	public function testDetectFailedValidators()
	{

		// this file is not a valid spreadsheet
		$file     = "./tests/resources/test_05.xlsx";
		$importer = new Eximporter($file);

		$importer->setValidationRules([
			'title'       => 'required',
			'description' => 'required',
			'amount'      => ['regexp:#^[0-9]+$#', ['custom1' => function ($value) { return $value > 100; }]],
		]);

		$importer->read();

	}

}