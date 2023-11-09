<?php

namespace Anemaloy\KafkaLocator\Infrastructure\Schema;

use Opis\JsonSchema\Validator;

class ReferenceValidator
{
    private Validator $validator;
    private string $baseUri;

    public function __construct(SchemaManager $schemaManager, string $baseUri)
    {
        $validator = new Validator();
        $validator->setLoader($schemaManager->getLoader());

        $this->validator = $validator;
        $this->baseUri = $baseUri;
    }

    public function check($value, string $reference): ReferenceValidateResult
    {
        $schemaUri = $this->baseUri . $reference;
        $result = $this->validator->uriValidation($value, $schemaUri, 100);

        return new ReferenceValidateResult($result);
    }

    public function isValid($value, string $reference): bool
    {
        return $this->check($value, $reference)->isValid();
    }
}
