<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Component\Customer\Domain\ReadModel;

use Broadway\ReadModel\SerializableReadModel;
use OpenLoyalty\Component\Core\Domain\Model\Label;
use OpenLoyalty\Component\Customer\Domain\CampaignId;
use OpenLoyalty\Component\Customer\Domain\Model\Address;
use OpenLoyalty\Component\Customer\Domain\Model\CampaignPurchase;
use OpenLoyalty\Component\Customer\Domain\Model\Coupon;
use OpenLoyalty\Component\Customer\Domain\Model\Gender;
use OpenLoyalty\Component\Customer\Domain\Model\Company;
use OpenLoyalty\Component\Customer\Domain\CustomerId;
use OpenLoyalty\Component\Customer\Domain\LevelId;
use OpenLoyalty\Component\Customer\Domain\Model\Status;
use OpenLoyalty\Component\Customer\Domain\PosId;
use OpenLoyalty\Component\Customer\Domain\SellerId;
use OpenLoyalty\Component\Customer\Domain\TransactionId;
use OpenLoyalty\Component\Level\Domain\ReadModel\LevelDetails;

/**
 * Class CustomerDetails.
 */
class CustomerDetails implements SerializableReadModel
{
    /**
     * @var CustomerId
     */
    protected $customerId;

    /**
     * @var bool
     */
    protected $active = false;

    /**
     * @var PosId
     */
    protected $posId = null;

    /**
     * @var SellerId
     */
    protected $sellerId = null;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var Gender
     */
    protected $gender;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var \DateTime
     */
    protected $birthDate;

    /**
     * @var \DateTime
     */
    protected $lastLevelRecalculation;

    /**
     * @var Address
     */
    protected $address;

    /**
     * @var string
     */
    protected $loyaltyCardNumber;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $firstPurchaseAt;

    /**
     * @var LevelId
     */
    protected $levelId;

    /**
     * @var LevelId
     */
    protected $manuallyAssignedLevelId;

    /**
     * @var bool
     */
    protected $agreement1 = false;

    /**
     * @var bool
     */
    protected $agreement2 = false;

    /**
     * @var bool
     */
    protected $agreement3 = false;

    /**
     * @var Company
     */
    protected $company = null;

    /**
     * @var Status
     */
    protected $status;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var CampaignPurchase[]
     */
    protected $campaignPurchases = [];

    /**
     * @var int
     */
    protected $transactionsCount = 0;

    /**
     * @var float
     */
    protected $transactionsAmount = 0;

    /**
     * @var float
     */
    protected $transactionsAmountWithoutDeliveryCosts = 0;

    /**
     * @var float
     */
    protected $amountExcludedForLevel = 0;

    /**
     * @var float
     */
    protected $averageTransactionAmount = 0;

    /**
     * @var TransactionId[]
     */
    protected $transactionIds = [];

    /**
     * @var \DateTime
     */
    protected $lastTransactionDate;

    /**
     * @var Label[]
     */
    protected $labels = [];

    /**
     * @var LevelDetails|null
     */
    protected $level;

