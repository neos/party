<?php

namespace Neos\Party\Domain\Validator;

/*
 * This file is part of the Neos.Party package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Validation\Validator\GenericObjectValidator;
use Neos\Flow\Validation\ValidatorResolver;
use Neos\Party\Domain\Model\ElectronicAddress;

/**
 * An electronic address validator
 *
 */
class ElectronicAddressValidator extends GenericObjectValidator
{
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
    public function injectValidatorResolver(ValidatorResolver $validatorResolver): void
    {
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
     * @throws \Neos\Flow\Validation\Exception\InvalidValidationConfigurationException
     * @throws \Neos\Flow\Validation\Exception\NoSuchValidatorException
     */
    public function isValid($value)
    {
        if ($value instanceof ElectronicAddress) {
            $addressType = $value->getType();
            switch ($addressType) {
                case 'Email':
                    $addressValidator = $this->validatorResolver->createValidator('EmailAddress');
                    break;
                default:
                    $addressValidator = $this->validatorResolver->createValidator('Neos.Party:' . $addressType . 'Address');
            }
            if ($addressValidator === null) {
                $this->addError('No validator found for electronic address of type "' . $addressType . '".', 1268676030);
            } else {
                $result = $addressValidator->validate($value->getIdentifier());
                if ($result->hasErrors()) {
                    foreach ($result->getErrors() as $error) {
                        $this->addError($error->getMessage(), $error->getCode());
                    }
                }
            }
        }
    }
}
