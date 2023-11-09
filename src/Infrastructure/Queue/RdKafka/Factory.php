<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Infrastructure\Queue\RdKafka;

use RdKafka\Conf;
use RdKafka\KafkaConsumer;
use RdKafka\Producer;

final class Factory
{
    private const KAFKA_DEFAULTS = [
        'metadata.broker.list' => '127.0.0.1:9092',
        'auto.offset.reset' => 'smallest',
    ];

    private const PRODUCER_DEFAULTS = [
        'socket.timeout.ms' => 200,
    ];

    private const CUSTOM_DEFAULTS = [
        'custom.consumer.timeout' => 10000,
        'custom.producer.flush.timeout' => 200,
        'custom.producer.poll.retries' => 200,
        'custom.producer.poll.timeout' => 10,
        'custom.topic.suffix' => '',
    ];

    /**
     * @param string[] $config
     */
    public function createProducerProvider(array $config): ProducerProvider
    {
        $producer = new Producer(
            $this->createConfig(
                \array_merge(self::PRODUCER_DEFAULTS, $config)
            )
        );

        $config = \array_replace(self::CUSTOM_DEFAULTS, $config);

        return new ProducerProvider(
            $producer,
            (int) $config['custom.producer.flush.timeout'],
            (int) $config['custom.producer.poll.retries'],
            (int) $config['custom.producer.poll.timeout'],
            (string) $config['custom.topic.suffix']
        );
    }

    /**
     * @param string[] $config
     */
    public function createConsumerProvider(array $config): ConsumerProvider
    {
        $consumer = new KafkaConsumer($this->createConfig($config));
        $config = \array_replace(self::CUSTOM_DEFAULTS, $config);

        return new ConsumerProvider(
            $consumer,
            (int) $config['custom.consumer.timeout'],
            (string) $config['custom.topic.suffix']
        );
    }

    /**
     * @param string[] $config
     */
    public function createConfig(array $config): Conf
    {
        $config = \array_replace(self::KAFKA_DEFAULTS, $config);
        $config = \array_diff_key($config, self::CUSTOM_DEFAULTS);

        $conf = new Conf();

        foreach ($config as $key => $value) {
            $conf->set($key, (string) $value);
        }

        if (\function_exists('pcntl_sigprocmask')) {
            \pcntl_sigprocmask(SIG_BLOCK, [SIGIO]);
            $conf->set('internal.termination.signal', (string) SIGIO);
        } else {
            $conf->set('queue.buffering.max.ms', '1');
        }

        return $conf;
    }
}
