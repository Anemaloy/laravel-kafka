<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Infrastructure\Schema;

use Opis\JsonSchema\ISchema;
use Opis\JsonSchema\ISchemaLoader;

interface SchemaManagerInterface
{
    /**
     * @return string[]
     */
    public function listSchemas(): array;

    /**
     * @return ?ISchema
     */
    public function getSchema(string $name, string $version): ?ISchema;

    /**
     * @return ?string
     */
    public function getSchemaUri(string $name, string $version): ?string;

    public function getLoader(): ISchemaLoader;
}
