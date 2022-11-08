<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueEntity({"user", "customer"}, ignoreNull=false)
 *
 * @Serializer\ExclusionPolicy("all")
 */
#[ORM\Table(name: 'kimai2_customers_rates')]
#[ORM\UniqueConstraint(columns: ['user_id', 'customer_id'])]
#[ORM\Entity(repositoryClass: 'App\Repository\CustomerRateRepository')]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class CustomerRate implements RateInterface
{
    use Rate;

    /**
     * @Assert\NotNull()
     */
    #[ORM\ManyToOne(targetEntity: 'App\Entity\Customer')]
    #[ORM\JoinColumn(onDelete: 'CASCADE', nullable: false)]
    private ?Customer $customer = null;

    public function setCustomer(?Customer $customer): CustomerRate
    {
        $this->customer = $customer;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function getScore(): int
    {
        return 1;
    }
}
