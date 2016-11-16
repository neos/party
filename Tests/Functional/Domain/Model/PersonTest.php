<?php
namespace TYPO3\Party\Tests\Functional\Domain\Model;

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
use TYPO3\Flow\Security\AccountFactory;
use TYPO3\Flow\Security\AccountRepository;
use TYPO3\Flow\Tests\FunctionalTestCase;
use TYPO3\Party\Domain\Repository\PartyRepository;
use TYPO3\Party\Domain\Model\ElectronicAddress;
use TYPO3\Party\Domain\Model\Person;
use TYPO3\Party\Domain\Model\PersonName;

class PersonTest extends FunctionalTestCase {

	/**
	* @var boolean
	*/
	static protected $testablePersistenceEnabled = TRUE;

	/**
	* @var PartyRepository
	*/
	protected $partyRepository;

	/**
	* @var AccountRepository
	*/
	protected $accountRepository;

	/**
	* @var AccountFactory
	*/
	protected $accountFactory;

	/**
	*/
	public function setUp() {
		parent::setUp();
		$this->partyRepository = $this->objectManager->get(PartyRepository::class);
		$this->accountRepository = $this->objectManager->get(AccountRepository::class);
		$this->accountFactory = $this->objectManager->get(AccountFactory::class);
	}

	/**
	* @return array Signature: firstName, middleName, lastName, emailAddress
	*/
	public function personsDataProvider() {
		return array(
			array('Catalina', 'G.', 'Dalrymple', 'CatalinaGDalrymple@teleworm.us'),
			array('Deanna', 'R.', 'Snead', 'dsnead@teleworm.us'),
			array('Donald', 'E.', 'Maus', 'donaldmaus@example.org'),
		);
	}

	/**
	* @dataProvider personsDataProvider
	* @test
	*/
	public function personsAndAccountPersistingAndRetrievingWorksCorrectly($firstName, $middleName, $lastName, $emailAddress) {
		$person = new Person();
		$person->setName(new PersonName('', $firstName, $middleName, $lastName));

		$electronicAddress = new ElectronicAddress();
		$electronicAddress->setType(ElectronicAddress::TYPE_EMAIL);
		$electronicAddress->setIdentifier($emailAddress);
		$person->setPrimaryElectronicAddress($electronicAddress);

		$account = $this->accountFactory->createAccountWithPassword($emailAddress, $this->persistenceManager->getIdentifierByObject($person));
		$this->accountRepository->add($account);
		$person->addAccount($account);

		$this->partyRepository->add($person);
		$this->persistenceManager->persistAll();
		$this->assertEquals(1, $this->partyRepository->countAll());

		$this->persistenceManager->clearState();
		$foundPerson = $this->partyRepository->findByIdentifier($this->persistenceManager->getIdentifierByObject($person));

		$this->assertEquals($foundPerson->getName()->getFullName(), $person->getName()->getFullName());
		$this->assertEquals($foundPerson->getName()->getFullName(), $firstName . ' ' . $middleName . ' ' . $lastName);
		$this->assertEquals($foundPerson->getPrimaryElectronicAddress()->getIdentifier(), $emailAddress);
	}
}
