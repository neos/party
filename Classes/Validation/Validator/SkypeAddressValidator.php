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
 * Validator for Skype addresses.
 *
 * @api
 * @Flow\Scope("singleton")
 */
class SkypeAddressValidator extends AbstractValidator
{
    /**
     * Checks if the given value is a valid Skype name.
     *
     * The Skype website says: "It must be between 6-32 characters, start with
     * a letter and contain only letters and numbers (no spaces or special
     * characters)."
     *
     * Nevertheless dash and underscore are allowed as special characters.
     * Furthermore, account names can contain a colon if they were auto-created
     * trough a connected Microsoft or Facebook profile. In this case, the syntax
     * is as follows:
     * - live:john.due
     * - Facebook:john.doe
     *
     * We added period and minus as additional characters because they are
     * suggested by Skype during registration.
     *
     * @param mixed $value The value that should be validated
     * @return void
     * @api
     */
    protected function isValid($value)
    {
        if (!is_string($value) || preg_match('/^[a-z][a-z0-9\._:-]{5,31}$/Dix', $value) !== 1) {
            $this->addError(
                'Please specify a valid Skype address (It must be between 6-32 characters and start with a letter).',
                1343235498
            );
        }
    }
}