    /**
     * CustomerDetails constructor.
     *
     * @param CustomerId $id
     */
    public function __construct(CustomerId $id)
    {
        $this->customerId = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->customerId->__toString();
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return Label[]
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param Label[] $labels
     */
    public function setLabels(array $labels = [])
    {
        $this->labels = $labels;
    }

    /**
     * @param array $data
     *
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        $data = static::resolveOptions($data);
        $customer = new self(new CustomerId($data['id']));
        $customer->setFirstName($data['firstName']);
        $customer->setLastName($data['lastName']);
        if (isset($data['phone'])) {
            $customer->setPhone($data['phone']);
        }
        if (!empty($data['gender'])) {
            $customer->setGender(new Gender($data['gender']));
        }
        $customer->setEmail($data['email']);
        if (!empty($data['birthDate'])) {
            if ($data['birthDate'] instanceof \DateTime) {
                $birthDate = $data['birthDate'];
            } else {
                $birthDate = new \DateTime();
                $birthDate->setTimestamp($data['birthDate']);
            }
            $customer->setBirthDate($birthDate);
        }

        if (!empty($data['lastLevelRecalculation'])) {
            if ($data['lastLevelRecalculation'] instanceof \DateTime) {
                $lastLevelRecalculation = $data['lastLevelRecalculation'];
            } else {
                $lastLevelRecalculation = new \DateTime();
                $lastLevelRecalculation->setTimestamp($data['lastLevelRecalculation']);
            }
            $customer->setLastLevelRecalculation($lastLevelRecalculation);
        }

        if (isset($data['createdAt'])) {
            if ($data['createdAt'] instanceof \DateTime) {
                $createdAt = $data['createdAt'];
            } else {
                $createdAt = new \DateTime();
                $createdAt->setTimestamp($data['createdAt']);
            }
        } else {
            $createdAt = new \DateTime();
        }

        $customer->setCreatedAt($createdAt);

        if (isset($data['address'])) {
            $customer->setAddress(Address::deserialize($data['address']));
        }

        if (isset($data['status'])) {
            $customer->setStatus(Status::deserialize($data['status']));
        }

        if (isset($data['company'])) {
            $customer->setCompany(Company::deserialize($data['company']));
        }
        if (isset($data['loyaltyCardNumber'])) {
            $customer->setLoyaltyCardNumber($data['loyaltyCardNumber']);
        }
        if (isset($data['levelId']) && $data['levelId']) {
            $customer->setLevelId(new LevelId($data['levelId']));
        }

        if (isset($data['manuallyAssignedLevelId']) && $data['manuallyAssignedLevelId']) {
            $customer->setManuallyAssignedLevelId(new LevelId($data['manuallyAssignedLevelId']));
        }

        if (isset($data['posId'])) {
            $customer->posId = new PosId($data['posId']);
        }

        if (isset($data['sellerId'])) {
            $customer->sellerId = new SellerId($data['sellerId']);
        }

        if (isset($data['agreement1'])) {
            $customer->agreement1 = $data['agreement1'];
        }

        if (isset($data['agreement2'])) {
            $customer->agreement2 = $data['agreement2'];
        }

        if (isset($data['agreement3'])) {
            $customer->agreement3 = $data['agreement3'];
        }

        if (isset($data['updatedAt'])) {
            $date = new \DateTime();
            $date->setTimestamp($data['updatedAt']);
            $customer->setUpdatedAt($date);
        }
        if (isset($data['campaignPurchases'])) {
            $campaigns = array_map(function ($model) {
                return CampaignPurchase::deserialize($model);
            }, $data['campaignPurchases']);
            $customer->setCampaignPurchases($campaigns);
        }

        if (isset($data['active'])) {
            $customer->active = $data['active'];
        }

        if (isset($data['transactionsCount'])) {
            $customer->setTransactionsCount($data['transactionsCount']);
        }
        if (isset($data['transactionsAmount'])) {
            $customer->setTransactionsAmount($data['transactionsAmount']);
        }
        if (isset($data['amountExcludedForLevel'])) {
            $customer->setAmountExcludedForLevel($data['amountExcludedForLevel']);
        }
        if (isset($data['transactionsAmountWithoutDeliveryCosts'])) {
            $customer->setTransactionsAmountWithoutDeliveryCosts($data['transactionsAmountWithoutDeliveryCosts']);
        }
        if (isset($data['averageTransactionAmount'])) {
            $customer->setAverageTransactionAmount($data['averageTransactionAmount']);
        }
        if (isset($data['transactionIds'])) {
            $customer->setTransactionIds(array_map(function ($id) {
                return new TransactionId($id);
            }, $data['transactionIds']));
        }

        if (isset($data['lastTransactionDate'])) {
            $tmp = new \DateTime();
            $tmp->setTimestamp($data['lastTransactionDate']);
            $customer->setLastTransactionDate($tmp);
        }

        if (isset($data['level'])) {
            $customer->level = LevelDetails::deserialize($data['level']);
        }

        $labels = [];
        if (isset($data['labels'])) {
            foreach ($data['labels'] as $label) {
                $labels[] = Label::deserialize($label);
            }
        }
        $customer->setLabels($labels);

        return $customer;
    }

    /**
     * @return array
     */
    public function serialize(): array
    {
        $serializedCampaigns = array_map(function (CampaignPurchase $campaignPurchase) {
            return $campaignPurchase->serialize();
        }, $this->campaignPurchases);

        $labels = [];
        foreach ($this->labels as $label) {
            $labels[] = $label->serialize();
        }

        return [
            'id' => $this->getId(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'gender' => $this->getGender() ? $this->getGender()->getType() : null,
            'email' => $this->getEmail(),
            'phone' => $this->getPhone(),
            'birthDate' => $this->getBirthDate() ? $this->getBirthDate()->getTimestamp() : null,
            'lastLevelRecalculation' => $this->getLastLevelRecalculation() ? $this->getLastLevelRecalculation()->getTimestamp() : null,
            'createdAt' => $this->getCreatedAt() ? $this->getCreatedAt()->getTimestamp() : null,
            'address' => $this->getAddress() ? $this->getAddress()->serialize() : null,
            'company' => $this->getCompany() ? $this->getCompany()->serialize() : null,
            'loyaltyCardNumber' => $this->getLoyaltyCardNumber(),
            'levelId' => $this->getLevelId() ? $this->getLevelId()->__toString() : null,
            'manuallyAssignedLevelId' => $this->getManuallyAssignedLevelId() ? $this->getManuallyAssignedLevelId()->__toString() : null,
            'posId' => $this->getPosId() ? $this->getPosId()->__toString() : null,
            'sellerId' => $this->getSellerId() ? $this->getSellerId()->__toString() : null,
            'agreement1' => $this->agreement1,
            'agreement2' => $this->agreement2,
            'agreement3' => $this->agreement3,
            'updatedAt' => $this->updatedAt ? $this->updatedAt->getTimestamp() : null,
            'campaignPurchases' => $serializedCampaigns ?: [],
            'active' => $this->active,
            'status' => $this->getStatus() ? $this->getStatus()->serialize() : null,
            'transactionsCount' => $this->transactionsCount,
            'transactionsAmount' => $this->transactionsAmount,
            'transactionsAmountWithoutDeliveryCosts' => $this->transactionsAmountWithoutDeliveryCosts,
            'averageTransactionAmount' => $this->averageTransactionAmount,
            'amountExcludedForLevel' => $this->amountExcludedForLevel,
            'lastTransactionDate' => $this->lastTransactionDate ? $this->lastTransactionDate->getTimestamp() : null,
            'labels' => $labels,
            'level' => $this->getLevel() ? $this->getLevel()->serialize() : null,
            'transactionIds' => array_map(function (TransactionId $transactionId) {
                return $transactionId->__toString();
            }, $this->transactionIds),
        ];
    }

    /**
     * @return PosId
     */
    public function getPosId()
    {
        return $this->posId;
    }

    /**
     * @param PosId $posId
     */
    public function setPosId($posId)
    {
        $this->posId = $posId;
    }

    /**
     * @return SellerId
     */
    public function getSellerId()
    {
        return $this->sellerId;
    }

    /**
     * @param SellerId $sellerId
     */
    public function setSellerId($sellerId)
    {
        $this->sellerId = $sellerId;
    }

    /**
     * @return LevelId
     */
    public function getLevelId()
    {
        return $this->levelId;
    }

    /**
     * @param LevelId $levelId
     */
    public function setLevelId($levelId)
    {
        $this->levelId = $levelId;
    }

    /**
     * @return LevelId
     */
    public function getManuallyAssignedLevelId()
    {
        return $this->manuallyAssignedLevelId;
    }

    /**
     * @param LevelId $manuallyAssignedLevelId
     */
    public function setManuallyAssignedLevelId($manuallyAssignedLevelId)
    {
        $this->manuallyAssignedLevelId = $manuallyAssignedLevelId;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return Gender
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param Gender $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * @param \DateTime $birthDate
     */
    public function setBirthDate(\DateTime $birthDate)
    {
        $this->birthDate = $birthDate;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param Address $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getLoyaltyCardNumber()
    {
        return $this->loyaltyCardNumber;
    }

    /**
     * @param string $loyaltyCardNumber
     */
    public function setLoyaltyCardNumber($loyaltyCardNumber)
    {
        $this->loyaltyCardNumber = $loyaltyCardNumber;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getFirstPurchaseAt()
    {
        return $this->firstPurchaseAt;
    }

    /**
     * @param \DateTime $firstPurchaseAt
     */
    public function setFirstPurchaseAt($firstPurchaseAt)
    {
        $this->firstPurchaseAt = $firstPurchaseAt;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param Company $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return bool
     */
    public function isAgreement1()
    {
        return $this->agreement1;
    }

    /**
     * @param bool $agreement1
     */
    public function setAgreement1($agreement1)
    {
        $this->agreement1 = $agreement1;
    }

    /**
     * @return bool
     */
    public function isAgreement2()
    {
        return $this->agreement2;
    }

    /**
     * @param bool $agreement2
     */
    public function setAgreement2($agreement2)
    {
        $this->agreement2 = $agreement2;
    }

    /**
     * @return bool
     */
    public function isAgreement3()
    {
        return $this->agreement3;
    }

    /**
     * @param bool $agreement3
     */
    public function setAgreement3($agreement3)
    {
        $this->agreement3 = $agreement3;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return Status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param Status $status
     */
    public function setStatus(Status $status)
    {
        $this->status = $status;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return \OpenLoyalty\Component\Customer\Domain\Model\CampaignPurchase[]
     */
    public function getCampaignPurchases()
    {
        return $this->campaignPurchases;
    }

    /**
     * @param \OpenLoyalty\Component\Customer\Domain\Model\CampaignPurchase[] $campaignPurchases
     */
    public function setCampaignPurchases($campaignPurchases)
    {
        $this->campaignPurchases = $campaignPurchases;
    }

    public function addCampaignPurchase(CampaignPurchase $campaignPurchase)
    {
        $this->campaignPurchases[] = $campaignPurchase;
    }

    /**
     * @param CampaignId $campaignId
     * @param Coupon     $coupon
     *
     * @return bool
     */
    public function canUsePurchase(CampaignId $campaignId, Coupon $coupon): bool
    {
        /** @var CampaignPurchase $purchase */
        foreach ($this->getPurchasesByCampaignId($campaignId) as $purchase) {
            if ($purchase->getCoupon()->getCode() == $coupon->getCode()) {
                return $purchase->canBeUsed();
            }
        }

        return false;
    }

    public function getPurchasesByCampaignId(CampaignId $campaignId)
    {
        $tmp = [];
        foreach ($this->campaignPurchases as $campaignPurchase) {
            if ($campaignPurchase->getCampaignId()->__toString() == $campaignId->__toString()) {
                $tmp[] = $campaignPurchase;
            }
        }

        return $tmp;
    }

    public function hasTransactionId(TransactionId $transactionId)
    {
        foreach ($this->transactionIds as $id) {
            if ($id->__toString() == $transactionId->__toString()) {
                return true;
            }
        }

        return false;
    }

    public function addTransactionId(TransactionId $transactionId)
    {
        $this->transactionIds[] = $transactionId;
    }

    /**
     * @return int
     */
    public function getTransactionsCount()
    {
        return $this->transactionsCount;
    }

    /**
     * @param int $transactionsCount
     */
    public function setTransactionsCount($transactionsCount)
    {
        $this->transactionsCount = $transactionsCount;
    }

    /**
     * @return float
     */
    public function getTransactionsAmount()
    {
        return $this->transactionsAmount;
    }

    /**
     * @param float $transactionsAmount
     */
    public function setTransactionsAmount($transactionsAmount)
    {
        $this->transactionsAmount = $transactionsAmount;
    }

    /**
     * @return float
     */
    public function getTransactionsAmountWithoutDeliveryCosts()
    {
        return $this->transactionsAmountWithoutDeliveryCosts;
    }

    /**
     * @param float $transactionsAmountWithoutDeliveryCosts
     */
    public function setTransactionsAmountWithoutDeliveryCosts($transactionsAmountWithoutDeliveryCosts)
    {
        $this->transactionsAmountWithoutDeliveryCosts = $transactionsAmountWithoutDeliveryCosts;
    }

    /**
     * @return float
     */
    public function getAverageTransactionAmount()
    {
        return $this->averageTransactionAmount;
    }

    /**
     * @param float $averageTransactionAmount
     */
    public function setAverageTransactionAmount($averageTransactionAmount)
    {
        $this->averageTransactionAmount = $averageTransactionAmount;
    }

    /**
     * @return \OpenLoyalty\Component\Customer\Domain\TransactionId[]
     */
    public function getTransactionIds()
    {
        return $this->transactionIds;
    }

    /**
     * @param \OpenLoyalty\Component\Customer\Domain\TransactionId[] $transactionIds
     */
    public function setTransactionIds($transactionIds)
    {
        $this->transactionIds = $transactionIds;
    }

    /**
     * @return \DateTime
     */
    public function getLastTransactionDate()
    {
        return $this->lastTransactionDate;
    }

    /**
     * @param \DateTime $lastTransactionDate
     */
    public function setLastTransactionDate($lastTransactionDate)
    {
        $this->lastTransactionDate = $lastTransactionDate;
    }

    /**
     * @return float
     */
    public function getAmountExcludedForLevel()
    {
        return $this->amountExcludedForLevel;
    }

    /**
     * @param float $amountExcludedForLevel
     */
    public function setAmountExcludedForLevel($amountExcludedForLevel)
    {
        $this->amountExcludedForLevel = $amountExcludedForLevel;
    }

    /**
     * @return LevelDetails|null
     */
    public function getLevel(): ?LevelDetails
    {
        return $this->level;
    }

    /**
     * @param LevelDetails|null $level
     */
    public function setLevel(?LevelDetails $level)
    {
        $this->level = $level;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastLevelRecalculation(): ?\DateTime
    {
        return $this->lastLevelRecalculation;
    }

    /**
     * @param \DateTime|null $lastLevelRecalculation
     */
    public function setLastLevelRecalculation(\DateTime $lastLevelRecalculation = null): void
    {
        $this->lastLevelRecalculation = $lastLevelRecalculation;
    }

    public static function resolveOptions($data)
    {
        $defaults = [
            'firstName' => null,
            'lastName' => null,
            'address' => null,
            'status' => null,
            'gender' => null,
            'birthDate' => null,
            'company' => null,
            'loyaltyCardNumber' => null,
            'agreement1' => false,
            'agreement2' => false,
            'agreement3' => false,
        ];

        return array_merge($defaults, $data);
    }
}
