<?php

use Eximporter\Eximporter;
use PHPUnit\Framework\TestCase;
use Eximporter\Exceptions\BadFile;

class TestInputFile extends TestCase
{

	public function testTriggerBadFileException()
	{

		$this->expectException(BadFile::class);

		// this file is not a valid spreadsheet
		$file     = "./tests/resources/missedfile";
		new Eximporter($file);

	}

	public function testNormalLoadAnyRealFile() {

		// this file is not a valid spreadsheet
		$file     = "./tests/resources/text.file.any";
		new Eximporter($file);


	}


}