<?php

declare(strict_types=1);

namespace Temo\KafkaLocator\Infrastructure\Queue\RdKafka;

use Temo\KafkaLocator\Infrastructure\Queue\ConsumerInterface;
use Temo\KafkaLocator\Infrastructure\Queue\Exception\ConsumerException;
use RdKafka\Exception;
use RdKafka\KafkaConsumer;

final class Consumer implements ConsumerInterface
{
    private KafkaConsumer $kafkaConsumer;
    private int $timeout;

    public function __construct(KafkaConsumer $kafkaConsumer, int $timeout)
    {
        $this->kafkaConsumer = $kafkaConsumer;
        $this->timeout = $timeout;
    }

    /**
     * {@inheritdoc}
     */
    public function consume(): string
    {
        try {
            $message = $this->kafkaConsumer->consume($this->timeout);
        } catch (Exception $e) {
            throw new ConsumerException($e->getMessage(), 0, $e);
        }

        switch ($message->err) {
            case RD_KAFKA_RESP_ERR_NO_ERROR:
                return (string) $message->payload;
            case RD_KAFKA_RESP_ERR__TIMED_OUT:
            case RD_KAFKA_RESP_ERR__PARTITION_EOF:
            case RD_KAFKA_RESP_ERR_UNKNOWN_TOPIC_OR_PART:
                return '';
        }

        throw new ConsumerException($message->errstr(), $message->err);
    }
}
