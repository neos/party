<?php
namespace TYPO3\Setup\ViewHelpers\Widget\Controller;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Setup".                *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Controller for the DatabaseSelector Fluid Widget
 */
class DatabaseSelectorController extends \TYPO3\Fluid\Core\Widget\AbstractWidgetController {

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\FLOW3\Configuration\ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('userFieldId', $this->widgetConfiguration['userFieldId']);
		$this->view->assign('passwordFieldId', $this->widgetConfiguration['passwordFieldId']);
		$this->view->assign('hostFieldId', $this->widgetConfiguration['hostFieldId']);
		$this->view->assign('dbNameTextFieldId', $this->widgetConfiguration['dbNameTextFieldId']);
		$this->view->assign('dbNameDropdownFieldId', $this->widgetConfiguration['dbNameDropdownFieldId']);
		$this->view->assign('statusContainerId', $this->widgetConfiguration['statusContainerId']);
	}

	/**
	 * @param string $user
	 * @param string $password
	 * @param string $host
	 * @return void
	 */
	public function checkConnectionAction($user, $password, $host) {
		$settings = $this->configurationManager->getConfiguration(\TYPO3\FLOW3\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.FLOW3');
		$connectionSettings = $settings['persistence']['backendOptions'];
		$connectionSettings['user'] = $user;
		$connectionSettings['password'] = $password;
		$connectionSettings['host'] = $host;
		unset($connectionSettings['dbname']);
		$result = $this->getDatabases($connectionSettings);
		return json_encode($result);
	}

	/**
	 * @param array $connectionSettings
	 * @return array
	 */
	protected function getDatabases(array $connectionSettings) {
		try {
			$connection = \Doctrine\DBAL\DriverManager::getConnection($connectionSettings);
			$connection->connect();
		} catch(\PDOException $e) {
			return array('success' => FALSE, 'errorMessage' => $e->getMessage(), 'errorCode' => $e->getCode());
		} catch(\Exception $e) {
			return array('success' => FALSE, 'errorMessage' => 'Unexpected exception', 'errorCode' => $e->getCode());
		}
		try {
			$databases = $connection->getSchemaManager()->listDatabases();
			return array('success' => TRUE, 'databases' => $databases);
		} catch(\Exception $e) {
			return array('success' => FALSE, 'errorMessage' => 'Unexpected exception', 'errorCode' => $e->getCode());
		}
	}
}

?>