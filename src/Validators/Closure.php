<?php

namespace Eximporter\Validators;

/**
 * Class Closure
 * Custom validator who incapsulates given closure and behaves as any normal validator
 *
 * @package Eximporter\Validators
 */
class Closure implements ValidatorInterface {

	private $callable = null;
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

	/**
	 * Set custom closure
	 *
	 * @param callable $callable
	 */
	public function setCallable($callable) {
		$this->callable = $callable;
	}

	public function validate($value)
	{
		return $this->callable->call($this,$value);
	}


	public function attachArgument($argument)
	{
		// not in use for this validator
	}


}