<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Infrastructure\Queue;

interface ConsumerProviderInterface
{
    /**
     * @param string[] $eventNames
     */
    public function getConsumer(array $eventNames): ConsumerInterface;
}
