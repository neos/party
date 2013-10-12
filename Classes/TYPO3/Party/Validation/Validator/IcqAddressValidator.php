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
 * Validator for ICQ addresses.
 *
 * @api
 * @Flow\Scope("singleton")
 */
class IcqAddressValidator extends \TYPO3\Flow\Validation\Validator\AbstractValidator {

	/**
	 * Checks if the given value is a valid ICQ UIN address.
	 *
	 * The ICQ UIN address has the following requirements: "It must be
	 * 9 numeric characters." More information is found on:
	 * http://www.icq.com/support/icq_8/start/authorization/en
	 *
	 * @param mixed $value The value that should be validated
	 * @return void
	 * @api
	 */
	protected function isValid($value) {
		if (!is_string($value) || preg_match('/^(-*[0-9]-*){7,9}$/', $value) !== 1) {
			$this->addError('Please specify a valid ICQ address.', 1343235498);
		}
	}
}
