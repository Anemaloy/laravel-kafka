<?php

namespace Anemaloy\KafkaLocator\Tests\Functional;

use Opis\JsonSchema\Schema;
use Opis\JsonSchema\ValidationError;
use Opis\JsonSchema\Validator;
use PHPUnit\Framework\TestCase;
use Anemaloy\KafkaLocator\Infrastructure\Schema\EventInfo\EventInfoReference;
use Anemaloy\KafkaLocator\Infrastructure\Schema\SchemaManager;

/**
 * Класс для тестирования каталога событий.
 */
abstract class AbstractCheckJsonFilesTest extends TestCase
{
    /**
     * Директории с событиями в формате $uriPrefix => $eventsDirectories:.
     *
     * ```php
     * yield 'https://foo.domain.org' => '../../Resources/events/foo.domain.org';
     * yield 'https://bar.domain.org' => '../../Resources/events/bar.domain.org';
     * ```
     */
    abstract protected function getEventDirectories(): iterable;

    /**
     * @dataProvider getDataWithJsonFiles
     */
    public function testJsonEventFile(string $uriPrefix, string $directory, string $jsonFilePath): void
    {
        $filename = str_replace($directory . '/', '', $jsonFilePath);

        // Проверка JSON формата
        $result = json_decode(file_get_contents($jsonFilePath), true);
        $this->assertIsArray($result, "Check file to json format: $filename");

        // Проверка валидности схемы
        $schema = Schema::fromJsonString(file_get_contents($jsonFilePath));
        $this->assertInstanceOf(Schema::class, $schema);

        // Проверка примера из схемы
        $jsonObject = json_decode(file_get_contents($jsonFilePath));
        if (property_exists($jsonObject, 'examples')) {
            $schemaManager = new SchemaManager();
            $schemaManager->registerPath($directory, $uriPrefix);
            $validator = new Validator();
            $validator->setLoader($schemaManager->getLoader());
            foreach ($jsonObject->examples as $example) {
                $validationResult = $validator->schemaValidation($example, $schema);
                if (!$validationResult->isValid()) {
                    $message = "$filename" . PHP_EOL;
                    $message .= $this->printError($validationResult->getFirstError());
                    $this->fail($message);
                }
                $this->assertTrue($validationResult->isValid(), "$filename");
            }
        }
    }

    private function printError(ValidationError $error, $shift = 0): string
    {
        $padding = str_repeat(' ', $shift);
        $message = "$padding";
        $pointer = $error->dataPointer();
        if (is_array($pointer)) {
            $message .= print_r(join('.', $pointer) . ': ', true);
        }

        $message .= print_r($error->keyword() . ' ' . json_encode($error->keywordArgs()), true) . PHP_EOL;

        if ($error->subErrors()) {
            $message .= $this->printError($error->subErrors()[0], $shift + 1);
        } else {
            $message .= $padding . print_r($error->data(), true) . PHP_EOL;
        }

        $message .= PHP_EOL;

        return $message;
    }

    public function getDataWithJsonFiles(): iterable
    {
        foreach ($this->getEventDirectories() as $uriPrefix => $directory) {
            $eventInfoReference = new EventInfoReference($directory);
            foreach ($eventInfoReference->getFilesList() as $fileName) {
                yield [$uriPrefix, $directory, $fileName];
            }
        }
    }
}
