<?php

declare(strict_types=1);

namespace Temo\KafkaLocator\Application\Locator;

use Temo\KafkaLocator\Application\Builder;
use Temo\KafkaLocator\Application\Events;
use Temo\KafkaLocator\Application\Handlers;
use Temo\KafkaLocator\Infrastructure\Queue\RdKafka\Factory;
use Temo\KafkaLocator\Infrastructure\Schema\SchemaManager;

abstract class AbstractKafkaLocator implements LocatorInterface
{
    /**
     * @var string[]
     */
    private array $kafkaConfig;

    /**
     * @var array<string, string>
     */
    protected array $eventDirectories;

    private ?Handlers $handlers = null;
    private ?Events $events = null;

    /**
     * @param string[] $kafkaConfig
     */
    public function __construct(array $kafkaConfig, array $eventDirectories = SchemaManager::DEFAULT_PATHS)
    {
        $this->kafkaConfig = $kafkaConfig;
        $this->eventDirectories = $eventDirectories;
    }

    public function getHandlers(): Handlers
    {
        if (null === $this->handlers) {
            $consumerProvider = (new Factory())->createConsumerProvider($this->kafkaConfig);

            $this->handlers = $this
                ->prepareBuilderForHandlers()
                ->setEventDirectories($this->eventDirectories)
                ->setConsumerProvider($consumerProvider)
                ->buildHandlers();
        }

        return $this->handlers;
    }

    public function getEvents(): Events
    {
        if (null === $this->events) {
            $factory = new Factory();

            $producerProvider = $factory->createProducerProvider($this->kafkaConfig);

            $this->events = $this
                ->prepareBuilderForEvents()
                ->setEventDirectories($this->eventDirectories)
                ->setProducerProvider($producerProvider)
                ->buildEvents();
        }

        return $this->events;
    }

    /**
     * Returns builder for handlers.
     */
    abstract protected function prepareBuilderForHandlers(): Builder;

    /**
     * Returns builder for events.
     */
    abstract protected function prepareBuilderForEvents(): Builder;
}
