<?php

declare(strict_types=1);

namespace Temo\KafkaLocator\Infrastructure\Event;

use Temo\KafkaLocator\Domain\EventEmitterInterface;
use Temo\KafkaLocator\Domain\EventInterface;
use Temo\KafkaLocator\Infrastructure\Queue\Exception\ProducerException;
use Temo\KafkaLocator\Infrastructure\Queue\ProducerProviderInterface;

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
