<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\App;

use BEAR\Resource\ResourceObject;
use DateTimeImmutable;

class Weekday extends ResourceObject
{
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function onGet(int $year, int $month, int $day): static
    {
        $dateTime = DateTimeImmutable::createFromFormat('Y-m-d', "$year-$month-$day") ?: null;
        $weekday = $dateTime?->format('D');
        $this->body = ['weekday' => $weekday];

        return $this;
    }
}
