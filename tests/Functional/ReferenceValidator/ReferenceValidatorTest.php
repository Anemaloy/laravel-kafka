<?php

namespace Anemaloy\KafkaLocator\Tests\Functional\ReferenceValidator;

use PHPUnit\Framework\TestCase;
use Anemaloy\KafkaLocator\Infrastructure\Schema\ReferenceValidateResult;
use Anemaloy\KafkaLocator\Infrastructure\Schema\ReferenceValidator;
use Anemaloy\KafkaLocator\Infrastructure\Schema\SchemaManager;

class ReferenceValidatorTest extends TestCase
{
    private SchemaManager $schemaManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schemaManager = new SchemaManager();
        $this->schemaManager->registerPath(__DIR__, 'https://events.pik-broker.ru/');
    }

    public function testIsValid()
    {
        $validator = new ReferenceValidator($this->schemaManager, 'https://events.pik-broker.ru/refs/');
        $reference = 'document/1.0.0.json#/definitions/sale';

        $this->assertTrue($validator->isValid(5, $reference));
        $this->assertFalse($validator->isValid(10, $reference));
    }

    public function testCheck()
    {
        $validator = new ReferenceValidator($this->schemaManager, 'https://events.pik-broker.ru/refs/');
        $reference = 'document/1.0.0.json#/definitions/sale';

        $result = $validator->check(5, $reference);
        $this->assertInstanceOf(ReferenceValidateResult::class, $result);
        $this->assertTrue($result->isValid());
        $this->assertNull($result->getError());

        $result = $validator->check(10, $reference);
        $this->assertFalse($result->isValid());
        $this->assertNotEmpty($result->getError());

        $data = ['foo1' => 1, 'foo2' => 'asdf'];
        $result = $validator->check(json_decode(json_encode($data)), 'document/1.0.0.json#/definitions/objectTest');
        $this->assertCount(2, json_decode($result->getError(), true));
    }
}
