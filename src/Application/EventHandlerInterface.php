<?php

declare(strict_types=1);

namespace Temo\KafkaLocator\Application;

use Temo\KafkaLocator\Domain\EventInterface;

interface EventHandlerInterface
{
    public function handle(EventInterface $event): void;
}
