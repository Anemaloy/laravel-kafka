<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Infrastructure\Event;

use Anemaloy\KafkaLocator\Domain\EventEmitterInterface;
use Anemaloy\KafkaLocator\Domain\EventInterface;
use Anemaloy\KafkaLocator\Infrastructure\Queue\Exception\ProducerException;
use Anemaloy\KafkaLocator\Infrastructure\Queue\ProducerProviderInterface;

final class EventEmitter implements EventEmitterInterface
{
    private ProducerProviderInterface $producersProvider;
    private EventBuilderInterface $eventBuilder;

    public function __construct(ProducerProviderInterface $producersProvider, EventBuilderInterface $eventBuilder)
    {
        $this->producersProvider = $producersProvider;
        $this->eventBuilder = $eventBuilder;
    }

    /**
     * {@inheritdoc}
     *
     * @throws ProducerException
     */
    public function emit(EventInterface $event): void
    {
        $message = $this->eventBuilder->serialize($event);

        $this->producersProvider->getProducer($event)->produce($message);
    }
}
