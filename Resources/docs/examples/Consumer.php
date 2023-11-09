<?php

declare(strict_types=1);

use Anemaloy\KafkaLocator\Application\EventHandlerInterface;
use Anemaloy\KafkaLocator\Application\Locator\KafkaLocator;
use Anemaloy\KafkaLocator\Application\Locator\LocatorInterface;
use Anemaloy\KafkaLocator\Domain\EventInterface;

final class Consumer
{
    private LocatorInterface $locator;

    public function __construct()
    {
        $kafkaConfig = [
            'metadata.broker.list' => '127.0.0.1:9092',
            'group.id' => 'tinkoff',
            'custom.topic.suffix' => 'dev',
        ];

        $eventDirectories = [
            'tinkoff' => __DIR__ . '/events/tinkoff',
        ];

        $this->locator = new KafkaLocator($kafkaConfig, $eventDirectories);
    }

    public function run(): void
    {
        // Список событий для подписки, можно указывать по одному
        $eventNames = [
            'tinkoff/notifications/employeePassword',
        ];

        // Обработчик лучше делать сервисом, но можно и анонимным классом
        $eventHandler = new class() implements EventHandlerInterface {
            public function handle(EventInterface $event): void
            {
                $payload = $event->getPayload();

                echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), PHP_EOL;
            }
        };

        // Можно подписывать обработчики на любой набор событий
        $this->locator->getHandlers()->subscribe(
            $eventNames,
            $eventHandler
        );

        // Запуск получения событий из очереди
        $this->locator->getHandlers()->start();
    }
}
