<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Infrastructure\Queue;

use Anemaloy\KafkaLocator\Infrastructure\Queue\Exception\ConsumerException;

interface ConsumerInterface
{
    /**
     * @throws ConsumerException
     */
    public function consume(): string;
}
