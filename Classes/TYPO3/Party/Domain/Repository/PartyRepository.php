<?php
namespace TYPO3\Party\Domain\Repository;

/*
 * This file is part of the TYPO3.Party package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

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
    const ENTITY_CLASSNAME = AbstractParty::class;

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
