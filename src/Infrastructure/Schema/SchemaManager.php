<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Infrastructure\Schema;

use Opis\JsonSchema\ISchema;
use Opis\JsonSchema\ISchemaLoader;

final class SchemaManager implements SchemaManagerInterface
{
    public const DEFAULT_PATHS = [
        '' => __DIR__ . '/../../../Resources/events/',
    ];

    private ISchemaLoader $loader;

    /**
     * @var array<string, string>
     */
    private array $eventDirectories;

    /**
     * @var ISchema[]
     */
    private array $schemas;

    public function __construct()
    {
        $this->schemas = [];

        $this->loader = new DirectoryLoader();
    }

    public static function createDefault(): SchemaManager
    {
        $schemaManager = new SchemaManager();
        foreach (self::DEFAULT_PATHS as $directory => $uriPrefix) {
            $schemaManager->registerPath($directory, $uriPrefix);
        }

        return $schemaManager;
    }

    /**
     * Регистрирует каталог с событиями.
     *
     * @param string $directory абсолютный путь к каталогу
     * @param string $uriPrefix префикс URI для схем в указанном каталоге
     */
    public function registerPath(string $directory, string $uriPrefix): void
    {
        $directory = \rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (!\is_readable($directory)) {
            throw new \InvalidArgumentException(\sprintf('Cannot open events directory "%s".', $directory));
        }

        $uriPrefix = \rtrim($uriPrefix, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $this->eventDirectories[$uriPrefix] = $directory;
        $this->loader->registerPath($directory, $uriPrefix);
    }

    /**
     * Возвращает список всех существующих схем событий.
     *
     * @return string[]
     */
    public function listSchemas(): array
    {
        $result = [];

        foreach ($this->eventDirectories as $eventDirectory) {
            $files = \glob($eventDirectory . '*' . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . '*') ?: [];

            $position = \strlen($eventDirectory);
            foreach ($files as $file) {
                $result[] = \substr($file, $position);
            }
        }

        return $result;
    }

    /**
     * @return ?ISchema
     */
    public function getSchema(string $name, string $version): ?ISchema
    {
        if (!isset($this->schemas[$name][$version])) {
            $schemaUri = $this->getSchemaUri($name, $version);
            if (!$schemaUri) {
                return null;
            }

            $schema = $this->loader->loadSchema($schemaUri);
            if (!$schema) {
                return null;
            }

            $this->schemas[$name][$version] = $schema;
        }

        return $this->schemas[$name][$version];
    }

    public function getLoader(): ISchemaLoader
    {
        return $this->loader;
    }

    /**
     * Возвращает URI JSON-схемы события $name.
     */
    public function getSchemaUri(string $name, string $version): ?string
    {
        foreach ($this->eventDirectories as $urlPrefix => $directory) {
            $filename = $directory . $name . DIRECTORY_SEPARATOR . $version . '.json';
            if (is_readable($filename)) {
                return $urlPrefix . $name . '/' . $version . '.json';
            }
        }

        return null;
    }
}
