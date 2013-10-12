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

use TYPO3\Flow\Annotations as Flow;

/**
 * Abstract base class for Step Conditions
 */
abstract class AbstractCondition implements ConditionInterface {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * @var array
	 */
	protected $options;

	/**
	 * Sets options of this condition
	 *
	 * @param array $options
	 * @return void
	 */
	public function setOptions(array $options) {
		$this->options = $options;
	}

}
