<?php
namespace TYPO3\Setup\Controller;

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
 * @Flow\Scope("singleton")
 */
class LoginController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @var string
	 */
	protected $keyName;

	/**
	 * The authentication manager
	 * @var \TYPO3\Flow\Security\Authentication\AuthenticationManagerInterface
	 * @Flow\Inject
	 */
	protected $authenticationManager;

	/**
	 * @var \TYPO3\Flow\Security\Cryptography\FileBasedSimpleKeyService
	 * @Flow\Inject
	 */
	protected $fileBasedSimpleKeyService;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * Gets the authentication provider configuration needed
	 *
	 * @return void
	 */
	public function initializeObject() {
		$settings = $this->configurationManager->getConfiguration(\TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.Flow');
		if (isset($settings['security']['authentication']['providers']['Typo3SetupProvider']['providerOptions']['keyName'])) {
			$this->keyName = $settings['security']['authentication']['providers']['Typo3SetupProvider']['providerOptions']['keyName'];
		}
	}

	/**
	 * @param integer $step The requested setup step
	 * @return void
	 */
	public function loginAction($step = 0) {
		if ($this->fileBasedSimpleKeyService->keyExists($this->keyName) === FALSE || file_exists($this->settings['initialPasswordFile'])) {
			$setupPassword = $this->fileBasedSimpleKeyService->generateKey($this->keyName);

			$initialPasswordFileContents = 'The setup password is:' . PHP_EOL;
			$initialPasswordFileContents .= PHP_EOL;
			$initialPasswordFileContents .= $setupPassword . PHP_EOL;
			$initialPasswordFileContents .= PHP_EOL;
			$initialPasswordFileContents .= 'After you successfully logged in, this file is automatically deleted for security reasons.' . PHP_EOL;
			$initialPasswordFileContents .= 'Make sure to save the setup password for later use.' . PHP_EOL;

			$result = file_put_contents($this->settings['initialPasswordFile'], $initialPasswordFileContents);
			if ($result === FALSE) {
				$this->addFlashMessage('It was not possible to save the initial setup password to file "%s". Check file permissions and re-try.', 'Password Generation Failure', \TYPO3\Flow\Error\Message::SEVERITY_ERROR, array($this->settings['initialPasswordFile']));
			} else {
				$this->view->assign('initialPasswordFile', $this->settings['initialPasswordFile']);
			}
		} else {
			$existingPasswordFile = \TYPO3\Flow\Utility\Files::concatenatePaths(array(FLOW_PATH_DATA, 'Persistent', 'FileBasedSimpleKeyService', $this->keyName));
			$this->view->assign('existingPasswordFile', $existingPasswordFile);
		}
		$this->view->assign('step', $step);
	}

	/**
	 * @param integer $step The requested setup step
	 * @return void
	 * @Flow\SkipCsrfProtection
	 */
	public function authenticateAction($step) {
		try {
			$this->authenticationManager->authenticate();

			if (file_exists($this->settings['initialPasswordFile'])) {
				unlink($this->settings['initialPasswordFile']);
			}
			$this->redirect('index', 'Setup', NULL, array('step' => $step));
		} catch (\TYPO3\Flow\Security\Exception\AuthenticationRequiredException $exception) {
			$this->addFlashMessage('Sorry, you were not able to authenticate.', 'Authentication error', \TYPO3\Flow\Error\Message::SEVERITY_ERROR);
			$this->redirect('login', NULL, NULL, array('step' => $step));
		}
	}

	/**
	 * Logout all active authentication tokens.
	 *
	 * @return void
	 */
	public function logoutAction() {
		$this->authenticationManager->logout();
		$this->addFlashMessage('Successfully logged out.', 'Logged out', \TYPO3\Flow\Error\Message::SEVERITY_OK);
		$this->redirect('login');
	}

}
