<?php

namespace Anemaloy\KafkaLocator\Tests\Infrastructure\Queue\RdKafka;

use PHPUnit\Framework\TestCase;
use Anemaloy\KafkaLocator\Infrastructure\Queue\RdKafka\Topic;

class TopicTest extends TestCase
{
    public function provideTopicNames()
    {
        yield [
            'prod',
            ['test/amo/deal/created', 'test/amo/deal/updated', 'test/amo/user/registered'],
            ['test.amo.deal.prod', 'test.amo.user.prod'],
        ];
        yield [
            'dev',
            ['test/amo/deal/created', 'test/amo/deal/updated', 'test/amo/user/created', 'test/amo/user/registered'],
            ['test.amo.deal.dev', 'test.amo.user.dev'],
        ];
    }

    /**
     * @dataProvider provideTopicNames
     */
    public function testGetTopicNames(string $suffix, array $eventNames, array $topicNames): void
    {
        $topic = new Topic($suffix);
        self::assertSame($topicNames, $topic->getTopicNames($eventNames));
    }
}
