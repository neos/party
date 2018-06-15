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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Security\Account;

/**
 * A party
 *
 * @Flow\Entity
 * @ORM\InheritanceType("JOINED")
 */
abstract class AbstractParty
{
    /**
     * A unidirectional OneToMany association (done with ManyToMany and a unique constraint) to accounts. This is
     * required to not have any dependencies from Account to AbstractParty (the other way round).
     *
     * @var Collection<\TYPO3\Flow\Security\Account>
     * @ORM\ManyToMany
     * @ORM\JoinTable(inverseJoinColumns={@ORM\JoinColumn(unique=true, onDelete="CASCADE")})
     */
    protected $accounts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->accounts = new ArrayCollection();
    }

    /**
     * Assigns the given account to this party.
     *
     * @param Account $account The account
     * @return void
     */
    public function addAccount(Account $account)
    {
        $this->accounts->add($account);
    }

    /**
     * Remove an account from this party
     *
     * @param Account $account The account to remove
     * @return void
     */
    public function removeAccount(Account $account)
    {
        $this->accounts->removeElement($account);
    }

    /**
     * Returns the accounts of this party
     *
     * @return Collection<Account>|Account[] All assigned TYPO3\Flow\Security\Account objects
     */
    public function getAccounts()
    {
        return $this->accounts;
    }
}
