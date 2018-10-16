<?php
/**
 * Copyright © 2018 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace OpenLoyalty\Component\Account\Tests\Unit\Infrastructure\Event\Listener;

use Broadway\CommandHandling\CommandBus;
use Broadway\ReadModel\Repository;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use OpenLoyalty\Component\Account\Domain\AccountId;
use OpenLoyalty\Component\Account\Domain\Command\SpendPoints;
use OpenLoyalty\Component\Account\Domain\Model\SpendPointsTransfer;
use OpenLoyalty\Component\Account\Domain\PointsTransferId;
use OpenLoyalty\Component\Account\Domain\ReadModel\AccountDetails;
use OpenLoyalty\Component\Account\Infrastructure\Event\Listener\SpendPointsOnCampaignListener;
use OpenLoyalty\Component\Campaign\Domain\Campaign;
use OpenLoyalty\Component\Customer\Domain\CampaignId;
use OpenLoyalty\Component\Customer\Domain\CustomerId;
use OpenLoyalty\Component\Customer\Domain\Event\CampaignWasBoughtByCustomer;
use OpenLoyalty\Component\Customer\Domain\Model\Coupon;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class SpendPointsOnCampaignListenerTest.
 */
final class SpendPointsOnCampaignListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $uuid = '00000000-0000-0000-0000-000000000000';

    /**
     * @test
     */
    public function it_spend_points_when_customer_bought_campaign(): void
    {
        $listener = new SpendPointsOnCampaignListener(
            $this->getCommandBus(
                new SpendPoints(
                    new AccountId($this->uuid),
                    new SpendPointsTransfer(
                        new PointsTransferId($this->uuid),
                        10,
                        null,
                        false,
                        'test, coupon: 123'
                    )
                )
            ),
            $this->getAccountDetailsRepository(),
            $this->getUuidGenerator()
        );
        $listener->onCustomerBoughtCampaign(new CampaignWasBoughtByCustomer(
            new CustomerId($this->uuid),
            new CampaignId($this->uuid),
            'test',
            10,
            new Coupon('123'),
            Campaign::REWARD_TYPE_DISCOUNT_CODE
        ));
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|UuidGeneratorInterface
     */
    protected function getUuidGenerator(): PHPUnit_Framework_MockObject_MockObject
    {
        $mock = $this->getMockBuilder(UuidGeneratorInterface::class)->getMock();
        $mock->method('generate')->willReturn($this->uuid);

        return $mock;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Repository
     */
    protected function getAccountDetailsRepository(): PHPUnit_Framework_MockObject_MockObject
    {
        $account = $this->getMockBuilder(AccountDetails::class)->disableOriginalConstructor()->getMock();
        $account->method('getAccountId')->willReturn(new AccountId($this->uuid));

        $repo = $this->getMockBuilder(Repository::class)->getMock();
        $repo->method('findBy')->with($this->arrayHasKey('customerId'))->willReturn([$account]);

        return $repo;
    }

    /**
     * @param $expected
     *
     * @return PHPUnit_Framework_MockObject_MockObject|CommandBus
     */
    protected function getCommandBus($expected): PHPUnit_Framework_MockObject_MockObject
    {
        $mock = $this->getMockBuilder(CommandBus::class)->getMock();
        $mock->method('dispatch')->with($this->equalTo($expected));

        return $mock;
    }
}
