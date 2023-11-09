<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Infrastructure\Queue\RdKafka;

use Temo\KafkaLocator\Infrastructure\Queue\ConsumerInterface;
use Temo\KafkaLocator\Infrastructure\Queue\ConsumerProviderInterface;
use RdKafka\Exception;
use RdKafka\KafkaConsumer;

final class ConsumerProvider implements ConsumerProviderInterface
{
    private KafkaConsumer $kafkaConsumer;
    private int $timeout;
    private Topic $topic;

    public function __construct(KafkaConsumer $kafkaConsumer, int $timeout, string $topicSuffix)
    {
        $this->kafkaConsumer = $kafkaConsumer;
        $this->timeout = $timeout;
        $this->topic = new Topic($topicSuffix);
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function getConsumer(array $eventNames): ConsumerInterface
    {
        $this->kafkaConsumer->subscribe($this->topic->getTopicNames($eventNames));

        return new Consumer($this->kafkaConsumer, $this->timeout);
    }
}
