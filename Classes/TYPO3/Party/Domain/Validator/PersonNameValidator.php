<?php
namespace TYPO3\Party\Domain\Validator;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Party".                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * A validator for person names
 *
 */
class PersonNameValidator extends \TYPO3\Flow\Validation\Validator\GenericObjectValidator {

	/**
	 * Checks if the concatenated person name has at least one character.
	 *
	 * Any errors can be retrieved through the getErrors() method.
	 *
	 * @param mixed $value The value that should be validated
	 * @return void
	 */
	public function isValid($value) {
		if ($value instanceof \TYPO3\Party\Domain\Model\PersonName) {
			if (strlen(trim($value->getFullName())) === 0) {
				$this->addError('The person name cannot be empty.', 1268676765);
			}
		}
	}

}
?>