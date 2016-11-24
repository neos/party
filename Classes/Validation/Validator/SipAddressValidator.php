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
 * Validator for Sip addresses.
 *
 * @api
 * @Flow\Scope("singleton")
 */
class SipAddressValidator extends AbstractValidator
{
    /**
     * Checks if the given value is a valid Sip name.
     *
     * The Sip address has the following structure: "sip:+4930432343@isp.com"
     * More information is found on:
     * http://wiki.snom.com/Features/Dial_Plan/Regular_Expressions
     *
     * @param mixed $value The value that should be validated
     * @return void
     * @api
     */
    protected function isValid($value)
    {
        if (!is_string($value) || preg_match('/^sip\:(?P<number>[0-9]+)@(.*)$/', $value) !== 1) {
            $this->addError('Please specify a valid Sip address.', 1343235498);
        }
    }
}
