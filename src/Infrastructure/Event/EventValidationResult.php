<?php

declare(strict_types=1);

namespace Temo\KafkaLocator\Infrastructure\Event;

use Opis\JsonSchema\ValidationResult;

final class EventValidationResult implements EventValidationResultInterface
{
    private ?string $message;
    private ?ValidationResult $validationResult;

    public function __construct(?string $message = null)
    {
        $this->setMessage($message);
        $this->validationResult = null;
    }

    /**
     * @inheritdoc
     */
    public function isValid(): bool
    {
        return \is_null($this->message);
    }

    /**
     * @inheritdoc
     */
    public function setMessage(?string $message = null): void
    {
        $this->message = $message;
    }

    /**
     * @inheritdoc
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @inheritdoc
     */
    public function setValidationResult(?ValidationResult $validationResult): void
    {
        $this->validationResult = $validationResult;
    }

    /**
     * @inheritdoc
     */
    public function getValidationResult(): ?ValidationResult
    {
        return $this->validationResult;
    }
}
