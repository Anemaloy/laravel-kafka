<?php

declare(strict_types=1);

namespace Temo\KafkaLocator\Application;

use Temo\KafkaLocator\Domain\EventEmitterInterface;
use Temo\KafkaLocator\Infrastructure\DateTime\CurrentDateTime;
use Temo\KafkaLocator\Infrastructure\DateTime\DateTimeFactoryInterface;
use Temo\KafkaLocator\Infrastructure\Event\EventBuilder;
use Temo\KafkaLocator\Infrastructure\Event\EventBuilderInterface;
use Temo\KafkaLocator\Infrastructure\Event\EventEmitter;
use Temo\KafkaLocator\Infrastructure\Event\EventValidator;
use Temo\KafkaLocator\Infrastructure\Event\EventValidatorInterface;
use Temo\KafkaLocator\Infrastructure\Queue\ConsumerProviderInterface;
use Temo\KafkaLocator\Infrastructure\Queue\ProducerProviderInterface;
use Temo\KafkaLocator\Infrastructure\Schema\SchemaManager;
use Temo\KafkaLocator\Infrastructure\Schema\SchemaManagerInterface;

final class Builder
{
    private array $eventDirectories = [];
    private ?EventEmitterInterface $eventEmitter = null;
    private ?SchemaManagerInterface $schemaManager = null;
    private ?DateTimeFactoryInterface $dateTimeFactory = null;
    private ?EventBuilderInterface $eventBuilder = null;
    private ?EventValidatorInterface $eventValidator = null;
    private ?ProducerProviderInterface $producerProvider = null;
    private ?ConsumerProviderInterface $consumerProvider = null;

    /**
     * @var callable
     */
    private $logCallback = null;

    public function buildEvents(): Events
    {
        return new Events(
            $this->getEventBuilder(),
            $this->getEventValidator(),
            $this->getEventEmitter(),
            $this->getDateTimeFactory()
        );
    }

    public function buildHandlers(): Handlers
    {
        return new Handlers(
            $this->getEventBuilder(),
            $this->getSchemaManager(),
            $this->getEventValidator(),
            $this->getConsumerProvider(),
            $this->getLogCallback()
        );
    }

    /**
     * @param array<string, string> $eventDirectories
     *
     * @return $this
     */
    public function setEventDirectories(array $eventDirectories): self
    {
        $this->eventDirectories = $eventDirectories;

        return $this;
    }

    public function getEventEmitter(): EventEmitterInterface
    {
        if (null === $this->eventEmitter) {
            $this->setEventEmitter(new EventEmitter($this->getProducerProvider(), $this->getEventBuilder()));
        }

        return $this->eventEmitter;
    }

    /**
     * @return $this
     */
    public function setEventEmitter(EventEmitterInterface $eventEmitter): self
    {
        $this->eventEmitter = $eventEmitter;

        return $this;
    }

    public function getSchemaManager(): SchemaManagerInterface
    {
        if (null === $this->schemaManager) {
            $schemaManager = new SchemaManager();
            foreach ($this->eventDirectories as $uriPrefix => $directory) {
                $schemaManager->registerPath($directory, $uriPrefix);
            }

            $this->setSchemaManager($schemaManager);
        }

        return $this->schemaManager;
    }

    /**
     * @return $this
     */
    public function setSchemaManager(SchemaManagerInterface $schemaManager): self
    {
        $this->schemaManager = $schemaManager;

        return $this;
    }

    public function getDateTimeFactory(): DateTimeFactoryInterface
    {
        if (null === $this->dateTimeFactory) {
            $this->setDateTimeFactory(new CurrentDateTime());
        }

        return $this->dateTimeFactory;
    }

    /**
     * @return $this
     */
    public function setDateTimeFactory(DateTimeFactoryInterface $dateTimeFactory): self
    {
        $this->dateTimeFactory = $dateTimeFactory;

        return $this;
    }

    public function getEventBuilder(): EventBuilderInterface
    {
        if (null === $this->eventBuilder) {
            $this->setEventBuilder(new EventBuilder());
        }

        return $this->eventBuilder;
    }

    /**
     * @return $this
     */
    public function setEventBuilder(EventBuilderInterface $eventBuilder): self
    {
        $this->eventBuilder = $eventBuilder;

        return $this;
    }

    public function getEventValidator(): EventValidatorInterface
    {
        if (null === $this->eventValidator) {
            $this->setEventValidator(new EventValidator($this->getSchemaManager()));
        }

        return $this->eventValidator;
    }

    /**
     * @return $this
     */
    public function setEventValidator(EventValidatorInterface $eventValidator): self
    {
        $this->eventValidator = $eventValidator;

        return $this;
    }

    public function getProducerProvider(): ProducerProviderInterface
    {
        if (null === $this->producerProvider) {
            throw new \LogicException('You must set producer provider.');
        }

        return $this->producerProvider;
    }

    /**
     * @return $this
     */
    public function setProducerProvider(ProducerProviderInterface $producerProvider): self
    {
        $this->producerProvider = $producerProvider;

        return $this;
    }

    public function getConsumerProvider(): ConsumerProviderInterface
    {
        if (null === $this->consumerProvider) {
            throw new \LogicException('You must set consumer provider.');
        }

        return $this->consumerProvider;
    }

    /**
     * @return $this
     */
    public function setConsumerProvider(ConsumerProviderInterface $consumerProvider): self
    {
        $this->consumerProvider = $consumerProvider;

        return $this;
    }

    public function getLogCallback(): callable
    {
        if (null === $this->logCallback) {
            $this->setLogCallback(function (): void {
            });
        }

        return $this->logCallback;
    }

    /**
     * @return $this
     */
    public function setLogCallback(callable $logCallback): self
    {
        $this->logCallback = $logCallback;

        return $this;
    }
}
