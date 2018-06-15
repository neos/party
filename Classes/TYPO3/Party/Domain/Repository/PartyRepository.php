<?php
namespace TYPO3\Party\Domain\Repository;

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
use TYPO3\Flow\Persistence\Repository;
use TYPO3\Flow\Security\Account;
use TYPO3\Party\Domain\Model\AbstractParty;

/**
 * Repository for parties
 *
 * @Flow\Scope("singleton")
 */
class PartyRepository extends Repository
{
    const ENTITY_CLASSNAME = 'TYPO3\Party\Domain\Model\AbstractParty';

    /**
     * Finds a Party instance, if any, which has the given Account attached.
     *
     * @param Account $account
     * @return AbstractParty
     */
    public function findOneHavingAccount(Account $account)
    {
        $query = $this->createQuery();

        return $query->matching($query->contains('accounts', $account))->execute()->getFirst();
    }
}
