<?php

namespace Eximporter\Exceptions;

use Exception;

/**
 * Class BadFile
 * Detects bad input file
 *
 * @package Eximport\Exceptions
 */
class BadFile extends Exception
{

	protected $input_file;

	public function __construct($input_file, $message, $code, Exception $previous = null)
	{
		$this->input_file = $input_file;

		parent::__construct($message, $code, $previous);
	}

	/**
	 * Get input file which is not good
	 *
	 * @return string
	 */
	public function file() { return $this->input_file; }

}