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
class LoginController extends \TYPO3\FLOW3\MVC\Controller\ActionController {

	/**
	 * @var string
	 */
	protected $keyName;

	/**
	 * The authentication manager
	 * @var \TYPO3\FLOW3\Security\Authentication\AuthenticationManagerInterface
	 * @FLOW3\Inject
	 */
	protected $authenticationManager;

	/**
	 * @var \TYPO3\FLOW3\Security\Cryptography\FileBasedSimpleKeyService
	 * @FLOW3\Inject
	 */
	protected $fileBasedSimpleKeyService;

	/**
	 * Injects the authentication provider configuration to be used
	 *
	 * @param array $settings
	 * @return void
	 */
	public function injectSettings(array $settings) {
		if (isset($settings['security']['authentication']['providers']['Typo3SetupProvider']['options']['name'])) {
			$this->keyName = $settings['security']['authentication']['providers']['Typo3SetupProvider']['options']['name'];
		}
	}

	/**
	 * @param integer $step The requested setup step
	 * @return void
	 */
	public function loginAction($step = 0) {
		if ($this->fileBasedSimpleKeyService->keyExists($this->keyName) === FALSE) {
			$this->view->assign('key', $this->fileBasedSimpleKeyService->generateKey($this->keyName));
		}
		$this->view->assign('step', $step);
	}

	/**
	 * @param integer $step The requested setup step
	 * @return void
	 */
	public function authenticateAction($step) {
		try {
			$this->authenticationManager->authenticate();
			$this->redirect('index', 'Setup', NULL, array('step' => $step));
		} catch (\TYPO3\FLOW3\Security\Exception\AuthenticationRequiredException $exception) {
		}

		$this->addFlashMessage('Sorry, you were not able to authenticate.', 'Authentication error', \TYPO3\FLOW3\Error\Message::SEVERITY_ERROR);
		$this->redirect('login', NULL, NULL, array('step' => $step));
	}

	/**
	 * Logout all active authentication tokens.
	 *
	 * @return void
	 */
	public function logoutAction() {
		$this->authenticationManager->logout();
		$this->addFlashMessage('Successfully logged out.', 'Logged out', \TYPO3\FLOW3\Error\Message::SEVERITY_OK);
		$this->redirect('login');
	}

}
?>