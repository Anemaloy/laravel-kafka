<?php

namespace Anemaloy\KafkaLocator\Tests\Functional\Infrastructure\Schema;

use PHPUnit\Framework\TestCase;
use Anemaloy\KafkaLocator\Infrastructure\Schema\EventInfo\EventInfo;
use Anemaloy\KafkaLocator\Infrastructure\Schema\EventInfo\EventInfoReference;
use Anemaloy\KafkaLocator\Tests\TestEventsTrait;

class EventInfoReferenceTest extends TestCase
{
    use TestEventsTrait;

    private EventInfoReference $reference;

    protected function setUp(): void
    {
        parent::setUp();

        $eventDirectory = $this->getEventDirectory();
        $this->reference = new EventInfoReference($eventDirectory);
    }

    public function testEventsInfo(): void
    {
        $eventsList = $this->reference->getEventsInfo();

        $this->assertNotEmpty($eventsList);

        $this->assertTrue(true);
    }

    public function testHasEvent(): void
    {
        $this->assertTrue($this->reference->hasEvent('test/amo/deal/statusUpdated'));
        $this->assertFalse($this->reference->hasEvent('test/amo/deal/statusCreated'));
    }

    public function testHasEventVersion(): void
    {
        $this->assertTrue($this->reference->hasEventVersion('test/amo/deal/statusUpdated', '1.0.0'));
        $this->assertTrue($this->reference->hasEventVersion('test/amo/deal/statusUpdated', '1.1.0'));
        $this->assertFalse($this->reference->hasEventVersion('test/amo/deal/statusUpdated', '0.1.0'));
    }

    public function testGetEventVersions(): void
    {
        $this->assertNotEmpty($this->reference->getEventVersions('test/amo/deal/statusUpdated'));
    }

    public function testGetLastEventVersion(): void
    {
        $eventVersions = $this->reference->getEventVersions('test/amo/deal/statusUpdated');
        $eventVersions = array_keys($eventVersions);
        sort($eventVersions);
        $lastEventVersion = end($eventVersions);

        $this->assertEquals($lastEventVersion, $this->reference->getLastEventVersion('test/amo/deal/statusUpdated'));
    }

    public function testGetEventVersion(): void
    {
        $this->assertInstanceOf(EventInfo::class, $this->reference->getEventInfo('test/amo/deal/statusUpdated', '1.0.0'));
        $this->assertNull($this->reference->getEventInfo('test/amo/deal/statusUpdated', '0.1.0'));
        $this->assertNull($this->reference->getEventInfo('test/amo/deal/statusCreated', '0.1.0'));
    }

    public function testGetEventInfo(): void
    {
        $this->assertInstanceOf(EventInfo::class, $this->reference->getLastEventInfo('test/amo/deal/statusUpdated'));
    }

    public function testEventData(): void
    {
        $info = $this->reference->getLastEventInfo('test/amo/deal/statusUpdated');

        $this->assertNotEmpty($info->getSource());

        $jsonData = json_decode(file_get_contents($info->getSource()), true);

        $this->assertEquals($jsonData['title'], $info->getCaption());
        $this->assertEquals($jsonData['examples'][0], $info->getExample());
    }
}
