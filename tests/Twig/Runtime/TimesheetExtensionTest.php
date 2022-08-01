<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Twig\Runtime;

use App\Entity\Timesheet;
use App\Entity\User;
use App\Repository\BookmarkRepository;
use App\Repository\TimesheetRepository;
use App\Timesheet\FavoriteRecordService;
use App\Twig\Runtime\TimesheetExtension;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @covers \App\Twig\Runtime\TimesheetExtension
 */
class TimesheetExtensionTest extends TestCase
{
    public function testActiveEntries()
    {
        $entries = [new Timesheet(), new Timesheet()];

        $repository = $this->createMock(TimesheetRepository::class);
        $repository->method('getActiveEntries')->willReturn($entries);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $bookmarks = $this->createMock(BookmarkRepository::class);
        $service = new FavoriteRecordService($repository, $bookmarks, $dispatcher);

        $sut = new TimesheetExtension($repository, $service);
        self::assertEquals($entries, $sut->activeEntries(new User()));
    }

    public function testRecentEntries()
    {
        $timesheet1 = $this->createMock(Timesheet::class);
        $timesheet1->method('getId')->willReturn(1);

        $timesheet2 = $this->createMock(Timesheet::class);
        $timesheet2->method('getId')->willReturn(2);

        $entries = [$timesheet1, $timesheet2];

        $repository = $this->createMock(TimesheetRepository::class);
        $repository->method('findTimesheetsById')->willReturn($entries);
        $repository->method('getRecentActivityIds')->willReturn([1, 2]);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $bookmarks = $this->createMock(BookmarkRepository::class);
        $service = new FavoriteRecordService($repository, $bookmarks, $dispatcher);

        $sut = new TimesheetExtension($repository, $service);
        $favorites = $sut->favoriteEntries(new User());
        self::assertCount(2, $favorites);
        self::assertEquals($entries[0], $favorites[0]->getTimesheet());
        self::assertFalse($favorites[0]->isFavorite());
        self::assertEquals($entries[1], $favorites[1]->getTimesheet());
        self::assertFalse($favorites[1]->isFavorite());
    }
}
