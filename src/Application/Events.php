<?php

declare(strict_types=1);

namespace Temo\KafkaLocator\Application;

use Temo\KafkaLocator\Application\Exception\ValidationException;
use Temo\KafkaLocator\Domain\EventEmitterInterface;
use Temo\KafkaLocator\Domain\EventInterface;
use Temo\KafkaLocator\Infrastructure\DateTime\DateTimeFactoryInterface;
use Temo\KafkaLocator\Infrastructure\Event\EventBuilderInterface;
use Temo\KafkaLocator\Infrastructure\Event\EventValidatorInterface;

final class Events
{
    private EventBuilderInterface $eventBuilder;
    private EventValidatorInterface $eventValidator;
    private EventEmitterInterface $emitter;
    private DateTimeFactoryInterface $dateTimeFactory;

    public function __construct(
        EventBuilderInterface $builder,
        EventValidatorInterface $validator,
        EventEmitterInterface $emitter,
        DateTimeFactoryInterface $timeFactory
    ) {
        $this->eventBuilder = $builder;
        $this->eventValidator = $validator;
        $this->emitter = $emitter;
        $this->dateTimeFactory = $timeFactory;
    }

    /**
     * @param string $name
     * @param string $version
     * @param mixed $payload
     *
     * @return EventInterface
     * @throws ValidationException
     */
    public function emit(string $name, string $version, mixed $payload): EventInterface
    {
        $event = $this->eventBuilder->build(
            $name,
            $version,
            $this->dateTimeFactory->getDateTime(),
            $payload
        );

        $validationResult = $this->eventValidator->validate($event);
        if (!$validationResult->isValid()) {
            throw new ValidationException(\sprintf('Invalid event payload: %s.', $validationResult->getMessage()), $validationResult->getValidationResult());
        }

        $this->emitter->emit($event);

        return $event;
    }
}
