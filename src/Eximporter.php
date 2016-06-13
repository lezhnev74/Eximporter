<?php
namespace Eximporter;

use Eximporter\Exceptions\BadFile;
use Eximporter\Validators\Manager;


/**
 * Class Eximporter
 * Allows to import spreadsheet and validate it's rows
 * It allows easily assign custom validation via closures
 *
 * @package Eximporter
 */
class Eximporter
{

	// source file with data
	private $input_file;

	// objects from underlying PHPExcel library
	private $object_reader;
	private $object_excel;

	// object to handle validation
	// tightly coupled, but works
	private $validation_manager = null;

	// closure to handle validated rows
	private $good_row_handler = null;
	// closure to handle rows which failed validation
	private $bad_row_handler = null;

	// rows counters
	private $good_rows_counter = 0;
	private $bad_rows_counter = 0;
	private $skipped_rows_counter = 0;


	function __construct($input_file)
	{

		$this->input_file = $input_file;

		try {

			$this->object_reader = \PHPExcel_IOFactory::createReaderForFile($this->input_file);
			$this->object_reader->setReadDataOnly(true); // we won't write to a file
			$this->object_reader->canRead($this->input_file); // check that file is readable
			$this->object_excel = $this->object_reader->load($this->input_file);

		} catch (\PHPExcel_Reader_Exception $e) {
			throw new BadFile($this->input_file, $e->getMessage(), $e->getCode());
		}

		$this->initValidators();

	}


	/**
	 * Init the validation manager
	 */
	private function initValidators()
	{

		$this->validation_manager = new Manager();

	}


	/**
	 * Assign validation rules
	 *
	 * Rules can be a string - will be exploded by | character to array
	 * Or it can be an array
	 * Each element can be a string - validator name or a closure (f($cell_value){ return bool; }) with custom validation logic
	 * You can address columns by it's title or by it's number from 0 to N
	 *
	 * @param $rules
	 */
	public function setValidationRules($rules)
	{

		$this->validation_manager->setRules($rules);

	}


	/**
	 * Set closure to be called for each good row
	 *
	 * @param $closure
	 */
	public function setHandlerForGoodRow($closure)
	{
		$this->good_row_handler = $closure;
	}

	/**
	 * Set closure to be called for each bad row, bad means failed validation
	 *
	 * @param $closure
	 */
	public function setHandlerForBadRow($closure)
	{
		$this->bad_row_handler = $closure;
	}


	/**
	 * Execute reading data from the source file
	 */
	public function read()
	{

		// detect sheets
		$sheets = $this->object_excel->getAllSheets();

		// work for each sheet
		foreach ($sheets as $sheet) {

			$column_titles = $this->detectColumnTitles($sheet);

			// let's iterate starting from second row (skip header's row)
			foreach ($sheet->getRowIterator(2) as $row) {
				$this->handleRow($row, $column_titles);
			}

		}


	}


	/**
	 * Detect what titles sheet has
	 * Every title has index like ["A" => "string title"]
	 *
	 * @param $sheet
	 *
	 * @return array
	 */
	private function detectColumnTitles($sheet)
	{
		$titles = [];

		// get title of each column
		// expect first line to contain titles
		foreach ($sheet->getRowIterator(1, 1) as $head_row) {
			// ok let's iterate over cells
			foreach ($head_row->getCellIterator() as $i => $cell) {
				$titles[$i] = $cell->getValue();
			}
		}

		return $titles;
	}


	/**
	 * Validate row and call a handler
	 *
	 * @param $row
	 * @param $column_titles
	 *
	 * @return void
	 */
	private function handleRow($row, $column_titles)
	{

		$cells = [];

		// populate cells values
		foreach ($row->getCellIterator() as $i => $cell) {
			$cell_value = $cell->getCalculatedValue();

			if (isset($column_titles[$i])) {
				$cells[$column_titles[$i]] = $cell_value;
			}

		}

		// if all of the cells are nulls then skip this row
		if (!count(array_filter($cells, function ($cell) { return !is_null($cell); }))) {
			$this->skipped_rows_counter++;
			return;
		}

		// now validate cell values
		$bad_cell_results = [];
		foreach ($cells as $title => $value) {
			$result = $this->validation_manager->validate($title, $value);
			if ($result->isFailed()) {
				$bad_cell_results[$title] = $result;
			}
		}

		// call handlers for good or bad rows
		if (!count($bad_cell_results)) {
			$this->good_rows_counter++;
			if (is_callable($this->good_row_handler)) {
				$this->good_row_handler->call($this, $cells);
			}
		} else {
			$this->bad_rows_counter++;
			if (is_callable($this->bad_row_handler)) {
				$this->bad_row_handler->call($this, $cells, $bad_cell_results);
			}
		}

	}


	/**
	 * Return the counter of rows which passed validation
	 */
	public function getGoodRowsCount() {
		return $this->good_rows_counter;
	}

	/**
	 * Return the counter of rows which failed validation
	 */
	public function getBadRowsCount() {
		return $this->bad_rows_counter;
	}

}