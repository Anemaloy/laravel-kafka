<?php

namespace Anemaloy\KafkaLocator\Infrastructure\Schema;

use Opis\JsonSchema\ValidationResult;
use OpisErrorPresenter\Implementation\MessageFormatterFactory;
use OpisErrorPresenter\Implementation\PresentedValidationErrorFactory;
use OpisErrorPresenter\Implementation\ValidationErrorPresenter;

class ReferenceValidateResult
{
    private ValidationResult $result;

    public function __construct(ValidationResult $result)
    {
        $this->result = $result;
    }

    public function getError(): ?string
    {
        if ($this->isValid()) {
            return null;
        }

        $presenter = new ValidationErrorPresenter(
            new PresentedValidationErrorFactory(
                new MessageFormatterFactory()
            ),
        );

        $errors = $this->result->getErrors();
        $presented = $presenter->present(...$errors);

        return json_encode($presented);
    }

    public function isValid(): bool
    {
        return $this->result->isValid();
    }
}
