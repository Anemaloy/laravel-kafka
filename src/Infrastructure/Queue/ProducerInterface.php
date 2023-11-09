<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Infrastructure\Queue;

use Anemaloy\KafkaLocator\Infrastructure\Queue\Exception\ProducerException;

interface ProducerInterface
{
    /**
     * @throws ProducerException
     */
    public function produce(string $message): void;
}
