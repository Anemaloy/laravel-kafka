<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Tests\Infrastructure\Event;

use PHPUnit\Framework\TestCase;
use Anemaloy\KafkaLocator\Domain\EventInterface;
use Anemaloy\KafkaLocator\Infrastructure\Event\Event;
use Anemaloy\KafkaLocator\Infrastructure\Event\EventBuilder;

class EventBuilderTest extends TestCase
{
    private EventBuilder $eventBuilder;

    protected function setUp(): void
    {
        $this->eventBuilder = new EventBuilder();
    }

    public function testBuild(): void
    {
        $name = 'lk/user/created';
        $version = '1.0.0';
        $time = new \DateTimeImmutable();
        $payload = ['foo' => 'bar'];

        $event = $this->eventBuilder->build($name, $version, $time, $payload);

        $this->assertInstanceOf(EventInterface::class, $event);
        $this->assertNotEmpty($event->getUuid());
        $this->assertEquals($name, $event->getName());
        $this->assertEquals($version, $event->getVersion());
        $this->assertEquals($time, $event->getTime());
        $this->assertEquals($payload['foo'], $event->getPayload()->foo);
    }

    public function testSerialization(): void
    {
        $uuid = '12345';
        $name = 'lk/user/created';
        $version = '1.0.0';
        $time = new \DateTimeImmutable();
        $payload = ['foo' => 'bar'];
        $event = new Event($uuid, $name, $version, $time, $payload);

        $serialized = $this->eventBuilder->serialize($event);
        $unserializedEvent = $this->eventBuilder->deserialize($serialized);

        $this->assertInstanceOf(EventInterface::class, $event);
        $this->assertEquals($uuid, $unserializedEvent->getUuid());
    }

    public function testSerializationWithEmptyUuid(): void
    {
        $name = 'lk/user/created';
        $version = '1.0.0';
        $time = new \DateTimeImmutable();
        $payload = ['foo' => 'bar'];
        $event = new Event('', $name, $version, $time, $payload);

        $serialized = $this->eventBuilder->serialize($event);
        $unserializedEvent = $this->eventBuilder->deserialize($serialized);

        $this->assertInstanceOf(EventInterface::class, $event);
        $this->assertNotEmpty($unserializedEvent->getUuid());
    }
}
