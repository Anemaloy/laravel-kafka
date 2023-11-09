<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Application;

use Anemaloy\KafkaLocator\Domain\EventInterface;

interface EventHandlerInterface
{
    public function handle(EventInterface $event): void;
}
