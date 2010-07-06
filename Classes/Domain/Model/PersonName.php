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
	 * @author Robert Lemke <robert@typo3.org>
	 * @var string
	 */
	public function __construct($title = '', $firstName = '', $middleName = '', $lastName = '', $otherName = '', $alias = '') {
		$this->title = $title;
		$this->firstName = $firstName;
		$this->middleName = $middleName;
		$this->lastName = $lastName;
		$this->otherName = $otherName;
		$this->alias = $alias;

		$nameParts = array(
			$this->title,
			$this->firstName,
			$this->middleName,
			$this->lastName,
			$this->otherName
		);
		$nameParts = array_map('trim', $nameParts);
		$filledNameParts = array();
		foreach($nameParts as $namePart) {
			if($namePart !== '') {
				$filledNameParts[] = $namePart;
			}
		}
		$this->fullName = implode(' ', $filledNameParts);
	}

	/**
	 * Getter for firstName
	 *
	 * @return string
	 */
	public function getFirstName() {
		return $this->firstName;
	}

	/**
	 * Getter for middleName
	 *
	 * @return string
	 */
	public function getMiddleName() {
		return $this->middleName;
	}

	/**
	 * Getter for lastName
	 *
	 * @return string
	 */
	public function getLastName() {
		return $this->lastName;
	}

	/**
	 * Getter for title
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Getter for otherName
	 *
	 * @return string
	 */
	public function getOtherName() {
		return $this->otherName;
	}

	/**
	 * Getter for alias
	 *
	 * @return string
	 */
	public function getAlias() {
		return $this->alias;
	}

	/**
	 * Returns the full name, e.g. "Mr. PhD John W. Doe"
	 *
	 * @return string The full person name
	 */
	public function getFullName() {
		return $this->fullName;
	}

	/**
	 * An alias for getFullName()
	 *
	 * @return string The full person name
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function  __toString() {
		return $this->fullName;
	}
}
?>