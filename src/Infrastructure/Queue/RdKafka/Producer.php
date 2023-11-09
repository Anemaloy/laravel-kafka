<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Infrastructure\Queue\RdKafka;

use Anemaloy\KafkaLocator\Infrastructure\Queue\ProducerInterface;

final class Producer implements ProducerInterface
{
    /**
     * @var callable
     */
    private $produceCallback;

    public function __construct(callable $produceCallback)
    {
        $this->produceCallback = $produceCallback;
    }

    /**
     * {@inheritdoc}
     */
    public function produce(string $message): void
    {
        ($this->produceCallback)($message);
    }
}
