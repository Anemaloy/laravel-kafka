<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Infrastructure\Queue\RdKafka;

final class Topic
{
    private string $topicSuffix;

    public function __construct(string $topicSuffix)
    {
        $this->topicSuffix = $topicSuffix;
    }

    public function getTopicName(string $eventName): string
    {
        [$country, $project, $entity] = \explode('/', $eventName, 4);

        return \implode('.', [
            $country,
            $project,
            $entity,
            $this->topicSuffix,
        ]);
    }

    /**
     * @param string[] $eventNames
     *
     * @return list<string>
     */
    public function getTopicNames(array $eventNames): array
    {
        $topicNames = [];
        foreach ($eventNames as $eventName) {
            $topicNames[] = $this->getTopicName($eventName);
        }

        return \array_values(\array_unique($topicNames));
    }
}
