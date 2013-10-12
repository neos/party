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
 * Validator for Skype addresses.
 *
 * @api
 * @Flow\Scope("singleton")
 */
class SkypeAddressValidator extends \TYPO3\Flow\Validation\Validator\AbstractValidator {

	/**
	 * Checks if the given value is a valid Skype name.
	 *
	 * The Skype website says: "It must be between 6-32 characters, start with
	 * a letter and contain only letters and numbers (no spaces or special
	 * characters)."
	 *
	 * We added period and minus as additional characters because they are
	 * suggested by Skype during registration.
	 *
	 * @param mixed $value The value that should be validated
	 * @return void
	 * @api
	 */
	protected function isValid($value) {
		if (!is_string($value) || preg_match('/^[a-z][a-z0-9\.-]{5,31}$/Dix', $value) !== 1) {
			$this->addError(
				'Please specify a valid Skype address (It must be between 6-32 characters and start with a letter).',
				1343235498
			);
		}
	}
}
