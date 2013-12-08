<?php
namespace TYPO3\Setup\ViewHelpers\Widget;

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

/**
 * Simple widget that checks given database credentials and returns a list of available database names via AJAX
 */
class DatabaseSelectorViewHelper extends \TYPO3\Fluid\Core\Widget\AbstractWidgetViewHelper {

	/**
	 * @var boolean
	 */
	protected $ajaxWidget = TRUE;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Setup\ViewHelpers\Widget\Controller\DatabaseSelectorController
	 */
	protected $controller;

	/**
	 * Don't create a session for this widget
	 * Note: You then need to manually add the serialized configuration data to your links, by
	 * setting "includeWidgetContext" to TRUE in the widget link and URI ViewHelpers!
	 *
	 * @var boolean
	 */
	protected $storeConfigurationInSession = FALSE;

	/**
	 *
	 * @param string $driverDropdownFieldId id of the DB driver input field
	 * @param string $userFieldId id of the DB username input field
	 * @param string $passwordFieldId id of the DB password input field
	 * @param string $hostFieldId id of the DB host input field
	 * @param string $dbNameTextFieldId id of the input field for the db name (fallback)
	 * @param string $dbNameDropdownFieldId id of the select field for the fetched db names (this is hidden by default)
	 * @param string $statusContainerId id of the element displaying AJAX status (gets class "loading", "success" or "error" depending on the state)
	 * @param string $metadataStatusContainerId id of the element displaying status information of the selected database (gets class "loading", "success" or "error" depending on the state)
	 * @return string
	 */
	public function render($driverDropdownFieldId, $userFieldId, $passwordFieldId, $hostFieldId, $dbNameTextFieldId, $dbNameDropdownFieldId, $statusContainerId, $metadataStatusContainerId) {
		return $this->initiateSubRequest();
	}
}
