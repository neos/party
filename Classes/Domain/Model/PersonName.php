<?php
declare(ENCODING = 'utf-8');
namespace F3\Party\Domain\Model;

/*                                                                        *
 * This script belongs to the FLOW3 framework.                            *
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
 * A person name
 *
 * @version $Id$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 * @scope prototype
 * @valueobject
 */
class PersonName {

	/**
	 * @var string
	 */
	protected $title;

	/**
	 *
	 * @var string
	 */
	protected $firstName;

	/**
	 *
	 * @var string
	 */
	protected $middleName;

	/**
	 *
	 * @var string
	 */
	protected $lastName;

	/**
	 *
	 * @var string
	 */
	protected $otherName;

	/**
	 *
	 * @var string
	 */
	protected $alias;

	/**
	 * Constructor
	 *
	 * @param string $firstName The most important name element by which this particular individual is identified in the group. E.g. John, Sam, Brian for Anglo-Saxon cultures.
	 * @param string $lastName Name element that identifies the group the individual belongs to and is identified by, such as Last Name, Surname, Family Name, etc.
	 * @param string $middleName Name elements related to additional identification of the individual, such as names are parents or places.
	 * @param string $otherName Any other additional names that are not directly used to identify or call the individual, such as names of ancestors, saints, etc.
	 * @param string $title Some sort of status, such as Mr, Miss, Ms (marriage status), or education such as Professor, PhD, Dr, etc.
	 * @param string $alias A simple nick name that is commonly used as part of the name. E.g. a fancy kick-boxer can be commonly known as Bill "Storm" Bababoons, where "Storm" is obviously an alias.
	 */

	public function setTitle($title) {
		$this->title = $title;
	}

	public function setFirstName($firstName) {
		$this->firstName = $firstName;
	}

	public function setMiddleName($middleName) {
		$this->middleName = $middleName;
	}

	public function setLastName($lastName) {
		$this->lastName = $lastName;
	}

	public function setOtherName($otherName) {
		$this->otherName = $otherName;
	}

	public function setAlias($alias) {
		$this->alias = $alias;
	}


	public function getFirstName() {
		return $this->firstName;
	}

	public function getMiddleName() {
		return $this->middleName;
	}

	public function getLastName() {
		return $this->lastName;
	}

	public function getTitle() {
		return $this->title;
	}

	public function getOtherName() {
		return $this->otherName;
	}

	public function getAlias() {
		return $this->alias;
	}

	/**
	 * Returns the full name, e.g. "Mr. PhD John W. Doe"
	 *
	 * @return string The full person name
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function getFullName() {
		return ($this->title !== NULL ? $this->title . ' ' : '') .
			$this->firstName . ' ' . ($this->middleName !== NULL ? ' ' . $this->middleName : '') . $this->lastName .
			($this->otherName !== NULL ? ' ' . $this->otherName : '');
	}

	/**
	 * An alias for getFullName()
	 *
	 * @return string The full person name
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function  __toString() {
		return $this->getFullName();
	}
}
?>