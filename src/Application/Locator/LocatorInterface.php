<?php

declare(strict_types=1);

namespace Temo\KafkaLocator\Application\Locator;

use Temo\KafkaLocator\Application\Events;
use Temo\KafkaLocator\Application\Handlers;

interface LocatorInterface
{
    public function getHandlers(): Handlers;

    public function getEvents(): Events;
}
