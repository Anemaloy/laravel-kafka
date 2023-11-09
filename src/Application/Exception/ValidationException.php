<?php

declare(strict_types=1);

namespace Temo\KafkaLocator\Application\Exception;

use Opis\JsonSchema\ValidationResult;
use Throwable;

final class ValidationException extends \Exception
{
    private ?ValidationResult $validationResult;

    public function __construct(
        string $message,
        ?ValidationResult $validationResult = null,
        ?Throwable $previous = null
    ) {
        $this->validationResult = $validationResult;

        parent::__construct($message, 0, $previous);
    }

    public function getValidationResult(): ?ValidationResult
    {
        return $this->validationResult;
    }
}
