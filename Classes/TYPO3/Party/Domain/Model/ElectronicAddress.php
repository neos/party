<?php
namespace TYPO3\Party\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Party".                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;

/**
 * An electronic address
 *
 * @Flow\Entity
 */
class ElectronicAddress {

	const TYPE_AIM = 'Aim';
	const TYPE_EMAIL = 'Email';
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
	 * @Flow\Validate(type="StringLength", options={ "minimum"=1, "maximum"=255 })
	 */
	protected $identifier;

	/**
	 * @var string
	 * @Flow\Validate(type="Alphanumeric")
	 * @Flow\Validate(type="StringLength", options={ "minimum"=1, "maximum"=20 })
	 * @ORM\Column(length=20)
	 */
	protected $type;

	/**
	 * @var string
	 * @Flow\Validate(type="Alphanumeric")
	 * @Flow\Validate(type="StringLength", options={ "minimum"=1, "maximum"=20 })
	 * @ORM\Column(name="usagetype", length=20, nullable=true)
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
	 */
	public function setIdentifier($identifier) {
		$this->identifier = $identifier;
	}

	/**
	 * Returns the identifier (= the value) of this electronic address.
	 *
	 * @return string The identifier
	 */
	public function getIdentifier() {
		return $this->identifier;
	}

	/**
	 * Returns the type of this electronic address
	 *
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Sets the type of this electronic address
	 *
	 * @param string $type If possible, use one of the TYPE_ constants
	 * @return void
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * Returns the usage of this electronic address
	 *
	 * @return string
	 */
	public function getUsage() {
		return $this->usage;
	}

	/**
	 * Sets the usage of this electronic address
	 *
	 * @param string $usage If possible, use one of the USAGE_ constants
	 * @return void
	 */
	public function setUsage($usage) {
		$this->usage = $usage;
	}

	/**
	 * Sets the approved status
	 *
	 * @param boolean $approved If this address has been approved or not
	 * @return void
	 */
	public function setApproved($approved) {
		$this->approved = $approved ? TRUE : FALSE;
	}

	/**
	 * Tells if this address has been approved
	 *
	 * @return boolean TRUE if the address has been approved, otherwise FALSE
	 */
	public function isApproved() {
		return $this->approved;
	}

	/**
	 * An alias for getIdentifier()
	 *
	 * @return string The identifier of this electronic address
	 */
	public function  __toString() {
		return $this->identifier;
	}
}
?>