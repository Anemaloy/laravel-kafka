<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Application\Locator;

use Anemaloy\KafkaLocator\Application\Events;
use Anemaloy\KafkaLocator\Application\Handlers;

interface LocatorInterface
{
    public function getHandlers(): Handlers;

    public function getEvents(): Events;
}
