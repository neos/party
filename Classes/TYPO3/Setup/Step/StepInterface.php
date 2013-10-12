<?php
namespace TYPO3\Setup\Step;

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
 * A contract for setup steps.
 */
interface StepInterface {

	/**
	 * Sets options of this step
	 *
	 * @param array $options
	 * @return void
	 */
	public function setOptions(array $options);

	/**
	 * Sets global settings of the Flow distribution
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

	/**
	 * This method is called when the form of this step has been submitted
	 *
	 * @param array $formValues
	 * @return void
	 */
	public function postProcessFormValues(array $formValues);

	/**
	 * @return boolean
	 */
	public function isOptional();

}
