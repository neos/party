<?php
namespace TYPO3\Party\Validation\Validator;

/*
 * This file is part of the TYPO3.Party package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Validation\Validator\AbstractValidator;

/**
 * Validator for AIM addresses.
 *
 * @api
 * @Flow\Scope("singleton")
 */
class AimAddressValidator extends AbstractValidator {

	/**
	 * Checks if the given value is a valid AIM name.
	 *
	 * The AIM name has the following requirements: "It must be
	 * between 3 and 16 alphanumeric characters in length and must
	 * begin with a letter."
	 *
	 * @param mixed $value The value that should be validated
	 * @return void
	 * @api
	 */
	protected function isValid($value) {
		if (!is_string($value) || preg_match('/\w[\w\d]{2,15}/i', $value) !== 1) {
			$this->addError('Please specify a valid AIM address.', 1343235498);
		}
	}
}
