<?php

namespace Eximporter\Validators;

/**
 * Class Result
 * Contains resulting data after validation - which validators passed and which failed
 *
 * @package Eximporter\Validators
 */
class Result
{

	private $passed_validators = [];
	private $failed_validators = [];


	/**
	 * Add passed validator
	 *
	 * @param $validator_title
	 */
	function addPassed($validator_title)
	{
		$this->passed_validators[] = $validator_title;
	}

	/**
	 * Add failed validator
	 *
	 * @param $validator_title
	 */
	function addFailed($validator_title)
	{
		$this->failed_validators[] = $validator_title;
	}

	/**
	 * Detect if result has failed validators
	 *
	 * @return bool
	 */
	public function isFailed()
	{
		return count($this->failed_validators) > 0;
	}

	/**
	 * Return failed validators
	 *
	 * @return array
	 */
	public function getFailed()
	{
		return $this->failed_validators;
	}


	/**
	 * Return passed validator
	 *
	 * @return array
	 */
	public function getPassed()
	{
		return $this->passed_validators;
	}

}