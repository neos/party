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
use TYPO3\Flow\Configuration\ConfigurationManager;
use TYPO3\Flow\Error\Error;
use TYPO3\Flow\Http\Component\ComponentContext;
use TYPO3\Flow\Http\Request;
use TYPO3\Flow\Http\RequestHandler as FlowRequestHandler;
use TYPO3\Flow\Http\Response;
use TYPO3\Flow\Error\Message;
use TYPO3\Flow\Http\Uri;
use TYPO3\Flow\Utility\Arrays;
use TYPO3\Flow\Utility\Files;

/**
 * A request handler which can handle HTTP requests.
 *
 * @Flow\Scope("singleton")
 */
class RequestHandler extends FlowRequestHandler {

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
		if (isset($this->settings['http']['baseUri'])) {
			$this->request->setBaseUri(new Uri($this->settings['http']['baseUri']));
		}

		$componentContext = new ComponentContext($this->request, $this->response);
		$this->baseComponentChain->handle($componentContext);

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
		if ($result instanceof Error) {
			$messageRenderer->showMessages(array($result));
			return;
		}

		$phpBinaryDetectionMessage = $this->checkAndSetPhpBinaryIfNeeded();
		if ($phpBinaryDetectionMessage instanceof Error) {
			$messageRenderer->showMessages(array($phpBinaryDetectionMessage));
			return;
		}

