<?php

namespace Anemaloy\KafkaLocator\Infrastructure\Schema\EventInfo;

class EventInfo
{
    private string $source;
    private string $name;
    private string $version;
    private array $eventData;

    public function __construct(string $source, string $name, string $version, array $eventData)
    {
        $this->source = $source;
        $this->name = $name;
        $this->version = $version;
        $this->eventData = $eventData;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getEventData(): array
    {
        return $this->eventData;
    }

    public function getCaption(): string
    {
        return $this->eventData['title'];
    }

    public function getExample(): array
    {
        return reset($this->eventData['examples']);
    }
}
