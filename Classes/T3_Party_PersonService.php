<?php
declare(encoding = 'utf-8');

/*                                                                        *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 2 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        */ 

/**
 * 
 * @package		Party
 * @version 	$Id$
 * @copyright	Copyright belongs to the respective authors
 * @license		http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class T3_Party_PersonService  {

	protected $dataMapper;
	
	/**
	 * The component manager
	 *
	 * @var T3_FLOW3ComponentManagerInterface The component manager
	 */
	protected $componentManager;
	
	/**
	 * Constructor
	 *
	 * @param  T3_FLOW3_Component_ManagerInterface $componentManager: A reference to the component manager
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function __construct(T3_FLOW3_Component_ManagerInterface $componentManager, T3_DataAccess_DomainModelDataMapper $dataMapper) {
		$this->componentManager = $componentManager;
		$this->dataMapper = $dataMapper;
	}
	
	public function create() {
		return $this->componentManager->getComponent('T3_Party_Person');
	}
	
	public function find($uuid) {
		return $this->dataMapper->find('T3_Party_Person', 'uuid', $uuid);
	}

	public function findByGender($gender) {
		return $this->dataMapper->find('//content/');
	}
	
	// find(array('uuid' => array('=','64grefsft7er'))
	
	public function delete() {
		
	}
}
	
?>