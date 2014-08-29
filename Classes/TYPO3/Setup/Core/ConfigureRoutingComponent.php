<?php
namespace TYPO3\Setup\Core;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Configuration\ConfigurationManager;
use TYPO3\Flow\Http\Component\ComponentContext;
use TYPO3\Flow\Http\Component\ComponentInterface;
use TYPO3\Flow\Mvc\Routing\Router;
use TYPO3\Flow\Object\ObjectManagerInterface;
use TYPO3\Flow\Package\PackageManagerInterface;

/**
 * Configure routing HTTP component for Neos setup
 */
class ConfigureRoutingComponent implements ComponentInterface {

	/**
	 * @Flow\Inject
	 * @var Router
	 */
	protected $router;

	/**
	 * @Flow\Inject
	 * @var ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * @Flow\Inject
	 * @var ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @Flow\Inject
	 * @var PackageManagerInterface
	 */
	protected $packageManager;

	/**
	 * @var array
	 */
	protected $options;

	/**
	 * @param array $options
	 */
	public function __construct(array $options = array()) {
		$this->options = $options;
	}

	/**
	 * Set the routes configuration for the Neos setup and configures the routing component
	 * to skip initialisation, which would overwrite the specific settings again.
	 *
	 * @param ComponentContext $componentContext
	 * @return void
	 */
	public function handle(ComponentContext $componentContext) {
		$configurationSource = $this->objectManager->get('TYPO3\Flow\Configuration\Source\YamlSource');
		$routesConfiguration = $configurationSource->load($this->packageManager->getPackage('TYPO3.Setup')->getConfigurationPath() . ConfigurationManager::CONFIGURATION_TYPE_ROUTES);
		$this->router->setRoutesConfiguration($routesConfiguration);
		$componentContext->setParameter('TYPO3\Flow\Mvc\Routing\RoutingComponent', 'skipRouterInitialization', TRUE);
	}
}