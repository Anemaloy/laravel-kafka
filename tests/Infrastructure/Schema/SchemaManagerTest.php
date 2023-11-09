<?php

namespace Anemaloy\KafkaLocator\Tests\Infrastructure\Schema;

use PHPUnit\Framework\TestCase;
use Anemaloy\KafkaLocator\Infrastructure\Schema\SchemaManager;
use Anemaloy\KafkaLocator\Tests\TestEventsTrait;

class SchemaManagerTest extends TestCase
{
    use TestEventsTrait;

    public function testListSchemas(): void
    {
        $schemaManager = new SchemaManager();
        $schemaManager->registerPath($this->getEventDirectory(), $this->getEventUriPrefix());

        $schemas = $schemaManager->listSchemas();

        self::assertContains('test/amo/deal/statusUpdated', $schemas);
        self::assertContains('test/mainsite/apartment/created', $schemas);
        self::assertNotContainsEquals('test/amo/deal/statusDeleted', $schemas);
    }
}
