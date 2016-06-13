<?php

namespace Eximporter\Validators;

use Eximporter\Exceptions\MissedValidator;

/**
 * Class Manager
 * Contains all validation logic, validates things
 *
 * @package Eximporter\Validators
 */
class Manager
{

	// a list of available validators
	private $validators = [];
	// a list of rules for each column title
	private $validation_rules = [];

	function __construct()
	{

		// built in validators
		$this->validators['required'] = new Required('required');
		$this->validators['regexp']   = new Regexp('regexp');

	}

	/**
	 * Assign rules to field names
	 *
	 * Rules can be a string - will be exploded by | character to array
	 * Or it can be an array
	 * Each element can be a string - validator name or a closure (f($cell_value){ return bool; }) with custom validation logic
	 * You can address columns by it's title or by it's number from 0 to N or A to ZN
	 *
	 * @param $rules
	 */
	public function setRules($rules)
	{
		if (!is_array($rules)) {
			throw new \InvalidArgumentException("Rules must be an array of field_title=>validators");
		}

		foreach($rules as $field=>$validators) {
			if (is_string($validators)) {
				$rules[$field] = explode("|", $validators);
			}
		}

		$this->validation_rules = $rules;
	}


	/**
	 * Validate given $value
	 *
	 * @param string $title
	 * @param mixed $value
	 *
	 * @return Result
	 */
	public function validate($title, $value) {

		// find a rule
		$validators = $this->getValidatorsForTitle($title);
		$result = $this->validateWith($value, $validators);

		return $result;
	}

	/**
	 * Get all loaded validation rules for a given field title
	 *
	 * @param $title
	 * @return array
	 */
	private function getValidatorsForTitle($title) {

		foreach($this->validation_rules as $field_title=>$validators) {
			// always cast titles to lower case to be consistent
			if(mb_strtolower($field_title) == mb_strtolower($title)) {
				return $validators;
			}
		}

		return [];
	}


	/**
	 * Validate value against given validator
	 *
	 * @param $value
	 * @param $validators
	 *
	 * @return Result
	 */
	private function validateWith($value, $validators) {

		$result = new Result();

		foreach($validators as $validator) {

			$object = $this->resolveValidator($validator);

			if($object->validate($value)) {
				$result->addPassed($object->getName());
			} else {
				$result->addFailed($object->getName());
			}

		}

		return $result;

	}


	/**
	 * Find implementation for validation rule
	 *
	 * @param $validator
	 *
	 * @return mixed
	 */
	private function resolveValidator($validator) {

		// in case this is a callable
		if(is_array($validator)) {
			$callable_title = array_keys($validator)[0];
			$callable = array_values($validator)[0];
			if(!is_callable($callable)) {
				throw new MissedValidator("Validator [".$callable_title."] was not resolved");
			}
			// this is a closure - call it
			$object = new Closure($callable_title);
			$object->setCallable($callable);
			return $object;
		}

		// string can contain arguments like "regex:#[a-z]+#"
		$items = explode(":",$validator);
		$validator_name = $items[0];
		$argument = isset($items[1])?$items[1]:null;

		foreach($this->validators as $exist_validator_name=>$object) {
			if($validator_name == $exist_validator_name) {
				$validator_object = clone $object;
				$validator_object->attachArgument($argument);
				return $validator_object;
			}
		}

	}
	
}