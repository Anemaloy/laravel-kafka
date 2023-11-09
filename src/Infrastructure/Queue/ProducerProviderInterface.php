<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Infrastructure\Queue;

use Anemaloy\KafkaLocator\Domain\EventInterface;

interface ProducerProviderInterface
{
    public function getProducer(EventInterface $event): ProducerInterface;
}
