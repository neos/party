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

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

/**
 * A person name
 *
 * @Flow\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 */
class PersonName
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $middleName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $otherName;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @var string
     */
    protected $fullName;

    /**
     * Constructs this person name
     *
     * @param string $title the title, e.g. "Mr." or "Mr. Phd"
     * @param string $firstName the first name
     * @param string $middleName the middle name
     * @param string $lastName the last name
     * @param string $otherName the "other" name, e.g. "IV." or "jr."
     * @param string $alias an alias or nickname
     * @var string
     */
    public function __construct($title = '', $firstName = '', $middleName = '', $lastName = '', $otherName = '', $alias = '')
    {
        $this->title = (string)$title;
        $this->firstName = (string)$firstName;
        $this->middleName = (string)$middleName;
        $this->lastName = (string)$lastName;
        $this->otherName = (string)$otherName;
        $this->alias = (string)$alias;

        $this->generateFullName();
    }

    /**
     * @return void
     */
    protected function generateFullName()
    {
        $nameParts = [
            $this->title,
            $this->firstName,
            $this->middleName,
            $this->lastName,
            $this->otherName
        ];
        $nameParts = array_map('trim', $nameParts);
        $filledNameParts = [];
        foreach ($nameParts as $namePart) {
            if ($namePart !== '') {
                $filledNameParts[] = $namePart;
            }
        }
        $this->fullName = implode(' ', $filledNameParts);
    }

    /**
     * Setter for firstName
     *
     * @param string $firstName
     * @return void
     */
    public function setFirstName($firstName)
    {
        $this->firstName = (string)$firstName;
        $this->generateFullName();
    }

    /**
     * Setter for middleName
     *
     * @param string $middleName
     * @return void
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = (string)$middleName;
        $this->generateFullName();
    }

    /**
     * Setter for lastName
     *
     * @param string $lastName
     * @return void
     */
    public function setLastName($lastName)
    {
        $this->lastName = (string)$lastName;
        $this->generateFullName();
    }

    /**
     * Setter for title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = (string)$title;
        $this->generateFullName();
    }

    /**
     * Setter for otherName
     *
     * @param string $otherName
     * @return void
     */
    public function setOtherName($otherName)
    {
        $this->otherName = (string)$otherName;
        $this->generateFullName();
    }

    /**
     * Setter for alias
     *
     * @param string $alias
     * @return void
     */
    public function setAlias($alias)
    {
        $this->alias = (string)$alias;
    }

    /**
     * Getter for firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Getter for middleName
     *
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * Getter for lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Getter for title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Getter for otherName
     *
     * @return string
     */
    public function getOtherName()
    {
        return $this->otherName;
    }

    /**
     * Getter for alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Returns the full name, e.g. "Mr. PhD John W. Doe"
     *
     * @return string The full person name
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * An alias for getFullName()
     *
     * @return string The full person name
     */
    public function __toString()
    {
        return $this->fullName;
    }
}
