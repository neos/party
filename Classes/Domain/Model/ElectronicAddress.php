<?php
declare(strict_types=1);

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

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Configuration\ConfigurationManager;
use Neos\Flow\Configuration\Exception\InvalidConfigurationTypeException;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;

/**
 * An electronic address
 *
 * @Flow\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 */
class ElectronicAddress
{
    /**
     * @var string[]
     * @Flow\InjectConfiguration(package="Neos.Party", path="availableElectronicAddressTypes")
     * @Flow\Transient
     */
    protected $availableElectronicAddressTypes = [];

    /**
     * @var string[]
     * @Flow\InjectConfiguration(package="Neos.Party", path="availableUsageTypes")
     * @Flow\Transient
     */
    protected $availableUsageTypes = [];

    /**
     * @var string
     * @Flow\Validate(type="NotEmpty")
     * @Flow\Validate(type="StringLength", options={ "minimum"=1, "maximum"=255 })
     */
    protected $identifier;

    /**
     * @var string
     * @Flow\Validate(type="Alphanumeric")
     * @Flow\Validate(type="NotEmpty")
     * @Flow\Validate(type="StringLength", options={ "minimum"=1, "maximum"=20 })
     * @ORM\Column(length=20)
     */
    protected $type;

    /**
     * @var string
     * @Flow\Validate(type="Alphanumeric")
     * @Flow\Validate(type="StringLength", options={ "minimum"=1, "maximum"=20 })
     * @ORM\Column(name="usagetype", length=20, nullable=true)
     */
    protected $usage;

    /**
     * @var boolean
     */
    protected $approved = false;

    /**
     * Get all electronic address types
     *
     * @return array
     */
    public function getAvailableElectronicAddressTypes(): array
    {
        return $this->availableElectronicAddressTypes;
    }

    /**
     * Get all usage types
     *
     * @return array
     */
    public function getAvailableUsageTypes()
    {
        return $this->availableUsageTypes;
    }

    /**
     * Sets the identifier (= the value) of this electronic address.
     *
     * Example: john@example.com
     *
     * @param string $identifier The identifier
     * @return void
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * Returns the identifier (= the value) of this electronic address.
     *
     * @return string The identifier
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the type of this electronic address
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the type of this electronic address
     *
     * @param string $type If possible, use one of the TYPE_ constants
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Returns the usage of this electronic address
     *
     * @return string
     */
    public function getUsage()
    {
        return $this->usage;
    }

    /**
     * Sets the usage of this electronic address
     *
     * @param string $usage If possible, use one of the USAGE_ constants
     * @return void
     */
    public function setUsage($usage)
    {
        $this->usage = $usage;
    }

    /**
     * Sets the approved status
     *
     * @param boolean $approved If this address has been approved or not
     * @return void
     */
    public function setApproved($approved)
    {
        $this->approved = $approved ? true : false;
    }

    /**
     * Tells if this address has been approved
     *
     * @return boolean TRUE if the address has been approved, otherwise FALSE
     */
    public function isApproved()
    {
        return $this->approved;
    }

    /**
     * An alias for getIdentifier()
     *
     * @return string The identifier of this electronic address
     */
    public function __toString()
    {
        return $this->identifier;
    }
}
