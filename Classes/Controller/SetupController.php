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
class SetupController extends \TYPO3\FLOW3\MVC\Controller\ActionController {

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
			// TODO this should not only check for any authentication but for Setup authentication (maybe use a special role?)
		if ($this->authenticationManager->isAuthenticated() === FALSE) {
			$this->forward('login', 'Login', NULL, array('step' => $step));
		}
		$controller = $this;
		$callback = function(\TYPO3\Form\Core\Model\FinisherContext $finisherContext) use ($controller) {
			$controller->postProcessFormValues($finisherContext->getFormRuntime()->getFormState()->getFormValues());
		};
		$currentStep = $this->instantiateCurrentStep($step);
		$formDefinition = $currentStep['stepClass']->getFormDefinition($callback);
		$response = new \TYPO3\FLOW3\MVC\Web\SubResponse($this->response);
		$form = $formDefinition->bind($this->request, $response);
		$stepIndex = $currentStep['stepIndex'];
		$this->view->assignMultiple(array(
			'form' => $form->render(),
			'step' => $stepIndex,
			'previousStep' => $stepIndex === 0 ? NULL : $stepIndex - 1
		));
	}

	/**
	 * @param integer $requestedStepIndex
	 * @return array
	 * @throws \TYPO3\Setup\Exception
	 */
	protected function instantiateCurrentStep($requestedStepIndex) {
		if (!isset($this->settings['stepOrder']) || !is_array($this->settings['stepOrder'])) {
			throw new \TYPO3\Setup\Exception('No "stepOrder" configured, setup can\'t be invoked', 1332167136);
		}
		$stepOrder = $this->settings['stepOrder'];
		if (!array_key_exists($requestedStepIndex, $stepOrder)) {
				// TODO instead of throwing an exception we might also quietly jump to another step
			throw new \TYPO3\Setup\Exception(sprintf('No setup step #%d configured, setup can\'t be invoked', $requestedStepIndex), 1332167418);
		}
		$stepIndex = $requestedStepIndex;
		while ($this->checkRequiredConditions($stepOrder[$stepIndex]) !== TRUE) {
			if ($stepIndex === 0) {
				throw new \TYPO3\Setup\Exception('Not all requirements are met for the first setup step, aborting setup', 1332169088);
			}
			$this->addFlashMessage('Not all requirements are met for step "%s"', '', \TYPO3\FLOW3\Error\Message::SEVERITY_WARNING, array($stepOrder[$stepIndex]));
			$this->redirect('index', NULL, NULL, array('step' => $stepIndex - 1));
		};
		$currentStepIdentifier = $stepOrder[$stepIndex];
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
		return array('stepIndex' => $stepIndex, 'stepClass' => $currentStep);
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
	 * @return void
	 */
	public function postProcessFormValues(array $formValues) {
		foreach ($formValues as $key => $value) {
			if (substr($key, 0, 9) === 'settings_') {
				$settingPath = str_replace('_', '.', substr($key, 9));
				$this->distributionSettings = \TYPO3\FLOW3\Utility\Arrays::setValueByPath($this->distributionSettings, $settingPath, $value);
			}
		}
		$this->configurationSource->save(FLOW3_PATH_CONFIGURATION . \TYPO3\FLOW3\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, $this->distributionSettings);
		if ($this->request->hasArgument('step')) {
			$currentStep = (integer)$this->request->getArgument('step');
		} else {
			$currentStep = 0;
		}
		$this->redirect('index', NULL, NULL, array('step' => $currentStep + 1));
	}

}
?>