<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\API\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("none")
 */
final class TimesheetConfig
{
    /**
     * The time-tracking mode, see also: https://www.kimai.org/documentation/timesheet.html#tracking-modes
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"Default"})
     * @Serializer\Type(name="string")
     * @phpstan-ignore-next-line
     */
    private string $trackingMode = 'default';
    /**
     * Default begin datetime in PHP format
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"Default"})
     * @Serializer\Type(name="string")
     * @phpstan-ignore-next-line
     */
    private string $defaultBeginTime = 'now';
    /**
     * How many running timesheets a user is allowed to have at the same time
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"Default"})
     * @Serializer\Type(name="integer")
     * @phpstan-ignore-next-line
     */
    private int $activeEntriesHardLimit = 1;
    /**
     * Whether entries for future times are allowed
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"Default"})
     * @Serializer\Type(name="boolean")
     * @phpstan-ignore-next-line
     */
    private bool $isAllowFutureTimes = true;
    /**
     * Whether overlapping entries are allowed
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"Default"})
     * @Serializer\Type(name="boolean")
     * @phpstan-ignore-next-line
     */
    private bool $isAllowOverlapping = true;

    public function setTrackingMode(string $trackingMode): void
    {
        $this->trackingMode = $trackingMode;
    }

    public function setDefaultBeginTime(string $defaultBeginTime): void
    {
        $this->defaultBeginTime = $defaultBeginTime;
    }

    public function setActiveEntriesHardLimit(int $activeEntriesHardLimit): void
    {
        $this->activeEntriesHardLimit = $activeEntriesHardLimit;
    }

    public function setIsAllowFutureTimes(bool $isAllowFutureTimes): void
    {
        $this->isAllowFutureTimes = $isAllowFutureTimes;
    }

    public function setIsAllowOverlapping(bool $isAllowOverlapping): void
    {
        $this->isAllowOverlapping = $isAllowOverlapping;
    }
}
