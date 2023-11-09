<?php

declare(strict_types=1);

namespace Temo\KafkaLocator\Application;

use Temo\KafkaLocator\Infrastructure\Event\EventBuilderInterface;
use Temo\KafkaLocator\Infrastructure\Event\EventValidatorInterface;
use Temo\KafkaLocator\Infrastructure\Event\Exception\InvalidEventFormatException;
use Temo\KafkaLocator\Infrastructure\Queue\ConsumerProviderInterface;
use Temo\KafkaLocator\Infrastructure\Queue\Exception\ConsumerException;
use Temo\KafkaLocator\Infrastructure\Schema\SchemaManagerInterface;

final class Handlers
{
    private EventBuilderInterface $serializer;
    private SchemaManagerInterface $schemaManager;
    private EventValidatorInterface $validator;
    private ConsumerProviderInterface $consumerProvider;

    /**
     * @var callable
     */
    private $logErrors;

    /**
     * @var EventHandlerInterface[]
     */
    private array $eventHandlers = [];

    /**
     * @param callable $logErrors function(string $message)
     */
    public function __construct(
        EventBuilderInterface $serializer,
        SchemaManagerInterface $formats,
        EventValidatorInterface $validator,
        ConsumerProviderInterface $consumerProvider,
        callable $logErrors
    ) {
        $this->serializer = $serializer;
        $this->schemaManager = $formats;
        $this->validator = $validator;
        $this->consumerProvider = $consumerProvider;
        $this->logErrors = $logErrors;
    }

    /**
     * @param string[] $eventNames
     */
    public function subscribe(array $eventNames, EventHandlerInterface $eventHandler): void
    {
        $unknownEvents = \array_diff($eventNames, $this->schemaManager->listSchemas());

        if ($unknownEvents) {
            throw new \InvalidArgumentException(\sprintf('Unknown events: %s.', \implode(', ', $unknownEvents)));
        }

        foreach ($eventNames as $eventName) {
            $this->eventHandlers[$eventName] = $eventHandler;
        }
    }

    /**
     * @throws ConsumerException
     */
    public function start(int $maxEvents = PHP_INT_MAX): void
    {
        $consumer = $this->consumerProvider->getConsumer(\array_keys($this->eventHandlers));

        while ($maxEvents--) {
            $message = $consumer->consume();

            if (!$message) {
                continue;
            }

            try {
                $event = $this->serializer->deserialize($message);

                $validationResult = $this->validator->validate($event);
                if (!$validationResult->isValid()) {
                    ($this->logErrors)(\sprintf('Invalid message consumed: "%s": %s', $message, $validationResult->getMessage()));
                    continue;
                }
            } catch (InvalidEventFormatException $e) {
                ($this->logErrors)(\sprintf('Consumed message: "%s" has incorrect format: %s', $message, $e->getMessage()));
                continue;
            }

            if (!\array_key_exists($event->getName(), $this->eventHandlers)) {
                continue;
            }

            $this->eventHandlers[$event->getName()]->handle($event);
        }
    }
}
