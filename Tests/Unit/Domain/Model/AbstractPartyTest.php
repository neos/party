<?php
namespace TYPO3\Party\Tests\Unit\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Party".                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\Common\Collections\Collection;
use TYPO3\Flow\Security\Account;
use TYPO3\Flow\Tests\UnitTestCase;
use TYPO3\Party\Domain\Model\AbstractParty;

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
     * @var Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockAccounts;

    public function setUp()
    {
        $this->abstractParty = $this->getMockForAbstractClass('TYPO3\Party\Domain\Model\AbstractParty', ['dummy']);

        $this->mockAccounts = $this->getMockBuilder('Doctrine\Common\Collections\Collection')->disableOriginalConstructor()->getMock();
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
        $this->assertSame($this->mockAccounts, $this->abstractParty->getAccounts());
    }
}
