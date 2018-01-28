<?php

namespace OpenLoyalty\Component\Customer\Tests\Domain\Command;

use Broadway\CommandHandling\Testing\CommandHandlerScenarioTestCase;
use Broadway\EventDispatcher\EventDispatcherInterface;
use Broadway\EventHandling\EventBusInterface;
use Broadway\EventStore\EventStoreInterface;
use OpenLoyalty\Bundle\AuditBundle\Service\AuditManagerInterface;
use OpenLoyalty\Component\Customer\Domain\Command\CustomerCommandHandler;
use OpenLoyalty\Component\Customer\Domain\CustomerRepository;
use OpenLoyalty\Component\Customer\Domain\Validator\CustomerUniqueValidator;

/**
 * Class CustomerCommandHandlerTest.
 */
abstract class CustomerCommandHandlerTest extends CommandHandlerScenarioTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createCommandHandler(EventStoreInterface $eventStore, EventBusInterface $eventBus, AuditManagerInterface $auditManager = null)
    {
        $eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $eventDispatcher->method('dispatch')->with($this->isType('string'))->willReturn(true);

        if (null === $auditManager) {
            $auditManager = $this->getMockBuilder(AuditManagerInterface::class)->getMock();
        }

        return $this->getCustomerCommandHandler($eventStore, $eventBus, $eventDispatcher, $auditManager);
    }

    public static function getCustomerData()
    {
        return [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'gender' => 'male',
            'email' => 'customer@open.com',
            'birthDate' => 653011200,
            'phone' => '123',
            'createdAt' => 1470646394,
            'loyaltyCardNumber' => '000000',
            'updatedAt' => 1470646394,
            'agreement1' => true,
            'company' => [
                'name' => 'test',
                'nip' => 'nip',
            ],
            'address' => [
                'street' => 'Dmowskiego',
                'address1' => '21',
                'city' => 'Wrocław',
                'country' => 'PL',
                'postal' => '50-300',
                'province' => 'Dolnośląskie',
            ],
            'status' => [
                'type' => 'new',
            ],
        ];
    }

    /**
     * @param EventStoreInterface      $eventStore
     * @param EventBusInterface        $eventBus
     * @param EventDispatcherInterface $eventDispatcher
     * @param AuditManagerInterface    $auditManager
     *
     * @return \OpenLoyalty\Component\Customer\Domain\Command\CustomerCommandHandler
     */
    protected function getCustomerCommandHandler(EventStoreInterface $eventStore, EventBusInterface $eventBus, EventDispatcherInterface $eventDispatcher, AuditManagerInterface $auditManager = null)
    {
        $customerDetailsRepository = $this->getMockBuilder('Broadway\ReadModel\RepositoryInterface')->getMock();
        $customerDetailsRepository->method('findBy')->willReturn([]);
        $validator = new CustomerUniqueValidator($customerDetailsRepository);

        if (null === $auditManager) {
            $auditManager = $this->getMockBuilder(AuditManagerInterface::class)->getMock();
        }

        return new CustomerCommandHandler(
            new CustomerRepository($eventStore, $eventBus),
            $validator,
            $eventDispatcher,
            $auditManager
        );
    }
}