<?php
namespace TYPO3\Setup\Step;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Setup".                *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

interface StepInterface {

	/**
	 * Sets options of this step
	 *
	 * @param array $options
	 * @return void
	 */
	public function setOptions(array $options);

	/**
	 * Sets global settings of the FLOW3 distribution
	 *
	 * @param array $distributionSettings
	 * @return void
	 */
	public function setDistributionSettings(array $distributionSettings);

	/**
	 * Returns the form definitions for the step
	 *
	 * @param \Closure $callback
	 * @return \TYPO3\Form\Core\Model\FormDefinition
	 */
	public function getFormDefinition(\Closure $callback);

}
?>