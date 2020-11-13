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

use Doctrine\Common\Collections\Collection;
use Neos\Flow\Security\Account;
use Neos\Flow\Tests\UnitTestCase;
use Neos\Party\Domain\Model\AbstractParty;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Testcase for an abstract party
 */
class AbstractPartyTest extends UnitTestCase
{
    /**
     * @var AbstractParty
     */
    protected $abstractParty;

    /**
     * @var Collection|MockObject
     */
    protected $mockAccounts;

    /**
     * @throws \ReflectionException
     */
    public function setUp(): void
    {
        $this->abstractParty = $this->getMockForAbstractClass(AbstractParty::class, ['dummy']);

        $this->mockAccounts = $this->getMockBuilder(Collection::class)->disableOriginalConstructor()->getMock();
        $this->inject($this->abstractParty, 'accounts', $this->mockAccounts);
    }

    /**
     * @test
     */
    public function addAccountAddsAccountToAccountsCollection()
    {
        $account = new Account();
        $this->mockAccounts->expects($this->once())->method('add')->with($account);
        $this->abstractParty->addAccount($account);
    }

    /**
     * @test
     */
    public function removeAccountRemovesAccountFromAccountsCollection()
    {
        $account = new Account();
        $this->mockAccounts->expects($this->once())->method('removeElement')->with($account);
        $this->abstractParty->removeAccount($account);
    }

    /**
     * @test
     */
    public function getAccountsReturnsAccounts()
    {
        Assert::assertSame($this->mockAccounts, $this->abstractParty->getAccounts());
    }
}
