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
 * A person
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @scope prototype
 * @entity
 */
class Person extends \TYPO3\Party\Domain\Model\AbstractParty {

	/**
	 * @var \TYPO3\Party\Domain\Model\PersonName
	 * @OneToOne(cascade={"all"}, orphanRemoval=true)
	 * @validate NotEmpty
	 */
	protected $name;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection<\TYPO3\Party\Domain\Model\ElectronicAddress>
	 * @ManyToMany(cascade={"persist"})
	 */
	protected $electronicAddresses;

	/**
	 * @var \TYPO3\Party\Domain\Model\ElectronicAddress
	 * @ManyToOne(cascade={"persist"})
	 */
	protected $primaryElectronicAddress;

	/**
	 * Constructs this Person
	 *
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function __construct() {
		parent::__construct();
		$this->electronicAddresses = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * Sets the current name of this person
	 *
	 * @param \TYPO3\Party\Domain\Model\PersonName $name Name of this person
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function setName(\TYPO3\Party\Domain\Model\PersonName $name) {
		$this->name = $name;
	}

	/**
	 * Returns the current name of this person
	 *
	 * @return \TYPO3\Party\Domain\Model\PersonName Name of this person
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Adds the given electronic address to this person.
	 *
	 * @param \TYPO3\Party\Domain\Model\ElectronicAddress $electronicAddress The electronic address
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function addElectronicAddress(\TYPO3\Party\Domain\Model\ElectronicAddress $electronicAddress) {
		$this->electronicAddresses->add($electronicAddress);
	}

	/**
	 * Removes the given electronic address from this person.
	 *
	 * @param \TYPO3\Party\Domain\Model\ElectronicAddress $electronicAddress The electronic address
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function removeElectronicAddress(\TYPO3\Party\Domain\Model\ElectronicAddress $electronicAddress) {
		$this->electronicAddresses->removeElement($electronicAddress);
		if ($electronicAddress === $this->primaryElectronicAddress) {
			unset($this->primaryElectronicAddress);
		}
	}

	/**
	 * Returns all known electronic addresses of this person.
	 *
	 * @return \Doctrine\Common\Collections\ArrayCollection<\TYPO3\Party\Domain\Model\ElectronicAddress>
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function getElectronicAddresses() {
		return clone $this->electronicAddresses;
	}

	/**
	 * Sets (and adds if necessary) the primary electronic address of this person.
	 *
	 * @param \TYPO3\Party\Domain\Model\ElectronicAddress $electronicAddress The electronic address
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function setPrimaryElectronicAddress(\TYPO3\Party\Domain\Model\ElectronicAddress $electronicAddress) {
		$this->primaryElectronicAddress = $electronicAddress;
		$this->electronicAddresses->add($electronicAddress);
	}

	/**
	 * Returns the primary electronic address, if one has been defined.
	 *
	 * @return \TYPO3\Party\Domain\Model\ElectronicAddress The primary electronic address or NULL
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function getPrimaryElectronicAddress() {
		return $this->primaryElectronicAddress;
	}
}

?>