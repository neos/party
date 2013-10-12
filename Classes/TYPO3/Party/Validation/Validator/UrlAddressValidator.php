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
 * Validator for URL addresses.
 *
 * @api
 * @Flow\Scope("singleton")
 */
class UrlAddressValidator extends \TYPO3\Flow\Validation\Validator\AbstractValidator {

	/**
	 * Checks if the given value is a valid URL.
	 *
	 * @param mixed $value The value that should be validated
	 * @return void
	 * @api
	 */
	protected function isValid($value) {
		if (!is_string($value) || preg_match('/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/', $value) !== 1) {
			$this->addError('Please specify a valid URL.', 1343235498);
		}
	}
}
