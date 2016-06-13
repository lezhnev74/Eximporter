<?php

namespace Eximporter\Validators;


/**
 * Interface Validator
 * Describes how every validator should handle validation
 *
 * @package Eximporter\Validators
 */
interface ValidatorInterface {

	/**
	 * ValidatorInterface constructor.
	 *
	 * @param $name
	 */
	function __construct($name);

	/**
	 * The method to validate any cell-value
	 *
	 * @param $value
	 *
	 * @return bool
	 */
	public function validate($value);

	/**
	 * Attach an argument for this validator
	 * For example string culd be like this:  "length:10,20" - means validator "length" has argument "10,20"
	 *
	 * @param      $mixed
	 *
	 * @return void
	 */
	public function attachArgument($argument);


	/**
	 * Return the name
	 *
	 * @return string
	 */
	public function getName();

}