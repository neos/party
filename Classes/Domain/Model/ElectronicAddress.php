<?php
namespace TYPO3\Party\Domain\Model;

/*                                                                        *
 * This script belongs to the FLOW3 package "Party".                      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * An electronic address
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @scope prototype
 * @entity
 */
class ElectronicAddress {

	const TYPE_AIM = 'Aim';
	const TYPE_EMAIL = 'Email';
	const TYPE_GIZMO = 'Gizmo';
	const TYPE_ICQ = 'Icq';
	const TYPE_JABBER = 'Jabber';
	const TYPE_MSN = 'Msn';
	const TYPE_SIP = 'Sip';
	const TYPE_SKYPE = 'Skype';
	const TYPE_URL = 'Url';
	const TYPE_YAHOO = 'Yahoo';

	const USAGE_HOME = 'Home';
	const USAGE_WORK = 'Work';

	/**
	 * @var string
	 * @validate StringLength(minimum = 1, maximum = 255)
	 * @identity
	 */
	protected $identifier;

	/**
	 * @var string
	 * @validate Alphanumeric, StringLength(minimum = 1, maximum = 20)
	 * @identity
	 * @column(length="20")
	 */
	protected $type;

	/**
	 * @var string
	 * @validate Alphanumeric, StringLength(minimum = 1, maximum = 20)
	 * @identity
	 * @column(name="usagetype", length="20")
	 */
	protected $usage;

	/**
	 * @var boolean
	 */
	protected $approved = FALSE;

	/**
	 * Sets the identifier (= the value) of this electronic address.
	 *
	 * Example: john@example.com
	 *
	 * @param string $identifier The identifier
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function setIdentifier($identifier) {
		$this->identifier = $identifier;
	}

	/**
	 * Returns the identifier (= the value) of this electronic address.
	 *
	 * @return string The identifier
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function getIdentifier() {
		return $this->identifier;
	}

	/**
	 * Returns the type of this electronic address
	 *
	 * @return string
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Sets the type of this electronic address
	 *
	 * @param string $type If possible, use one of the TYPE_ constants
	 * @return void
 	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * Returns the usage of this electronic address
	 *
	 * @return string
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function getUsage() {
		return $this->usage;
	}

	/**
	 * Sets the usage of this electronic address
	 *
	 * @param string $usage If possible, use one of the USAGE_ constants
	 * @return void
	 * @author Robert Lemke
	 */
	public function setUsage($usage) {
		$this->usage = $usage;
	}

	/**
	 * Sets the approved status
	 *
	 * @param boolean $approved If this address has been approved or not
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function setApproved($approved) {
		$this->approved = $approved ? TRUE : FALSE;
	}

	/**
	 * Tells if this address has been approved
	 *
	 * @return boolean TRUE if the address has been approved, otherwise FALSE
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function isApproved() {
		return $this->approved;
	}

	/**
	 * An alias for getIdentifier()
	 *
	 * @return string The identifier of this electronic address
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function  __toString() {
		return $this->identifier;
	}
}
?>