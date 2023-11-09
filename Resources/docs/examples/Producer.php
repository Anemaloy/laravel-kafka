<?php

declare(strict_types=1);

use Anemaloy\KafkaLocator\Application\Locator\KafkaLocator;
use Anemaloy\KafkaLocator\Application\Locator\LocatorInterface;

final class Producer
{
    private LocatorInterface $locator;

    public function __construct()
    {
        $kafkaConfig = [
            'metadata.broker.list' => '127.0.0.1:9092',
            'custom.topic.suffix' => 'dev',
        ];

        $eventDirectories = [
            'tinkoff' => __DIR__ . '/events/tinkoff',
        ];

        $this->locator = new KafkaLocator($kafkaConfig, $eventDirectories);
    }

    /**
     * @throws \Anemaloy\KafkaLocator\Application\Exception\ValidationException
     */
    public function produce(): void
    {
        $this->locator->getEvents()->emit(
            'tinkoff/notifications/employeePassword',// Название события
            '1.0.0', // Версия события
            [ // В качестве данныз можно передавать массив или объект
                'id' => 1,
                'phone' => '71234567890',
                'name' => 'Василий Пупкин',
                'email' => 'v.pupkin@example.com',
                'notifyByEmail' => false,
                'notifyByPhone' => false,
                'notifyBySms' => true,
                'createdAt' => '2020-01-13T11:22:39+00:00',
                'registeredAt' => '2020-01-16T23:40:01+00:00',
            ]
        );
    }
}
