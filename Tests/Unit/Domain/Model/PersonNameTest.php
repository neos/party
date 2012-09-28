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
 * Testcase for a personName
 *
 */
class PersonNameTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @test
	 */
	public function fullNameIsBuiltUpRightFromNameParts() {
		$personName = new \TYPO3\Party\Domain\Model\PersonName(NULL, 'Sebastian', NULL, 'Michaelsen', '(born Gebhard)');
		$this->assertEquals('Sebastian Michaelsen (born Gebhard)', $personName->getFullName());
	}

}
?>