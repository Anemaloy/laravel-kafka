<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Infrastructure\Queue\RdKafka;

use Temo\KafkaLocator\Domain\EventInterface;
use Temo\KafkaLocator\Infrastructure\Queue\Exception\ProducerException;
use Temo\KafkaLocator\Infrastructure\Queue\ProducerInterface;
use Temo\KafkaLocator\Infrastructure\Queue\ProducerProviderInterface;
use RdKafka\Producer as KafkaProducer;

final class ProducerProvider implements ProducerProviderInterface
{
    private KafkaProducer $kafkaProducer;
    private int $flushTimeout;
    private int $pollRetries;
    private int $pollTimeout;
    private Topic $topic;

    /**
     * @var ProducerInterface
     */
    private $producers = [];

    public function __construct(
        KafkaProducer $kafkaProducer,
        int $flushTimeout,
        int $pollRetries,
        int $pollTimeout,
        string $topicSuffix
    ) {
        $this->kafkaProducer = $kafkaProducer;
        $this->flushTimeout = $flushTimeout;
        $this->pollRetries = $pollRetries;
        $this->pollTimeout = $pollTimeout;
        $this->topic = new Topic($topicSuffix);
    }

    /**
     * {@inheritdoc}
     */
    public function getProducer(EventInterface $event): ProducerInterface
    {
        $eventName = $event->getName();

        if (!isset($this->producers[$eventName])) {
            $this->producers[$eventName] = $this->createProducer($eventName);
        }

        return $this->producers[$eventName];
    }

    private function createProducer(string $eventName): Producer
    {
        $topic = $this->kafkaProducer->newTopic($this->topic->getTopicName($eventName));

        return new Producer(function (string $message) use ($topic): void {
            $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);

            if ($this->pollRetries <= 0) {
                $result = $this->kafkaProducer->flush($this->flushTimeout);

                if ($result) {
                    throw new ProducerException(\sprintf('Unable to send message into topic "%s".', $topic->getName()));
                }

                return;
            }

            $retries = $this->pollRetries;

            while ($this->kafkaProducer->getOutQLen()) {
                $this->kafkaProducer->poll($this->pollTimeout);

                if ($retries > 0) {
                    --$retries;

                    continue;
                }

                $this->kafkaProducer->purge(RD_KAFKA_PURGE_F_QUEUE);

                throw new ProducerException(\sprintf('Unable to send message into topic "%s".', $topic->getName()));
            }
        });
    }
}
