<?php

declare(strict_types=1);

namespace Temo\KafkaLocator\Infrastructure\Event;

use Opis\JsonSchema\ISchema;
use Opis\JsonSchema\ValidationResult;
use Opis\JsonSchema\Validator;
use OpisErrorPresenter\Implementation\MessageFormatterFactory;
use OpisErrorPresenter\Implementation\PresentedValidationErrorFactory;
use OpisErrorPresenter\Implementation\Strategies\BestMatchError;
use OpisErrorPresenter\Implementation\ValidationErrorPresenter;
use Temo\KafkaLocator\Domain\EventInterface;
use Temo\KafkaLocator\Infrastructure\Schema\SchemaManagerInterface;

final class EventValidator implements EventValidatorInterface
{
    private SchemaManagerInterface $schemaManager;

    public function __construct(SchemaManagerInterface $schemaManager)
    {
        $this->schemaManager = $schemaManager;
    }

    public function validate(EventInterface $event, int $maxErrors = 1): EventValidationResultInterface
    {
        $result = new EventValidationResult();

        // Check version
        if (!\preg_match('/\d+\.\d+\.\d+/', $event->getVersion())) {
            $result->setMessage(\sprintf('Incorrect event version format %s', $event->getVersion()));

            return $result;
        }

        // Check json schema
        $schema = $this->schemaManager->getSchema($event->getName(), $event->getVersion());
        if (!$schema) {
            $result->setMessage(\sprintf('Schema "%s" with version "%s" not found', $event->getName(), $event->getVersion()));

            return $result;
        }

        // Validate payload
        return $this->validatePayload($schema, $event->getPayload(), $maxErrors);
    }

    /**
     * @param mixed $payload
     */
    private function validatePayload(ISchema $schema, $payload, int $maxErrors): EventValidationResultInterface
    {
        $result = new EventValidationResult();

        try {
            $validator = new Validator();
            $validator->setLoader($this->schemaManager->getLoader());

            $schemaValidationResult = $validator->schemaValidation($payload, $schema, $maxErrors);

            if (!$schemaValidationResult->isValid()) {
                $result->setMessage($this->buildErrorMessageString($schemaValidationResult));
                $result->setValidationResult($schemaValidationResult);
            }
        } catch (\Throwable $e) {
            $result->setMessage(\sprintf('Validation exception %s', $e->getMessage()));
        }

        return $result;
    }

    private function buildErrorMessageString(ValidationResult $schemaValidationResult): string
    {
        $presenter = new ValidationErrorPresenter(
            new PresentedValidationErrorFactory(
                new MessageFormatterFactory()
            ),
            new BestMatchError()
        );

        $presented = $presenter->present(...$schemaValidationResult->getErrors());

        return json_encode($presented);
    }
}
