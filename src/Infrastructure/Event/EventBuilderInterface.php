<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Infrastructure\Event;

use Anemaloy\KafkaLocator\Domain\EventInterface;
use Anemaloy\KafkaLocator\Infrastructure\Event\Exception\InvalidEventFormatException;

interface EventBuilderInterface
{
    /**
     * @param mixed $payload
     */
    public function build(string $name, string $version, \DateTimeImmutable $time, $payload): EventInterface;

    public function serialize(EventInterface $event): string;

    /**
     * @throws InvalidEventFormatException
     */
    public function deserialize(string $data): EventInterface;
}
