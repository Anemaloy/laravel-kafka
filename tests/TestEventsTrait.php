<?php

namespace Anemaloy\KafkaLocator\Tests;

trait TestEventsTrait
{
    protected function getEventDirectory(): string
    {
        return realpath(__DIR__ . '/events');
    }

    protected function getEventUriPrefix(): string
    {
        return '';
    }
}
