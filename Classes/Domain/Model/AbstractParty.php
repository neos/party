<?php
declare(ENCODING = 'utf-8');
namespace F3\Party\Domain\Model;

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
 */
abstract class AbstractParty {

	/**
	 * An entity must have identity, but we cannot know what the identity of
	 * concrete subclasses will be, thus we introduce something artificial.
	 *
	 * @var string
	 * @identity
	 */
	protected $artificialIdentity;

	/**
	 * @var \SplObjectStorage<\F3\FLOW3\Security\Account>
	 */
	protected $accounts;

	/**
	 * Constructor
	 *
	 * @return void
	 * @author Andreas Förthner <andreas.foerthner@netlogix.de>
	 */
	public function __construct() {
		$this->artificialIdentity = \F3\FLOW3\Utility\Algorithms::generateUUID();
		$this->accounts = new \SplObjectStorage();
	}

	/**
	 * Assigns the given account to this party. Note: The internal reference of the account is
	 * set to this party.
	 *
	 * @return F3\FLOW3\Security\Account $account The account
	 * @author Andreas Förthner <andreas.foerthner@netlogix.de>
	 */
	public function addAccount(\F3\FLOW3\Security\Account $account) {
		$this->accounts->attach($account);
		$account->setParty($this);
	}

	/**
	 * Remove an account from this party
	 *
	 * @param F3\FLOW3\Security\Account $account The account to remove
	 * @return void
	 * @author Andreas Förthner <andreas.foerthner@netlogix.de>
	 */
	public function removeAccount(\F3\FLOW3\Security\Account $account) {
		$this->accounts->detach($account);
	}

	/**
	 * Returns the accounts of this party
	 *
	 * @return SplObjectStorage All assigned F3\FLOW3\Security\Account objects
	 * @author Andreas Förthner <andreas.foerthner@netlogix.de>
	 */
	public function getAccounts() {
		return $this->accounts;
	}

}
?>