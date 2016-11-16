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

use TYPO3\Party\Domain\Model\PersonName;

/**
 * Testcase for the person name entity
 */
class PersonNameTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @test
	 */
	public function fullNameIsBuiltUpRightFromNameParts() {
		$personName = new PersonName(NULL, 'Sebastian', NULL, 'Michaelsen', '(born Gebhard)');
		$this->assertEquals('Sebastian Michaelsen (born Gebhard)', $personName->getFullName());
	}

}
