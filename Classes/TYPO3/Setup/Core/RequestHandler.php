<?php
namespace TYPO3\Setup\Core;

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
use TYPO3\Flow\Http\Request;
use TYPO3\Flow\Http\Response;
use TYPO3\Flow\Error\Message;
use TYPO3\Flow\Utility\Files;

/**
 * A request handler which can handle HTTP requests.
 *
 * @Flow\Scope("singleton")
 */
class RequestHandler extends \TYPO3\Flow\Http\RequestHandler {

	/**
	 * @var \TYPO3\Flow\Http\Response
	 */
	protected $response;

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

		$packageManager = $this->bootstrap->getEarlyInstance('TYPO3\Flow\Package\PackageManagerInterface');
		$configurationSource = $this->bootstrap->getObjectManager()->get('TYPO3\Flow\Configuration\Source\YamlSource');

		$this->router->setRoutesConfiguration($configurationSource->load($packageManager->getPackage('TYPO3.Setup')->getConfigurationPath() . \TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_ROUTES));
		$actionRequest = $this->router->route($this->request);

		$this->securityContext->setRequest($actionRequest);

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
		$messageRenderer = new MessageRenderer($this->bootstrap);
		$basicRequirements = new BasicRequirements();
		$result = $basicRequirements->findError();
		if ($result instanceof \TYPO3\Flow\Error\Error) {
			$messageRenderer->showMessage($result);
		}

		$result = $this->checkAndSetPhpBinaryIfNeeded();
		if ($result instanceof \TYPO3\Flow\Error\Error) {
			$messageRenderer->showMessage($result);
		}

		$currentUri = substr($this->request->getUri(), strlen($this->request->getBaseUri()));
		if ($currentUri === 'setup' || $currentUri === 'setup/') {
			$redirectUri = ($currentUri === 'setup/' ? 'index': 'setup/index');
			$messageRenderer->showMessage(new Message('We are now redirecting you to the setup. <b>This might take 10-60 seconds on the first run,</b> as TYPO3 Flow needs to build up various caches.', NULL, array(), 'Your environment is suited for installing TYPO3 Flow!'), '<meta http-equiv="refresh" content="2;URL=\'' . $redirectUri . '\'">');
		}
	}

	/**
	 * Checks if the configured PHP binary is executable and the same version as the one
	 * running the current (web server) PHP process. If not or if there is no binary configured,
	 * tries to find the correct one on the PATH.
	 *
	 * Once found, the binary will be written to the configuration, if it is not the default one
	 * (PHP_BINARY or in PHP_BINDIR).
	 *
	 * @return boolean|\TYPO3\Flow\Error\Error TRUE on success, otherwise an instance of \TYPO3\Flow\Error\Error
	 */
	protected function checkAndSetPhpBinaryIfNeeded() {
		$configurationSource = new \TYPO3\Flow\Configuration\Source\YamlSource();
		$distributionSettings = $configurationSource->load(FLOW_PATH_CONFIGURATION . \TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS);
		if (isset($distributionSettings['TYPO3']['Flow']['core']['phpBinaryPathAndFilename'])) {
			return $this->checkPhpBinary($distributionSettings['TYPO3']['Flow']['core']['phpBinaryPathAndFilename']);
		}
		$phpBinaryPathAndFilename = $this->detectPhpBinaryPathAndFilename();
		if ($phpBinaryPathAndFilename !== NULL) {
			$defaultPhpBinaryPathAndFilename = PHP_BINDIR . '/php';
			if (DIRECTORY_SEPARATOR !== '/') {
				$defaultPhpBinaryPathAndFilename = str_replace('\\', '/', $defaultPhpBinaryPathAndFilename) . '.exe';
			}
			if ($phpBinaryPathAndFilename !== $defaultPhpBinaryPathAndFilename) {
				$distributionSettings = \TYPO3\Flow\Utility\Arrays::setValueByPath($distributionSettings, 'TYPO3.Flow.core.phpBinaryPathAndFilename', $phpBinaryPathAndFilename);
				$configurationSource->save(FLOW_PATH_CONFIGURATION . \TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, $distributionSettings);
			}
			return TRUE;
		} else {
			return new \TYPO3\Flow\Error\Error('The path to your PHP binary could not be detected. Please set it manually in Configuration/Settings.yaml.', 1341499159, array(), 'Environment requirements not fulfilled');
		}
	}

	/**
	 * Checks if the given PHP binary is executable and of the same version as the currently running one.
	 *
	 * @param string $phpBinaryPathAndFilename
	 * @return boolean|\TYPO3\Flow\Error\Error
	 */
	protected function checkPhpBinary($phpBinaryPathAndFilename) {
		$phpVersion = NULL;
		if (file_exists($phpBinaryPathAndFilename) && is_file($phpBinaryPathAndFilename)) {
			if (DIRECTORY_SEPARATOR === '/') {
				$phpCommand = '"' . escapeshellcmd(Files::getUnixStylePath($phpBinaryPathAndFilename)) . '"';
			} else {
				$phpCommand = escapeshellarg(Files::getUnixStylePath($phpBinaryPathAndFilename));
			}
			$phpVersion = trim(exec($phpCommand . ' -r "echo PHP_VERSION;"'));
			if ($phpVersion === PHP_VERSION) {
				return TRUE;
			}
		}
		if ($phpVersion === NULL) {
			return new \TYPO3\Flow\Error\Error('The specified path to your PHP binary (see Configuration/Settings.yaml) is incorrect.', 1341839376, array(), 'Environment requirements not fulfilled');
		} else {
			return new \TYPO3\Flow\Error\Error('The specified path to your PHP binary (see Configuration/Settings.yaml) points to a PHP binary with the version "%s". This is not the same version as is currently running ("%s").', 1341839377, array($phpVersion, PHP_VERSION), 'Environment requirements not fulfilled');
		}
	}

	/**
	 * Traverse the PATH locations and check for the existence of a valid PHP binary.
	 * If found, the path and filename are returned, if not NULL is returned.
	 *
	 * We only use PHP_BINARY if it's set to a file in the path PHP_BINDIR.
	 * This is because PHP_BINARY might, for example, be "/opt/local/sbin/php54-fpm"
	 * while PHP_BINDIR contains "/opt/local/bin" and the actual CLI binary is "/opt/local/bin/php".
	 *
	 * @return string
	 */
	protected function detectPhpBinaryPathAndFilename() {
		if (defined('PHP_BINARY') && PHP_BINARY !== '' && dirname(PHP_BINARY) === PHP_BINDIR) {
			return PHP_BINARY;
		}

		$environmentPaths = explode(PATH_SEPARATOR, getenv('PATH'));
		$environmentPaths[] = PHP_BINDIR;
		foreach ($environmentPaths as $path) {
			$path = rtrim(str_replace('\\', '/', $path), '/');
			if (strlen($path) === 0) {
				continue;
			}
			$phpBinaryPathAndFilename = $path . '/php' . (DIRECTORY_SEPARATOR !== '/' ? '.exe' : '');
			if ($this->checkPhpBinary($phpBinaryPathAndFilename) === TRUE) {
				return $phpBinaryPathAndFilename;
			}
		}
		return NULL;
	}
}
