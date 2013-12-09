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

use TYPO3\Flow\Annotations as Flow,
	TYPO3\Form\Core\Model\FormDefinition;

/**
 * @Flow\Scope("singleton")
 */
class FinalStep extends \TYPO3\Setup\Step\AbstractStep {

	/**
	 * Returns the form definitions for the step
	 *
	 * @param \TYPO3\Form\Core\Model\FormDefinition $formDefinition
	 * @return void
	 */
	protected function buildForm(\TYPO3\Form\Core\Model\FormDefinition $formDefinition) {
		$page1 = $formDefinition->createPage('page1');
		$page1->setRenderingOption('header', 'Setup complete');

		$title = $page1->createElement('connectionSection', 'TYPO3.Form:Section');
		$title->setLabel('Congratulations');

		$success = $title->createElement('success', 'TYPO3.Form:StaticText');
		$success->setProperty('text', 'You successfully completed the setup');
		$success->setProperty('class', 'alert alert-success');

		$link = $title->createElement('link', 'TYPO3.Setup:LinkElement');
		$link->setLabel('Go to the homepage');
		$link->setProperty('href', '/');
		$link->setProperty('class', 'btn btn-large btn-primary');

		$info = $title->createElement('info', 'TYPO3.Form:StaticText');
		$info->setProperty('text', 'If the homepage doesn\'t work, you might need configure routing in Configuration/Routes.yaml');
		$info->setProperty('class', 'alert alert-info');
	}
}