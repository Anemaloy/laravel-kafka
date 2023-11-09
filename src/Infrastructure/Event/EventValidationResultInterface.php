<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Infrastructure\Event;

use Opis\JsonSchema\ValidationResult;

interface EventValidationResultInterface
{
    public function isValid(): bool;

    public function setMessage(?string $message): void;

    public function getMessage(): ?string;

    public function setValidationResult(?ValidationResult $validationResult): void;

    public function getValidationResult(): ?ValidationResult;
}
