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
 * Validator for MSN addresses.
 *
 * @api
 * @Flow\Scope("singleton")
 */
class MsnAddressValidator extends AbstractValidator
{
    /**
     * Checks if the given value is a valid MSN address.
     *
     * The MSN address has the following structure:
     * "name@hotmail.com, name@live.com, name@msn.com, name@outlook.com"
     *
     * @param mixed $value The value that should be validated
     * @return void
     * @api
     */
    protected function isValid($value)
    {
        if (!is_string($value) || preg_match('/^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[?msn|live|hotmail|outlook]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/', $value) !== 1) {
            $this->addError('Please specify a valid MSN address.', 1343235498);
        }
    }
}
