<?php
namespace Neos\Party\Tests\Functional\Domain\Model;

/*
 * This file is part of the Neos.Party package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Security\AccountFactory;
use Neos\Flow\Security\AccountRepository;
use Neos\Flow\Tests\FunctionalTestCase;
use Neos\Party\Domain\Model\ElectronicAddress;
use Neos\Party\Domain\Model\Person;
use Neos\Party\Domain\Model\PersonName;
use Neos\Party\Domain\Repository\PartyRepository;

class PersonTest extends FunctionalTestCase
{
    /**
     * @var boolean
     */
    protected static $testablePersistenceEnabled = true;

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
    public function setUp()
    {
        parent::setUp();
        $this->partyRepository = $this->objectManager->get(PartyRepository::class);
        $this->accountRepository = $this->objectManager->get(AccountRepository::class);
        $this->accountFactory = $this->objectManager->get(AccountFactory::class);
    }

    /**
     * @return array Signature: firstName, middleName, lastName, emailAddress
     */
    public function personsDataProvider()
    {
        return [
            ['Catalina', 'G.', 'Dalrymple', 'CatalinaGDalrymple@teleworm.us'],
            ['Deanna', 'R.', 'Snead', 'dsnead@teleworm.us'],
            ['Donald', 'E.', 'Maus', 'donaldmaus@example.org'],
        ];
    }

    /**
     * @dataProvider personsDataProvider
     * @test
     */
    public function personsAndAccountPersistingAndRetrievingWorksCorrectly($firstName, $middleName, $lastName, $emailAddress)
    {
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
