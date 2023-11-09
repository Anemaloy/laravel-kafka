<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Infrastructure\Schema;

use Opis\JsonSchema\ISchemaLoader;
use Opis\JsonSchema\Schema;

final class DirectoryLoader implements ISchemaLoader
{
    /**
     * @var string[]
     */
    private array $directoryMap = [];

    /**
     * @var Schema[]
     */
    private array $loadedSchemas = [];

    public static function getEventDirectory(): string
    {
        return realpath(__DIR__ . '/../../../Resources/events');
    }

    /**
     * @inheritdoc
     */
    public function loadSchema(string $uri)
    {
        if (isset($this->loadedSchemas[$uri])) {
            return $this->loadedSchemas[$uri];
        }

        foreach ($this->directoryMap as $uriPrefix => $directory) {
            if (0 === \strpos($uri, $uriPrefix)) {
                $path = \substr($uri, \strlen($uriPrefix) + 1);
                $path = $directory . '/' . \ltrim($path, '/');

                if (\file_exists($path)) {
                    $schema = Schema::fromJsonString(\file_get_contents($path));
                    $this->loadedSchemas[$uri] = $schema;

                    return $schema;
                }
            }
        }

        return null;
    }

    public function registerPath(string $directory, string $uriPrefix): bool
    {
        if (!\is_dir($directory)) {
            return false;
        }

        $uriPrefix = \rtrim($uriPrefix, '/');
        $directory = \rtrim($directory, '/');

        $this->directoryMap[$uriPrefix] = $directory;

        return true;
    }
}
