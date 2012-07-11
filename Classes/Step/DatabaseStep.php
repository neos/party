<?php
namespace TYPO3\Setup\Step;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Setup".                *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3,
	TYPO3\Form\Core\Model\FormDefinition;

/**
 * @FLOW3\Scope("singleton")
 */
class DatabaseStep extends \TYPO3\Setup\Step\AbstractStep {

	/**
	 * @var \TYPO3\FLOW3\Configuration\Source\YamlSource
	 * @FLOW3\Inject
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
		$databaseUser->setDefaultValue(\TYPO3\FLOW3\Utility\Arrays::getValueByPath($this->distributionSettings, 'TYPO3.FLOW3.persistence.backendOptions.user'));
		$databaseUser->addValidator(new \TYPO3\FLOW3\Validation\Validator\NotEmptyValidator());

		$databasePassword = $connectionSection->createElement('password', 'TYPO3.Form:Password');
		$databasePassword->setLabel('DB Password');
		$databasePassword->setDefaultValue(\TYPO3\FLOW3\Utility\Arrays::getValueByPath($this->distributionSettings, 'TYPO3.FLOW3.persistence.backendOptions.password'));

		$databaseHost = $connectionSection->createElement('host', 'TYPO3.Form:SingleLineText');
		$databaseHost->setLabel('DB Host');
		$defaultHost = \TYPO3\FLOW3\Utility\Arrays::getValueByPath($this->distributionSettings, 'TYPO3.FLOW3.persistence.backendOptions.host');
		if ($defaultHost === NULL) {
			$defaultHost = '127.0.0.1';
		}
		$databaseHost->setDefaultValue($defaultHost);
		$databaseHost->addValidator(new \TYPO3\FLOW3\Validation\Validator\NotEmptyValidator());

		$databaseSection = $page1->createElement('databaseSection', 'TYPO3.Form:Section');
		$databaseSection->setLabel('Database');

		$databaseName = $databaseSection->createElement('dbname', 'TYPO3.Setup:DatabaseSelector');
		$databaseName->setLabel('DB Name');
		$databaseName->setProperty('userFieldId', $databaseUser->getUniqueIdentifier());
		$databaseName->setProperty('passwordFieldId', $databasePassword->getUniqueIdentifier());
		$databaseName->setProperty('hostFieldId', $databaseHost->getUniqueIdentifier());
		$databaseName->setDefaultValue(\TYPO3\FLOW3\Utility\Arrays::getValueByPath($this->distributionSettings, 'TYPO3.FLOW3.persistence.backendOptions.dbname'));
		$databaseName->addValidator(new \TYPO3\FLOW3\Validation\Validator\NotEmptyValidator());
	}

	/**
	 * This method is called when the form of this step has been submitted
	 *
	 * @param array $formValues
	 * @return void
	 */
	public function postProcessFormValues(array $formValues) {
		$this->distributionSettings = \TYPO3\FLOW3\Utility\Arrays::setValueByPath($this->distributionSettings, 'TYPO3.FLOW3.persistence.backendOptions.dbname', $formValues['dbname']);
		$this->distributionSettings = \TYPO3\FLOW3\Utility\Arrays::setValueByPath($this->distributionSettings, 'TYPO3.FLOW3.persistence.backendOptions.user', $formValues['user']);
		$this->distributionSettings = \TYPO3\FLOW3\Utility\Arrays::setValueByPath($this->distributionSettings, 'TYPO3.FLOW3.persistence.backendOptions.password', $formValues['password']);
		$this->distributionSettings = \TYPO3\FLOW3\Utility\Arrays::setValueByPath($this->distributionSettings, 'TYPO3.FLOW3.persistence.backendOptions.host', $formValues['host']);
		$this->configurationSource->save(FLOW3_PATH_CONFIGURATION . \TYPO3\FLOW3\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, $this->distributionSettings);

		$this->configurationManager->flushConfigurationCache();

		$settings = $this->configurationManager->getConfiguration(\TYPO3\FLOW3\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.FLOW3');
		$connectionSettings = $settings['persistence']['backendOptions'];
		$connectionEstablished = $this->connectToDatabase($connectionSettings);
		if ($connectionEstablished !== TRUE) {
			$this->createDatabase($connectionSettings, $formValues['dbname']);
			$connectionEstablished = $this->connectToDatabase($connectionSettings);
		}

		if ($connectionEstablished) {
			\TYPO3\FLOW3\Core\Booting\Scripts::executeCommand('typo3.flow3:doctrine:migrate', $settings, FALSE);
		}
	}

	/**
	 * Tries to connect to the database using the specified $connectionSettings
	 *
	 * @param array $connectionSettings array in the format array('user' => 'dbuser', 'password' => 'dbpassword', 'host' => 'dbhost', 'dbname' => 'dbname')
	 * @return boolean TRUE if the connection could be established, otherwise FALSE
	 */
	protected function connectToDatabase(array $connectionSettings) {
		$connection = \Doctrine\DBAL\DriverManager::getConnection($connectionSettings);
		try {
			$connection->connect();
		} catch(\PDOException $e) {
		}
		return $connection->isConnected();
	}

	/**
	 * Connects to the database using the specified $connectionSettings
	 * and tries to create a database named $databaseName.
	 *
	 * @param array $connectionSettings array in the format array('user' => 'dbuser', 'password' => 'dbpassword', 'host' => 'dbhost', 'dbname' => 'dbname')
	 * @param string $databaseName name of the database to create
	 * @return boolean TRUE if the database could be created, otherwise FALSE
	 */
	protected function createDatabase(array $connectionSettings, $databaseName) {
		unset($connectionSettings['dbname']);
		$connection = \Doctrine\DBAL\DriverManager::getConnection($connectionSettings);
		try {
			$connection->getSchemaManager()->createDatabase($databaseName);
			$connection->close();
			return TRUE;
		} catch(\PDOException $e) {
		}
		return FALSE;
	}
}