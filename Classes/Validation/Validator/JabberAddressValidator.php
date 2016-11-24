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
 * Validator for Jabber addresses.
 *
 * @api
 * @Flow\Scope("singleton")
 */
class JabberAddressValidator extends AbstractValidator
{
    /**
     * Checks if the given value is a valid Jabber name.
     *
     * The Jabber address has the following structure: "name@jabber.org"
     * More information is found on:
     * http://tracker.phpbb.com/browse/PHPBB3-3832
     *
     * @param mixed $value The value that should be validated
     * @return void
     * @api
     */
    protected function isValid($value)
    {
        if (!is_string($value) || preg_match('#^[a-z0-9\.\-_\+]+?@(.*?\.)*?[a-z0-9\-_]+?\.[a-z]{2,4}(/.*)?$#i', $value) !== 1) {
            $this->addError('Please specify a valid Jabber address.', 1343235498);
        }
    }
}
