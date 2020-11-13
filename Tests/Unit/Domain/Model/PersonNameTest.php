<?php
namespace Neos\Party\Tests\Unit\Domain\Model;

/*
 * This file is part of the Neos.Party package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Tests\UnitTestCase;
use Neos\Party\Domain\Model\PersonName;
use PHPUnit\Framework\Assert;

/**
 * Testcase for the person name entity
 */
class PersonNameTest extends UnitTestCase
{
    /**
     * @test
     */
    public function fullNameIsBuiltUpRightFromNameParts()
    {
        $personName = new PersonName(null, 'Sebastian', null, 'Michaelsen', '(born Gebhard)');
        Assert::assertEquals('Sebastian Michaelsen (born Gebhard)', $personName->getFullName());
    }
}
