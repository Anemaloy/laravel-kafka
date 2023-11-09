<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Infrastructure\Event;

use Anemaloy\KafkaLocator\Domain\EventInterface;
use Anemaloy\KafkaLocator\Infrastructure\Event\Exception\InvalidEventFormatException;
use Ramsey\Uuid\Uuid;

final class EventBuilder implements EventBuilderInterface
{
    /**
     * @param mixed $payload
     */
    public function build(string $name, string $version, \DateTimeImmutable $time, $payload): EventInterface
    {
        $uuid = $this->generateUuid();
        $payload = \json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $payload = \json_decode($payload);

        return new Event(
            $uuid,
            $name,
            $version,
            $time,
            $payload
        );
    }

    /**
     * @throws InvalidEventFormatException
     */
    public function deserialize(string $data): EventInterface
    {
        $data = \json_decode($data);

        return new Event(
            $this->getUuid($data),
            $this->getName($data),
            $this->getVersion($data),
            $this->getTime($data),
            $this->getPayload($data)
        );
    }

    public function serialize(EventInterface $event): string
    {
        $message = [
            'uuid' => $event->getUuid(),
            'name' => $event->getName(),
            'time' => $event->getTime()->format(\DateTimeInterface::RFC3339),
            'version' => $event->getVersion(),
            'payload' => $event->getPayload(),
        ];

        return \json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param mixed $data
     */
    private function getUuid($data): string
    {
        if (empty($data->uuid) || !\is_string($data->uuid)) {
            $data->uuid = 'null:' . $this->generateUuid();
        }

        return $data->uuid;
    }

    /**
     * @param mixed $data
     *
     * @throws InvalidEventFormatException
     */
    private function getName($data): string
    {
        if (!isset($data->name) || !\is_string($data->name)) {
            throw new InvalidEventFormatException('Event has no correct name.');
        }

        return (string) $data->name;
    }

    /**
     * @param mixed $data
     *
     * @throws InvalidEventFormatException
     */
    private function getVersion($data): string
    {
        if (!isset($data->version) || !\is_string($data->version)) {
            throw new InvalidEventFormatException('Event has no correct version.');
        }

        return (string) $data->version;
    }

    /**
     * @param mixed $data
     *
     * @throws InvalidEventFormatException
     */
    private function getTime($data): \DateTimeImmutable
    {
        try {
            if (!isset($data->time) || !\is_string($data->time)) {
                throw new InvalidEventFormatException();
            }

            return new \DateTimeImmutable($data->time);
        } catch (\Throwable $e) {
            throw new InvalidEventFormatException('Event has no correct message time.');
        }
    }

    /**
     * @param mixed $data
     *
     * @throws InvalidEventFormatException
     */
    private function getPayload($data): object
    {
        if (!isset($data->payload) || !\is_object($data->payload)) {
            throw new InvalidEventFormatException('Event has no correct payload.');
        }

        return $data->payload;
    }

    private function generateUuid(): string
    {
        return Uuid::uuid4()->toString();
    }
}
