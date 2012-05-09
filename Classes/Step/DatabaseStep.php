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
	 * @var \TYPO3\FLOW3\Persistence\Doctrine\Service
	 * @FLOW3\Inject
	 */
	protected $doctrineService;

	/**
	 * Returns the form definitions for the step
	 *
	 * @param \TYPO3\Form\Core\Model\FormDefinition $formDefinition
	 * @return void
	 */
	protected function buildForm(\TYPO3\Form\Core\Model\FormDefinition $formDefinition) {
		$page1 = $formDefinition->createPage('page1');

		$introduction = $page1->createElement('introduction', 'TYPO3.Form:StaticText');
		$introduction->setProperty('text', 'Please enter Database details below:');

		$connectionSection = $page1->createElement('connectionSection', 'TYPO3.Form:Section');
		$connectionSection->setLabel('Connection');

		$databaseUser = $connectionSection->createElement('user', 'TYPO3.Form:SingleLineText');
		$databaseUser->setLabel('DB Username');
		$databaseUser->setDefaultValue(\TYPO3\FLOW3\Utility\Arrays::getValueByPath($this->distributionSettings, 'TYPO3.FLOW3.persistence.backendOptions.user'));
		$databaseUser->addValidator(new \TYPO3\FLOW3\Validation\Validator\NotEmptyValidator());

		$databasePassword = $connectionSection->createElement('password', 'TYPO3.Form:Password');
		$databasePassword->setLabel('DB Password');
		$databasePassword->setDefaultValue(\TYPO3\FLOW3\Utility\Arrays::getValueByPath($this->distributionSettings, 'TYPO3.FLOW3.persistence.backendOptions.password'));

		$databaseConnectionCondition = new \TYPO3\Setup\Condition\DatabaseConnectionCondition();
		$databaseConnection = $page1->createElement('databaseConnection', 'TYPO3.Form:StaticText');
		if ($databaseConnectionCondition->isMet()) {
			$databaseConnection->setProperty('text', 'Database connection established');
			$databaseConnection->setProperty('class', 'alert alert-success');
		} else {
			$databaseConnection->setProperty('text', 'Database connection not established');
			$databaseConnection->setProperty('class', 'alert alert-error');
		}

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

		$dbName = \TYPO3\FLOW3\Utility\Arrays::getValueByPath($this->distributionSettings, 'TYPO3.FLOW3.persistence.backendOptions.dbname');
		if ($databaseConnectionCondition->isMet()) {
			$settings = $this->configurationManager->getConfiguration(\TYPO3\FLOW3\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.FLOW3');
			$connection = \Doctrine\DBAL\DriverManager::getConnection($settings['persistence']['backendOptions']);
			$databases = $connection->getSchemaManager()->listDatabases();
			$databaseName = $page1->createElement('dbname', 'TYPO3.Form:SingleSelectDropdown');
			$databaseName->setProperty('options', array_combine($databases, $databases));
			$databaseName->setDefaultValue($dbName);
		} else {
			$databaseName = $databaseSection->createElement('dbname', 'TYPO3.Form:SingleLineText');
			$databaseName->setDefaultValue($dbName);
		}
		$databaseName->setLabel('DB Name');
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

		$databaseConnectionCondition = new \TYPO3\Setup\Condition\DatabaseConnectionCondition();
		if ($databaseConnectionCondition->isMet()) {
			$this->doctrineService->executeMigrations();
		}
	}
}