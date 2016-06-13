<?php

namespace Eximporter\Validators;

/**
 * Class Regexp
 * Validates string against a pattern
 *
 * @package Eximporter\Validators
 */
class Regexp implements ValidatorInterface
{

	protected $pattern = null;
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
		if (is_null($this->pattern)) {
			return false; // @todo probably we can throw an Exception here
		}

		return preg_match($this->pattern, $value);
	}


	/**
	 * Assign arguments for this validator
	 *
	 * @param $argument string
	 */
	public function attachArgument($argument)
	{
		$this->pattern = $argument;
	}


}