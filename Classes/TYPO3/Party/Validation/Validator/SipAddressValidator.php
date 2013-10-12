<?php
namespace TYPO3\Party\Validation\Validator;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Party".                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Validator for Sip addresses.
 *
 * @api
 * @Flow\Scope("singleton")
 */
class SipAddressValidator extends \TYPO3\Flow\Validation\Validator\AbstractValidator {

	/**
	 * Checks if the given value is a valid Sip name.
	 *
	 * The Sip address has the following structure: "sip:+4930432343@isp.com"
	 * More information is found on:
	 * http://wiki.snom.com/Features/Dial_Plan/Regular_Expressions
	 *
	 * @param mixed $value The value that should be validated
	 * @return void
	 * @api
	 */
	protected function isValid($value) {
		if (!is_string($value) || preg_match('/^sip\:(?P<number>[0-9]+)@(.*)$/', $value) !== 1) {
			$this->addError('Please specify a valid Sip address.', 1343235498);
		}
	}
}
