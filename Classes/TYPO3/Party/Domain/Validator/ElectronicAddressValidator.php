<?php
namespace TYPO3\Party\Domain\Validator;

/*
 * This file is part of the TYPO3.Party package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use TYPO3\Flow\Validation\Validator\GenericObjectValidator;
use TYPO3\Flow\Validation\ValidatorResolver;
use TYPO3\Party\Domain\Model\ElectronicAddress;

/**
 * An electronic address validator
 *
 */
class ElectronicAddressValidator extends GenericObjectValidator {

	/**
	 * @var ValidatorResolver
	 */
	protected $validatorResolver;

	/**
	 * Injects the validator resolver
	 *
	 * @param ValidatorResolver $validatorResolver
	 * @return void
	 */
	public function injectValidatorResolver(ValidatorResolver $validatorResolver) {
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
		if ($value instanceof ElectronicAddress) {

			$addressType = $value->getType();
			switch ($addressType) {
				case 'Email':
					$addressValidator = $this->validatorResolver->createValidator('EmailAddress');
					break;
				default;
					$addressValidator = $this->validatorResolver->createValidator('TYPO3.Party:' . $addressType . 'Address');
			}
			if ($addressValidator === NULL) {
				$this->addError('No validator found for electronic address of type "' . $addressType . '".', 1268676030);
			} else {
				$result = $addressValidator->validate($value->getIdentifier());
				if ($result->hasErrors()) {
					$this->result = $result;
				}
			}
		}
	}

}
