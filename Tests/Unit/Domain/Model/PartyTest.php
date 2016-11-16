<?php
namespace TYPO3\Party\Tests\Unit\Domain\Model;

/*
 * This file is part of the TYPO3.Party package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Doctrine\Common\Collections\Collection;
use TYPO3\Flow\Security\Account;
use TYPO3\Flow\Tests\UnitTestCase;
use TYPO3\Party\Domain\Model\AbstractParty;

/**
 * Testcase for an abstract party
 */
class AbstractPartyTest extends UnitTestCase {

	/**
	 * @var AbstractParty
	 */
	protected $abstractParty;

	/**
	 * @var Collection|\PHPUnit_Framework_MockObject_MockObject
	 */
	protected $mockAccounts;

	public function setUp() {
		$this->abstractParty = $this->getMockForAbstractClass(AbstractParty::class, array('dummy'));

		$this->mockAccounts = $this->getMockBuilder(Collection::class)->disableOriginalConstructor()->getMock();
		$this->inject($this->abstractParty, 'accounts', $this->mockAccounts);
	}

	/**
	 * @test
	 */
	public function addAccountAddsAccountToAccountsCollection() {
		$account = new Account();
		$this->mockAccounts->expects($this->once())->method('add')->with($account);
		$this->abstractParty->addAccount($account);
	}

	/**
	 * @test
	 */
	public function removeAccountRemovesAccountFromAccountsCollection() {
		$account = new Account();
		$this->mockAccounts->expects($this->once())->method('removeElement')->with($account);
		$this->abstractParty->removeAccount($account);
	}

	/**
	 * @test
	 */
	public function getAccountsReturnsAccounts() {
		$this->assertSame($this->mockAccounts, $this->abstractParty->getAccounts());
	}

}
