<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Infrastructure\DateTime;

interface DateTimeFactoryInterface
{
    public function getDateTime(): \DateTimeImmutable;
}
