<?php
namespace TYPO3\Setup\Condition;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Setup".           *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Contract for Step Conditions
 */
interface ConditionInterface {

	/**
	 * Sets options of this condition
	 *
	 * @param array $options
	 * @return void
	 */
	public function setOptions(array $options);

	/**
	 * Returns TRUE if the condition is satisfied, otherwise FALSE
	 *
	 * @return boolean
	 * @api
	 */
	public function isMet();

}