		$currentUri = substr($this->request->getUri(), strlen($this->request->getBaseUri()));
		if ($currentUri === 'setup' || $currentUri === 'setup/') {
			$redirectUri = ($currentUri === 'setup/' ? 'index': 'setup/index');
			$messages = array(new Message('We are now redirecting you to the setup. <b>This might take 10-60 seconds on the first run,</b> as TYPO3 Flow needs to build up various caches.', NULL, array(), 'Your environment is suited for installing TYPO3 Flow!'));
			if ($phpBinaryDetectionMessage !== NULL) {
				array_unshift($messages, $phpBinaryDetectionMessage);
			}
			$messageRenderer->showMessages($messages, '<meta http-equiv="refresh" content="2;URL=\'' . $redirectUri . '\'">');
		}
	}

	/**
	 * Create a HTTP component chain that adds our own routing configuration component
	 * only for this request handler.
	 *
	 * @return void
	 */
	protected function resolveDependencies() {
		$objectManager = $this->bootstrap->getObjectManager();
		$componentChainFactory = $objectManager->get('TYPO3\Flow\Http\Component\ComponentChainFactory');
		$configurationManager = $objectManager->get('TYPO3\Flow\Configuration\ConfigurationManager');
		$this->settings = $configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.Flow');
		$setupSettings = $configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.Setup');
		$httpChainSettings = Arrays::arrayMergeRecursiveOverrule($this->settings['http']['chain'], $setupSettings['http']['chain']);
		$this->baseComponentChain = $componentChainFactory->create($httpChainSettings);
	}

	/**
	 * Checks if the configured PHP binary is executable and the same version as the one
	 * running the current (web server) PHP process. If not or if there is no binary configured,
	 * tries to find the correct one on the PATH.
	 *
	 * Once found, the binary will be written to the configuration, if it is not the default one
	 * (PHP_BINARY or in PHP_BINDIR).
	 *
	 * @return Message An error or warning message or NULL if PHP was detected successfully
	 */
	protected function checkAndSetPhpBinaryIfNeeded() {
		$configurationSource = new \TYPO3\Flow\Configuration\Source\YamlSource();
		$distributionSettings = $configurationSource->load(FLOW_PATH_CONFIGURATION . ConfigurationManager::CONFIGURATION_TYPE_SETTINGS);
		if (isset($distributionSettings['TYPO3']['Flow']['core']['phpBinaryPathAndFilename'])) {
			return $this->checkPhpBinary($distributionSettings['TYPO3']['Flow']['core']['phpBinaryPathAndFilename']);
		}
		list($phpBinaryPathAndFilename, $message) = $this->detectPhpBinaryPathAndFilename();
		if ($phpBinaryPathAndFilename !== NULL) {
			$defaultPhpBinaryPathAndFilename = PHP_BINDIR . '/php';
			if (DIRECTORY_SEPARATOR !== '/') {
				$defaultPhpBinaryPathAndFilename = str_replace('\\', '/', $defaultPhpBinaryPathAndFilename) . '.exe';
			}
			if ($phpBinaryPathAndFilename !== $defaultPhpBinaryPathAndFilename) {
				$distributionSettings = \TYPO3\Flow\Utility\Arrays::setValueByPath($distributionSettings, 'TYPO3.Flow.core.phpBinaryPathAndFilename', $phpBinaryPathAndFilename);
				$configurationSource->save(FLOW_PATH_CONFIGURATION . ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, $distributionSettings);
			}
		}
		return $message;
	}

	/**
	 * Checks if the given PHP binary is executable and of the same version as the currently running one.
	 *
	 * @param string $phpBinaryPathAndFilename
	 * @return Message An error or warning message or NULL if the PHP binary was detected successfully
	 */
	protected function checkPhpBinary($phpBinaryPathAndFilename) {
		$phpVersion = NULL;
		if (file_exists($phpBinaryPathAndFilename) && is_file($phpBinaryPathAndFilename)) {
			if (DIRECTORY_SEPARATOR === '/') {
				$phpCommand = '"' . escapeshellcmd(Files::getUnixStylePath($phpBinaryPathAndFilename)) . '"';
			} else {
				$phpCommand = escapeshellarg(Files::getUnixStylePath($phpBinaryPathAndFilename));
			}

			exec($phpCommand . ' -v', $phpVersionString);
			if (!isset($phpVersionString[0]) || strpos($phpVersionString[0], '(cli)') === FALSE) {
				return new Error('The specified path to your PHP binary (see Configuration/Settings.yaml) is incorrect or not a PHP command line (cli) version.', 1341839376, array(), 'Environment requirements not fulfilled');
			}
			$versionStringParts = explode(' ', $phpVersionString[0]);
			$phpVersion = isset($versionStringParts[1]) ? trim($versionStringParts[1]) : NULL;
			if ($phpVersion === PHP_VERSION) {
				return NULL;
			}
		}
		if ($phpVersion === NULL) {
			return new Error('The specified path to your PHP binary (see Configuration/Settings.yaml) is incorrect.', 1341839376, array(), 'Environment requirements not fulfilled');
		} else {
			$phpMinorVersionMatch = array_slice(explode('.', $phpVersion), 0, 2) === array_slice(explode('.', PHP_VERSION), 0, 2);
			if ($phpMinorVersionMatch) {
				return new \TYPO3\Flow\Error\Warning('The specified path to your PHP binary (see Configuration/Settings.yaml) points to a PHP binary with the version "%s". This is not the exact same version as is currently running ("%s").', 1416913501, array($phpVersion, PHP_VERSION), 'Possible PHP version mismatch');
			} else {
				return new Error('The specified path to your PHP binary (see Configuration/Settings.yaml) points to a PHP binary with the version "%s". This is not compatible to the version that is currently running ("%s").', 1341839377, array($phpVersion, PHP_VERSION), 'Environment requirements not fulfilled');
			}
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
	 * @return array PHP binary path as string or NULL if not found and a possible Message
	 */
	protected function detectPhpBinaryPathAndFilename() {
		if (defined('PHP_BINARY') && PHP_BINARY !== '' && dirname(PHP_BINARY) === PHP_BINDIR) {
			return array(PHP_BINARY, NULL);
		}

		$environmentPaths = explode(PATH_SEPARATOR, getenv('PATH'));
		$environmentPaths[] = PHP_BINDIR;
		$lastCheckMessage = NULL;
		foreach ($environmentPaths as $path) {
			$path = rtrim(str_replace('\\', '/', $path), '/');
			if (strlen($path) === 0) {
				continue;
			}
			$phpBinaryPathAndFilename = $path . '/php' . (DIRECTORY_SEPARATOR !== '/' ? '.exe' : '');
			$lastCheckMessage = $this->checkPhpBinary($phpBinaryPathAndFilename);
			if (!$lastCheckMessage instanceof Error) {
				return array($phpBinaryPathAndFilename, $lastCheckMessage);
			}
		}
		return array(NULL, $lastCheckMessage);
	}
}
