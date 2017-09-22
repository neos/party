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
use Neos\Party\Domain\Model\PersonName;

/**
 * A validator for person names
 *
 */
class PersonNameValidator extends GenericObjectValidator
{
    /**
     * Checks if the concatenated person name has at least one character.
     *
     * Any errors can be retrieved through the getErrors() method.
     *
     * @param mixed $value The value that should be validated
     * @return void
     */
    public function isValid($value)
    {
        if ($value instanceof PersonName) {
            if (strlen(trim($value->getFullName())) === 0) {
                $this->addError('The person name cannot be empty.', 1268676765);
            }
        }
    }
}
