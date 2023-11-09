<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Infrastructure\DateTime;

final class StaticDateTime implements DateTimeFactoryInterface
{
    private \DateTimeImmutable $dateTime;

    public function __construct(\DateTimeImmutable $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public function getDateTime(): \DateTimeImmutable
    {
        return $this->dateTime;
    }
}
