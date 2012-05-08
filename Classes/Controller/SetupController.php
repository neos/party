<?php
namespace TYPO3\Setup\Controller;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Setup".                *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * @FLOW3\Scope("singleton")
 */
class SetupController extends \TYPO3\FLOW3\Mvc\Controller\ActionController {

	/**
	 * The authentication manager
	 * @var \TYPO3\FLOW3\Security\Authentication\AuthenticationManagerInterface
	 * @FLOW3\Inject
	 */
	protected $authenticationManager;

	/**
	 * @var \TYPO3\FLOW3\Configuration\Source\YamlSource
	 * @FLOW3\Inject
	 */
	protected $configurationSource;

	/**
	 * The settings parsed from Settings.yaml
	 *
	 * @var array
	 */
	protected $distributionSettings;

	/**
	 * Contains the current step to be executed
	 * @see determineCurrentStepIndex()
	 *
	 * @var integer
	 */
	protected $currentStepIndex = 0;

	/**
	 * @return void
	 */
	public function initializeAction() {
		$this->distributionSettings = $this->configurationSource->load(FLOW3_PATH_CONFIGURATION . \TYPO3\FLOW3\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS);
	}

	/**
	 * @param integer $step
	 * @return void
	 */
	public function indexAction($step = 0) {
		$this->currentStepIndex = $step;
		$this->checkRequestedStepIndex();
		$currentStep = $this->instantiateCurrentStep();
		$controller = $this;
		$callback = function(\TYPO3\Form\Core\Model\FinisherContext $finisherContext) use ($controller, $currentStep) {
			$controller->postProcessStep($finisherContext->getFormRuntime()->getFormState()->getFormValues(), $currentStep);
		};
		$formDefinition = $currentStep->getFormDefinition($callback);
		$response = new \TYPO3\FLOW3\Http\Response($this->response);
		$form = $formDefinition->bind($this->request, $response);
		$this->view->assignMultiple(array(
			'form' => $form->render(),
			'step' => $this->currentStepIndex,
			'nextStep' => $this->currentStepIndex + 1,
			'optional' => $currentStep->isOptional()
		));
		if ($this->currentStepIndex > 0) {
			$this->view->assign('previousStep', $this->currentStepIndex - 1);
		}
	}

	/**
	 * @return void
	 * @throws \TYPO3\Setup\Exception
	 */
	protected function checkRequestedStepIndex() {
		if (!isset($this->settings['stepOrder']) || !is_array($this->settings['stepOrder'])) {
			throw new \TYPO3\Setup\Exception('No "stepOrder" configured, setup can\'t be invoked', 1332167136);
		}
		$stepOrder = $this->settings['stepOrder'];
		if (!array_key_exists($this->currentStepIndex, $stepOrder)) {
			// TODO instead of throwing an exception we might also quietly jump to another step
			throw new \TYPO3\Setup\Exception(sprintf('No setup step #%d configured, setup can\'t be invoked', $this->currentStepIndex), 1332167418);
		}
		while ($this->checkRequiredConditions($stepOrder[$this->currentStepIndex]) !== TRUE) {
			if ($this->currentStepIndex === 0) {
				throw new \TYPO3\Setup\Exception('Not all requirements are met for the first setup step, aborting setup', 1332169088);
			}
			$this->addFlashMessage('Not all requirements are met for step "%s"', '', \TYPO3\FLOW3\Error\Message::SEVERITY_WARNING, array($stepOrder[$this->currentStepIndex]));
			$this->redirect('index', NULL, NULL, array('step' => $this->currentStepIndex - 1));
		};
	}

	/**
	 * @return \TYPO3\Setup\Step\StepInterface
	 * @throws \TYPO3\Setup\Exception
	 */
	protected function instantiateCurrentStep() {
		$currentStepIdentifier = $this->settings['stepOrder'][$this->currentStepIndex];
		$currentStepConfiguration = $this->settings['steps'][$currentStepIdentifier];
		if (!isset($currentStepConfiguration['className'])) {
			throw new \TYPO3\Setup\Exception(sprintf('No className specified for setup step "%s", setup can\'t be invoked', $currentStepIdentifier), 1332169398);
		}
		$currentStep = new $currentStepConfiguration['className']();
		if (!$currentStep instanceof \TYPO3\Setup\Step\StepInterface) {
			throw new \TYPO3\Setup\Exception(sprintf('ClassName %s of setup step "%s" does not implement StepInterface, setup can\'t be invoked', $currentStepConfiguration['className'], $currentStepIdentifier), 1332169576);
		}
		if (isset($currentStepConfiguration['options'])) {
			$currentStep->setOptions($currentStepConfiguration['options']);
		}
		$currentStep->setDistributionSettings($this->distributionSettings);
		return $currentStep;
	}

	/**
	 * @param string $stepIdentifier
	 * @return boolean TRUE if all required conditions were met, otherwise FALSE
	 * @throws \TYPO3\Setup\Exception
	 */
	protected function checkRequiredConditions($stepIdentifier) {
		if (!isset($this->settings['steps'][$stepIdentifier]) || !is_array($this->settings['steps'][$stepIdentifier])) {
			throw new \TYPO3\Setup\Exception(sprintf('No configuration found for setup step "%s", setup can\'t be invoked', $stepIdentifier), 1332167685);
		}
		$stepConfiguration = $this->settings['steps'][$stepIdentifier];
		if (!isset($stepConfiguration['requiredConditions'])) {
			return TRUE;
		}
		foreach ($stepConfiguration['requiredConditions'] as $index => $conditionConfiguration) {
			if (!isset($conditionConfiguration['className'])) {
				throw new \TYPO3\Setup\Exception(sprintf('No condition className specified for condition #%d in setup step "%s", setup can\'t be invoked', $index, $stepIdentifier), 1332168070);
			}
			$condition = new $conditionConfiguration['className']();
			if (!$condition instanceof \TYPO3\Setup\Condition\ConditionInterface) {
				throw new \TYPO3\Setup\Exception(sprintf('Condition #%d (%s) in setup step "%s" does not implement ConditionInterface, setup can\'t be invoked', $index, $conditionConfiguration['className'], $stepIdentifier), 1332168070);
			}
			if (isset($conditionConfiguration['options'])) {
				$condition->setOptions($conditionConfiguration['options']);
			}
			if ($condition->isMet() !== TRUE) {
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * @param array $formValues
	 * @param \TYPO3\Setup\Step\StepInterface $currentStep
	 * @return void
	 */
	public function postProcessStep(array $formValues, \TYPO3\Setup\Step\StepInterface $currentStep) {
		$currentStep->postProcessFormValues($formValues);
		$this->redirect('index', NULL, NULL, array('step' => $this->currentStepIndex + 1));
	}

}
?>