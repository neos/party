<?php
namespace Neos\Party\Domain\Model;

/*
 * This file is part of the Neos.Party package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Security\Account;

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
     * @var Collection<\Neos\Flow\Security\Account>
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
     * @return Collection<Account>|Account[] All assigned Neos\Flow\Security\Account objects
     */
    public function getAccounts()
    {
        return $this->accounts;
    }
}
