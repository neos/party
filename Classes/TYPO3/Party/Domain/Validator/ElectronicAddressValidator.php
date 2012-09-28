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
 * An electronic address validator
 *
 */
class ElectronicAddressValidator extends \TYPO3\Flow\Validation\Validator\GenericObjectValidator {

	/**
	 * @var \TYPO3\Flow\Validation\ValidatorResolver
	 */
	protected $validatorResolver;

	/**
	 * Injects the validator resolver
	 *
	 * @param \TYPO3\Flow\Validation\ValidatorResolver $validatorResolver
	 * @return void
	 */
	public function injectValidatorResolver(\TYPO3\Flow\Validation\ValidatorResolver $validatorResolver) {
		$this->validatorResolver = $validatorResolver;
	}

	/**
	 * Checks if the given value is a valid electronic address according to its type.
	 *
	 * If at least one error occurred, the result is FALSE and any errors can
	 * be retrieved through the getErrors() method.
	 *
	 * @param mixed $value The value that should be validated
	 * @return void
	 */
	public function isValid($value) {
		if ($value instanceof \TYPO3\Party\Domain\Model\ElectronicAddress) {
			if ($this->isValidatedAlready($value)) {
				return;
			}

			$addressValidator = $this->validatorResolver->createValidator($value->getType() . 'Address');
			if ($addressValidator === NULL) {
				$this->addError('No validator found for electronic address of type "' . $value->getType() . '".', 1268676030);
			} else {
				$result = $addressValidator->validate($value->getIdentifier());
				if ($result->hasErrors()) {
					$this->result = $result;
				}
			}
		}
	}

}
?>