<?php
namespace TYPO3\Setup\Step;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Setup".           *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow,
	TYPO3\Form\Core\Model\FormDefinition;

/**
 * @Flow\Scope("singleton")
 */
class DatabaseStep extends \TYPO3\Setup\Step\AbstractStep {

	/**
	 * @var \TYPO3\Flow\Configuration\Source\YamlSource
	 * @Flow\Inject
	 */
	protected $configurationSource;

	/**
	 * Returns the form definitions for the step
	 *
	 * @param \TYPO3\Form\Core\Model\FormDefinition $formDefinition
	 * @return void
	 */
	protected function buildForm(\TYPO3\Form\Core\Model\FormDefinition $formDefinition) {
		$page1 = $formDefinition->createPage('page1');

		$introduction = $page1->createElement('introduction', 'TYPO3.Form:StaticText');
		$introduction->setProperty('text', 'Please enter database details below:');

		$connectionSection = $page1->createElement('connectionSection', 'TYPO3.Form:Section');
		$connectionSection->setLabel('Connection');

		$databaseUser = $connectionSection->createElement('user', 'TYPO3.Form:SingleLineText');
		$databaseUser->setLabel('DB Username');
		$databaseUser->setDefaultValue(\TYPO3\Flow\Utility\Arrays::getValueByPath($this->distributionSettings, 'TYPO3.Flow.persistence.backendOptions.user'));
		$databaseUser->addValidator(new \TYPO3\Flow\Validation\Validator\NotEmptyValidator());

		$databasePassword = $connectionSection->createElement('password', 'TYPO3.Form:Password');
		$databasePassword->setLabel('DB Password');
		$databasePassword->setDefaultValue(\TYPO3\Flow\Utility\Arrays::getValueByPath($this->distributionSettings, 'TYPO3.Flow.persistence.backendOptions.password'));

		$databaseHost = $connectionSection->createElement('host', 'TYPO3.Form:SingleLineText');
		$databaseHost->setLabel('DB Host');
		$defaultHost = \TYPO3\Flow\Utility\Arrays::getValueByPath($this->distributionSettings, 'TYPO3.Flow.persistence.backendOptions.host');
		if ($defaultHost === NULL) {
			$defaultHost = '127.0.0.1';
		}
		$databaseHost->setDefaultValue($defaultHost);
		$databaseHost->addValidator(new \TYPO3\Flow\Validation\Validator\NotEmptyValidator());

		$databaseSection = $page1->createElement('databaseSection', 'TYPO3.Form:Section');
		$databaseSection->setLabel('Database');

		$databaseName = $databaseSection->createElement('dbname', 'TYPO3.Setup:DatabaseSelector');
		$databaseName->setLabel('DB Name');
		$databaseName->setProperty('userFieldId', $databaseUser->getUniqueIdentifier());
		$databaseName->setProperty('passwordFieldId', $databasePassword->getUniqueIdentifier());
		$databaseName->setProperty('hostFieldId', $databaseHost->getUniqueIdentifier());
		$databaseName->setDefaultValue(\TYPO3\Flow\Utility\Arrays::getValueByPath($this->distributionSettings, 'TYPO3.Flow.persistence.backendOptions.dbname'));
		$databaseName->addValidator(new \TYPO3\Flow\Validation\Validator\NotEmptyValidator());
	}

	/**
	 * This method is called when the form of this step has been submitted
	 *
	 * @param array $formValues
	 * @return void
	 */
	public function postProcessFormValues(array $formValues) {
		$this->distributionSettings = \TYPO3\Flow\Utility\Arrays::setValueByPath($this->distributionSettings, 'TYPO3.Flow.persistence.backendOptions.dbname', $formValues['dbname']);
		$this->distributionSettings = \TYPO3\Flow\Utility\Arrays::setValueByPath($this->distributionSettings, 'TYPO3.Flow.persistence.backendOptions.user', $formValues['user']);
		$this->distributionSettings = \TYPO3\Flow\Utility\Arrays::setValueByPath($this->distributionSettings, 'TYPO3.Flow.persistence.backendOptions.password', $formValues['password']);
		$this->distributionSettings = \TYPO3\Flow\Utility\Arrays::setValueByPath($this->distributionSettings, 'TYPO3.Flow.persistence.backendOptions.host', $formValues['host']);
		$this->configurationSource->save(FLOW_PATH_CONFIGURATION . \TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, $this->distributionSettings);

		$this->configurationManager->flushConfigurationCache();

		$settings = $this->configurationManager->getConfiguration(\TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.Flow');
		$connectionSettings = $settings['persistence']['backendOptions'];
		try {
			$this->connectToDatabase($connectionSettings);
		} catch (\PDOException $exception) {
			try {
				$this->createDatabase($connectionSettings, $formValues['dbname']);
			} catch (\PDOException $exception) {
				throw new \TYPO3\Setup\Exception(sprintf('Database "%s" could not be created. Please check the permissions for user "%s". PDO Exception: "%s"', $formValues['dbname'], $formValues['user'], $exception->getMessage()), 1346758663, $exception);
			}
			try {
				$this->connectToDatabase($connectionSettings);
			} catch (\PDOException $exception) {
				throw new \TYPO3\Setup\Exception(sprintf('Could not connect to database "%s". Please check the permissions for user "%s". PDO Exception: "%s"', $formValues['dbname'], $formValues['user'], $exception->getMessage()), 1346758737);
			}
		}

		$migrationExecuted = \TYPO3\Flow\Core\Booting\Scripts::executeCommand('typo3.flow:doctrine:migrate', $settings, FALSE);
		if ($migrationExecuted !== TRUE) {
			throw new \TYPO3\Setup\Exception(sprintf('Could not execute database migrations. Please check the permissions for user "%s" and execute "./flow typo3.flow:doctrine:migrate" manually.', $formValues['user']), 1346759486);
		}
	}

	/**
	 * Tries to connect to the database using the specified $connectionSettings
	 *
	 * @param array $connectionSettings array in the format array('user' => 'dbuser', 'password' => 'dbpassword', 'host' => 'dbhost', 'dbname' => 'dbname')
	 * @return void
	 * @throws \PDOException if the connection fails
	 */
	protected function connectToDatabase(array $connectionSettings) {
		$connection = \Doctrine\DBAL\DriverManager::getConnection($connectionSettings);
		$connection->connect();
	}

	/**
	 * Connects to the database using the specified $connectionSettings
	 * and tries to create a database named $databaseName.
	 *
	 * @param array $connectionSettings array in the format array('user' => 'dbuser', 'password' => 'dbpassword', 'host' => 'dbhost', 'dbname' => 'dbname')
	 * @param string $databaseName name of the database to create
	 * @return void
	 * @throws \PDOException if creation of database failed
	 */
	protected function createDatabase(array $connectionSettings, $databaseName) {
		unset($connectionSettings['dbname']);
		$connection = \Doctrine\DBAL\DriverManager::getConnection($connectionSettings);
		$databaseName = $connection->getSchemaManager()->getDatabasePlatform()->quoteIdentifier($databaseName);
		$connection->getSchemaManager()->createDatabase($databaseName);
		$connection->close();
	}
}