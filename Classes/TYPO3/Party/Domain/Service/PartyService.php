<?php
namespace TYPO3\Party\Domain\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Party".                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\PersistenceManagerInterface;
use TYPO3\Flow\Security\Account;
use TYPO3\Party\Domain\Model\AbstractParty;
use TYPO3\Party\Domain\Repository\PartyRepository;

/**
 * This is the Domain Service which acts as a helper for tasks
 * affecting entities inside the Party context.
 *
 * @Flow\Scope("singleton")
 */
class PartyService {

	/**
	 * This is a helper cache to store account identifiers and which party is assigned to which account
	 * because it might be possible that an account is assigned and fetched in the same request.
	 * @var array
	 */
	protected $accountsInPartyRuntimeCache = array();

	/**
	 * @Flow\Inject
	 * @var PartyRepository
	 */
	protected $partyRepository;

	/**
	 * @Flow\Inject
	 * @var PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * Assigns an Account to a Party
	 *
	 * @param Account $account
	 * @param AbstractParty $party
	 * @return void
	 */
	public function assignAccountToParty(Account $account, AbstractParty $party) {
		if ($party->getAccounts()->contains($account)) {
			return;
		}
		$party->addAccount($account);

		$accountIdentifier = $this->persistenceManager->getIdentifierByObject($account);
		$this->accountsInPartyRuntimeCache[$accountIdentifier] = $party;
	}

	/**
	 * Gets the Party having an Account assigned
	 *
	 * @param Account $account
	 * @return AbstractParty
	 */
	public function getAssignedPartyOfAccount(Account $account) {
		$accountIdentifier = $this->persistenceManager->getIdentifierByObject($account);
		if (!isset($this->accountsInPartyRuntimeCache[$accountIdentifier])) {
			$party = $this->partyRepository->findOneHavingAccount($account);
			$this->accountsInPartyRuntimeCache[$accountIdentifier] = $party;
		}

		return $this->accountsInPartyRuntimeCache[$accountIdentifier];
	}
}
