<?php
namespace TYPO3\Setup\Core;

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
use TYPO3\FLOW3\Http\Request;
use TYPO3\FLOW3\Http\Response;

/**
 * A request handler which can handle HTTP requests.
 *
 * @FLOW3\Scope("singleton")
 */
class RequestHandler extends \TYPO3\FLOW3\Http\RequestHandler {

	/**
	 * This request handler can handle any web request.
	 *
	 * @return boolean If the request is a web request, TRUE otherwise FALSE
	 */
	public function canHandleRequest() {
		return (PHP_SAPI !== 'cli' && ((strlen($_SERVER['REQUEST_URI']) === 6 && $_SERVER['REQUEST_URI'] === '/setup') || in_array(substr($_SERVER['REQUEST_URI'], 0, 7), array('/setup/', '/setup?'))));
	}

	/**
	 * Returns the priority - how eager the handler is to actually handle the
	 * request.
	 *
	 * @return integer The priority of the request handler.
	 */
	public function getPriority() {
		return 200;
	}

	/**
	 * Handles a HTTP request
	 *
	 * @return void
	 */
	public function handleRequest() {
			// Create the request very early so the Resource Management has a chance to grab it:
		$this->request = Request::createFromEnvironment();
		$this->response = new Response();

		$this->checkBasicRequirementsAndDisplayLoadingScreen();

		$this->boot();
		$this->resolveDependencies();
		$this->request->injectSettings($this->settings);

		$packageManager = $this->bootstrap->getEarlyInstance('TYPO3\FLOW3\Package\PackageManagerInterface');
		$configurationSource = $this->bootstrap->getObjectManager()->get('TYPO3\FLOW3\Configuration\Source\YamlSource');

		$this->router->setRoutesConfiguration($configurationSource->load($packageManager->getPackage('TYPO3.Setup')->getConfigurationPath() . \TYPO3\FLOW3\Configuration\ConfigurationManager::CONFIGURATION_TYPE_ROUTES));
		$actionRequest = $this->router->route($this->request);

		$this->securityContext->injectRequest($actionRequest);

		$this->dispatcher->dispatch($actionRequest, $this->response);

		$this->response->makeStandardsCompliant($this->request);
		$this->response->send();

		$this->bootstrap->shutdown('Runtime');
		$this->exit->__invoke();
	}

	/**
	 * Check the basic requirements, and display a loading screen on initial request.
	 *
	 * @return void
	 */
	protected function checkBasicRequirementsAndDisplayLoadingScreen() {
		$basicRequirements = new BasicRequirements();
		$messageRenderer = new MessageRenderer($this->bootstrap);
		$error = $basicRequirements->findError();
		if ($error !== NULL) {
			$messageRenderer->showMessage($error);
		}
		$currentUri = substr($this->request->getUri(), strlen($this->request->getBaseUri()));
		if ($currentUri === 'setup' || $currentUri === 'setup/') {
			$redirectUri = ($currentUri === 'setup/' ? 'index': 'setup/index');
			$messageRenderer->showMessage(new \TYPO3\FLOW3\Error\Message('We are now redirecting you to the setup. <b>This might take 10-60 seconds on the first run,</b> as FLOW3 needs to build up various caches.', 0, array(), 'Your environment is suited for installing FLOW3!'), '<meta http-equiv="refresh" content="2;URL=\'' . $redirectUri . '\'">');
		}
	}
}
?>