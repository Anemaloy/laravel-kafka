<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Infrastructure\DateTime;

final class CurrentDateTime implements DateTimeFactoryInterface
{
    public function getDateTime(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
