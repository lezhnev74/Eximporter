<?php

use Eximporter\Eximporter;
use PHPUnit\Framework\TestCase;

class TestHandlers extends TestCase
{

	public function testRequiredValidator()
	{

		// this file is not a valid spreadsheet
		$file     = "./tests/resources/test_05.xlsx";
		$importer = new Eximporter($file);

		$importer->setValidationRules([
			'description' => 'required',
			'amount'      => ['regexp:#^[0-9]+$#', ['custom1' => function ($value) { return $value > 100; }]],
		]);

		// set handler for each bad cell
		$importer->setHandlerForBadRow(function ($row, $bad_cells) {
			foreach ($bad_cells as $cell_title => $validation_result) {
				echo $cell_title . " failed validators: " . implode(", ", $validation_result->getFailed());
				echo "\n";
			}
		});
		
		// set handlers for good rows
		$importer->setHandlerForGoodRow(function ($row) {
			// business logic with $row
		});
		

		$importer->read();


	}


}