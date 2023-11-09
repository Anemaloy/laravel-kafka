<?php

namespace Anemaloy\KafkaLocator\Tests\Functional;

use Anemaloy\KafkaLocator\Infrastructure\Schema\SchemaManager;
use Anemaloy\KafkaLocator\Tests\TestEventsTrait;

class CheckJsonFilesTest extends AbstractCheckJsonFilesTest
{
    use TestEventsTrait;

    protected function getEventDirectories(): iterable
    {
        yield $this->getEventUriPrefix() => $this->getEventDirectory();
        yield from SchemaManager::DEFAULT_PATHS;
    }
}
