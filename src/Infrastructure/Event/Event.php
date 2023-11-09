<?php

declare(strict_types=1);

namespace Temo\KafkaLocator\Infrastructure\Event;

use Temo\KafkaLocator\Domain\EventInterface;

final class Event implements EventInterface
{
    private string $uuid;
    private string $name;
    private string $version;
    private \DateTimeImmutable $time;

    /**
     * @var mixed
     */
    private $payload;

    /**
     * @param mixed $payload
     */
    public function __construct(string $uuid, string $name, string $version, \DateTimeImmutable $time, $payload)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->version = $version;
        $this->time = $time;
        $this->payload = $payload;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getTime(): \DateTimeImmutable
    {
        return $this->time;
    }

    /**
     * @return mixed
     */
    public function getPayload(): mixed
    {
        return $this->payload;
    }
}
