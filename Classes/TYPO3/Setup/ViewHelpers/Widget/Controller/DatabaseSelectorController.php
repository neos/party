<?php
namespace TYPO3\Setup\ViewHelpers\Widget\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Setup".           *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Configuration\ConfigurationManager;

/**
 * Controller for the DatabaseSelector Fluid Widget
 */
class DatabaseSelectorController extends \TYPO3\Fluid\Core\Widget\AbstractWidgetController {

	/**
	 * @Flow\Inject
	 * @var ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('driverDropdownFieldId', $this->widgetConfiguration['driverDropdownFieldId']);
		$this->view->assign('userFieldId', $this->widgetConfiguration['userFieldId']);
		$this->view->assign('passwordFieldId', $this->widgetConfiguration['passwordFieldId']);
		$this->view->assign('hostFieldId', $this->widgetConfiguration['hostFieldId']);
		$this->view->assign('dbNameTextFieldId', $this->widgetConfiguration['dbNameTextFieldId']);
		$this->view->assign('dbNameDropdownFieldId', $this->widgetConfiguration['dbNameDropdownFieldId']);
		$this->view->assign('statusContainerId', $this->widgetConfiguration['statusContainerId']);
		$this->view->assign('metadataStatusContainerId', $this->widgetConfiguration['metadataStatusContainerId']);
	}

	/**
	 * @param string $driver
	 * @param string $user
	 * @param string $password
	 * @param string $host
	 * @return string
	 */
	public function checkConnectionAction($driver, $user, $password, $host) {
		$this->response->setHeader('Content-Type', 'application/json');
		$connectionSettings = $this->buildConnectionSettingsArray($driver, $user, $password, $host);
		try {
			$connection = $this->getConnectionAndConnect($connectionSettings);
			$databases = $connection->getSchemaManager()->listDatabases();
			$result = array('success' => TRUE, 'databases' => $databases);
		} catch(\PDOException $e) {
			$result = array('success' => FALSE, 'errorMessage' => $e->getMessage(), 'errorCode' => $e->getCode());
		} catch(\Exception $e) {
			$result = array('success' => FALSE, 'errorMessage' => 'Unexpected exception', 'errorCode' => $e->getCode());
		}
		return json_encode($result);
	}

	/**
	 * This fetches information about the database provided, in particular the charset being used.
	 * Depending on whether it is utf8 or not, the (JSON-) response is layed out accordingly.
	 *
	 * @param string $driver
	 * @param string $user
	 * @param string $password
	 * @param string $host
	 * @param string $databaseName
	 * @return string
	 */
	public function getMetadataAction($driver, $user, $password, $host, $databaseName) {
		$this->response->setHeader('Content-Type', 'application/json');
		$connectionSettings = $this->buildConnectionSettingsArray($driver, $user, $password, $host);
		$connectionSettings['dbname'] = $databaseName;
		try {
			$connection = $this->getConnectionAndConnect($connectionSettings);
			$databasePlatform = $connection->getDatabasePlatform();
			if ($databasePlatform instanceof MySqlPlatform) {
				$queryResult = $connection->executeQuery('SHOW VARIABLES LIKE ?', array('character_set_database'))->fetch();
				$databaseCharacterSet = strtolower($queryResult['Value']);
			} else if ($databasePlatform instanceof PostgreSqlPlatform) {
				$queryResult = $connection->executeQuery('SELECT pg_encoding_to_char(encoding) FROM pg_database WHERE datname = ?', array($databaseName))->fetch();
				$databaseCharacterSet = strtolower($queryResult['pg_encoding_to_char']);
			} else {
				$result = array('level' => 'notice', 'message' => sprintf('Only MySQL/MariaDB and PostgreSQL are supported, the selected database is "%s".',  $databasePlatform->getName()));
			}
			if (isset($databaseCharacterSet)) {
				if ($databaseCharacterSet === 'utf8') {
					$result = array('level' => 'ok', 'message' => 'The selected database\'s character set is set to "utf8" which is the recommended setting.');
				} else {
					$result = array(
						'level' => 'notice',
						'message' => sprintf('The selected database\'s character set is "%s", however changing it to "utf8" is urgently recommended. This setup tool won\'t do this up for you.', $databaseCharacterSet)
					);
				}
			}
		} catch(\PDOException $e) {
			$result = array('level' => 'error', 'message' => $e->getMessage(), 'errorCode' => $e->getCode());
		} catch(\Exception $e) {
			$result = array('level' => 'error', 'message' => 'Unexpected exception', 'errorCode' => $e->getCode());
		}
		return json_encode($result);
	}

	/**
	 * @param string $driver
	 * @param string $user
	 * @param string $password
	 * @param string $host
	 * @return mixed
	 */
	protected function buildConnectionSettingsArray($driver, $user, $password, $host) {
		$settings = $this->configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.Flow');
		$connectionSettings = $settings['persistence']['backendOptions'];
		$connectionSettings['driver'] = $driver;
		$connectionSettings['user'] = $user;
		$connectionSettings['password'] = $password;
		$connectionSettings['host'] = $host;
		if ($connectionSettings['driver'] === 'pdo_pgsql') {
			$connectionSettings['dbname'] = 'template1';
			return $connectionSettings;
		} else {
			unset($connectionSettings['dbname']);
			return $connectionSettings;
		}
	}

	/**
	 * @param array $connectionSettings
	 * @return \Doctrine\DBAL\Connection
	 */
	protected function getConnectionAndConnect(array $connectionSettings) {
		$connection = \Doctrine\DBAL\DriverManager::getConnection($connectionSettings);
		$connection->connect();
		return $connection;
	}
}
