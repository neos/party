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

/**
 * A person
 *
 * @Flow\Entity
 */
class Person extends AbstractParty
{
    /**
     * @var PersonName
     * @ORM\OneToOne
     * @Flow\Validate(type="NotEmpty")
     */
    protected $name;

    /**
     * @var Collection<\Neos\Party\Domain\Model\ElectronicAddress>
     * @ORM\ManyToMany
     */
    protected $electronicAddresses;

    /**
     * @var ElectronicAddress
     * @ORM\ManyToOne
     */
    protected $primaryElectronicAddress;

    /**
     * Constructs this Person
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->electronicAddresses = new ArrayCollection();
    }

    /**
     * Sets the current name of this person
     *
     * @param PersonName $name Name of this person
     * @return void
     */
    public function setName(PersonName $name)
    {
        $this->name = $name;
    }

    /**
     * Returns the current name of this person
     *
     * @return PersonName Name of this person
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Adds the given electronic address to this person.
     *
     * @param ElectronicAddress $electronicAddress The electronic address
     * @return void
     */
    public function addElectronicAddress(ElectronicAddress $electronicAddress)
    {
        $this->electronicAddresses->add($electronicAddress);
    }

    /**
     * Removes the given electronic address from this person.
     *
     * @param ElectronicAddress $electronicAddress The electronic address
     * @return void
     */
    public function removeElectronicAddress(ElectronicAddress $electronicAddress)
    {
        $this->electronicAddresses->removeElement($electronicAddress);
        if ($electronicAddress === $this->primaryElectronicAddress) {
            $this->primaryElectronicAddress = null;
        }
    }

    /**
     * Sets the electronic addresses of this person.
     *
     * @param \Doctrine\Common\Collections\Collection<\Neos\Party\Domain\Model\ElectronicAddress> $electronicAddresses
     * @return void
     */
    public function setElectronicAddresses(Collection $electronicAddresses)
    {
        if ($this->primaryElectronicAddress !== null && !$this->electronicAddresses->contains($this->primaryElectronicAddress)) {
            $this->primaryElectronicAddress = null;
        }
        $this->electronicAddresses = $electronicAddresses;
    }

    /**
     * Returns all known electronic addresses of this person.
     *
     * @return Collection<\Neos\Party\Domain\Model\ElectronicAddress>
     */
    public function getElectronicAddresses()
    {
        return clone $this->electronicAddresses;
    }

    /**
     * Sets (and adds if necessary) the primary electronic address of this person.
     *
     * @param ElectronicAddress $electronicAddress The electronic address
     * @return void
     */
    public function setPrimaryElectronicAddress(ElectronicAddress $electronicAddress)
    {
        $this->primaryElectronicAddress = $electronicAddress;
        if (!$this->electronicAddresses->contains($electronicAddress)) {
            $this->electronicAddresses->add($electronicAddress);
        }
    }

    /**
     * Returns the primary electronic address, if one has been defined.
     *
     * @return ElectronicAddress The primary electronic address or NULL
     */
    public function getPrimaryElectronicAddress()
    {
        return $this->primaryElectronicAddress;
    }
}
