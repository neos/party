<?php
namespace TYPO3\Party\Domain\Model;

/*                                                                        *
 * This script belongs to the FLOW3 package "Party".                      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * A party
 *
 * @FLOW3\Entity
 * @ORM\InheritanceType("JOINED")
 */
abstract class AbstractParty {

	/**
	 * @var \Doctrine\Common\Collections\Collection<\TYPO3\FLOW3\Security\Account>
	 * @ORM\OneToMany(mappedBy="party")
	 */
	protected $accounts;

	/**
	 * Constructor
	 *
	 * @return void
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
	 */
	public function removeAccount(\TYPO3\FLOW3\Security\Account $account) {
		$this->accounts->removeElement($account);
	}

	/**
	 * Returns the accounts of this party
	 *
	 * @return \Doctrine\Common\Collections\Collection All assigned TYPO3\FLOW3\Security\Account objects
	 */
	public function getAccounts() {
		return $this->accounts;
	}

}
?>