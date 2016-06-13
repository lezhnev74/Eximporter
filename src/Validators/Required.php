<?php

namespace Eximporter\Validators;

/**
 * Class Required
 * Validates string has length > 0
 *
 * @package Eximporter\Validators
 */
class Required implements ValidatorInterface {

	private $name;

	function __construct($name) {
		$this->name = $name;
	}

	/**
	 * Get name of this validator
	 *
	 * @return mixed
	 */
	function getName() {
		return $this->name;
	}

	public function validate($value)
	{
		// make sure it is a string
		if(!$value) {
			return false;
		}

		// trim trailing spaces
		$value = trim(strval($value));
		if(!mb_strlen($value)) {
			return false;
		}

		return true;
	}


	public function attachArgument($argument)
	{
		// not in use for this validator
	}


}