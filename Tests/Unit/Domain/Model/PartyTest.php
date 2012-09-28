<?php
namespace TYPO3\Party\Tests\Unit\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Testcase for an abstract party
 *
 */
class AbstractPartyTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @test
	 */
	public function addAccountSetsThePartyPropertyInTheAccountToThisParty() {
		$party = $this->getMockForAbstractClass('TYPO3\Party\Domain\Model\AbstractParty', array('dummy'));

		$mockAccount = $this->getMock('TYPO3\Flow\Security\Account');
		$mockAccount->expects($this->once())->method('setParty')->with($party);

		$party->addAccount($mockAccount);
	}
}
?>