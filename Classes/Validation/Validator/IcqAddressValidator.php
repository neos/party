<?php
namespace Neos\Party\Validation\Validator;

/*
 * This file is part of the Neos.Party package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Validation\Validator\AbstractValidator;

/**
 * Validator for ICQ addresses.
 *
 * @api
 * @Flow\Scope("singleton")
 */
class IcqAddressValidator extends AbstractValidator
{
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
    protected function isValid($value)
    {
        if (!is_string($value) || preg_match('/^(-*[0-9]-*){7,9}$/', $value) !== 1) {
            $this->addError('Please specify a valid ICQ address.', 1343235498);
        }
    }
}
