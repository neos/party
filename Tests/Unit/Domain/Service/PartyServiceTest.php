<?php
namespace TYPO3\Party\Tests\Unit\Domain\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Party".                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Persistence\PersistenceManagerInterface;
use TYPO3\Flow\Security\Account;
use TYPO3\Flow\Tests\UnitTestCase;
use TYPO3\Party\Domain\Model\AbstractParty;
use TYPO3\Party\Domain\Model\Person;
use TYPO3\Party\Domain\Repository\PartyRepository;
use TYPO3\Party\Domain\Service\PartyService;

class PartyServiceTest extends UnitTestCase
{
    /**
     * @var PartyRepository
     */
    protected $mockPartyRepository;

    /**
     * @var PersistenceManagerInterface
     */
    protected $mockPersistenceManager;

    /**
     * @var PartyService
     */
    protected $partyService;

    /**
     * @var Account
     */
    protected $account;

    /**
     * @var AbstractParty
     */
    protected $party;

    protected function setUp()
    {
        $this->mockPartyRepository = $this->createMock(PartyRepository::class);
        $this->mockPersistenceManager = $this->createMock(PersistenceManagerInterface::class);

        $this->partyService = new PartyService();
        $this->inject($this->partyService, 'partyRepository', $this->mockPartyRepository);
        $this->inject($this->partyService, 'persistenceManager', $this->mockPersistenceManager);

        $this->account = new Account();
        $this->party = new Person();
    }

    /**
     * @test
     */
    public function assignAccountToPartyAddsAccount()
    {
        $this->partyService->assignAccountToParty($this->account, $this->party);

        $this->assertContains($this->account, $this->party->getAccounts());
    }

    /**
     * @test
     */
    public function assignAccountToPartyCachesAssignedParty()
    {
        $accountIdentifier = '723e3913-f803-42c8-a44c-fd7115f555c3';
        $partyIdentifier = 'f8033913-723e-42c8-a44c-fd7115f555c3';
        $this->mockPersistenceManager->expects($this->at(1))->method('getIdentifierByObject')->with($this->party)->will($this->returnValue($partyIdentifier));
        $this->mockPersistenceManager->expects($this->at(2))->method('getIdentifierByObject')->with($this->account)->will($this->returnValue($accountIdentifier));
        $this->mockPersistenceManager->expects($this->any())->method('getObjectByIdentifier')->with($partyIdentifier)->will($this->returnValue($this->party));
        $this->mockPartyRepository->expects($this->any())->method('findOneHavingAccount')->with($this->account)->will($this->returnValue($this->party));

        $this->partyService->assignAccountToParty($this->account, $this->party);

        $assignedParty = $this->partyService->getAssignedPartyOfAccount($this->account);

        $this->assertSame($this->party, $assignedParty);
    }

    /**
     * @test
     */
    public function getAssignedPartyOfAccountCachesParty()
    {
        $accountIdentifier = '723e3913-f803-42c8-a44c-fd7115f555c3';
        $partyIdentifier = 'f8033913-723e-42c8-a44c-fd7115f555c3';
        $this->mockPersistenceManager->expects($this->at(1))->method('getIdentifierByObject')->with($this->party)->will($this->returnValue($partyIdentifier));
        $this->mockPersistenceManager->expects($this->at(2))->method('getIdentifierByObject')->with($this->account)->will($this->returnValue($accountIdentifier));
        $this->mockPersistenceManager->expects($this->any())->method('getObjectByIdentifier')->with($partyIdentifier)->will($this->returnValue($this->party));
        $this->mockPartyRepository->expects($this->any())->method('findOneHavingAccount')->with($this->account)->will($this->returnValue($this->party));

        $this->party->addAccount($this->account);

        $assignedParty = $this->partyService->getAssignedPartyOfAccount($this->account);
        $this->assertSame($this->party, $assignedParty);

        $assignedParty = $this->partyService->getAssignedPartyOfAccount($this->account);
        $this->assertSame($this->party, $assignedParty);
    }
}
