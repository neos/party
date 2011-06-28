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
 * A party
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @scope prototype
 * @entity
 * @InheritanceType("JOINED")
 */
abstract class AbstractParty {

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection<\TYPO3\FLOW3\Security\Account>
	 * @OneToMany(mappedBy="party")
	 */
	protected $accounts;

	/**
	 * Constructor
	 *
	 * @return void
	 * @author Andreas Förthner <andreas.foerthner@netlogix.de>
	 */
	public function __construct() {
		$this->accounts = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * Assigns the given account to this party. Note: The internal reference of the account is
	 * set to this party.
	 *
	 * @param \TYPO3\FLOW3\Security\Account $account The account
	 * @return void
	 * @author Andreas Förthner <andreas.foerthner@netlogix.de>
	 */
	public function addAccount(\TYPO3\FLOW3\Security\Account $account) {
		$this->accounts->add($account);
		$account->setParty($this);
	}

	/**
	 * Remove an account from this party
	 *
	 * @param \TYPO3\FLOW3\Security\Account $account The account to remove
	 * @return void
	 * @author Andreas Förthner <andreas.foerthner@netlogix.de>
	 */
	public function removeAccount(\TYPO3\FLOW3\Security\Account $account) {
		$this->accounts->removeElement($account);
	}

	/**
	 * Returns the accounts of this party
	 *
	 * @return \Doctrine\Common\Collections\ArrayCollection All assigned TYPO3\FLOW3\Security\Account objects
	 * @author Andreas Förthner <andreas.foerthner@netlogix.de>
	 */
	public function getAccounts() {
		return $this->accounts;
	}

}
?>